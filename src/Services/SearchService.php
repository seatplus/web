<?php


namespace Seatplus\Web\Services;


use Illuminate\Support\Arr;
use Seatplus\Eveapi\Actions\Eseye\RetrieveEsiDataAction;
use Seatplus\Eveapi\Containers\EsiRequestContainer;

class SearchService
{
    public function execute(string $category, string $query): array
    {
        $container = new EsiRequestContainer([
            'method' => 'get',
            'version' => 'v2',
            'endpoint' => '/search/',
            'query_string' => [
                'categories' => $category,
                'search' => $query
            ],
        ]);

        $result = (new RetrieveEsiDataAction)->execute($container);

        return $result->$category ?? [];
    }

}
