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

namespace Seatplus\Web\Http\Controllers\Character;

use Seatplus\Eveapi\Models\Character\CharacterAffiliation;
use Seatplus\Eveapi\Models\Contacts\Contact;
use Seatplus\Web\Http\Controllers\Controller;
use Seatplus\Web\Http\Resources\ContactResource;
use Seatplus\Web\Services\Controller\CreateDispatchTransferObject;
use Seatplus\Web\Services\Controller\GetAffiliatedIdsService;
use Seatplus\Web\Services\GetRecruitIdsService;

class ContactsController extends Controller
{
    public function index()
    {
        $dispatchTransferObject = CreateDispatchTransferObject::new()
            ->create(Contact::class);

        $ids = GetAffiliatedIdsService::make()
            ->viaDispatchTransferObject($dispatchTransferObject)
            ->setRequestFlavour('character')
            ->get();

        $characters = CharacterAffiliation::whereIn('character_id', $ids)->with('character.corporation')->get();

        return inertia('Character/Contact/Index', [
            'dispatchTransferObject' => $dispatchTransferObject,
            'characters' => $characters,
        ]);
    }

    public function detail(int $id)
    {
        $affiliated_ids = CharacterAffiliation::whereIn('character_id', [...getAffiliatedIdsByClass(Contact::class), ...GetRecruitIdsService::get()])
            ->cursor()
            ->map(fn ($affiliation) => [$affiliation->character_id, $affiliation->corporation_id, $affiliation->alliance_id])
            ->flatten()
            ->unique()
            ->toArray();

        abort_unless(in_array($id, $affiliated_ids),
            403,
            'You seem not to be affiliated to the requested id, if you seeing this error in recruiting,' .
            'this either means the alliance or corporation contact details are not present');

        $query = Contact::with('labels')
            ->where('contactable_id', $id);

        return ContactResource::collection(
            $query->paginate()
        )->additional(['meta' => [
            'entity_id' => $id,
        ]]);
    }
}
