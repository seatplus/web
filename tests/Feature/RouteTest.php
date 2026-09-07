<?php

it('protects configurations routes', function () {
    $response = $this->actingAs($this->test_user)
        ->get(route('server.settings'))
        ->assertForbidden();
});

it('access control routes require view access control permission', function () {
    $this->actingAs($this->test_user)
        ->get(route('acl.hub'))
        ->assertForbidden();
});
