<?php

namespace Seatplus\Web\Http\Controllers\Recruitment;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Support\Collection;
use Inertia\Inertia;
use Inertia\Response;
use Seatplus\Auth\Models\User;
use Seatplus\Eveapi\Models\Application;
use Seatplus\Eveapi\Models\Character\CharacterInfo;
use Seatplus\Web\Http\Controllers\Controller;
use Seatplus\Web\Models\Recruitment\EnlistmentReviewRound;
use Seatplus\Web\Services\Recruitment\StageGate;

/**
 * The reviewer inbox: every open application currently sitting at a stage the signed-in user is
 * allowed to review, across all corporations they recruit for. This is the visibility filter that
 * makes "stage 1 = Junior HR, stage 2 = Senior HR" real — a reviewer only sees the rounds their
 * control group handles, not every round.
 */
class ReviewInboxController extends Controller
{
    final public const string RECRUITER_PERMISSION = 'can accept or deny applications';

    public function __invoke(StageGate $stageGate): Response
    {
        $user = auth()->user();
        $isSuperuser = $user->can('superuser');

        $recruiterCorpIds = $this->getAffiliatedIds->get(
            permissions: [self::RECRUITER_PERMISSION],
            corporationRoles: ['Director'],
            user: $user,
        );

        $applications = Application::query()
            ->where('status', 'open')
            ->when(! $isSuperuser, fn (Builder $query) => $query->whereIn('corporation_id', $recruiterCorpIds))
            ->with([
                'logEntries',
                'corporation',
                'applicationable' => fn (MorphTo $morphTo) => $morphTo->morphWith([
                    User::class => ['mainCharacter'],
                    CharacterInfo::class => [],
                ]),
            ])
            ->get();

        $roundsByCorporation = EnlistmentReviewRound::query()
            ->whereIn('corporation_id', $applications->pluck('corporation_id')->unique())
            ->orderBy('position')
            ->get()
            ->groupBy('corporation_id');

        $pending = $applications
            ->map(fn (Application $application) => $this->present($application, $roundsByCorporation))
            ->filter(fn (?array $row) => $stageGate->allows($user, $row['role_id']))
            ->map(function (array $row) {
                unset($row['role_id']);

                return $row;
            })
            ->values();

        return Inertia::render('Recruitment/Reviews/Index', [
            'pending' => $pending,
        ]);
    }

    /**
     * @param  Collection<int, Collection<int, EnlistmentReviewRound>>  $roundsByCorporation
     * @return array{application_id: string, role_id: ?int, applicant: array, corporation: array, stage: array, total_stages: int, review_url: string}
     */
    private function present(Application $application, Collection $roundsByCorporation): array
    {
        $rounds = $roundsByCorporation->get($application->corporation_id, collect());
        $position = $application->logEntries->where('type', 'decision')->count();
        $round = $rounds->firstWhere('position', $position);

        $applicationable = $application->applicationable;
        $mainCharacter = $applicationable instanceof User ? $applicationable->mainCharacter : $applicationable;

        return [
            'application_id' => $application->id,
            'role_id' => $round?->role_id,
            'applicant' => [
                'name' => $mainCharacter?->name,
                'character_id' => $mainCharacter?->character_id,
                'is_user' => $applicationable instanceof User,
            ],
            'corporation' => [
                'corporation_id' => $application->corporation->corporation_id,
                'name' => $application->corporation->name,
                'ticker' => $application->corporation->ticker,
            ],
            'stage' => [
                'position' => $position,
                'label' => $round?->label ?? 'Open',
            ],
            'total_stages' => $rounds->count(),
            'review_url' => route('get.application', $application->id),
        ];
    }
}
