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

namespace Seatplus\Web\Http\Controllers\AccessControl;

use Seatplus\Auth\Models\Permissions\Role;
use Seatplus\Eveapi\Models\Alliance\AllianceInfo;
use Seatplus\Eveapi\Models\Corporation\CorporationInfo;
use Seatplus\Web\Http\Controllers\Controller;
use Seatplus\Web\Http\Resources\RoleRessource;

class ListControlGroupsController extends Controller
{
    public function __invoke()
    {
        $character_ids = auth()->user()->characters->map(fn ($character) => $character->character_id)->toArray();

        $query = Role::with('moderators.affiliatable.characters', 'acl_members')
            ->when(auth()->user()->can('superuser'),
                // Condition if user has superuser
                fn ($query) => $query->orWhereNotIn('id', []),
                // if user does not have superuser
                fn ($query) => $query
                    ->whereHas('members', fn ($query) => $query->whereUserId(auth()->user()->getAuthIdentifier()))
                    ->orWhereHas('acl_affiliations', fn ($query) => $query->whereHasMorph('affiliatable',
                        [CorporationInfo::class, AllianceInfo::class],
                        fn ($query) => $query->whereHas('characters', fn ($query) => $query->whereIn('character_infos.character_id', $character_ids))
                    ))
            );

        return RoleRessource::collection(
            $query->paginate()
        );
    }
}
