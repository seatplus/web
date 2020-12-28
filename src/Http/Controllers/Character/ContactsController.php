<?php

/*
 * MIT License
 *
 * Copyright (c) 2019, 2020 Felix Huber
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

use Seatplus\Eveapi\Jobs\Hydrate\Character\ContactHydrateBatch;
use Seatplus\Eveapi\Models\Character\CharacterAffiliation;
use Seatplus\Eveapi\Models\Contacts\Contact;
use Seatplus\Web\Http\Controllers\Controller;
use Seatplus\Web\Http\Resources\ContactResource;

class ContactsController extends Controller
{
    public function index()
    {
        $ids = request()->has('ids')
            ? fn () => collect(request()->get('ids'))->map(fn ($character_id) => intval($character_id))->intersect(getAffiliatedIdsByClass(Contact::class))->toArray()
            : auth()->user()->characters->pluck('character_id')->toArray();

        $characters = CharacterAffiliation::whereIn('character_id', $ids)->with('character.corporation')->get();

        return inertia('Character/Contact/Index', [
            'dispatch_transfer_object' => $this->buildDispatchTransferObject(),
            'characters' => $characters,
        ]);
    }

    public function detail(int $id)
    {
        $affiliated_ids = CharacterAffiliation::whereIn('character_id', getAffiliatedIdsByClass(Contact::class))
            ->cursor()
            ->map(fn ($affiliation) => [$affiliation->character_id, $affiliation->corporation_id, $affiliation->alliance_id])
            ->flatten()
            ->unique()
            ->toArray();

        abort_unless(in_array($id, $affiliated_ids), 403);

        $query = Contact::with('labels')
            ->where('contactable_id', $id);

        return ContactResource::collection(
            $query->paginate()
        )->additional(['meta' => [
            'entity_id' => $id,
        ]]);
    }

    private function buildDispatchTransferObject(): object
    {
        return (object) [
            'manual_job' => array_search(ContactHydrateBatch::class, config('web.jobs')),
            'permission' => config('eveapi.permissions.' . Contact::class),
            'required_scopes' => config('eveapi.scopes.character.contacts'),
            'required_corporation_role' => '',
        ];
    }
}
