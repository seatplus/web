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

namespace Seatplus\Web\Traits;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Query\Builder as QueryBuilder;
use Illuminate\Http\Request;

trait HasWatchlist
{
    private function handleWatchlist(Builder|QueryBuilder $query, Request $request)
    {
        $query->where(function ($query) use ($request) {
            $query->where(function ($query) use ($request) {
                $request->whenHas('systems', fn ($system_ids) => $query->orWhere(fn ($query) => $query->inSystems($system_ids)));
                $request->whenHas('regions', fn ($region_ids) => $query->orWhere(fn ($query) => $query->inRegion($region_ids)));
                $request->whenHas('types', fn ($type_ids) => $query->orWhere(fn ($query) => $query->ofTypes($type_ids)));
                $request->whenHas('groups', fn ($group_ids) => $query->orWhere(fn ($query) => $query->ofGroups($group_ids)));
                $request->whenHas('categories', fn ($category_ids) => $query->orWhere(fn ($query) => $query->ofCategories($category_ids)));
            });
        });
    }
}
