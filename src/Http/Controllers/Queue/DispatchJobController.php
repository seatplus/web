<?php

/*
 * MIT License
 *
 * Copyright (c) 2019, 2020, 2021 Felix Huber
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in all
 * copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
 * SOFTWARE.
 */

namespace Seatplus\Web\Http\Controllers\Queue;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\LazyCollection;
use Seatplus\Eveapi\Models\Character\CharacterInfo;
use Seatplus\Eveapi\Models\Character\CharacterRole;
use Seatplus\Eveapi\Models\Corporation\CorporationInfo;
use Seatplus\Eveapi\Models\RefreshToken;
use Seatplus\Eveapi\Services\FindCorporationRefreshToken;
use Seatplus\Web\Contracts\WebJobsRepository;
use Seatplus\Web\Http\Controllers\Controller;
use Seatplus\Web\Http\Controllers\Request\DispatchIndividualJob;
use Seatplus\Web\Jobs\ManualDispatchedJob;
use Seatplus\Web\Services\GetAffiliatedIds;

class DispatchJobController extends Controller
{
    protected array $dispatch_transfer_object;

    public function __construct(
        Request $request,
        GetAffiliatedIds $getAffiliatedIds,
        private readonly WebJobsRepository $web_jobs,
    ) {
        parent::__construct($request, $getAffiliatedIds);
    }

    public function dispatch(DispatchIndividualJob $job): RedirectResponse|string
    {
        $this->dispatch_transfer_object = $job->get('dispatch_transfer_object');

        $id = $job->get('character_id') ?? $job->get('corporation_id');
        $manual_job = Arr::get($this->dispatch_transfer_object, 'manual_job');

        $cache_key = "{$manual_job}:{$id}";

        if (cache($cache_key)) {
            return redirect()->back()->with('error', 'job was already queued');
        }

        $batch_name = sprintf('Manual batch update of %s', $cache_key);

        $batch_id = (new ManualDispatchedJob)
            ->setJobs($this->web_jobs->getConstructedJobs($manual_job, $this->getRefreshToken($job)))
            ->setName($batch_name)
            ->handle();

        cache([$cache_key => $batch_id], now()->addHour());

        return $batch_id;
    }

    public function getEntities(Request $request): LengthAwarePaginator
    {
        $validated_data = $request->validate([
            'manual_job' => ['required', fn (string $attribute, mixed $value, \Closure $fail) => in_array($value, $this->web_jobs->getJobKeys()) ?: $fail($attribute.' is invalid.')],
            'permission' => ['required'],
            'required_scopes' => ['required', 'array'],
            'required_corporation_role' => ['present', 'array'],
            'required_corporation_role.*' => ['string'],
            'ownership' => ['sometimes', 'in:owned,affiliated'],
        ]);

        $permission = data_get($validated_data, 'permission');
        $corporation_roles = data_get($validated_data, 'required_corporation_role', []);
        $ownership = data_get($validated_data, 'ownership', 'owned');

        $isCorporationScope = filled($corporation_roles);

        // The user's own characters come from the cached permission object (no query). The
        // owned section scopes straight to them; only the affiliated section pays for the
        // broader affiliation resolve — and it drops owned characters so the two sections
        // never overlap and the eager owned list stays O(own characters), not O(affiliated).
        $owned_character_ids = $this->getAffiliatedIds->ownedCharacterIds();

        $character_ids = $ownership === 'owned'
            ? $owned_character_ids
            : array_values(array_diff(
                $this->getAffiliatedIds->get(permissions: [$permission], corporationRoles: $corporation_roles),
                $owned_character_ids,
            ));

        $tokens = RefreshToken::query()
            ->whereHas('character', fn (Builder $query) => $query->whereIn('character_id', $character_ids))
            ->with('character', 'character.roles', 'character.corporation')
            ->cursor()
            ->filter(fn (RefreshToken $token) => collect($validated_data['required_scopes'])->intersect($token->scopes)->isNotEmpty())
            ->when(
                $isCorporationScope,
                fn (LazyCollection $tokens) => $tokens->filter(function (RefreshToken $token) use ($corporation_roles): bool {
                    /** @var CharacterInfo $character */
                    $character = $token->character;
                    /** @var CharacterRole $roles */
                    $roles = $character->roles;

                    return collect($corporation_roles)->contains(fn (string $role) => $roles->hasRole('roles', $role));
                })
            )
            ->collect();

        // Corp scope, affiliated section: drop corporations the user already manages through
        // one of their own characters, so a corporation never appears in both sections.
        if ($isCorporationScope && $ownership === 'affiliated') {
            $owned_corporation_ids = CharacterInfo::query()
                ->whereIn('character_id', $owned_character_ids)
                ->pluck('corporation_id');

            $tokens = $tokens->reject(fn (RefreshToken $token) => $owned_corporation_ids->contains($token->corporation_id));
        }

        $entities = $tokens
            ->when($isCorporationScope, fn (Collection $tokens) => $tokens->unique(fn (RefreshToken $token) => $token->corporation_id))
            ->map(function (RefreshToken $token) use ($isCorporationScope, $validated_data): array {
                /** @var CharacterInfo $character */
                $character = $token->character;
                /** @var CorporationInfo $corporation */
                $corporation = $token->corporation;

                return collect([
                    'character_id' => $isCorporationScope ? null : $token->character_id,
                    'corporation_id' => $isCorporationScope ? $token->corporation_id : null,
                    'name' => $isCorporationScope ? $corporation->name : $character->name,
                    'batch' => $this->getBatchStatus(cache($this->getCacheKey($validated_data['manual_job'], $isCorporationScope ? $token->corporation_id : $token->character_id))),
                ])->filter()->toArray();
            })
            ->values();

        return $this->paginate($entities, $request);
    }

    /**
     * @param  Collection<int, array<string, mixed>>  $items
     */
    private function paginate(Collection $items, Request $request): LengthAwarePaginator
    {
        $per_page = (int) $request->get('per_page', 10);
        $page = (int) $request->get('page', 1);

        return new LengthAwarePaginator(
            $items->forPage($page, $per_page)->values(),
            $items->count(),
            $per_page,
            $page,
            [
                'path' => $request->url(),
                'query' => $request->query(),
            ]
        );
    }

    private function getRefreshToken(DispatchIndividualJob $job): ?RefreshToken
    {
        if ($job->get('character_id')) {
            return RefreshToken::find($job->get('character_id'));
        }

        return (new FindCorporationRefreshToken)(
            $job->get('corporation_id'),
            Arr::get($this->dispatch_transfer_object, 'required_scopes'),
            Arr::get($this->dispatch_transfer_object, 'required_corporation_role')
        );
    }

    private function getCacheKey(string $job_name, int $id): string
    {
        return sprintf('%s:%s', $job_name, $id);
    }

    public function getBatchStatus(?string $batch_id): array|string
    {
        if (is_null($batch_id)) {
            return [
                'state' => 'ready',
            ];
        }

        $batch = Bus::findBatch($batch_id);

        if ($batch->failedJobs > 0 && $batch->progress() < 100) {
            return [
                'state' => 'failures',
                'time' => $batch->finishedAt,
                'batch_id' => $batch_id,
            ];
        }

        if ($batch->progress() == 100) {
            return [
                'state' => 'finished',
                'time' => $batch->finishedAt,
                'batch_id' => $batch_id,
            ];
        }

        if ($batch->pendingJobs > 0 && ! $batch->failedJobs) {
            return [
                'state' => 'pending',
                'time' => $batch->createdAt,
                'batch_id' => $batch_id,
            ];
        }

        return 'unknown';
    }
}
