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

use Illuminate\Http\Request;
use Inertia\Inertia;
use Laravel\Horizon\Contracts\JobRepository;
use Seatplus\Eveapi\Http\Resources\CharacterAsset as CharacterAssetResource;
use Seatplus\Eveapi\Jobs\Hydrate\Character\CharacterAssetsHydrateBatch;
use Seatplus\Eveapi\Models\Assets\CharacterAsset;
use Seatplus\Eveapi\Models\Universe\Region;
use Seatplus\Web\Http\Controllers\Controller;

class AssetsController extends Controller
{
    public function __construct(
        /**
         * @var \Laravel\Horizon\Contracts\JobRepository
         */
        private JobRepository $jobs
    ) {
    }

    public function index(Request $request)
    {
        $filters = fn () => ['regions' => Region::all()];

        return Inertia::render('Character/Assets', [
            'filters' => $filters,
            'dispatch_transfer_object' => $this->buildDispatchTransferObject(),
        ]);
    }

    public function details(int $item_id)
    {
        $query = CharacterAsset::with('location', 'type', 'type.group', 'container', 'content', 'content.content', 'content.type', 'content.type.group')
            ->affiliated(getAffiliatedIdsByClass(CharacterAsset::class), request()->query('character_ids'))
            ->where('item_id', $item_id);

        $item = CharacterAssetResource::collection($query->get());

        return Inertia::render('Character/ItemDetails', [
            'item' => $item,
        ]);
    }

    private function buildDispatchTransferObject(): object
    {
        return (object) [
            'manual_job' => array_search(CharacterAssetsHydrateBatch::class, config('web.jobs')),
            'permission' => config('eveapi.permissions.' . CharacterAsset::class),
            'required_scopes' => config('eveapi.scopes.character.assets'),
            'required_corporation_role' => '',
        ];
    }
}
