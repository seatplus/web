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

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use Seatplus\Eveapi\Containers\EsiRequestContainer;
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

    public function findSolarSystem(string $search)
    {
        $ids_to_resolve = (new SearchService)->execute('solar_system', $search);

        // get names for IDs
        if (empty($ids_to_resolve)) {
            return $ids_to_resolve;
        }

        $esi_results = (new GetNamesFromIdsService)->execute($ids_to_resolve);

        return $esi_results;
    }

    public function systems()
    {
        $query = request()->get('search');

        if (Str::length($query) < 3) {
            return response('the minimum length of 3 is not met', 403);
        }

        $system_ids = (new SearchService)->execute('solar_system', $query);

        return (new GetNamesFromIdsService)->execute(array_slice($system_ids, 0, 15));
    }

    public function regions()
    {
        $query = request()->get('search');

        if (Str::length($query) < 3) {
            return response('the minimum length of 3 is not met', 403);
        }

        $region_ids = (new SearchService)->execute('region', $query);

        return (new GetNamesFromIdsService)->execute(array_slice($region_ids, 0, 15));
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
            ->map(fn($entry) => [
                'id' => intval(match ($entry->category) {
                        'type' => 1,
                        'group' => 2,
                        'category' => 3,
                    } . $entry->id),
                'name' => sprintf('%s (%s)',$entry->name, $entry->category),
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
        return Http::get(sprintf('https://images.evetech.net/%s/%s', $resource_type, $resource_id))->json();
    }

    public function getMarketsPrices()
    {
        if ($prices = cache('market_prices')) {
            return $prices->toJson();
        }

        $container = new EsiRequestContainer([
            'method' => 'get',
            'version' => 'v1',
            'endpoint' => '/markets/prices/',
        ]);

        $esi_results = RetrieveEsiData::execute($container);

        $prices = collect($esi_results);

        cache(['market_prices' => $prices], now()->addDay());

        return $prices->toJson();
    }
}
