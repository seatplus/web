<?php

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Event;
use Seatplus\Auth\Models\User;
use Seatplus\Eveapi\Models\Corporation\CorporationInfo;
use Seatplus\Eveapi\Models\SsoScopes;
use Seatplus\Web\Http\Middleware\CheckRequiredScopes;
use Symfony\Component\HttpKernel\Exception\HttpException;

beforeEach(function () {
    test()->request = Mockery::mock(Request::class);
    test()->next = function ($request) {
        $request->forward();

        return new Response;
    };
});

/**
 * redirectTo() renders through the *global* request, not the mocked one the middleware is handed. Make
 * that global request an Inertia one so the response is the page object as JSON rather than the root
 * view with the payload buried in a data-page attribute.
 */
function respondToInertia(): void
{
    app()->instance('request', Request::create('/', server: ['HTTP_X_INERTIA' => 'true']));
}

/**
 * Require one scope of $user's corporation that its character's token does not carry.
 */
function requireUnheldScopeOf(User $user): void
{
    $character = $user->characters->first();

    SsoScopes::factory()->create([
        'morphable_id' => $character->corporation->corporation_id,
        'morphable_type' => CorporationInfo::class,
        'type' => 'default',
        'selected_scopes' => ['esi-mail.read_mail.v1'],
    ]);
}

it('should skip handle if environment is not production', function () {
    test()->request->shouldReceive('forward')->times(1);

    $middleware = Mockery::mock(CheckRequiredScopes::class, [])
        ->makePartial()
        ->shouldAllowMockingProtectedMethods();

    $middleware->shouldReceive('redirectTo')->times(0);

    $middleware->handle(test()->request, test()->next);
});

it('denies an unauthenticated request rather than skipping the check', function () {
    app()->detectEnvironment(fn () => 'production');

    test()->request->shouldReceive('user')->andReturnNull();
    test()->request->shouldReceive('forward')->times(0);

    // The guard itself now lives in seatplus/auth's parent handle(); this pins that web still inherits
    // it, because passing an unauthenticated request on would skip scope enforcement altogether.
    try {
        (new CheckRequiredScopes)->handle(test()->request, test()->next);

        test()->fail('An unauthenticated request should not have been allowed through.');
    } catch (HttpException $exception) {
        expect($exception->getStatusCode())->toBe(403);
    }
});

it('lets a compliant user through in production', function () {
    app()->detectEnvironment(fn () => 'production');

    test()->request->shouldReceive('user')->andReturn(test()->test_user);
    test()->request->shouldReceive('forward')->times(1);

    // Nothing is required of anyone, so the acting user is compliant.
    (new CheckRequiredScopes)->handle(test()->request, test()->next);
});

it('renders the re-authorise page for a non-compliant user', function () {
    app()->detectEnvironment(fn () => 'production');

    requireUnheldScopeOf(test()->test_user);
    respondToInertia();

    test()->request->shouldReceive('user')->andReturn(test()->test_user);
    test()->request->shouldReceive('forward')->times(0);

    // Not a mocked redirectTo: the real renderer used to fatal here, because the parent handed it
    // pluck('missing_scopes') — scope strings with the character dropped — while it dereferences
    // ->character->character_id.
    $response = (new CheckRequiredScopes)->handle(test()->request, test()->next);

    $character = test()->test_character;
    $page = json_decode($response->getContent(), true, flags: JSON_THROW_ON_ERROR);

    expect($response->getStatusCode())->toBe(200)
        ->and($page['component'])->toBe('Auth/MissingRequiredScopes')
        ->and($page['props']['characters'])->toHaveCount(1)
        ->and($page['props']['characters'][0]['character_id'])->toBe($character->character_id)
        ->and($page['props']['characters'][0]['name'])->toBe($character->name)
        ->and($page['props']['characters'][0]['upgrade_url'])->toContain('esi-mail.read_mail.v1');
});

// The regression this file exists for: BuildScopesArrayService::get() applies its user constraint only
// for a CharacterInfo, so for a User it fell through to an unconstrained ->first() and judged whichever
// user happened to come back. test_user is created in setUp and so sorts first — if the middleware
// still resolved the user that way, the compliant actor below would be walled by test_user's gap.
it('judges the acting user, not an arbitrary one', function () {
    app()->detectEnvironment(fn () => 'production');

    requireUnheldScopeOf(test()->test_user);

    $actor = Event::fakeFor(fn () => User::factory()->create());

    expect($actor->getKey())->toBeGreaterThan(test()->test_user->getKey());

    test()->request->shouldReceive('user')->andReturn($actor);
    test()->request->shouldReceive('forward')->times(1);

    (new CheckRequiredScopes)->handle(test()->request, test()->next);
});
