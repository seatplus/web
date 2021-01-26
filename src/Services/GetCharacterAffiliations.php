<?php


namespace Seatplus\Web\Services;


use Illuminate\Support\Collection;
use Seatplus\Eveapi\Actions\Eseye\RetrieveEsiDataAction;
use Seatplus\Eveapi\Containers\EsiRequestContainer;

class GetCharacterAffiliations
{
    public function execute(array $character_ids) : Collection
    {
        $character_affiliation_container = new EsiRequestContainer([
            'method' => 'post',
            'version' => 'v1',
            'endpoint' => '/characters/affiliation/',
            'request_body' => $character_ids,
        ]);

        $character_affiliations = (new RetrieveEsiDataAction)->execute($character_affiliation_container);

        return collect($character_affiliations);
    }

}
