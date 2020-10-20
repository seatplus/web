<?php


namespace Seatplus\Web\Http\Controllers\Corporation\Recruitment;


use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Http\Request;
use Seatplus\Auth\Models\User;
use Seatplus\Eveapi\Models\Applications;
use Seatplus\Eveapi\Models\Character\CharacterInfo;
use Seatplus\Eveapi\Services\GetOwnedIds;
use Seatplus\Web\Http\Actions\Recruitment\HandleApplicationAction;
use Seatplus\Web\Http\Controllers\Controller;
use Seatplus\Web\Http\Controllers\Request\ApplicationRequest;
use Seatplus\Web\Http\Resources\ApplicationRessource;

class ApplicationsController extends Controller
{
    public function apply(ApplicationRequest $application_request)
    {

        (new HandleApplicationAction)->execute($application_request->all());

        return back()->with('success', 'Application submitted');
    }

    public function pullCharacterApplication(int $character_id)
    {
        $user_owns_character_id = in_array($character_id, (new GetOwnedIds)->execute());

        abort_unless($user_owns_character_id, 403, 'submitted character_id does not belong to user');

        CharacterInfo::find($character_id)->application()->delete();

        return back()->with('success', 'Application deleted');
    }

    public function pullUserApplication()
    {
        auth()->user()->application()->delete();

        return back()->with('success', 'Application deleted');
    }


    public function getOpenCorporationApplications(int $corporation_id)
    {
        $applications =  Applications::where('corporation_id', $corporation_id)
            ->with([
                'applicationable' => fn(MorphTo $morph_to) => $morph_to->morphWith([
                    User::class => ['characters.refresh_token', 'main_character', 'characters.application.corporation.ssoScopes', 'characters.application.corporation.alliance.ssoScopes'],
                    CharacterInfo::class => ['refresh_token', 'application.corporation.ssoScopes', 'application.corporation.alliance.ssoScopes']
                ]),
            ])->whereStatus('open');

        return ApplicationRessource::collection($applications->paginate());
    }

    public function getUserApplication(User $recruit)
    {
        return inertia('Corporation/Recruitment/Application', [
            'recruit' => $recruit->loadMissing('main_character', 'characters')
        ]);
    }

    public function reviewUserApplication(Request $request, User $recruit)
    {
        $request->validate([
            'decision' => 'required',
            'explanation' => 'required_if:decision,rejected'
        ]);

        $recruit->application()->update([
            'status' => $request->get('decision'),
            'comment' => $request->get('explanation', '')
        ]);


        return redirect()->route('corporation.recruitment')->with('success', sprintf('User %s', $request->get('decision')));
    }

    public function getCharacterApplication(int $character_id)
    {
        $character = CharacterInfo::find($character_id);

        $recruit = collect([
            'main_character' => $character,
            'characters' => [$character]
        ]);

        return inertia('Corporation/Recruitment/Application', [
            'recruit' => $recruit
        ]);
    }

    public function reviewCharacterApplication(Request $request, int $character_id)
    {
        $request->validate([
            'decision' => 'required',
            'explanation' => 'required_if:decision,rejected'
        ]);

        CharacterInfo::find($character_id)->application()->update([
            'status' => $request->get('decision'),
            'comment' => $request->get('explanation', '')
        ]);

        return redirect()->route('corporation.recruitment')->with('success', sprintf('Character %s', $request->get('decision')));
    }




}
