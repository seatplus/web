<?php


namespace Seatplus\Web\Services;


use Seatplus\Eveapi\Actions\Jobs\Alliance\AllianceInfoAction;
use Seatplus\Eveapi\Actions\Jobs\Corporation\CorporationInfoAction;
use Seatplus\Eveapi\Models\Alliance\AllianceInfo;

class DispatchCorporationOrAllianceInfoJob
{
    public function handle(string $type, int $id)
    {
        $type === AllianceInfo::class ? $this->handleAllianceInfo($id) : $this->handleCorporationInfo($id);

    }

    private function handleAllianceInfo(int $entity_id)
    {

        (new AllianceInfoAction)->execute($entity_id);

    }

    private function handleCorporationInfo(int $entity_id)
    {
        (new CorporationInfoAction)->execute($entity_id);
    }

}
