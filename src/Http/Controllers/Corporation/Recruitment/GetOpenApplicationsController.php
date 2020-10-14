<?php


namespace Seatplus\Web\Http\Controllers\Corporation\Recruitment;


use Illuminate\Database\Eloquent\Relations\MorphTo;
use Inertia\Inertia;
use Seatplus\Auth\Models\User;
use Seatplus\Eveapi\Jobs\Character\CharacterInfo;
use Seatplus\Eveapi\Models\Applications;
use Seatplus\Eveapi\Models\Recruitment\Enlistments;
use Seatplus\Web\Http\Controllers\Controller;
use Seatplus\Web\Http\Resources\ApplicationRessource;

class GetOpenApplicationsController extends Controller
{
    const RECRUITERPERMISSION = 'can accept or deny applications';

    public function __invoke(int $corporation_id)
    {

        abort_unless(auth()->user()->can(self::RECRUITERPERMISSION) && in_array($corporation_id, getAffiliatedIdsByPermission(self::RECRUITERPERMISSION)), 403, 'You have no rights accessing this data');

        $applications =  Applications::where('corporation_id', $corporation_id)
            ->with([
                'applicationable' => fn(MorphTo $morph_to) => $morph_to->morphWith([
                    User::class => ['characters.refresh_token', 'main_character', 'characters.application.corporation.ssoScopes', 'characters.application.corporation.alliance.ssoScopes'],
                    CharacterInfo::class => ['refresh_token', 'application.corporation.ssoScopes', 'application.corporation.alliance.ssoScopes']
                ]),
            ])->whereStatus('open');

        return ApplicationRessource::collection($applications->paginate());
    }

}
