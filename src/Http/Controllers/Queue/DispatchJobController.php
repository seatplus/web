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

use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Bus;
use Seatplus\Auth\Services\Dtos\AffiliationsDto;
use Seatplus\Eveapi\Containers\JobContainer;
use Seatplus\Eveapi\Models\RefreshToken;
use Seatplus\Eveapi\Services\FindCorporationRefreshToken;
use Seatplus\Web\Http\Controllers\Controller;
use Seatplus\Web\Http\Controllers\Request\DispatchIndividualJob;
use Seatplus\Web\Jobs\ManualDispatchedJob;

class DispatchJobController extends Controller
{
    protected array $dispatch_transfer_object;

    public function dispatch(DispatchIndividualJob $job)
    {
        $this->dispatch_transfer_object = $job->get('dispatch_transfer_object');

        $id = $job->get('character_id') ?? $job->get('corporation_id');

        $cache_key = $this->getCacheKey(Arr::get($this->dispatch_transfer_object, 'manual_job'), $id);

        if (cache($cache_key)) {
            return redirect()->back()->with('error', 'job was already queued');
        }

        $hydrate_job_string = config('web.jobs.' . Arr::get($this->dispatch_transfer_object, 'manual_job'));
        $job_container = new JobContainer(['refresh_token' => $this->getRefreshToken($job)]);

        $hydrate_job = new $hydrate_job_string($job_container);
        $batch_name = sprintf('Manual batch update of %s', $cache_key);

        $batch_id = (new ManualDispatchedJob)
            ->setJobs([$hydrate_job])
            ->setName($batch_name)
            ->handle();

        cache([$cache_key => $batch_id], now()->addHour());

        return $batch_id;
    }

    public function getEntities(Request $request)
    {
        $validated_data = $request->validate([
            'manual_job' => ['required', fn ($attribute, $value, $fail) => Arr::has(config('web.jobs'), $value) ?: $fail($attribute . ' is invalid.')],
            'permission' => ['required'],
            'required_scopes' => ['required', 'array'],
            'required_corporation_role' => ['nullable', 'string'],
        ]);

        $affiliationsDto = new AffiliationsDto(
            user: auth()->user(),
            permissions: [data_get($validated_data, 'permission')],
            corporation_roles:  data_get($validated_data, 'required_corporation_role') ? [ data_get($validated_data, 'required_corporation_role') ] : null,
        );

        $isCorporationScope = ! ! $affiliationsDto->corporation_roles;

        $tokens = RefreshToken::query()
            ->whereHas('character', fn ($query) => $query->whereAffiliatedCharacters($affiliationsDto))
            ->with('character', 'character.roles', 'character.corporation')
            ->cursor()
            ->filter(fn ($token) => collect($request->get('required_scopes'))->intersect($token->scopes)->isNotEmpty())
            ->when(
                $isCorporationScope,
                fn ($tokens) => $tokens
                ->filter(fn ($token) => $token->character->roles->hasRole('roles', $request->get('required_corporation_role')))
                ->unique(fn ($token) => $token->corporation_id)
            )
            ->map(fn ($token) => collect([
                'character_id' => $isCorporationScope ? null : $token->character_id,
                'corporation_id' => $isCorporationScope ? $token->corporation_id : null,
                'name' => $isCorporationScope ? $token->corporation->name : $token->character->name,
                'batch' => $this->getBatchStatus(cache($this->getCacheKey($request->get('manual_job'), $isCorporationScope ? $token->corporation_id : $token->character_id))),
            ])->filter()->toArray())
            ->values();

        return new LengthAwarePaginator($tokens, $tokens->count(), $tokens->count());
    }

    private function getRefreshToken(DispatchIndividualJob $job)
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

    public function getBatchStatus(?string $batch_id)
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
