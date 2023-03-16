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
use Seatplus\Eveapi\Containers\EsiRequestContainer;
use Seatplus\Eveapi\Services\Facade\RetrieveEsiData;

class GetIdsFromNamesService
{
    private Collection $result;

    public function __construct()
    {
        $this->result = collect();
    }

    public static function make()
    {
        return new static();
    }

    public function execute(array $names): Collection
    {
        $names_to_resolve = collect($names)->filter(function ($name) {
            if (! cache()->has(sprintf('id:%s', $name))) {
                return true;
            }

            $this->result->push(cache(sprintf('id:%s', $name)));

            return false;
        })->filter();

        if ($names_to_resolve->isEmpty()) {
            return $this->result;
        }

        $container = new EsiRequestContainer(
            method: 'post',
            version: 'v1',
            endpoint: '/universe/ids/',
            request_body: [...$names_to_resolve->toArray()],
        );

        $esi_results = RetrieveEsiData::execute($container);

        return collect($esi_results)->flatten()->each(fn ($esi_result) => cache([sprintf('id:%s', $esi_result->name) => $esi_result], now()->addDay()))
            ->merge($this->result);
    }
}
