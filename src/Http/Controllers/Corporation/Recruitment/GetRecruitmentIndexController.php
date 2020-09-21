<?php


namespace Seatplus\Web\Http\Controllers\Corporation\Recruitment;


use Inertia\Inertia;
use Seatplus\Eveapi\Models\Recruitment\Enlistments;
use Seatplus\Web\Http\Controllers\Controller;

class GetRecruitmentIndexController extends Controller
{
    const MANAGEPERMISSION = 'can open or close corporations for recruitment';
    const RECRUITERPERMISSION = 'can accept or deny applications';

    public function __invoke()
    {

        $can_manage_recruitment = auth()->user()->can(self::MANAGEPERMISSION);

        return Inertia::render('Corporation/Recruitment/RecruitmentIndex',[
            'can_manage_recruitment' => $can_manage_recruitment,
            'corporations' => $this->getCorporations()
        ]);
    }

    private function getCorporations()
    {
        $manageable_ids = getAffiliatedIdsByPermission(self::MANAGEPERMISSION);
        $recruitable_ids = getAffiliatedIdsByPermission(self::RECRUITERPERMISSION);

        return Enlistments::whereIn('corporation_id', [...$manageable_ids, ...$recruitable_ids])
            ->with('corporation.alliance')
            ->get()
            ->map(function($enlistment) use ($manageable_ids, $recruitable_ids){

                $corporation = $enlistment->corporation;
                $corporation->can_manage = in_array($enlistment->corporation_id, $manageable_ids);
                $corporation->can_recruit = in_array($enlistment->corporation_id, $recruitable_ids);

                return $corporation;
            });
    }

}
