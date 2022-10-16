<?php

use Seatplus\Web\Models\Onboarding;
use function Pest\Laravel\actingAs;

it('throws error when turned off and navigated to onboarding if turned off', function () {
    config()->set('web.config.ONBOARDING', false);

    actingAs($this->test_user);

    $response = $this->get(route('onboarding'))->assertStatus(500);

});

it('redirects to onboarding if turned on', function () {
    config()->set('web.config.ONBOARDING', true);

    actingAs($this->test_user);

    expect($this->test_user->created_at->diffInMinutes(now()))->not()->toBeGreaterThan(60);

    $response = $this->get(route('home'))
        ->assertRedirect(route('onboarding'));

});

it('does not redirect to onboarding if user has been created longer then an hour ago', function () {
    config()->set('web.config.ONBOARDING', true);

    $this->test_user->created_at = now()->subHour();

    $this->test_user->save();

    actingAs($this->test_user);

    $this->get(route('home'))
        ->assertOk();
});

it('does not redirect to onboarding if user has completed onboarding', function () {
    config()->set('web.config.ONBOARDING', true);

    actingAs($this->test_user);

    Onboarding::create([
        'user_id' => $this->test_user->getAuthIdentifier(),
    ]);

    $this->get(route('home'))
        ->assertOk();
});

it('shows onboarding page', function () {
    config()->set('web.config.ONBOARDING', true);

    actingAs($this->test_user);

    $this->withoutMiddleware(\Seatplus\Web\Http\Middleware\OnboardingMiddleware::class);

    $response = $this->get(route('onboarding'));

    $response->assertOk()
        ->assertInertia(fn (\Inertia\Testing\AssertableInertia $page) => $page->component('Onboarding/Index'));

});

it('completes onboarding', function () {
    config()->set('web.config.ONBOARDING', true);

    actingAs($this->test_user);

    $this->withoutMiddleware(\Seatplus\Web\Http\Middleware\OnboardingMiddleware::class);

    expect(Onboarding::all())->toBeEmpty();

    $this->post(route('onboarding.complete'))
        ->assertRedirect(route('home'));

    expect(Onboarding::firstWhere('user_id', $this->test_user->getAuthIdentifier()))->toBeInstanceOf(Onboarding::class);
});