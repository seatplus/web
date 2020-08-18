<?php


namespace Seatplus\Web\Http\Controllers\Corporation\MemberTracking;


use Inertia\Inertia;
use Seatplus\Eveapi\Models\Corporation\CorporationMemberTracking;
use Seatplus\Web\Http\Controllers\Controller;

class IndexMemberTrackingController extends Controller
{
    public function __invoke()
    {
        return Inertia::render('Corporation/MemberTracking', [
            'member_tracking' => CorporationMemberTracking::Affiliated()
                ->with('corporation.ssoScopes', 'corporation.alliance.ssoScopes', 'character.refresh_token', 'location.locatable', 'ship')
                ->get()
                ->groupBy('corporation_id')
                ->map(function ($members) {

                    $corporation = $members->first()->corporation;
                    $sso_scopes = collect([
                        'corporation_scopes' => $corporation->ssoScopes->selected_scopes ?? [],
                        'alliance_scopes' => $corporation->alliance->ssoScopes->selected_scopes ?? []
                    ])->flatten(1)->filter();

                    return [
                        'corporation' => $corporation,
                        'members' => $members->map(function($member) use ($sso_scopes) {
                            $member->missing_sso_scopes = $sso_scopes->diff($member->character->refresh_token->scopes ?? []);

                            return $member;
                        })
                    ];
                })
        ]);

    }

}
