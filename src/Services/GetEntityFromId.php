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

namespace Seatplus\Web\Services;

use Illuminate\Support\Collection;
use Seatplus\Eveapi\Models\Character\CharacterAffiliation;

class GetEntityFromId
{
    private string $type;

    private Collection $names;

    private GetNamesFromIdsService $get_names_from_ids_service;

    private string $cache_key;

    private ?array $cached_affiliation;

    public function __construct(public int $id)
    {
        $this->names = collect();
        $this->get_names_from_ids_service = new GetNamesFromIdsService();
        $this->cache_key = sprintf('entityById:%s', $id);
        $this->cached_affiliation = cache($this->cache_key);
    }

    public function execute()
    {
        if ($this->cached_affiliation) {
            return $this->cached_affiliation;
        }

        $character_affiliation = CharacterAffiliation::where('character_id', $this->id)
            ->orWhere('corporation_id', $this->id)
            ->orWhere('alliance_id', $this->id)
            ->with('character', 'corporation', 'alliance')
            ->first();

        if ($character_affiliation) {
            $this->determineTyp($character_affiliation);
        }

        if (!$character_affiliation) {
            $character_affiliation = $this->makeCharacterAffiliation();
        }

        return $this->buildResponse($character_affiliation);
    }

    private function makeCharacterAffiliation()
    {
        $this->convertIdsToNames([$this->id]);

        $this->type = $this->names->first()->category;

        if (!in_array($this->type, ['character', 'corporation', 'alliance'])) {
            return null;
        }

        $character_affiliation = new CharacterAffiliation();

        if ($this->type === 'character') {
            $response = (new GetCharacterAffiliations())->execute([$this->id])->first();

            $character_affiliation->character_id = $response->character_id;
            $character_affiliation->corporation_id = $response->corporation_id;
            $character_affiliation->alliance_id = optional($response)->alliance_id;
        }

        if ($this->type === 'alliance') {
            $character_affiliation->alliance_id = $this->id;
        }

        if ($this->type === 'corporation') {
            $response = (new GetCorporationInfo())->execute($this->id);

            $character_affiliation->corporation_id = $this->id;
            $character_affiliation->alliance_id = optional($response)->alliance_id;
        }

        return $character_affiliation;
    }

    private function determineTyp(CharacterAffiliation $character_affiliation)
    {
        if ($character_affiliation->character_id == $this->id) {
            $this->type = 'character';
        }

        if ($character_affiliation->corporation_id == $this->id) {
            $this->type = 'corporation';
        }

        if ($character_affiliation->alliance_id == $this->id) {
            $this->type = 'alliance';
        }
    }

    private function convertIdsToNames(array $ids)
    {
        $result = $this->get_names_from_ids_service->execute($ids);

        $this->names->push(...$result->toArray());
    }

    private function buildResponse(?CharacterAffiliation $character_affiliation): array
    {
        if (is_null($character_affiliation)) {
            return $this->buildUnknownResponse();
        }

        $this->convertUnknownIdsToNames($character_affiliation);

        $affiliation = $this->type === 'character'
            ? $this->buildCharacterResponse($character_affiliation)
            : (
                $this->type === 'corporation'
                ? $this->buildCorporationResponse($character_affiliation)
                : $this->buildAllianceResponse($character_affiliation)
            );

        cache([$this->cache_key => $affiliation], now()->addDay());

        return $affiliation;
    }

    private function convertUnknownIdsToNames(CharacterAffiliation $character_affiliation): void
    {
        $unknown_ids = collect();

        if (is_null($character_affiliation->character)) {
            $unknown_ids->push($character_affiliation->character_id);
        }

        if (is_null($character_affiliation->corporation)) {
            $unknown_ids->push($character_affiliation->corporation_id);
        }

        if (is_null($character_affiliation->alliance)) {
            $unknown_ids->push($character_affiliation->alliance_id);
        }

        if ($unknown_ids->isNotEmpty()) {
            $this->convertIdsToNames($unknown_ids->filter()->unique()->toArray());
        }
    }

    private function buildCharacterResponse(CharacterAffiliation $character_affiliation): array
    {
        $character = [
            'id'           => $this->id,
            'character_id' => $this->id,
            'name'         => $character_affiliation?->character?->name ?? $this->names->first(fn ($name)         => $name->id === $this->id)->name,
            'corporation'  => [
                'name' => $character_affiliation?->corporation?->name ?? $this->names->first(fn ($name) => $name->id === $character_affiliation->corporation_id)->name,
            ],
        ];

        if ($character_affiliation->alliance_id) {
            $character['alliance'] = ['name' => $character_affiliation?->alliance?->name ?? $this->names->first(fn ($name) => $name->id === $character_affiliation->allince_id)];
        }

        return $character;
    }

    private function buildCorporationResponse(CharacterAffiliation $character_affiliation): array
    {
        $corporation = [
            'id'             => $this->id,
            'corporation_id' => $this->id,
            'name'           => $character_affiliation?->corporation?->name ?? $this->names->first(fn ($name)           => $name->id === $this->id)->name,
        ];

        if ($character_affiliation->alliance_id) {
            $corporation['alliance'] = ['name' => data_get($character_affiliation, 'alliance.name') ?? $this->names->first(fn ($name) => $name->id === $character_affiliation->alliance_id)->name];
        }

        return $corporation;
    }

    private function buildAllianceResponse(CharacterAffiliation $character_affiliation): array
    {
        return [
            'id'          => $this->id,
            'alliance_id' => $this->id,
            'name'        => $character_affiliation?->alliance?->name ?? $this->names->first(fn ($name)        => $name->id === $this->id)->name,
        ];
    }

    private function buildUnknownResponse(): array
    {
        $unknown = $this->names->first();

        $return_value = [
            'id'   => $unknown->id,
            'name' => $unknown->name,
        ];

        if ($unknown->category === 'character') {
            $return_value['character_id'] = $unknown->id;
        }

        if ($unknown->category === 'corporation') {
            $return_value['corporation_id'] = $unknown->id;
        }

        if ($unknown->category === 'alliance' || !in_array($unknown->category, ['character', 'corporation'])) {
            $return_value['alliance_id'] = $unknown->id;
        }

        return $return_value;
    }
}
