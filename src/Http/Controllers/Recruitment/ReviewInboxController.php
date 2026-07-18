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
            ->filter(fn (Application $application) => $stageGate->allows($user, $this->currentRoleId($application, $roundsByCorporation)))
            ->map(fn (Application $application) => $this->present($application, $roundsByCorporation))
            ->values()
            ->all();

        return Inertia::render('Recruitment/Reviews/Index', [
            'pending' => $pending,
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
    private function present(Application $application, Collection $roundsByCorporation): array
    {
        /** @var Collection<int, EnlistmentReviewRound> $rounds */
        $rounds = $roundsByCorporation->get($application->corporation_id, collect());
        $position = $application->logEntries->where('type', 'decision')->count();
        $round = $rounds->firstWhere('position', $position);

        $applicationable = $application->applicationable;
        $mainCharacter = $applicationable instanceof User
            ? $applicationable->mainCharacter
            : ($applicationable instanceof CharacterInfo ? $applicationable : null);

        /** @var CorporationInfo $corporation */
        $corporation = $application->corporation;

        return [
            'application_id' => $application->id,
            'applicant' => [
                'name' => $mainCharacter instanceof CharacterInfo ? $mainCharacter->name : null,
                'character_id' => $mainCharacter instanceof CharacterInfo ? $mainCharacter->character_id : null,
                'is_user' => $applicationable instanceof User,
            ],
            'corporation' => CorporationShape::make($corporation),
            'stage' => [
                'position' => $position,
                'label' => $round instanceof EnlistmentReviewRound ? $round->label : 'Open',
            ],
            'total_stages' => $rounds->count(),
            'review_url' => route('get.application', $application->id),
        ];
    }
}
