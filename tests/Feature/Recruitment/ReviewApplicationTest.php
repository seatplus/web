<?php

use Illuminate\Support\Facades\Event;
use Seatplus\Auth\Models\Permissions\Role;
use Seatplus\Auth\Models\User;
use Seatplus\Eveapi\Models\Application;
use Seatplus\Eveapi\Models\Corporation\CorporationInfo;
use Seatplus\Web\Enums\EmploymentStatus;
use Seatplus\Web\Http\Actions\Recruitment\ReviewApplicationAction;
use Seatplus\Web\Models\Employment\Employment;
use Seatplus\Web\Models\Recruitment\Enlistment;
use Seatplus\Web\Models\Recruitment\EnlistmentReviewRound;
use Seatplus\Web\Tests\TestCase;
use Symfony\Component\HttpKernel\Exception\HttpException;

beforeEach(function () {
    $this->corp = CorporationInfo::factory()->create();
    Enlistment::query()->create(['corporation_id' => $this->corp->corporation_id, 'type' => 'character']);

    $this->juniorRole = Role::findOrCreate('Junior HR');
    $this->seniorRole = Role::findOrCreate('Senior HR');

    EnlistmentReviewRound::factory()->create(['corporation_id' => $this->corp->corporation_id, 'position' => 0, 'label' => 'Screen', 'role_id' => $this->juniorRole->id]);
    EnlistmentReviewRound::factory()->create(['corporation_id' => $this->corp->corporation_id, 'position' => 1, 'label' => 'Final', 'role_id' => $this->seniorRole->id]);

    $this->junior = Event::fakeFor(fn () => tap(User::factory()->create(), fn (User $u) => $u->assignRole($this->juniorRole)));
    $this->senior = Event::fakeFor(fn () => tap(User::factory()->create(), fn (User $u) => $u->assignRole($this->seniorRole)));

    $applicant = Event::fakeFor(fn () => User::factory()->create());
    $this->application = Application::factory()->create([
        'corporation_id' => $this->corp->corporation_id,
        'applicationable_type' => User::class,
        'applicationable_id' => $applicant->getKey(),
    ]);
});

function review(TestCase $case, User $reviewer, Application $application, string $decision): void
{
    $case->actingAs($reviewer);
    app(ReviewApplicationAction::class)->execute($application, $decision, $decision === 'rejected' ? 'not a fit' : null);
}

it('keeps the application open after the first of two rounds is approved', function () {
    review($this, $this->junior, $this->application, 'accepted');

    expect($this->application->refresh()->status)->toBe('open')
        ->and($this->application->decision_count)->toBe(1)
        ->and(Employment::query()->count())->toBe(0);
});

it('blocks a reviewer who is not in the round control group', function () {
    // Round 0 is gated to Junior HR; the senior reviewer is not a member.
    expect(fn () => review($this, $this->senior, $this->application, 'accepted'))
        ->toThrow(HttpException::class);

    expect($this->application->refresh()->decision_count)->toBe(0);
});

it('hires the applicant when the final round is accepted', function () {
    review($this, $this->junior, $this->application, 'accepted');
    review($this, $this->senior, $this->application, 'accepted');

    expect($this->application->refresh()->status)->toBe('accepted');

    $employment = Employment::query()->first();

    expect($employment)->not->toBeNull()
        ->and($employment->status)->toBe(EmploymentStatus::Active)
        ->and($employment->corporation_id)->toBe($this->corp->corporation_id)
        ->and($employment->application_id)->toBe($this->application->id);
});

it('rejects the application and hires no one', function () {
    review($this, $this->junior, $this->application, 'rejected');

    expect($this->application->refresh()->status)->toBe('rejected')
        ->and(Employment::query()->count())->toBe(0);
});
