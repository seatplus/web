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

namespace Seatplus\Web\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Collection;
use Seatplus\Eveapi\Models\Character\CharacterAffiliation;

class ContactResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request
     * @return array
     */
    public function toArray($request)
    {
        $corpAllianceContacts = $request->session()->get('contacts');

        return [
            'contact_id' => $this->contact_id,
            'contact_type' => $this->contact_type,
            'is_blocked' => $this->is_blocked,
            'is_watched' => $this->is_watched,
            'standing' => $this->standing,
            'labels' => $this->labels,
            'corporation_standing' => $this->getStanding($this->affiliation, data_get($corpAllianceContacts, 'corporation_contacts')),
            'alliance_standing' => $this->getStanding($this->affiliation, data_get($corpAllianceContacts, 'alliance_contacts')),
        ];
    }

    private function getStanding(?CharacterAffiliation $affiliation, Collection $contacts): ?int
    {
        if(is_null($affiliation) || $contacts->isEmpty()) {
            return null;
        }

        $contact = collect([
            $affiliation->alliance_id,
            $affiliation->corporation_id,
            $affiliation->faction_id,
            $affiliation->character_id
        ])
            ->filter()
            ->map(fn (int $contact_id) => $contacts->firstWhere('contact_id', $contact_id))
            ->first();

        return $contact?->standing;

    }
}
