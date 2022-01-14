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

use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Seatplus\Auth\Models\User;
use Seatplus\Eveapi\Jobs\Seatplus\UpdateCharacter;
use Seatplus\Eveapi\Models\Application;
use Seatplus\Eveapi\Models\BatchUpdate;
use Seatplus\Eveapi\Models\Character\CharacterInfo;
use Seatplus\Eveapi\Models\RefreshToken;
use Seatplus\Eveapi\Services\GetOwnedIds;
use Seatplus\Web\Http\Actions\Corporation\Recruitment\WatchlistArrayAction;
use Seatplus\Web\Http\Actions\Recruitment\CreateApplicationLogEntryAction;
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
        $applications = Application::query()
            ->with('log_entries')
            ->ofCorporation($corporation_id)->whereStatus('open');

        return ApplicationRessource::collection($applications->paginate());
    }

    public function getAcceptedCorporationApplications(int $corporation_id)
    {
        $applications = Application::ofCorporation($corporation_id)->whereStatus('accepted');

        return ApplicationRessource::collection($applications->paginate());
    }

    public function getApplication(int $application_id, WatchlistArrayAction $action)
    {
        $application = Application::query()
            ->with(['applicationable' => function(MorphTo $morphTo) {
                $morphTo->morphWith([
                    User::class => ['main_character', 'characters', 'characters.batch_update'],
                    CharacterInfo::class => ['batch_update']
                ]);
            }])
            ->find($application_id);


        $recruit = match($application->applicationable_type) {
            User::class => $application->applicationable,
            CharacterInfo::class => [
                'main_character' => $application->applicationable,
                'characters' => [$application->applicationable],
            ]
        };

        return inertia('Corporation/Recruitment/Application', [
            'recruit' => $recruit,
            'target_corporation' => $application->corporation,
            'watchlist' => $action->execute($application->corporation_id),
            'activeSidebarElement' => 'corporation.recruitment',
        ]);
    }

    public function reviewApplication(Request $request, int $application_id, CreateApplicationLogEntryAction $action)
    {
        $request->validate([
            'decision' => ['required', Rule::in(['rejected', 'accepted'])],
            'explanation' => 'required_if:decision,rejected',
        ]);

        $action->setComment($request->get('explanation') ?? '')
            ->setType('decision')
            ->setApplicationId($application_id)
            ->execute();

        $application = Application::find($application_id);


        if($request->get('decision') === 'rejected') {

            $application->status = 'rejected';
            $application->save();
        }

        if($request->get('decision') === 'accepted' && $application->enlistment->steps_count <= $application->decision_count) {

            $application->status = 'accepted';
            $application->save();
        }

        return redirect()->route('corporation.recruitment')
            ->with('success', sprintf('%s %s', match ($application->applicationable_type) {
                User::class => 'User',
                CharacterInfo::class => 'Character'
            }, $request->get('decision')));
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
