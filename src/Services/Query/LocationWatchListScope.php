<?php

namespace Seatplus\Web\Services\Query;

use Illuminate\Database\Eloquent\Builder;
use Seatplus\Eveapi\Models\LocationWatchListInterface;

class LocationWatchListScope
{
    private ?array $system_ids = null;

    private ?array $region_ids = null;

    public function __construct(array $request)
    {
        $this->system_ids = data_get($request, 'systems');
        $this->region_ids = data_get($request, 'regions');
    }

    public function __invoke(Builder $query): void
    {
        $model_class = get_class($query->getModel());

        if (! new $model_class instanceof LocationWatchListInterface) {
            return;
        }

        $query->where(function (Builder $query) {
            $query->where(function (Builder $query) {

                $query->when($this->system_ids, fn (Builder $query) => $query->filterBySystemIds($this->system_ids));
                $query->when($this->region_ids, fn (Builder $query) => $query->filterByRegionIds($this->region_ids));
            });

        });
    }
}
