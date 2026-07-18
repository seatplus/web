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
use Symfony\Component\HttpKernel\Exception\HttpException;

beforeEach(function () {
    test()->corp = CorporationInfo::factory()->create();
    Enlistment::query()->create(['corporation_id' => test()->corp->corporation_id, 'type' => 'character']);

    test()->juniorRole = Role::findOrCreate('Junior HR');
    test()->seniorRole = Role::findOrCreate('Senior HR');

    EnlistmentReviewRound::factory()->create(['corporation_id' => test()->corp->corporation_id, 'position' => 0, 'label' => 'Screen', 'role_id' => test()->juniorRole->id]);
    EnlistmentReviewRound::factory()->create(['corporation_id' => test()->corp->corporation_id, 'position' => 1, 'label' => 'Final', 'role_id' => test()->seniorRole->id]);

    test()->junior = Event::fakeFor(fn () => tap(User::factory()->create(), fn (User $u) => $u->assignRole(test()->juniorRole)));
    test()->senior = Event::fakeFor(fn () => tap(User::factory()->create(), fn (User $u) => $u->assignRole(test()->seniorRole)));

    test()->application = Application::factory()->create(['corporation_id' => test()->corp->corporation_id]);
});

function review(User $reviewer, string $decision): void
{
    test()->actingAs($reviewer);
    app(ReviewApplicationAction::class)->execute(test()->application, $decision, $decision === 'rejected' ? 'not a fit' : null);
}

it('keeps the application open after the first of two rounds is approved', function () {
    review(test()->junior, 'accepted');

    expect(test()->application->refresh()->status)->toBe('open')
        ->and(test()->application->decision_count)->toBe(1)
        ->and(Employment::query()->count())->toBe(0);
});

it('blocks a reviewer who is not in the round control group', function () {
    // Round 0 is gated to Junior HR; the senior reviewer is not a member.
    expect(fn () => review(test()->senior, 'accepted'))
        ->toThrow(HttpException::class);

    expect(test()->application->refresh()->decision_count)->toBe(0);
});

it('hires the applicant when the final round is accepted', function () {
    review(test()->junior, 'accepted');
    review(test()->senior, 'accepted');

    expect(test()->application->refresh()->status)->toBe('accepted');

    $employment = Employment::query()->first();

    expect($employment)->not->toBeNull()
        ->and($employment->status)->toBe(EmploymentStatus::Active)
        ->and($employment->corporation_id)->toBe(test()->corp->corporation_id)
        ->and($employment->application_id)->toBe(test()->application->id);
});

it('rejects the application and hires no one', function () {
    review(test()->junior, 'rejected');

    expect(test()->application->refresh()->status)->toBe('rejected')
        ->and(Employment::query()->count())->toBe(0);
});
