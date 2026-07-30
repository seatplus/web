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
use Seatplus\Eveapi\Models\Corporation\CorporationInfo;
use Seatplus\Web\Http\Controllers\Controller;
use Seatplus\Web\Models\Recruitment\ApplicationGroupMember;
use Seatplus\Web\Models\Recruitment\EnlistmentReviewRound;
use Seatplus\Web\Services\Recruitment\StageGate;
use Seatplus\Web\Support\CorporationShape;

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
        /** @var User $user */
        $user = auth()->user();
        $isSuperuser = $user->can('superuser');

        $applications = Application::query()
            ->when(! $isSuperuser, fn (Builder $query) => $this->getAffiliatedIds->scope(
                query: $query,
                column: 'corporation_id',
                permissions: [self::RECRUITER_PERMISSION],
                corporationRoles: ['Director'],
                user: $user,
            ))
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

        // Multi-character applications are one review: collapse a group into a single row and expose how
        // many characters it covers. Ungrouped (single-char / whole-account) applications are groups of one.
        $groupIdByApplication = ApplicationGroupMember::query()
            ->whereIn('application_id', $applications->pluck('id'))
            ->pluck('group_id', 'application_id');
        $groupSizes = $groupIdByApplication->countBy();

        $groupKey = fn (Application $application): string => (string) $groupIdByApplication->get((string) $application->id, (string) $application->id);
        $coveredCount = fn (Application $application): int => (int) $groupSizes->get($groupIdByApplication->get((string) $application->id), 1);

        // Open applications sitting at a stage this user's control group reviews.
        $pending = $applications
            ->where('status', 'open')
            ->filter(fn (Application $application) => $stageGate->allows($user, $this->currentRoleId($application, $roundsByCorporation)))
            ->unique($groupKey)
            ->map(fn (Application $application) => $this->present($application, $roundsByCorporation, $coveredCount($application)))
            ->values()
            ->all();

        // Past decisions (accepted/rejected) for the corporations this user recruits for.
        $history = $applications
            ->whereIn('status', ['accepted', 'rejected'])
            ->sortByDesc('updated_at')
            ->unique($groupKey)
            ->map(fn (Application $application) => $this->presentClosed($application, $roundsByCorporation, $coveredCount($application)))
            ->values()
            ->all();

        return Inertia::render('Recruitment/Reviews/Index', [
            'pending' => $pending,
            'history' => $history,
        ]);
    }

    /**
     * The control group gating the round this application currently sits at (null = flat permission).
     *
     * @param  Collection<int|string, Collection<int, EnlistmentReviewRound>>  $roundsByCorporation
     */
    private function currentRoleId(Application $application, Collection $roundsByCorporation): ?int
    {
        /** @var Collection<int, EnlistmentReviewRound> $rounds */
        $rounds = $roundsByCorporation->get($application->corporation_id, collect());
        $position = $application->logEntries->where('type', 'decision')->count();
        $round = $rounds->firstWhere('position', $position);

        return $round instanceof EnlistmentReviewRound ? $round->role_id : null;
    }

    /**
     * @param  Collection<int|string, Collection<int, EnlistmentReviewRound>>  $roundsByCorporation
     * @return array<string, mixed>
     */
    private function present(Application $application, Collection $roundsByCorporation, int $coveredCount = 1): array
    {
        /** @var Collection<int, EnlistmentReviewRound> $rounds */
        $rounds = $roundsByCorporation->get($application->corporation_id, collect());
        $position = $application->logEntries->where('type', 'decision')->count();
        $round = $rounds->firstWhere('position', $position);

        return [
            'application_id' => $application->id,
            'applicant' => $this->applicant($application),
            'covered_count' => $coveredCount,
            'corporation' => CorporationShape::make($this->corporationOf($application)),
            'stage' => [
                'position' => $position,
                'label' => $round instanceof EnlistmentReviewRound ? $round->label : 'Open',
            ],
            'total_stages' => $rounds->count(),
            'review_url' => route('get.application', $application->id),
        ];
    }

    /**
     * A settled application for the history list: who, which corp, the decision and when.
     *
     * @param  Collection<int|string, Collection<int, EnlistmentReviewRound>>  $roundsByCorporation
     * @return array<string, mixed>
     */
    private function presentClosed(Application $application, Collection $roundsByCorporation, int $coveredCount = 1): array
    {
        /** @var Collection<int, EnlistmentReviewRound> $rounds */
        $rounds = $roundsByCorporation->get($application->corporation_id, collect());

        return [
            'application_id' => $application->id,
            'applicant' => $this->applicant($application),
            'covered_count' => $coveredCount,
            'corporation' => CorporationShape::make($this->corporationOf($application)),
            'status' => $application->status,
            'decided_at' => $application->updated_at?->toDateTimeString(),
            'total_stages' => $rounds->count(),
            'review_url' => route('get.application', $application->id),
        ];
    }

    /**
     * @return array<string, mixed>
     */
    private function applicant(Application $application): array
    {
        $applicationable = $application->applicationable;
        $mainCharacter = $applicationable instanceof User
            ? $applicationable->mainCharacter
            : ($applicationable instanceof CharacterInfo ? $applicationable : null);

        return [
            'name' => $mainCharacter instanceof CharacterInfo ? $mainCharacter->name : null,
            'character_id' => $mainCharacter instanceof CharacterInfo ? $mainCharacter->character_id : null,
            'is_user' => $applicationable instanceof User,
        ];
    }

    private function corporationOf(Application $application): CorporationInfo
    {
        /** @var CorporationInfo $corporation */
        $corporation = $application->corporation;

        return $corporation;
    }
}
