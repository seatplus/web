<?php


namespace Seatplus\Web\Services\SsoSettings;


use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Seatplus\Eveapi\Actions\Eseye\RetrieveEsiDataAction;
use Seatplus\Eveapi\Containers\EsiRequestContainer;

class SearchCorporationOrAlliance
{
    /**
     * @var string
     */
    private string $searchParam;

    /**
     * @var \Seatplus\Eveapi\Actions\Eseye\RetrieveEsiDataAction
     */
    private RetrieveEsiDataAction $retrieve_esi_data_action;

    public function __construct(string $searchParam)
    {

        $this->searchParam = $searchParam;
        $this->retrieve_esi_data_action = new RetrieveEsiDataAction;
    }

    public function search() : string
    {
        // if search parameter is to short esi will not respond
        if(Str::length($this->searchParam) <3)
            // return an empty json response string
            return collect()->toJson();

        $entity_ids_that_match_sub_string = $this->searchEsi();

        $result = $this->getNamesFromIds($entity_ids_that_match_sub_string);

        return $result->toJson();
    }

    private function searchEsi() : array
    {
        $container = new EsiRequestContainer([
            'method' => 'get',
            'version' => 'v2',
            'endpoint' => '/search/',
            'query_string' => [
                'categories' => ['alliance','corporation'],
                'search' => $this->searchParam
            ]
        ]);

        $result = $this->retrieve_esi_data_action->execute($container);

        return collect($result)->flatten()->toArray();
    }

    private function getNamesFromIds(array $entity_ids_that_match_sub_string) : Collection
    {
        $container = new EsiRequestContainer([
            'method' => 'post',
            'version' => 'v3',
            'endpoint' => '/universe/names/',
            'request_body' => $entity_ids_that_match_sub_string
        ]);

        $result = $this->retrieve_esi_data_action->execute($container);

        return collect($result);
    }

}
