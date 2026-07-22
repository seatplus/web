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
use Illuminate\Http\Response;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Seatplus\EsiClient\EsiClient;
use Seatplus\Eveapi\Models\RefreshToken;
use Seatplus\Eveapi\Models\Universe\Category;
use Seatplus\Eveapi\Models\Universe\Group;
use Seatplus\Eveapi\Models\Universe\Type;
use Seatplus\Web\Http\Controllers\Controller;
use Seatplus\Web\Services\GetEntityFromId;
use Seatplus\Web\Services\GetNamesFromIdsService;
use Seatplus\Web\Services\SearchService;

class HelperController extends Controller
{
    public function getEntityFromId(EsiClient $esi, int $id): array
    {
        return (new GetEntityFromId($id))->execute($esi);
    }

    public function token(): int
    {
        $token = $this->getEsiSearchToken();

        return $token ? 1 : 0;
    }

    public function esiSearch(Request $request, EsiClient $esi): Collection
    {
        $validated_data = $request->validate([
            'search' => ['required', 'string', 'min:3'],
            'categories' => ['required', 'array'],
        ]);

        $token = $this->getEsiSearchToken();

        throw_if(! $token, new \Exception('No ESI Search Token found, at least one character needs to have the scope esi-search.search_structures.v1'));

        $ids = (new SearchService)->execute($esi, $token, $validated_data['categories'], $validated_data['search']);

        return (new GetNamesFromIdsService)->execute($esi, collect($ids)->flatten()->take(15)->toArray());
    }

    public function typesOrGroupsOrCategories(): Response|Collection
    {
        $term = request()->get('search');

        if (Str::length($term) < 3) {
            return response('the minimum length of 3 is not met', 403);
        }

        $typeQuery = Type::query()
            ->select(['type_id as id', 'name'])
            ->where('name', 'like', $term.'%')
            ->addSelect(DB::raw("'type' as category"))
            ->getQuery();

        $groupQuery = Group::query()
            ->select(['group_id as id', 'name'])
            ->where('name', 'like', $term.'%')
            ->addSelect(DB::raw("'group' as category"))
            ->getQuery();

        $categoryQuery = Category::query()
            ->select(['category_id as id', 'name'])
            ->where('name', 'like', $term.'%')
            ->addSelect(DB::raw("'category' as category"));

        return $categoryQuery
            ->union($groupQuery)
            ->union($typeQuery)
            ->limit(15)
            ->get()
            ->map(fn (Category $entry) => [
                'id' => intval(match ((string) $entry->getAttribute('category')) {
                    'type' => 1,
                    'group' => 2,
                    'category' => 3,
                    default => 0,
                }.(int) $entry->getAttribute('id')),
                'name' => sprintf('%s (%s)', $entry->name, $entry->getAttribute('category')),
                'watchable_id' => intval($entry->getAttribute('id')),
                'watchable_type' => match ((string) $entry->getAttribute('category')) {
                    'type' => Type::class,
                    'group' => Group::class,
                    'category' => Category::class,
                    default => Type::class,
                },
            ]);
    }

    private function getEsiSearchToken(): ?RefreshToken
    {
        return SearchService::getTokenFromCurrentUser();
    }
}
