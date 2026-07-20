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

namespace Seatplus\Web\Http\Actions\Recruitment;

use Illuminate\Support\Arr;
use Seatplus\Auth\Models\User;
use Seatplus\Eveapi\Models\Character\CharacterInfo;
use Seatplus\Web\Services\Recruitment\ApplicationGroupService;

class HandleApplicationAction
{
    private User $user;

    private ?int $corporation_id = null;

    public function __construct(
        private readonly ApplicationGroupService $groupService,
    ) {
        $this->user = auth()->user();
    }

    public function execute(array $data): void
    {
        $this->corporation_id = Arr::get($data, 'corporation_id');

        $characterIds = $this->resolveCharacterIds($data);

        if ($characterIds === []) {
            $this->handleUserApplication();

            return;
        }

        $this->handleCharacterApplications($characterIds);
    }

    /**
     * A single-character apply (`character_id`) and a multi-character apply (`character_ids[]`) are
     * normalised to one list; whole-account applies pass neither.
     *
     * @param  array<string, mixed>  $data
     * @return array<int, int>
     */
    private function resolveCharacterIds(array $data): array
    {
        $ids = (array) Arr::get($data, 'character_ids', []);

        if ($ids === [] && Arr::get($data, 'character_id')) {
            $ids = [Arr::get($data, 'character_id')];
        }

        return array_map('intval', array_values(array_filter($ids)));
    }

    private function handleUserApplication(): void
    {
        $this->user->application()->create(['corporation_id' => $this->corporation_id]);
    }

    /**
     * @param  array<int, int>  $characterIds
     */
    private function handleCharacterApplications(array $characterIds): void
    {
        $applicationIds = [];

        foreach ($characterIds as $characterId) {
            abort_unless($this->characterBelongsToUser($characterId), 403, 'submitted character_id does not belong to user');

            /** @var CharacterInfo $character */
            $character = CharacterInfo::findOrFail($characterId);

            // Don't stack a second open application onto a character already applying to this corp.
            if ($character->application()->where('corporation_id', $this->corporation_id)->exists()) {
                continue;
            }

            $application = $character->application()->create(['corporation_id' => $this->corporation_id]);
            $applicationIds[] = (string) $application->getKey();
        }

        // A group only matters when several characters applied together; a lone application stays
        // ungrouped and is rendered via the singleton fallback in ApplicationGroupService.
        if (count($applicationIds) > 1) {
            $this->groupService->create($applicationIds);
        }
    }

    private function characterBelongsToUser(int $characterId): bool
    {
        return in_array($characterId, $this->user->characters->pluck('character_id')->toArray());
    }
}
