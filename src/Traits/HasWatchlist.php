<?php

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
            })
                ->orWhere(fn (Builder $query) => $query
                    ->when($request->hasAny(['types', 'groups', 'categories']), fn ($query) => $query->where(function ($query) use ($request) {
                        $request->whenHas('types', fn ($type_ids) => $query->orWhere(fn ($query) => $query->ofTypes($type_ids)));
                        $request->whenHas('groups', fn ($group_ids) => $query->orWhere(fn ($query) => $query->ofGroups($group_ids)));
                        $request->whenHas('categories', fn ($category_ids) => $query->orWhere(fn ($query) => $query->ofCategories($category_ids)));
                    }))
                );
        });
    }
}