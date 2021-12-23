<?php

namespace Seatplus\Web\Http\Actions\Corporation\Recruitment;

use Seatplus\Web\Models\Recruitment\Enlistment;

class WatchlistArrayAction
{
    public function execute($corporation_id): array
    {
        $enlistment = Enlistment::with('systems', 'regions', 'types', 'groups', 'categories')->find($corporation_id);

        if(is_null($enlistment)) {
            return [];
        }

        return [
            'systems' => $enlistment->systems?->pluck('system_id'),
            'regions' => $enlistment->regions?->pluck('region_id'),
            'types' => $enlistment->types?->pluck('type_id'),
            'groups' => $enlistment->groups?->pluck('group_id'),
            'categories' => $enlistment->categories?->pluck('category_id'),
        ];
    }
}