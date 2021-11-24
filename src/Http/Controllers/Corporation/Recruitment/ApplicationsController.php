<?php

/*
 * MIT License
 *
 * Copyright (c) 2019, 2020, 2021 Felix Huber
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in all
 * copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
 * SOFTWARE.
 */

namespace Seatplus\Web\Http\Controllers\Corporation\Recruitment;

use Illuminate\Http\Request;
use Seatplus\Auth\Models\User;
use Seatplus\Eveapi\Jobs\Seatplus\UpdateCharacter;
use Seatplus\Eveapi\Models\Application;
use Seatplus\Eveapi\Models\BatchUpdate;
use Seatplus\Eveapi\Models\Character\CharacterInfo;
use Seatplus\Eveapi\Models\RefreshToken;
use Seatplus\Eveapi\Services\GetOwnedIds;
use Seatplus\Web\Http\Actions\Recruitment\HandleApplicationAction;
use Seatplus\Web\Http\Controllers\Controller;
use Seatplus\Web\Http\Controllers\Request\ApplicationRequest;
use Seatplus\Web\Http\Resources\ApplicationRessource;
use Seatplus\Web\Models\Recruitment\Enlistment;

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
        $applications = Application::ofCorporation($corporation_id)->whereStatus('open');

        return ApplicationRessource::collection($applications->paginate());
    }

    public function getAcceptedCorporationApplications(int $corporation_id)
    {
        $applications = Application::ofCorporation($corporation_id)->whereStatus('accepted');

        return ApplicationRessource::collection($applications->paginate());
    }

    public function getUserApplication(User $recruit)
    {
        $corporation_id = $recruit->application->corporation->corporation_id;
        $enlistment = Enlistment::with('systems', 'regions')->find($corporation_id);

        return inertia('Corporation/Recruitment/Application', [
            'recruit' => $recruit->loadMissing('main_character', 'characters', 'characters.batch_update'),
            'target_corporation' => $recruit->application->corporation,
            'watchlist' => [
                'systems' => $enlistment->systems?->pluck('system_id'),
                'regions' => $enlistment->regions?->pluck('region_id'),
            ],
            'activeSidebarElement' => 'corporation.recruitment',
        ]);
    }

    public function reviewUserApplication(Request $request, User $recruit)
    {
        $request->validate([
            'decision' => 'required',
            'explanation' => 'required_if:decision,rejected',
        ]);

        $recruit->application()->update([
            'status' => $request->get('decision'),
            'comment' => $request->get('explanation') ?? '',
        ]);

        return redirect()->route('corporation.recruitment')->with('success', sprintf('User %s', $request->get('decision')));
    }

    public function getCharacterApplication(int $character_id)
    {
        $character = CharacterInfo::with('application', 'batch_update')->find($character_id);

        $recruit = collect([
            'main_character' => $character,
            'characters' => [$character],
        ]);

        $corporation_id = $character->application->corporation->corporation_id;
        $enlistment = Enlistment::with('systems', 'regions')->find($corporation_id);

        return inertia('Corporation/Recruitment/Application', [
            'recruit' => $recruit,
            'target_corporation' => $character->application->corporation,
            'watchlist' => [
                'systems' => $enlistment->systems?->pluck('system_id'),
                'regions' => $enlistment->regions?->pluck('region_id'),
            ],
            'activeSidebarElement' => 'corporation.recruitment',
        ]);
    }

    public function reviewCharacterApplication(Request $request, int $character_id)
    {
        $request->validate([
            'decision' => 'required',
            'explanation' => 'required_if:decision,rejected',
        ]);

        CharacterInfo::find($character_id)->application()->update([
            'status' => $request->get('decision'),
            'comment' => $request->get('explanation') ?? '',
        ]);

        return redirect()->route('corporation.recruitment')->with('success', sprintf('Character %s', $request->get('decision')));
    }

    public function dispatchBatchUpdate(int $character_id)
    {
        $refresh_token = RefreshToken::find($character_id);

        abort_if(is_null($refresh_token), 500, 'refresh_token could not be found');

        UpdateCharacter::dispatchAfterResponse($refresh_token);

        return response('success');
    }

    public function getBatchUpdate(int $character_id)
    {
        return BatchUpdate::query()
            ->where('batchable_id', $character_id)
            ->first()
            ->toJson();
    }
}
