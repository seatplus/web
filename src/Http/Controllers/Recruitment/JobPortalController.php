<?php

namespace Seatplus\Web\Http\Controllers\Recruitment;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Inertia\Inertia;
use Inertia\Response;
use Seatplus\Auth\Models\User;
use Seatplus\Eveapi\Models\Application;
use Seatplus\Eveapi\Models\Character\CharacterInfo;
use Seatplus\Web\Http\Controllers\Controller;
use Seatplus\Web\Http\Resources\JobPostingResource;
use Seatplus\Web\Models\Recruitment\ApplicationRoundReview;
use Seatplus\Web\Models\Recruitment\Enlistment;
use Seatplus\Web\Models\Recruitment\EnlistmentReviewRound;

/**
 * The central job portal: a cross-corporation view over every open posting (enlistment), decoupled
 * from the corporation-management UI. Any authenticated user can browse and apply here.
 */
class JobPortalController extends Controller
{
    public function __invoke(): Response
    {
        /** @var User $user */
        $user = auth()->user();

        $postings = Enlistment::query()
            ->with([
                'corporation.alliance',
                'reviewRounds' => fn ($query) => $query->orderBy('position'),
            ])
            ->get();

        return Inertia::render('Recruitment/Portal/Index', [
            'postings' => JobPostingResource::collection($postings)->resolve(),
            'characters' => $user->characters
                ->map(fn (CharacterInfo $character) => [
                    'character_id' => $character->character_id,
                    'name' => $character->name,
                ])
                ->values(),
            'myApplications' => $this->myApplications($user),
            // Server-provided endpoint so the page needs neither Ziggy nor a generated Wayfinder import.
            'postApplicationUrl' => route('recruitment.apply'),
        ]);
    }

    /**
     * The applicant's own applications with progress and a reviewer-action timeline (no internal
     * comments): where they are in each corporation's review process and who advanced each stage.
     *
     * @return array<int, array<string, mixed>>
     */
    private function myApplications(User $user): array
    {
        $characterIds = $user->characters->pluck('character_id')->toArray();

        $applications = Application::query()
            ->whereHasMorph(
                'applicationable',
                [User::class, CharacterInfo::class],
                function (Builder $query, string $type) use ($user, $characterIds) {
                    match ($type) {
                        User::class => $query->whereKey($user->getAuthIdentifier()),
                        CharacterInfo::class => $query->whereIn('character_id', $characterIds),
                        default => null,
                    };
                }
            )
            ->with(['corporation', 'logEntries'])
            ->latest('updated_at')
            ->get();

        $roundsByCorporation = EnlistmentReviewRound::query()
            ->whereIn('corporation_id', $applications->pluck('corporation_id')->unique())
            ->orderBy('position')
            ->get()
            ->groupBy('corporation_id');

        $reviewsByApplication = ApplicationRoundReview::query()
            ->whereIn('application_id', $applications->pluck('id'))
            ->with(['causer' => fn (MorphTo $morphTo) => $morphTo->morphWith([User::class => ['mainCharacter']])])
            ->orderBy('position')
            ->get()
            ->groupBy('application_id');

        return $applications->map(function (Application $application) use ($roundsByCorporation, $reviewsByApplication) {
            $rounds = $roundsByCorporation->get($application->corporation_id, collect());
            $position = $application->logEntries->where('type', 'decision')->count();

            return [
                'application_id' => $application->id,
                'corporation' => [
                    'corporation_id' => $application->corporation->corporation_id,
                    'name' => $application->corporation->name,
                    'ticker' => $application->corporation->ticker,
                ],
                'status' => $application->status,
                'current_position' => $position,
                'current_stage' => $application->status === 'open'
                    ? ($rounds->firstWhere('position', $position)?->label ?? 'Open')
                    : null,
                'total_stages' => $rounds->count(),
                'timeline' => $reviewsByApplication->get($application->id, collect())
                    ->map(fn (ApplicationRoundReview $review) => [
                        'position' => $review->position,
                        'stage_label' => $rounds->firstWhere('position', $review->position)?->label ?? 'Stage '.($review->position + 1),
                        // Whose main character acted; comments are deliberately omitted from the applicant view.
                        'reviewer' => $review->causer?->mainCharacter?->name ?? 'A recruiter',
                        'decision' => $review->decision,
                        'at' => $review->created_at?->toDateTimeString(),
                    ])
                    ->values(),
            ];
        })->values()->toArray();
    }
}
