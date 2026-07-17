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

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Validation\Rule;
use Inertia\Inertia;
use Inertia\Response;
use Seatplus\Auth\Models\User;
use Seatplus\Eveapi\Jobs\Seatplus\UpdateCharacter;
use Seatplus\Eveapi\Models\Application;
use Seatplus\Eveapi\Models\BatchUpdate;
use Seatplus\Eveapi\Models\Character\CharacterInfo;
use Seatplus\Eveapi\Models\Character\CorporationHistory;
use Seatplus\Eveapi\Models\Recruitment\Enlistments;
use Seatplus\Eveapi\Models\RefreshToken;
use Seatplus\Web\Http\Actions\Corporation\Recruitment\WatchlistArrayAction;
use Seatplus\Web\Http\Actions\Recruitment\CreateApplicationLogEntryAction;
use Seatplus\Web\Http\Actions\Recruitment\DeleteCharacterApplicationAction;
use Seatplus\Web\Http\Actions\Recruitment\HandleApplicationAction;
use Seatplus\Web\Http\Controllers\Character\CorporationHistoryController;
use Seatplus\Web\Http\Controllers\Controller;
use Seatplus\Web\Http\Controllers\Request\ApplicationRequest;
use Seatplus\Web\Http\Resources\ApplicationRessource;
use Seatplus\Web\Support\Translations;

class ApplicationsController extends Controller
{
    public function apply(ApplicationRequest $application_request): RedirectResponse
    {
        (new HandleApplicationAction)->execute($application_request->all());

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

    public function getOpenCorporationApplications(int $corporation_id, int $decision_count): AnonymousResourceCollection
    {
        $query = Application::query()
            ->with('logEntries')
            ->whereHas('logEntries', function (Builder $query) {
                $query->where('type', 'decision');
            }, '=', $decision_count)
            ->ofCorporation($corporation_id)
            ->whereStatus('open');

        return ApplicationRessource::collection($query->paginate());
    }

    public function getClosedCorporationApplications(int $corporation_id): AnonymousResourceCollection
    {
        $applications = Application::query()->ofCorporation($corporation_id)
            ->latest('updated_at')
            ->where('status', '<>', 'open');

        return ApplicationRessource::collection($applications->paginate());
    }

    public function getApplication(string $application_id, WatchlistArrayAction $action): Response
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
            ->find($application_id);

        $recruit = match ($application->applicationable_type) {
            User::class => $application->applicationable,
            CharacterInfo::class => collect([
                // snake_case to match the raw User-model branch above (and the Vue read
                // `recruit.main_character` in Pages/Corporation/Recruitment/Application.vue)
                'main_character' => $application->applicationable,
                'characters' => [$application->applicationable],
            ]),
            default => collect([]),
        };

        // The recruit's character ids drive the (lazy) corporation-history prop below. `$recruit` is
        // either a User model (with its `characters` relation) or a Collection wrapping a single
        // CharacterInfo — both expose a `characters` list, so one traversal covers account- and
        // character-type applications alike.
        $recruitCharacterIds = collect(data_get($recruit, 'characters', []))
            ->map(fn (mixed $character): int => (int) data_get($character, 'character_id'))
            ->all();

        return inertia('Corporation/Recruitment/Application', [
            'recruit' => $recruit->toArray(),
            'application' => $application,
            'watchlist' => $action->execute($application->corporation_id),
            'activeSidebarElement' => 'corporation.recruitment',
            'pageTranslations' => Translations::gather(['web::wallet_journal']),
            // The recruit's per-character corporation history, keyed by character_id. Declared
            // `optional` so it is NOT resolved on the initial page render (the history tab is not
            // the default tab and sits below the fold) — it is only computed when the tab's
            // CorporationHistoryComponent scrolls into view and its <WhenVisible data="corporationHistory">
            // issues a partial reload for exactly this prop. Delegates to the same
            // CorporationHistoryController@index the standalone corporation.history endpoint uses, so
            // the (bounded, un-paginated) query lives in one place.
            'corporationHistory' => Inertia::optional(fn (): array => $this->corporationHistoriesForCharacters($recruitCharacterIds)),
        ]);
    }

    /**
     * Resolve each character's corporation history keyed by character_id, reusing the same
     * CorporationHistoryController@index query the standalone endpoint serves so it lives in one place.
     *
     * @param  array<int, int>  $characterIds
     * @return array<int, Collection<int, CorporationHistory>>
     */
    private function corporationHistoriesForCharacters(array $characterIds): array
    {
        $historyController = app(CorporationHistoryController::class);

        return collect($characterIds)
            ->mapWithKeys(fn (int $characterId): array => [
                $characterId => $historyController->index($characterId),
            ])
            ->all();
    }

    public function reviewApplication(Request $request, string $application_id, CreateApplicationLogEntryAction $action): RedirectResponse
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

        if ($request->get('decision') === 'rejected') {
            $application->status = 'rejected';
            $application->save();
        }

        /** @var Enlistments $enlistment */
        $enlistment = $application->enlistment;

        if ($request->get('decision') === 'accepted' && $enlistment->steps_count <= $application->decision_count) {
            $application->status = 'accepted';
            $application->save();
        }

        return redirect()->route('corporation.recruitment')
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

    public function getActivityLog(string $application_id): ?Application
    {
        return Application::query()
            ->with([
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
            ->find($application_id);
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
