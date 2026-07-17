<?php

it('protects configurations routes', function () {
    $response = test()->actingAs(test()->test_user)
        ->get(route('server.settings'))
        ->assertForbidden();
});

it('access control routes require view access control permission', function () {
    test()->actingAs(test()->test_user)
        ->get(route('acl.hub'))
        ->assertForbidden();
});
