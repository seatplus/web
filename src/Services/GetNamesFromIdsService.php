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
use Seatplus\Eveapi\Actions\Eseye\RetrieveEsiDataAction;
use Seatplus\Eveapi\Containers\EsiRequestContainer;

class GetNamesFromIdsService
{
    private Collection $result;

    public function __construct()
    {
        $this->result = collect();
    }

    public function execute(array $ids): Collection
    {
        $ids_to_resolve = collect($ids)->filter(function ($id) {
            if (! cache()->has(sprintf('name:%s', $id))) {
                return true;
            }

            $this->result->push(cache(sprintf('name:%s', $id)));

            return false;
        })->filter();

        if ($ids_to_resolve->isEmpty()) {
            return $this->result;
        }

        $container = new EsiRequestContainer([
            'method' => 'post',
            'version' => 'v3',
            'endpoint' => '/universe/names/',
            'request_body' => [...$ids_to_resolve->toArray()],
        ]);

        $esi_results = (new RetrieveEsiDataAction)->execute($container);

        return collect($esi_results)->each(fn ($esi_result) => cache([sprintf('name:%s', $esi_result->id) => $esi_result], now()->addDay()))
            ->merge($this->result);
    }
}
