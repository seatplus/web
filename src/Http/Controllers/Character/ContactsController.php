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

use Seatplus\Auth\Services\Affiliations\GetOwnedAffiliatedIdsService;
use Seatplus\Auth\Services\Dtos\AffiliationsDto;
use Seatplus\Eveapi\Models\Alliance\AllianceInfo;
use Seatplus\Eveapi\Models\Character\CharacterInfo;
use Seatplus\Eveapi\Models\Contacts\Contact;
use Seatplus\Eveapi\Models\Corporation\CorporationInfo;
use Seatplus\Web\Http\Controllers\Controller;
use Seatplus\Web\Http\Controllers\Request\ContactsRequest;
use Seatplus\Web\Http\Resources\ContactResource;
use Seatplus\Web\Services\Controller\CreateDispatchTransferObject;

class ContactsController extends Controller
{
    public function index()
    {
        $dispatchTransferObject = CreateDispatchTransferObject::new()
            ->create(Contact::class);

        $affiliations_dto = new AffiliationsDto(
            permissions: [data_get($dispatchTransferObject, 'permission')],
            user: auth()->user()
        );

        $owned_characters = GetOwnedAffiliatedIdsService::make($affiliations_dto)
            ->getQuery();

        $characters = CharacterInfo::query()
            ->has('contacts')
            ->when(
                request()->has('character_ids'),
                fn ($query) => $query->whereIn('character_id', request()->get('character_ids')),
                fn ($query) => $query->joinSub($owned_characters, 'owned_characters', 'character_infos.character_id', '=', 'owned_characters.affiliated_id')
            )->get();

        return inertia('Character/Contact/Index', [
            'dispatchTransferObject' => $dispatchTransferObject,
            'characters' => $characters,
        ]);
    }

    public function getContacts(int $character_id, ContactsRequest $request)
    {
        $validated = $request->validated();

        $corp_alliance_standing = Contact::query()
            ->whereIn('contactable_id', collect([$validated['corporation_id'], data_get($validated, 'alliance_id')])->filter()->toArray())
            ->get();

        $query = Contact::with(['labels', 'character_affiliation', 'corporation_affiliation', 'alliance_affiliation', 'faction_affiliation'])
            ->where('contactable_id', $character_id);

        $request->session()->now('contacts', [
            'corporation_contacts' => $corp_alliance_standing->filter(fn (Contact $contact) => $contact->contactable_type === CorporationInfo::class),
            'alliance_contacts' => $corp_alliance_standing->filter(fn (Contact $contact) => $contact->contactable_type === AllianceInfo::class),
        ]);

        return ContactResource::collection(
            $query->paginate(),
        );
    }
}
