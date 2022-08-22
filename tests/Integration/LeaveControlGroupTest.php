<?php


use Illuminate\Support\Facades\Queue;
use Seatplus\Auth\Models\Permissions\Role;
use Seatplus\Auth\Models\User;
use Seatplus\Eveapi\Models\Character\CharacterInfo;

beforeEach(function () {
    Queue::fake();

    $role = Role::create(['name' => 'test', 'type' => 'on-request']);
    test()->role = Role::find($role->id);

    test()->secondary_user = User::factory()->create();
    test()->secondary_character = test()->secondary_user->characters->first();
});

test('user can leave himself', function () {
    // First create affiliation
    test()->role->acl_affiliations()->create([
        'affiliatable_id' => test()->test_character->character_id,
        'affiliatable_type' => CharacterInfo::class,
    ]);


    // Second make test character member
    test()->role->activateMember(test()->test_user);

    expect(test()->test_user->hasRole(test()->role))->toBeTrue();

    assignPermissionToTestUser(['view access control']);

    $response = test()->actingAs(test()->test_user)
        ->delete(route('acl.leave', [
            'user_id' => test()->test_user->id,
            'role_id' => test()->role->id,
        ]));

    expect(test()->test_user->refresh()->hasRole(test()->role))->toBeFalse();
});

test('user can kick other user as superuser', function () {
    // First create affiliation
    test()->role->acl_affiliations()->create([
        'affiliatable_id' => test()->secondary_character->character_id,
        'affiliatable_type' => CharacterInfo::class,
    ]);

    // Second make secondary character member
    test()->role->activateMember(test()->secondary_user);

    expect(test()->test_user->hasRole(test()->role))->toBeFalse();
    expect(test()->secondary_user->hasRole(test()->role))->toBeTrue();

    assignPermissionToTestUser(['view access control', 'superuser']);

    expect(test()->test_user->can('superuser'))->toBeTrue();

    $response = test()->actingAs(test()->test_user)
        ->delete(route('acl.leave', [
            'user_id' => test()->secondary_user->id,
            'role_id' => test()->role->id,
        ]));

    expect(test()->secondary_user->refresh()->hasRole(test()->role))->toBeFalse();
});

test('user can kick other user as moderator', function () {
    // First create affiliation
    test()->role->acl_affiliations()->create([
        'affiliatable_id' => test()->secondary_character->character_id,
        'affiliatable_type' => CharacterInfo::class,
    ]);

    // Second make secondary character member
    test()->role->activateMember(test()->secondary_user);
    expect(test()->secondary_user->hasRole(test()->role))->toBeTrue();

    // Thirdly make primary character moderator
    expect(test()->role->moderators->isEmpty())->toBeTrue();
    test()->role->moderators()->create([
        'affiliatable_id' => test()->test_character->character_id,
        'affiliatable_type' => CharacterInfo::class,
        'can_moderate' => true,
    ]);
    expect(test()->role->refresh()->moderators->isNotEmpty())->toBeTrue();

    // Apparently a moderator does not need to be member
    expect(test()->test_user->hasRole(test()->role))->toBeFalse();

    assignPermissionToTestUser(['view access control']);
    expect(test()->test_user->can('superuser'))->toBeFalse();

    $response = test()->actingAs(test()->test_user)
        ->delete(route('acl.leave', [
            'user_id' => test()->secondary_user->id,
            'role_id' => test()->role->id,
        ]));

    expect(test()->secondary_user->refresh()->hasRole(test()->role))->toBeFalse();
});

test('user can not kick other user as vanilla user', function () {
    // First create affiliation
    test()->role->acl_affiliations()->create([
        'affiliatable_id' => test()->secondary_character->character_id,
        'affiliatable_type' => CharacterInfo::class,
    ]);

    // Second make secondary character member
    test()->role->activateMember(test()->secondary_user);
    expect(test()->secondary_user->hasRole(test()->role))->toBeTrue();

    assignPermissionToTestUser(['view access control']);
    expect(test()->test_user->can('superuser'))->toBeFalse();

    $response = test()->actingAs(test()->test_user)
        ->delete(route('acl.leave', [
            'user_id' => test()->secondary_user->id,
            'role_id' => test()->role->id,
        ]));

    expect($response->getStatusCode())->toEqual(403);

    expect(test()->secondary_user->refresh()->hasRole(test()->role))->toBeTrue();
});
