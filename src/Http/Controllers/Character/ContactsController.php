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

use Illuminate\Database\Eloquent\Builder;
use Inertia\Inertia;
use Inertia\Response;
use Seatplus\Eveapi\Models\Alliance\AllianceInfo;
use Seatplus\Eveapi\Models\Character\CharacterAffiliation;
use Seatplus\Eveapi\Models\Character\CharacterInfo;
use Seatplus\Eveapi\Models\Contacts\Contact;
use Seatplus\Eveapi\Models\Corporation\CorporationInfo;
use Seatplus\Web\Http\Controllers\Controller;
use Seatplus\Web\Http\Resources\ContactResource;
use Seatplus\Web\Services\Controller\CreateDispatchTransferObject;

class ContactsController extends Controller
{
    public function index(): Response
    {
        $dispatchTransferObject = CreateDispatchTransferObject::new()
            ->create(Contact::class);

        // Always hard-scope to the authorised set (composed as a subquery, not a materialised
        // affiliated-id array); a requested character_ids filter only narrows *within* that set. This
        // route has no CheckAuthorization middleware, so the query is the sole tamper guard — a
        // character_ids the user isn't affiliated with must never leak through.
        $query = CharacterInfo::query()
            ->has('contacts')
            ->with('characterAffiliation');

        $this->getAffiliatedIds->scope(
            query: $query,
            column: 'character_id',
            permissions: $dispatchTransferObject->permission,
            corporationRoles: $dispatchTransferObject->required_corporation_role,
        );

        $characters = $query
            ->when(
                request()->has('character_ids'),
                fn (Builder $query) => $query->whereIn('character_id', (array) request('character_ids')),
            )
            ->get()
            ->each->append('corporation_id');

        return Inertia::render('Character/Contact/Index', [
            'dispatchTransferObject' => $dispatchTransferObject,
            'characters' => $characters,
            // Keyed by character_id → the character's own contacts, resolved eagerly per character.
            // ContactResource reads the corp/alliance standings from the request session, so each
            // character's collection must be resolved before the next iteration overwrites it.
            'contacts' => Inertia::defer(fn () => $characters->mapWithKeys(fn (CharacterInfo $character) => [
                $character->character_id => $this->resolveContacts($character),
            ])),
        ]);
    }

    private function resolveContacts(CharacterInfo $character): array
    {
        $affiliation = CharacterAffiliation::query()
            ->firstWhere('corporation_id', $character->corporation_id);

        $contactable_ids = array_filter([
            $affiliation?->corporation_id,
            $affiliation?->alliance_id,
        ]);

        $corp_alliance_standing = Contact::query()
            ->whereIn('contactable_id', $contactable_ids)
            ->get();

        request()->session()->now('contacts', [
            'corporation_contacts' => $corp_alliance_standing->filter(fn (Contact $contact) => $contact->contactable_type === CorporationInfo::class),
            'alliance_contacts' => $corp_alliance_standing->filter(fn (Contact $contact) => $contact->contactable_type === AllianceInfo::class),
        ]);

        $contacts = Contact::with(['labels', 'characterAffiliation', 'corporationAffiliation', 'allianceAffiliation', 'factionAffiliation'])
            ->where('contactable_id', $character->character_id)
            ->get();

        return ContactResource::collection($contacts)->resolve(request());
    }
}
