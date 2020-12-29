<?php

/*
 * MIT License
 *
 * Copyright (c) 2019, 2020 Felix Huber
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

namespace Seatplus\Web\Services\SsoSettings;

use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Seatplus\Eveapi\Actions\Eseye\RetrieveEsiDataAction;
use Seatplus\Eveapi\Containers\EsiRequestContainer;

class SearchCorporationOrAlliance
{
    /**
     * @var \Seatplus\Eveapi\Actions\Eseye\RetrieveEsiDataAction
     */
    private RetrieveEsiDataAction $retrieve_esi_data_action;

    public function __construct(/**
     * @var string
     */
    private string $searchParam)
    {
        $this->retrieve_esi_data_action = new RetrieveEsiDataAction;
    }

    public function search(): string
    {
        // if search parameter is to short esi will not respond
        if (Str::length($this->searchParam) < 3) {
            // return an empty json response string
            return collect()->toJson();
        }

        $entity_ids_that_match_sub_string = $this->searchEsi();

        $result = $this->getNamesFromIds($entity_ids_that_match_sub_string);

        return $result->toJson();
    }

    private function searchEsi(): array
    {
        $container = new EsiRequestContainer([
            'method' => 'get',
            'version' => 'v2',
            'endpoint' => '/search/',
            'query_string' => [
                'categories' => ['alliance', 'corporation'],
                'search' => $this->searchParam,
            ],
        ]);

        $result = $this->retrieve_esi_data_action->execute($container);

        return collect($result)->flatten()->toArray();
    }

    private function getNamesFromIds(array $entity_ids_that_match_sub_string): Collection
    {
        $container = new EsiRequestContainer([
            'method' => 'post',
            'version' => 'v3',
            'endpoint' => '/universe/names/',
            'request_body' => $entity_ids_that_match_sub_string,
        ]);

        $result = $this->retrieve_esi_data_action->execute($container);

        return collect($result);
    }
}
