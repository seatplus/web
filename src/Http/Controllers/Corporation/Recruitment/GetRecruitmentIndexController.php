<?php


namespace Seatplus\Web\Http\Controllers\Corporation\Recruitment;


use Inertia\Inertia;
use Seatplus\Eveapi\Models\Corporation\CorporationInfo;
use Seatplus\Web\Http\Controllers\Controller;

class GetRecruitmentIndexController extends Controller
{
    const PERMISSION = 'can open or close corporations for recruitment';

    public function __invoke()
    {

        $can_manage_recruitment = auth()->user()->can(self::PERMISSION);

        return Inertia::render('Corporation/Recruitment/RecruitmentIndex',[
            'can_manage_recruitment' => $can_manage_recruitment,
            'corporations' => $this->getCorporations()
        ]);
    }

    private function getCorporations()
    {
        $affiliated_ids = getAffiliatedIdsByPermission(self::PERMISSION);

        return CorporationInfo::whereIn('corporation_id', $affiliated_ids)->get();
    }

}
