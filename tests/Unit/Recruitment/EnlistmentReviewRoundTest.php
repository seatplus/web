<?php

use Seatplus\Auth\Models\Permissions\Role;
use Seatplus\Auth\Models\User;
use Seatplus\Eveapi\Models\Recruitment\Enlistments;
use Seatplus\Web\Models\Recruitment\EnlistmentReviewRound;
use Seatplus\Web\Services\Recruitment\StageGate;

it('creates a review round together with its posting', function () {
    $round = EnlistmentReviewRound::factory()->create();

    expect($round->role_id)->toBeNull()
        ->and($round->role)->toBeNull()
        ->and($round->enlistment)->toBeInstanceOf(Enlistments::class)
        ->and($round->enlistment->corporation_id)->toBe($round->corporation_id);
});

it('resolves the control-group relation when a role is set', function () {
    $role = Role::findOrCreate('Senior HR');

    $round = EnlistmentReviewRound::factory()->create(['role_id' => $role->id]);

    expect($round->role)->toBeInstanceOf(Role::class)
        ->and($round->role->id)->toBe($role->id);
});

it('gate falls back to the recruiter permission when the round has no control-group', function () {
    $user = User::factory()->create();
    $gate = new StageGate;

    expect($gate->allows($user, null))->toBeFalse();

    $user->givePermissionTo(StageGate::RECRUITER_PERMISSION);

    expect($gate->allows($user->fresh(), null))->toBeTrue();
});

it('gate lets a superuser through regardless of round configuration', function () {
    $user = User::factory()->create();
    $user->givePermissionTo('superuser');

    expect((new StageGate)->allows($user->fresh(), null))->toBeTrue();
});

it('gate checks control-group membership when the round has a role', function () {
    $role = Role::findOrCreate('Junior HR');
    $user = User::factory()->create();
    $gate = new StageGate;

    expect($gate->allows($user, $role->id))->toBeFalse();

    $user->assignRole($role);

    expect($gate->allows($user->fresh(), $role->id))->toBeTrue();
});

it('backfills the steps string into ordered review rounds', function () {
    $multi = Enlistments::query()->create(['corporation_id' => 98000001, 'type' => 'user', 'steps' => 'Screen; Interview; Final']);
    $default = Enlistments::query()->create(['corporation_id' => 98000002, 'type' => 'character']);

    $migration = require __DIR__.'/../../../database/migrations/2026_07_18_000003_backfill_enlistment_review_rounds_from_steps.php';
    $migration->up();

    $multiRounds = EnlistmentReviewRound::query()->where('corporation_id', 98000001)->orderBy('position')->pluck('label')->all();
    $defaultRounds = EnlistmentReviewRound::query()->where('corporation_id', 98000002)->pluck('label')->all();

    expect($multiRounds)->toBe(['Screen', 'Interview', 'Final'])
        ->and($defaultRounds)->toBe(['Open']);
});
