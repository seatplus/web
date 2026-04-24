<?php

namespace Seatplus\Web\Services\Query;

use Illuminate\Database\Eloquent\Builder;
use Seatplus\Eveapi\Models\Assets\Asset;
use Seatplus\Eveapi\Models\Contracts\Contract;
use Seatplus\Eveapi\Models\TypeWatchListInterface;

class TypeWatchListScope
{
    private ?array $type_ids = null;

    private ?array $group_ids = null;

    private ?array $category_ids = null;

    public function __construct(array $request)
    {
        $this->type_ids = data_get($request, 'types');
        $this->group_ids = data_get($request, 'groups');
        $this->category_ids = data_get($request, 'categories');
    }

    public function __invoke(Builder|TypeWatchListInterface $arg): bool
    {
        return match (true) {
            $arg instanceof Builder => $this->handleBuilder($arg),
            $arg instanceof Asset => $this->handleAsset($arg),
            $arg instanceof Contract => throw new \Exception('Not implemented yet'),
            default => false,
        };
    }

    private function handleBuilder(Builder $query): bool
    {
        $model_class = get_class($query->getModel());

        if (! new $model_class instanceof TypeWatchListInterface) {
            return false;
        }

        $query->where(function (mixed $query) {
            $query->where(function (mixed $query) {

                $query->when($this->type_ids, fn (mixed $query) => $query->filterByTypeIds($this->type_ids));
                $query->when($this->group_ids, fn (mixed $query) => $query->filterByGroupIds($this->group_ids));
                $query->when($this->category_ids, fn (mixed $query) => $query->filterByCategoryIds($this->category_ids));
            });

        });

        return true;
    }

    private function handleAsset(Asset $asset): bool // TODO WatchListInterface $asset
    {
        $propertyMapping = [
            'type_ids' => 'type_id',
            'group_ids' => 'group_id',
            'category_ids' => 'category_id',
        ];

        // if no property is set, return true
        if (! collect($propertyMapping)->filter(fn (mixed $assetProperty, mixed $requestProperty) => $this->$requestProperty)->isNotEmpty()) {
            return true;
        }

        foreach ($propertyMapping as $requestProperty => $assetProperty) {
            if ($this->$requestProperty && in_array($asset->$assetProperty, $this->$requestProperty)) {
                return true;
            }
        }

        return false;
    }
}
