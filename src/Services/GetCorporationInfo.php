<?php


namespace Seatplus\Web\Services;


use Seatplus\Eveapi\Actions\Eseye\RetrieveEsiDataAction;
use Seatplus\Eveapi\Containers\EsiRequestContainer;

class GetCorporationInfo
{
    public function execute($corporation_id)
    {
        $corporation_info_container = new EsiRequestContainer([
            'method' => 'get',
            'version' => 'v4',
            'endpoint' => '/corporations/{corporation_id}/',
            'path_values' => ['corporation_id' => $corporation_id],
        ]);

        return (new RetrieveEsiDataAction)->execute($corporation_info_container);
    }

}
