<?php

namespace Seatplus\Web\Http\Actions\Corporation\Recruitment;

use Seatplus\Eveapi\Models\Universe\Category;
use Seatplus\Eveapi\Models\Universe\Group;
use Seatplus\Eveapi\Models\Universe\Type;
use Seatplus\Web\Models\Recruitment\Enlistment;

class WatchedArrayAction
{
    private array $watched = [];

    private Enlistment $enlistment;

    public function execute(int $corporation_id) : array
    {
        /** @noinspection PhpFieldAssignmentTypeMismatchInspection */
        $this->enlistment = Enlistment::with(
            'systems',
            'regions',
            'types',
            'groups',
            'categories'
        )
            ->find($corporation_id);

        $this->handleSystems();
        $this->handleRegions();
        $this->handleItems();

        return $this->watched;
    }

    private function handleSystems()
    {
        $entries = $this->enlistment->systems->map(function ($system) {
            $system->id = $system->system_id;

            return $system;
        }) ?? [];

        data_set($this->watched, 'systems', $entries);
    }

    private function handleItems()
    {
        $types = $this->enlistment->types->map(fn ($type) => [
                'id' => intval(1 . $type->type_id),
                'name' => $type->name . "(type)",
                'watchable_id' => $type->type_id,
                'watchable_type' => Type::class
            ]) ?? [];

        $groups = $this->enlistment->groups->map(fn ($group) => [
                'id' => intval(1 . $group->group_id),
                'name' => $group->name . "(group)",
                'watchable_id' => $group->group_id,
                'watchable_type' => Group::class
            ]) ?? [];

        $categories = $this->enlistment->categories->map(fn ($category) => [
                'id' => intval(1 . $category->category_id),
                'name' => $category->name . "(category)",
                'watchable_id' => $category->category_id,
                'watchable_type' => Category::class
            ]) ?? [];

        data_set($this->watched, 'items', [...$types, ...$groups, ...$categories]);
    }

    private function handleRegions()
    {
        $entries = $this->enlistment->regions->map(function ($region) {
                $region->id = $region->region_id;

                return $region;
            }) ?? [];

        data_set($this->watched, 'regions', $entries);
    }
}