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

namespace Seatplus\Web\Services;

use Illuminate\Support\Collection;
use Seatplus\EsiClient\EsiClient;

class GetNamesFromIdsService
{
    private readonly Collection $result;

    public function __construct()
    {
        $this->result = collect();
    }

    public function execute(EsiClient $esi, array $ids): Collection
    {
        $ids_to_resolve = collect($ids)->filter(function (int $id) {
            if (! cache()->has(sprintf('name:%s', $id))) {
                return true;
            }

            $this->result->push(cache(sprintf('name:%s', $id)));

            return false;
        })->filter();

        if ($ids_to_resolve->isEmpty()) {
            return $this->result;
        }

        $response = $esi->invoke(
            method: 'post',
            path: '/universe/names/',
            requestBody: [...$ids_to_resolve->toArray()],
        );

        return collect((array) $response->data)
            ->map(function (object $esi_result) {
                match ($esi_result->category) {
                    'character', 'corporation', 'alliance', 'type' => data_set($esi_result, 'has_image', true) && data_set($esi_result, $esi_result->category.'_id', $esi_result->id),
                    default => $esi_result,
                };

                return $esi_result;
            })
            ->each(fn (object $esi_result) => cache([sprintf('name:%s', $esi_result->id) => $esi_result], now()->addDay()))
            ->merge($this->result);
    }
}
