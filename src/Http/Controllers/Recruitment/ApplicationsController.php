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

namespace Seatplus\Web\Http\Controllers\Recruitment;

use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Inertia\Response;
use Seatplus\Auth\Models\User;
use Seatplus\Eveapi\Jobs\Seatplus\UpdateCharacter;
use Seatplus\Eveapi\Models\Application;
use Seatplus\Eveapi\Models\BatchUpdate;
use Seatplus\Eveapi\Models\Character\CharacterInfo;
use Seatplus\Eveapi\Models\RefreshToken;
use Seatplus\Web\Http\Actions\Corporation\Recruitment\WatchlistArrayAction;
use Seatplus\Web\Http\Actions\Recruitment\CreateApplicationLogEntryAction;
use Seatplus\Web\Http\Actions\Recruitment\DeleteCharacterApplicationAction;
use Seatplus\Web\Http\Actions\Recruitment\HandleApplicationAction;
use Seatplus\Web\Http\Actions\Recruitment\ReviewApplicationAction;
use Seatplus\Web\Http\Controllers\Controller;
use Seatplus\Web\Http\Controllers\Request\ApplicationRequest;
use Seatplus\Web\Services\CharacterInspectionScrollProps;
use Seatplus\Web\Services\Recruitment\ApplicationGroupService;
use Seatplus\Web\Support\Translations;

class ApplicationsController extends Controller
{
    public function apply(ApplicationRequest $application_request, HandleApplicationAction $action): RedirectResponse
    {
        $action->execute($application_request->all());

        return back()->with('success', 'Application submitted');
    }

    public function pullCharacterApplication(int $character_id, DeleteCharacterApplicationAction $action): RedirectResponse
    {
        $action->execute($character_id);

        return back()->with('success', 'Application deleted');
    }

    public function pullUserApplication(): RedirectResponse
    {
        auth()->user()->application()->delete();

        return back()->with('success', 'Application deleted');
    }

    public function getApplication(string $application_id, WatchlistArrayAction $action, CharacterInspectionScrollProps $inspectionProps, ApplicationGroupService $groupService): Response
    {
        $application = Application::query()
            ->with([
                'corporation',
                'logEntries.causer' => function (MorphTo $morphTo) {
                    $morphTo->morphWith([User::class => ['mainCharacter']]);
                },
                'applicationable' => function (MorphTo $morphTo) {
                    $morphTo->morphWith([
                        User::class => ['mainCharacter', 'characters', 'characters.batchUpdate'],
                        CharacterInfo::class => ['batchUpdate'],
                    ]);
                },
            ])
            ->findOrFail($application_id);

        $applicationable = $application->applicationable;

        if ($applicationable instanceof User) {
            $recruit = $applicationable;
            $characterIds = $applicationable->characters->pluck('character_id')->map(fn (mixed $id): int => (int) $id)->all();
        } elseif ($applicationable instanceof CharacterInfo) {
            // A single-character application may cover several characters as one group — review them all
            // together: the recruit's characters are every covered character, not just the opened one.
            $coveredCharacterIds = $groupService->groupFor($application)
                ->pluck('applicationable_id')
                ->map(fn (mixed $id): int => (int) $id);

            $characters = CharacterInfo::query()
                ->whereIn('character_id', $coveredCharacterIds)
                ->with('batchUpdate')
                ->get();

            // snake_case to match the User-model branch (and the Vue read `recruit.main_character`).
            $recruit = collect([
                'main_character' => $applicationable,
                'characters' => $characters,
            ]);
            $characterIds = $characters->pluck('character_id')->map(fn (mixed $id): int => (int) $id)->all();
        } else {
            $recruit = collect([]);
            $characterIds = [];
        }

        $watchlist = $action->execute($application->corporation_id);

        return inertia('Recruitment/Review/Application', array_merge([
            'recruit' => $recruit->toArray(),
            'application' => $application,
            'watchlist' => $watchlist,
            'activeSidebarElement' => 'recruitment.reviews',
            'pageTranslations' => Translations::gather(['web::wallet_journal']),
        ], $inspectionProps->build($characterIds, request(), $watchlist)));
    }

    public function reviewApplication(Request $request, string $application_id, ReviewApplicationAction $action, ApplicationGroupService $groupService): RedirectResponse
    {
        $request->validate([
            'decision' => ['required', Rule::in(['rejected', 'accepted'])],
            'explanation' => 'required_if:decision,rejected',
        ]);

        $application = Application::findOrFail($application_id);

        // A multi-character application is decided as one: every covered character advances the same
        // round and, on final acceptance, is hired as its own Employment. Ungrouped applications are a
        // group of one. Each member gates on the round's control group and records its own decision log.
        $groupService->groupFor($application)->each(
            fn (Application $member) => $action->execute($member, $request->get('decision'), $request->get('explanation')),
        );

        return redirect()->route('recruitment.reviews')
            ->with('success', sprintf('%s %s', match ($application->applicationable_type) {
                User::class => 'User',
                CharacterInfo::class => 'Character',
                default => 'Applicant',
            }, $request->get('decision')));
    }

    public function addComment(string $application_id, Request $request, CreateApplicationLogEntryAction $action): RedirectResponse
    {
        $request->validate(['comment' => ['required', 'string']]);

        $action->setApplicationId($application_id)
            ->setType('comment')
            ->setComment($request->get('comment'))
            ->execute();

        return back()->with('success', 'comment created');
    }

    public function dispatchBatchUpdate(int $character_id): \Illuminate\Http\Response
    {
        $refresh_token = RefreshToken::find($character_id);

        abort_if(is_null($refresh_token), 500, 'refresh_token could not be found');

        UpdateCharacter::dispatchAfterResponse($refresh_token)->onQueue('high');

        return response('success');
    }

    public function getBatchUpdate(int $character_id): string
    {
        return BatchUpdate::query()
            ->where('batchable_id', $character_id)
            ->first()
            ->toJson();
    }
}
