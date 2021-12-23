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

use Seatplus\Web\Models\Recruitment\Enlistment;

class WatchlistArrayAction
{
    public function execute($corporation_id): array
    {
        $enlistment = Enlistment::with('systems', 'regions', 'types', 'groups', 'categories')->find($corporation_id);

        return [
            'systems' => $enlistment->systems?->pluck('system_id'),
            'regions' => $enlistment->regions?->pluck('region_id'),
            'types' => $enlistment->types?->pluck('type_id'),
            'groups' => $enlistment->groups?->pluck('group_id'),
            'categories' => $enlistment->categories?->pluck('category_id'),
        ];
    }
}
