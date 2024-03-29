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

namespace Seatplus\Web\Http\Controllers\Shared;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use Seatplus\Eveapi\Containers\EsiRequestContainer;
use Seatplus\Eveapi\Models\RefreshToken;
use Seatplus\Eveapi\Models\Universe\Category;
use Seatplus\Eveapi\Models\Universe\Group;
use Seatplus\Eveapi\Models\Universe\Type;
use Seatplus\Eveapi\Services\Facade\RetrieveEsiData;
use Seatplus\Web\Http\Controllers\Controller;
use Seatplus\Web\Services\GetCharacterAffiliations;
use Seatplus\Web\Services\GetCorporationInfo;
use Seatplus\Web\Services\GetEntityFromId;
use Seatplus\Web\Services\GetNamesFromIdsService;
use Seatplus\Web\Services\SearchService;

class HelperController extends Controller
{
    public function ids()
    {
        $result = (new GetNamesFromIdsService)->execute(request()->all());

        return $result->toJson();
    }

    public function characterAffiliations()
    {
        $result = (new GetCharacterAffiliations())->execute(request()->all());

        return $result->toJson();
    }

    public function getCorporationInfo(int $corporation_id)
    {
        $result = (new GetCorporationInfo())->execute($corporation_id);

        return collect($result)->toJson();
    }

    public function getEntityFromId(int $id)
    {
        return (new GetEntityFromId($id))->execute();
    }

    public function token()
    {
        $token = $this->getEsiSearchToken();

        return $token ? 1 : 0;
    }

    public function esiSearch(Request $request)
    {
        $validated_data = $request->validate([
            'search' => ['required', 'string', 'min:3'],
            'categories' => ['required','array'],
        ]);

        $token = $this->getEsiSearchToken();

        throw_if(! $token, new \Exception('No ESI Search Token found, at least one character needs to have the scope esi-search.search_structures.v1'));

        $ids = (new SearchService)->execute($token, $validated_data['categories'], $validated_data['search']);

        return (new GetNamesFromIdsService)->execute(collect($ids)->flatten()->take(15)->toArray());
    }

    public function typesOrGroupsOrCategories()
    {
        $term = request()->get('search');

        if (Str::length($term) < 3) {
            return response('the minimum length of 3 is not met', 403);
        }

        $typeQuery = Type::query()
            ->select(['type_id as id', 'name'])
            ->where('name', 'like', $term . '%')
            ->addSelect(DB::raw("'type' as category"))
            ->getQuery();

        $groupQuery = Group::query()
            ->select(['group_id as id', 'name'])
            ->where('name', 'like', $term . '%')
            ->addSelect(DB::raw("'group' as category"))
            ->getQuery();

        $categoryQuery = Category::query()
            ->select(['category_id as id', 'name'])
            ->where('name', 'like', $term . '%')
            ->addSelect(DB::raw("'category' as category"));

        return $categoryQuery
            ->union($groupQuery)
            ->union($typeQuery)
            ->limit(15)
            ->get()
            ->map(fn ($entry) => [
                'id' => intval(match ($entry->category) {
                    'type' => 1,
                    'group' => 2,
                    'category' => 3,
                } . $entry->id),
                'name' => sprintf('%s (%s)', $entry->name, $entry->category),
                'watchable_id' => intval($entry->id),
                'watchable_type' => match ($entry->category) {
                    'type' => Type::class,
                    'group' => Group::class,
                    'category' => Category::class,
                },
            ]);
    }

    public function getResourceVariants(string $resource_type, int $resource_id)
    {
        $url = "https://images.evetech.net/${resource_type}/${resource_id}";

        $image_variants = cache($url);

        if (! $image_variants) {
            $image_variants = Http::get(sprintf('https://images.evetech.net/%s/%s', $resource_type, $resource_id))->json();

            //Cache::put($url, $image_variants, now()->addDay());
            cache([$url => $image_variants], now()->addDay());
        }

        return $image_variants;
    }

    public function getMarketsPrices()
    {
        if ($prices = cache('market_prices')) {
            return $prices->toJson();
        }

        $container = new EsiRequestContainer(
            method: 'get',
            version: 'v1',
            endpoint: '/markets/prices/',
        );

        $esi_results = RetrieveEsiData::execute($container);

        $prices = collect($esi_results);

        cache(['market_prices' => $prices], now()->addDay());

        return $prices->toJson();
    }

    private function getEsiSearchToken() : ?RefreshToken
    {
        return SearchService::getTokenFromCurrentUser();
    }
}
