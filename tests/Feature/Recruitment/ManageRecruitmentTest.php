<?php

use Inertia\Testing\AssertableInertia as Assert;
use Seatplus\Eveapi\Models\Corporation\CorporationInfo;
use Seatplus\Eveapi\Models\SsoScopes;
use Seatplus\Web\Models\Recruitment\Enlistment;
use Seatplus\Web\Models\Recruitment\EnlistmentReviewRound;

beforeEach(function () {
    assignPermissionToTestUser('superuser');
    cache()->flush();
});

it('renders the manage workspace with the manageable postings', function () {
    $corp = CorporationInfo::factory()->create();
    Enlistment::query()->create(['corporation_id' => $corp->corporation_id, 'type' => 'user']);

    test()->actingAs(test()->test_user)
        ->get(route('recruitment.manage'))
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('Recruitment/Manage/Index')
            ->has('postings', 1)
            ->has('postings.0.required_scopes')
            ->has('controlGroups')
            ->has('availableScopes')
        );
});

it('opens a posting and seeds a default review stage', function () {
    $corp = CorporationInfo::factory()->create();

    test()->actingAs(test()->test_user)
        ->post(route('recruitment.posting.open'), ['corporation_id' => $corp->corporation_id, 'type' => 'user'])
        ->assertRedirect();

    expect(Enlistment::query()->find($corp->corporation_id))->not->toBeNull()
        ->and(EnlistmentReviewRound::query()->where('corporation_id', $corp->corporation_id)->pluck('label')->all())->toBe(['Open']);
});

it('replaces the review stages of a posting', function () {
    $corp = CorporationInfo::factory()->create();
    Enlistment::query()->create(['corporation_id' => $corp->corporation_id, 'type' => 'user']);
    EnlistmentReviewRound::factory()->create(['corporation_id' => $corp->corporation_id, 'position' => 0, 'label' => 'Open']);

    test()->actingAs(test()->test_user)
        ->put(route('recruitment.posting.save', $corp->corporation_id), [
            'stages' => [
                ['label' => 'Screen', 'role_id' => null],
                ['label' => 'Final', 'role_id' => null],
            ],
        ])
        ->assertRedirect();

    expect(EnlistmentReviewRound::query()->where('corporation_id', $corp->corporation_id)->orderBy('position')->pluck('label')->all())
        ->toBe(['Screen', 'Final']);
});

it('saves the corporation required SSO scopes as the default type', function () {
    $corp = CorporationInfo::factory()->create();
    Enlistment::query()->create(['corporation_id' => $corp->corporation_id, 'type' => 'user']);
    EnlistmentReviewRound::factory()->create(['corporation_id' => $corp->corporation_id, 'position' => 0, 'label' => 'Open']);

    test()->actingAs(test()->test_user)
        ->put(route('recruitment.posting.save', $corp->corporation_id), [
            'stages' => [['label' => 'Open', 'role_id' => null]],
            'selected_scopes' => ['esi-assets.read_assets.v1'],
        ])
        ->assertRedirect();

    $scopes = SsoScopes::query()->where('morphable_id', $corp->corporation_id)->first();

    expect($scopes)->not->toBeNull()
        ->and($scopes->type)->toBe('default')
        ->and($scopes->selected_scopes)->toContain('esi-assets.read_assets.v1');
});

it('removes the required SSO scopes when the selection is cleared', function () {
    $corp = CorporationInfo::factory()->create();
    Enlistment::query()->create(['corporation_id' => $corp->corporation_id, 'type' => 'user']);
    EnlistmentReviewRound::factory()->create(['corporation_id' => $corp->corporation_id, 'position' => 0, 'label' => 'Open']);
    SsoScopes::factory()->create([
        'morphable_id' => $corp->corporation_id,
        'morphable_type' => CorporationInfo::class,
        'type' => 'default',
    ]);

    test()->actingAs(test()->test_user)
        ->put(route('recruitment.posting.save', $corp->corporation_id), [
            'stages' => [['label' => 'Open', 'role_id' => null]],
            'selected_scopes' => [],
        ])
        ->assertRedirect();

    expect(SsoScopes::query()->where('morphable_id', $corp->corporation_id)->exists())->toBeFalse();
});

it('closes a posting and cascades its stages', function () {
    $corp = CorporationInfo::factory()->create();
    Enlistment::query()->create(['corporation_id' => $corp->corporation_id, 'type' => 'user']);
    EnlistmentReviewRound::factory()->create(['corporation_id' => $corp->corporation_id, 'position' => 0, 'label' => 'Open']);

    test()->actingAs(test()->test_user)
        ->delete(route('recruitment.posting.close', $corp->corporation_id))
        ->assertRedirect();

    expect(Enlistment::query()->find($corp->corporation_id))->toBeNull()
        ->and(EnlistmentReviewRound::query()->where('corporation_id', $corp->corporation_id)->count())->toBe(0);
});
