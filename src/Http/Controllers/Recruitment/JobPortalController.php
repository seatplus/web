<?php

namespace Seatplus\Web\Http\Controllers\Recruitment;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Support\Collection;
use Inertia\Inertia;
use Inertia\Response;
use Seatplus\Auth\Models\User;
use Seatplus\Eveapi\Models\Application;
use Seatplus\Eveapi\Models\Character\CharacterAffiliation;
use Seatplus\Eveapi\Models\Character\CharacterInfo;
use Seatplus\Eveapi\Models\Corporation\CorporationInfo;
use Seatplus\Web\Http\Controllers\Controller;
use Seatplus\Web\Http\Resources\JobPostingResource;
use Seatplus\Web\Models\Recruitment\ApplicationGroupMember;
use Seatplus\Web\Models\Recruitment\ApplicationRoundReview;
use Seatplus\Web\Models\Recruitment\Enlistment;
use Seatplus\Web\Models\Recruitment\EnlistmentReviewRound;
use Seatplus\Web\Support\CorporationShape;

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

        $user->loadMissing('characters.characterAffiliation');

        // The corporation(s) the applicant currently belongs to — read straight from the affiliation
        // (present even before the CorporationInfo row is). You don't apply to your own corp, so their
        // postings are pushed to the bottom of the portal rather than surfaced first.
        $currentCorporationIds = $user->characters
            ->map(function (CharacterInfo $character): ?int {
                $affiliation = $character->characterAffiliation;

                return $affiliation instanceof CharacterAffiliation ? (int) $affiliation->corporation_id : null;
            })
            ->filter()
            ->unique()
            ->values();

        $postings = Enlistment::query()
            ->with([
                'corporation.alliance',
                'reviewRounds' => fn (HasMany $query) => $query->orderBy('position'),
            ])
            ->get()
            ->sortBy(fn (Enlistment $posting) => $currentCorporationIds->contains((int) $posting->corporation_id) ? 1 : 0)
            ->values();

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
            ->with([
                'corporation',
                'logEntries',
                'applicationable' => fn (MorphTo $morphTo) => $morphTo->morphWith([CharacterInfo::class => []]),
            ])
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

        // A multi-character application is one submission — collapse its members into a single entry
        // that lists the covered characters. Ungrouped applications are a group of one.
        $groupIdByApplication = ApplicationGroupMember::query()
            ->whereIn('application_id', $applications->pluck('id'))
            ->pluck('group_id', 'application_id');

        $groupKey = fn (Application $application): string => (string) ($groupIdByApplication->get((string) $application->id) ?? $application->id);

        $coveredCharactersByGroup = $applications
            ->filter(fn (Application $application) => $application->applicationable instanceof CharacterInfo)
            ->groupBy($groupKey)
            ->map(fn (Collection $members) => $members
                ->map(function (Application $application): string {
                    /** @var CharacterInfo $character */
                    $character = $application->applicationable;

                    return (string) $character->name;
                })
                ->values()
                ->all());

        return $applications->unique($groupKey)->map(function (Application $application) use ($roundsByCorporation, $reviewsByApplication, $groupKey, $coveredCharactersByGroup) {
            /** @var Collection<int, EnlistmentReviewRound> $rounds */
            $rounds = $roundsByCorporation->get($application->corporation_id, collect());
            $position = $application->logEntries->where('type', 'decision')->count();
            $currentRound = $rounds->firstWhere('position', $position);

            /** @var CorporationInfo $corporation */
            $corporation = $application->corporation;

            return [
                'application_id' => $application->id,
                'corporation' => CorporationShape::make($corporation),
                'status' => $application->status,
                // The character(s) this application covers (empty for a whole-account application).
                'covered_characters' => $coveredCharactersByGroup->get($groupKey($application), []),
                'withdraw_url' => route('recruitment.withdraw', $application->id),
                'current_position' => $position,
                'current_stage' => $application->status === 'open'
                    ? ($currentRound instanceof EnlistmentReviewRound ? $currentRound->label : 'Open')
                    : null,
                'total_stages' => $rounds->count(),
                'timeline' => $reviewsByApplication->get($application->id, collect())
                    ->map(function (ApplicationRoundReview $review) use ($rounds) {
                        $round = $rounds->firstWhere('position', $review->position);
                        $causer = $review->causer;
                        $mainCharacter = $causer instanceof User ? $causer->mainCharacter : null;

                        return [
                            'position' => $review->position,
                            'stage_label' => $round instanceof EnlistmentReviewRound ? $round->label : 'Stage '.($review->position + 1),
                            // Whose main character acted; comments are deliberately omitted from the applicant view.
                            'reviewer' => $mainCharacter instanceof CharacterInfo ? $mainCharacter->name : 'A recruiter',
                            'decision' => $review->decision,
                            'at' => $review->created_at?->toDateTimeString(),
                        ];
                    })
                    ->values()
                    ->all(),
            ];
        })->values()->all();
    }
}
