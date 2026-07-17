<?php

use Illuminate\Foundation\Testing\RefreshDatabase;
use Pest\Browser\Api\PendingAwaitablePage;
use Pest\Browser\Enums\Device;
use Pest\Browser\Playwright\Playwright;
use Seatplus\Auth\Models\CharacterUser;
use Seatplus\Eveapi\Models\Character\CharacterInfo;
use Seatplus\Eveapi\Models\Contacts\Contact;

if (! function_exists('deviceVisit')) {
    /**
     * Visit $url on the given viewport ("desktop" or "iphone"). Browser tests run in the core app,
     * whose tests/Pest.php is not overlaid, so this helper is defined here (guarded) alongside the
     * suite's other function_exists helpers rather than in tests/Pest.php.
     */
    function deviceVisit(string $device, string $url, array $options = []): mixed
    {
        // iPhone: build a persistent page at the mobile viewport the same way visit() builds the
        // desktop one, so the page loads mobile from the start (no desktop-load-then-resize reflow,
        // no per-call re-navigation like ->on()->iPhone15()).
        if ($device === 'iphone') {
            return new PendingAwaitablePage(
                Playwright::defaultBrowserType(),
                Device::IPHONE_15,
                $url,
                $options,
            );
        }

        return visit($url, $options);
    }
}

if (! function_exists('snap')) {
    /**
     * Settle before screenshotting: flip lazy EVE-image portraits/logos to eager so off-screen
     * (full-page) images fetch, wait for the network to go idle, then capture — so screenshots show
     * resolved images instead of loading placeholders. Best-effort: a slow/absent image won't fail.
     */
    function snap($page, string $name): void
    {
        $page->script("document.querySelectorAll('img').forEach((i) => { i.loading = 'eager'; });");
        $page->waitForEvent('networkidle');
        $page->screenshot(true, $name);
    }
}

/*
 * Character contacts browser tests — run against the real assembled core app.
 *
 * Covers the modernized contacts view: the per-character CharacterContactsComponent now loads
 * its rows with a fetch-swap POST (axios/Ziggy-free — postJson + the Wayfinder getContacts
 * action) instead of the old CompleteLoadingHelper. The request body is `{ target_corporation_id }`
 * (the key the ContactsRequest actually validates); the old `{ corporation_id, alliance_id }`
 * body 422'd, so these tests assert the rows load at all. The contact row's EntityByIdBlock
 * resolves each contact_id asynchronously via the resolve.id + EveImage path (offline from the
 * DB — a factory CharacterInfo carries its own CharacterAffiliation, so its name renders without
 * ESI). A user always sees their OWN characters' contacts, so no permission is granted.
 * Provisioning comes from the suite helper actingAsCharacter() (core tests/Pest.php).
 */

uses(RefreshDatabase::class);

if (! function_exists('realCharacterId')) {
    /**
     * A real EVE character id (verified CEO) so images.evetech.net serves a real portrait in
     * screenshots instead of the generic default it returns for fabricated ids. Picks one not yet
     * used in this (RefreshDatabase-isolated) test; falls back to a random id if the pool is spent.
     */
    function realCharacterId(): int
    {
        $pool = [197343093, 1319140135, 92081232, 1191750472, 94391213, 887625289, 1435633555, 1809892636];
        $available = array_values(array_diff($pool, CharacterInfo::query()->pluck('character_id')->all()));

        return $available[0] ?? fake()->unique()->numberBetween(9000000, 98000000);
    }
}

if (! function_exists('attachOwnedCharacter')) {
    /**
     * Attach an additional owned character to the same user that owns $existing, so a browser test
     * can exercise the multi-character case — character-scoped pages render one card per every
     * character the logged-in user owns, not just the main. Returns the newly attached character.
     * Guarded so each Browser file can define it standalone without colliding when the suite loads
     * several.
     */
    function attachOwnedCharacter(CharacterInfo $existing): CharacterInfo
    {
        $user = CharacterUser::query()
            ->where('character_id', $existing->character_id)
            ->firstOrFail()
            ->user;

        $character = CharacterInfo::factory()->create(['character_id' => realCharacterId()]);

        CharacterUser::create([
            'user_id' => $user->getKey(),
            'character_id' => $character->character_id,
            'character_owner_hash' => sha1((string) $character->character_id),
        ]);

        return $character;
    }
}

if (! function_exists('makeCharacterContact')) {
    /**
     * Give $owner one character-type contact and return the contacted character. The contact_id is a
     * real EVE character (its own factory CharacterInfo + CharacterAffiliation), so the row's
     * EntityByIdBlock resolves the name/portrait offline via resolve.id. The contact is attached to
     * the owner (contactable), which is what the index() `has('contacts')` filter keys off of.
     */
    function makeCharacterContact(CharacterInfo $owner): CharacterInfo
    {
        $contactCharacter = CharacterInfo::factory()->create(['character_id' => realCharacterId()]);

        Contact::factory()->create([
            'contactable_id' => $owner->character_id,
            'contactable_type' => CharacterInfo::class,
            'contact_id' => $contactCharacter->character_id,
            'contact_type' => 'character',
            'standing' => 10.0,
        ]);

        return $contactCharacter;
    }
}

it('loads a character’s contacts via the fetch-swap POST (no 422)', function (string $device) {
    $character = actingAsCharacter();
    $contact = makeCharacterContact($character);

    $page = deviceVisit($device, '/character/contacts');
    $page->assertNoSmoke();
    $page->waitForText('Character Contacts');

    // The card header renders the owning character straight from the page prop (no async).
    $page->waitForText($character->name);

    // The rows are fetched on mount with { target_corporation_id } — the key ContactsRequest
    // validates. If the old { corporation_id, alliance_id } body were still sent the request would
    // 422 and no row would ever render, so the contact's resolved name appearing proves the load
    // succeeded end-to-end (postJson → getContacts → ContactResource → EntityByIdBlock resolve).
    $page->waitForText($contact->name);
    $page->assertNoSmoke();

    snap($page, "character-contacts-{$device}");
})->with(['desktop', 'iphone']);

it('renders a contacts card for every character the user owns', function (string $device) {
    $mainCharacter = actingAsCharacter();
    $secondCharacter = attachOwnedCharacter($mainCharacter);

    // Each owned character needs at least one contact — the index() query only lists characters
    // that `has('contacts')`. The view then renders one CharacterContactsComponent per such owned
    // character, so both card headers must be present; a single-character scope would show only
    // the main's.
    $mainContact = makeCharacterContact($mainCharacter);
    $secondContact = makeCharacterContact($secondCharacter);

    $page = deviceVisit($device, '/character/contacts');
    $page->assertNoSmoke();
    $page->waitForText('Character Contacts');

    // Both owning characters' cards render (header EntityBlock is fed the whole character object,
    // so its name is present without async)…
    $page->waitForText($mainCharacter->name);
    $page->waitForText($secondCharacter->name);

    // …and each card's fetch-swap load resolves its own contact row.
    $page->waitForText($mainContact->name);
    $page->waitForText($secondContact->name);

    snap($page, "character-contacts-multiple-characters-{$device}");
})->with(['desktop', 'iphone']);
