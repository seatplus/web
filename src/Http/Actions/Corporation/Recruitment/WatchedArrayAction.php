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

namespace Seatplus\Web\Http\Actions\Corporation\Recruitment;

use Seatplus\Eveapi\Models\Universe\Category;
use Seatplus\Eveapi\Models\Universe\Group;
use Seatplus\Eveapi\Models\Universe\Type;
use Seatplus\Web\Models\Recruitment\Enlistment;

class WatchedArrayAction
{
    private array $watched = [];

    private Enlistment $enlistment;

    public function execute(int $corporation_id): array
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
            'name' => $type->name . '(type)',
            'watchable_id' => $type->type_id,
            'watchable_type' => Type::class,
        ]) ?? [];

        $groups = $this->enlistment->groups->map(fn ($group) => [
            'id' => intval(1 . $group->group_id),
            'name' => $group->name . '(group)',
            'watchable_id' => $group->group_id,
            'watchable_type' => Group::class,
        ]) ?? [];

        $categories = $this->enlistment->categories->map(fn ($category) => [
            'id' => intval(1 . $category->category_id),
            'name' => $category->name . '(category)',
            'watchable_id' => $category->category_id,
            'watchable_type' => Category::class,
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
