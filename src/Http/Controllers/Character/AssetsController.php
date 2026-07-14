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

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;
use Seatplus\Eveapi\Models\Assets\Asset as EveApiAsset;
use Seatplus\Eveapi\Models\Character\CharacterInfo;
use Seatplus\Eveapi\Models\Universe\Location;
use Seatplus\Web\Http\Actions\Character\Asset\GetCharacterAssetLocationAction;
use Seatplus\Web\Http\Actions\Character\Asset\GetLocationTopLevelAssetsAction;
use Seatplus\Web\Http\Controllers\Controller;
use Seatplus\Web\Http\Resources\AssetResource;
use Seatplus\Web\Http\Resources\LocationRessource;
use Seatplus\Web\Services\Controller\CreateDispatchTransferObject;
use Seatplus\Web\Services\Controller\DispatchTransferObject;

class AssetsController extends Controller
{
    public function index(Request $request, GetCharacterAssetLocationAction $action): Response
    {
        $dispatchTransferObject = $this->getDispatchTransferObject();
        $characterIds = $this->getCharacterIds($dispatchTransferObject, 'assets');

        // Filters + (a subset of) character scope come from the page query. Constrain requested
        // character_ids to the authorised set; default to all of it.
        $validated = $request->only(['search', 'systems', 'regions', 'types', 'groups', 'categories', 'only_unknown_locations']);
        $requested = collect($request->input('character_ids'))->map(fn ($id): int => (int) $id)->filter();
        $validated['character_ids'] = $requested->isEmpty()
            ? $characterIds->values()->all()
            : $characterIds->intersect($requested)->values()->all();

        return Inertia::render('Character/Assets', [
            'dispatchTransferObject' => $dispatchTransferObject,
            'characterIds' => $characterIds,
            // Fully flatten each location (assets → type → group, etc.) to primitive arrays.
            // A bare ->resolve() only resolves the top level, leaving nested JsonResources as
            // objects that Inertia then re-wraps/serializes inconsistently; encoding the whole
            // tree once yields the plain arrays the Vue side expects.
            'assets' => Inertia::scroll(fn () => $action->execute($validated)
                ->through(fn (Location $location): array => json_decode(
                    (string) json_encode((new LocationRessource($location))->resolve()),
                    true,
                ))),
        ]);
    }

    /**
     * A single location's top-level items (filtered-top-level via root_item_id), paginated and
     * lazy-loaded when the location scrolls into view. Authorised identically to index(): the
     * requested character_ids are intersected with getCharacterIds('assets') — own + assets-
     * permission-affiliated (corp/alliance member management) + Director + superuser (+ recruits
     * once impersonated) — and the query is hard-scoped to that set, so an arbitrary location_id
     * or character_id can only ever return authorised assets.
     */
    public function location(int $location_id, Request $request, GetLocationTopLevelAssetsAction $action): JsonResponse
    {
        $authorized = $this->getCharacterIds($this->getDispatchTransferObject(), 'assets');

        $requested = collect($request->input('character_ids'))->map(fn ($id): int => (int) $id)->filter();

        $validated = $request->only(['search', 'systems', 'regions', 'types', 'groups', 'categories']);
        $validated['character_ids'] = $requested->isEmpty()
            ? $authorized->values()->all()
            : $authorized->intersect($requested)->values()->all();

        return AssetResource::collection($action->execute($location_id, $validated))->response();
    }

    public function item(int $character_id, int $item_id, Request $request): Response|JsonResponse
    {
        $query = EveApiAsset::query()
            ->where('assetable_id', $character_id)
            ->where('assetable_type', CharacterInfo::class)
            ->where('item_id', $item_id);

        // Modal drill: the item + ONE level of contents (+ a count for deeper chevrons), as JSON.
        if ($request->header('X-Modal')) {
            return response()->json(
                AssetResource::collection(
                    (clone $query)
                        ->with(['type.group', 'content' => fn ($contentQuery) => $contentQuery->with('type.group')->withCount('content')])
                        ->get()
                )->resolve()
            );
        }

        // Shareable deep link: full ItemDetails page.
        $item = AssetResource::collection(
            $query->with([
                'location', 'type', 'type.group', 'container',
                'content' => ['content', 'type', 'type.group', 'assetable'],
            ])->get()
        );

        return Inertia::render('Character/ItemDetails', [
            'item' => $item,
        ]);
    }

    private function getDispatchTransferObject(): DispatchTransferObject
    {
        return CreateDispatchTransferObject::new()->create(EveApiAsset::class);
    }
}
