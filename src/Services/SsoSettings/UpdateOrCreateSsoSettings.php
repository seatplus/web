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

namespace Seatplus\Web\Services\SsoSettings;

use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Seatplus\Eveapi\Models\Alliance\AllianceInfo;
use Seatplus\Eveapi\Models\Corporation\CorporationInfo;
use Seatplus\Eveapi\Models\SsoScopes;
use Seatplus\Web\Services\DispatchCorporationOrAllianceInfoJob;

class UpdateOrCreateSsoSettings
{
    private Collection $selected_scopes;

    private readonly Collection $entities;

    private readonly string $type;

    /**
     * Entities that were submitted but could not be saved. Reported back to the caller so a
     * malformed selection surfaces in the UI instead of being dropped without a trace.
     *
     * @var list<array{label: string, reason: string}>
     */
    private array $skipped = [];

    private int $saved = 0;

    public function __construct(
        private readonly array $request
    ) {
        $this->buildSelectedScopes();

        $this->entities = collect(Arr::get($this->request, 'selectedEntities'));
        $this->type = Arr::get($this->request, 'type');
    }

    public function execute(): void
    {
        $this->entities->whenEmpty(
            function (Collection $collection) {
                if ($this->type === 'global') {
                    // Matched on the absence of a morphable, not on the type alone: an entity row
                    // carrying type 'global' would otherwise be hijacked and have this installation-wide
                    // list written over its corporation's own requirement.
                    SsoScopes::updateOrCreate(
                        ['morphable_id' => null, 'morphable_type' => null],
                        ['selected_scopes' => $this->selected_scopes->unique()->values(), 'type' => 'global'],
                    );

                    $this->saved++;
                }
            },
            fn (Collection $collection) => $collection
                ->each(function (array $entity) {
                    $entity_id = Arr::get($entity, 'id');
                    $category = Arr::get($entity, 'category');

                    if ($entity_id === null) {
                        // Without this the null would reach DispatchCorporationOrAllianceInfoJob::handle(),
                        // whose $id is a non-nullable int, and throw a TypeError (#1387).
                        $this->skip($entity, 'no id was submitted');

                        return;
                    }

                    $morphable_type = match ($category) {
                        'corporation' => CorporationInfo::class,
                        'alliance' => AllianceInfo::class,
                        default => null,
                    };

                    if ($morphable_type === null) {
                        $this->skip($entity, 'unknown category');

                        return;
                    }

                    (new DispatchCorporationOrAllianceInfoJob)->handle($morphable_type, $entity_id);

                    // Matched on the whole morph, not on the id alone: a corporation and an alliance
                    // may share an id, and matching by id lets one hijack the other's record.
                    SsoScopes::updateOrCreate([
                        'morphable_id' => $entity_id,
                        'morphable_type' => $morphable_type,
                    ], [
                        'selected_scopes' => $this->selected_scopes->unique(),
                        'type' => $this->type,
                    ]);

                    $this->saved++;
                })
        );
    }

    /**
     * Entities that were submitted but not saved, so the caller can tell the user which of their
     * selections did not take effect.
     *
     * @return list<array{label: string, reason: string}>
     */
    public function skippedEntities(): array
    {
        return $this->skipped;
    }

    /**
     * How many sso_scopes rows this run wrote. Zero means nothing was saved, so a caller must not
     * report success.
     */
    public function savedCount(): int
    {
        return $this->saved;
    }

    /**
     * @param  array<string, mixed>  $entity
     */
    private function skip(array $entity, string $reason): void
    {
        $name = Arr::get($entity, 'name');
        $category = Arr::get($entity, 'category');

        $this->skipped[] = [
            'label' => match (true) {
                is_string($name) && $name !== '' => $name,
                is_string($category) && $category !== '' => $category,
                default => 'unknown entity',
            },
            'reason' => $reason,
        ];

        logger()->warning('Skipped an SSO settings entity that could not be saved', [
            'reason' => $reason,
            'entity' => $entity,
        ]);
    }

    private function buildSelectedScopes(): void
    {
        $this->selected_scopes = collect();

        collect(Arr::get($this->request, 'selectedScopes'))
            ->flatMap(fn (string $scope) => explode(',', (string) $scope))
            ->each(function (string $scope) {
                // If it is a corporation scope, we need to know the characters role
                if (Str::of($scope)->contains('corporation')) {
                    $this->selected_scopes->push('esi-characters.read_corporation_roles.v1');
                }

                $this->selected_scopes->push($scope);
            });

        if (Arr::hasAny($this->selected_scopes->unique()->toArray(), ['esi-wallet.read_corporation_wallets.v1'])) {
            $this->selected_scopes->push('esi-corporations.read_divisions.v1');
        }
    }
}
