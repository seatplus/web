<?php

use Inertia\Testing\AssertableInertia as Assert;
use Seatplus\Auth\Models\Permissions\Permission;

beforeEach(function () {
    $permission = Permission::findOrCreate('superuser');

    $this->test_user->givePermissionTo($permission);

    // now re-register all the roles and permissions
});

test('has dispatchable job', function () {
    $response = $this->actingAs($this->test_user)
        ->get(route('character.skills'));

    $response->assertInertia(
        fn (Assert $page) => $page
            ->component('Character/Skill/Index')
            ->has('dispatchTransferObject')
    );
});

test('skills and skill queue are deferred and keyed by character id', function () {
    $characterId = $this->test_character->character_id;

    $response = $this->actingAs($this->test_user)
        ->get(route('character.skills'));

    // Deferred props are absent from the initial render, then resolved on the partial reload.
    $response->assertInertia(
        fn (Assert $page) => $page
            ->component('Character/Skill/Index')
            ->has('character_ids')
            ->missing('skills')
            ->missing('skillQueue')
            ->loadDeferredProps(fn (Assert $reload) => $reload
                ->has('skills')
                ->has('skills.'.$characterId)
                ->has('skillQueue')
                ->has('skillQueue.'.$characterId)
            )
    );
});
