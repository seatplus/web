<?php

declare(strict_types=1);

namespace Seatplus\Web\Http\Actions\Recruitment;

use Seatplus\Auth\Models\User;
use Seatplus\Eveapi\Models\Application;
use Seatplus\Web\Enums\EmploymentStatus;
use Seatplus\Web\Models\Employment\Employment;
use Seatplus\Web\Models\Recruitment\ApplicationRoundReview;
use Seatplus\Web\Models\Recruitment\EnlistmentReviewRound;
use Seatplus\Web\Services\Recruitment\StageGate;

/**
 * The multi-stage review engine. An application advances one review round per decision: the current
 * round is the number of decisions taken so far, and each round is gated by the control group
 * configured for that position (or the flat recruiter permission when none is set). Accepting on the
 * final round hires the applicant — creating the Employment record that the observation layer watches.
 */
class ReviewApplicationAction
{
    public function __construct(
        private readonly StageGate $stageGate,
        private readonly CreateApplicationLogEntryAction $logEntry,
    ) {}

    public function execute(Application $application, string $decision, ?string $explanation): void
    {
        /** @var User $reviewer */
        $reviewer = auth()->user();

        $rounds = EnlistmentReviewRound::query()
            ->where('corporation_id', $application->corporation_id)
            ->orderBy('position')
            ->get();

        // The current round is the count of decisions already recorded (0-based).
        $position = $application->logEntries()->where('type', 'decision')->count();
        $roleId = $rounds->firstWhere('position', $position)?->role_id;

        abort_unless($this->stageGate->allows($reviewer, $roleId), 403, 'You may not review this stage.');

        // Legacy decision log (advances the round counter) + structured per-round audit.
        $this->logEntry
            ->setApplicationId((string) $application->id)
            ->setType('decision')
            ->setComment($explanation ?? '')
            ->execute();

        ApplicationRoundReview::query()->create([
            'application_id' => (string) $application->id,
            'position' => $position,
            'role_id' => $roleId,
            'causer_type' => User::class,
            'causer_id' => $reviewer->getAuthIdentifier(),
            'decision' => $decision,
            'comment' => $explanation,
        ]);

        if ($decision === 'rejected') {
            $application->update(['status' => 'rejected']);

            return;
        }

        // Accepted: if this was the final round, the applicant passes the whole process and is hired.
        if ($rounds->count() <= $position + 1) {
            $application->update(['status' => 'accepted']);
            $this->hire($application);
        }
    }

    private function hire(Application $application): void
    {
        Employment::query()->firstOrCreate(
            [
                'subject_type' => $application->applicationable_type,
                'subject_id' => $application->applicationable_id,
                'corporation_id' => $application->corporation_id,
            ],
            [
                'application_id' => $application->id,
                'status' => EmploymentStatus::Active,
                'hired_at' => now(),
            ],
        );
    }
}
