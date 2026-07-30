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

namespace Seatplus\Web\Http\Controllers\Corporation\MemberTracking;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Inertia\Inertia;
use Inertia\Response;
use Seatplus\Eveapi\Models\Corporation\CorporationInfo;
use Seatplus\Eveapi\Models\Corporation\CorporationMemberTracking;
use Seatplus\Web\Http\Controllers\Controller;
use Seatplus\Web\Http\Resources\MemberTrackingResource;
use Seatplus\Web\Services\Controller\CreateDispatchTransferObject;
use Seatplus\Web\Services\Controller\DispatchTransferObject;

class MemberTrackingController extends Controller
{
    public function index(): Response
    {
        $dispatchTransferObject = CreateDispatchTransferObject::new()
            ->setIsCharacter(false)
            ->create(CorporationMemberTracking::class);

        $corporations = $this->getAffiliatedCorporations($dispatchTransferObject);

        // One InfiniteScroll prop per corporation (mirrors the wallet/contracts migration),
        // replacing the axios/Ziggy useInfinityScrolling loader. Each paginator carries the
        // MemberTrackingResource shape and its own pageName so the per-corporation scroll
        // state never collides.
        $members = $corporations->mapWithKeys(fn (CorporationInfo $corporation): array => [
            "members_{$corporation->corporation_id}" => Inertia::scroll(
                fn () => $this->memberQuery($corporation->corporation_id)
                    ->paginate(pageName: "members_{$corporation->corporation_id}")
                    ->through(fn (CorporationMemberTracking $member) => (new MemberTrackingResource($member))->resolve()),
            ),
        ])->all();

        return Inertia::render('Corporation/MemberTracking/MemberTracking', [
            'dispatchTransferObject' => $dispatchTransferObject,
            'corporations' => $corporations,
            ...$members,
        ]);
    }

    /**
     * Base query for a corporation's ESI member tracking with everything the row cells render.
     * Pure ESI member tracking — SSO-scope compliance lives in Personnel → Observation.
     *
     * @return Builder<CorporationMemberTracking>
     */
    private function memberQuery(int $corporation_id): Builder
    {
        return CorporationMemberTracking::where('corporation_id', $corporation_id)
            ->with('character', 'location.locatable', 'ship');
    }

    private function getAffiliatedCorporations(DispatchTransferObject $dispatchTransferObject): Collection
    {
        $query = CorporationInfo::query()
            ->with('alliance')
            ->has('members');

        // Default view = the corporations the user operates (member + required role / Director); an
        // explicit corporation_ids selection is honoured only within the authorised set (that ∪ the
        // affiliated corps, composed as a subquery).
        if (request()->has('corporation_ids')) {
            $this->getAffiliatedIds->scope(
                query: $query,
                column: 'corporation_id',
                permissions: $dispatchTransferObject->permission,
                corporationRoles: $dispatchTransferObject->required_corporation_role,
            );
            $query->whereIn('corporation_id', request()->get('corporation_ids'));
        } else {
            $this->getAffiliatedIds->scopeOwned(
                query: $query,
                column: 'corporation_id',
                corporationRoles: $dispatchTransferObject->required_corporation_role,
            );
        }

        return $query->get();
    }
}
