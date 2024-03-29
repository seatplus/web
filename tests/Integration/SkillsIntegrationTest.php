<?php


use Inertia\Testing\AssertableInertia as Assert;
use Seatplus\Auth\Models\Permissions\Permission;
use Spatie\Permission\PermissionRegistrar;

beforeEach(function () {
    $permission = Permission::findOrCreate('superuser');

    test()->test_user->givePermissionTo($permission);

    // now re-register all the roles and permissions
    app()->make(PermissionRegistrar::class)->registerPermissions();
});

test('has dispatchable job', function () {
    $response = test()->actingAs(test()->test_user)
        ->get(route('character.skills'));

    $response->assertInertia(
        fn (Assert $page) => $page
        ->component('Character/Skill/Index')
        ->has('dispatchTransferObject')
    );
});

test('one get skills per character', function () {
    $response = test()->actingAs(test()->test_user)
        ->get(route('get.character.skills', test()->test_character->character_id))
        ->assertOk();
});

test('one get skill queue per character', function () {
    $response = test()->actingAs(test()->test_user)
        ->get(route('get.character.skill.queue', test()->test_character->character_id))
        ->assertOk();
});
