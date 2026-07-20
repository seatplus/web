<?php

use Illuminate\Foundation\Testing\RefreshDatabase;

require_once __DIR__.'/helpers.php';

/*
 * Character contacts browser tests — run against the real assembled core app.
 *
 * Covers the modernized contacts view: the per-character CharacterContactsComponent now receives
 * its rows via a keyed Inertia deferred prop (contacts, keyed by character_id) resolved on a
 * follow-up partial reload — no manual fetch. The card renders its rows once the deferred prop
 * arrives; before that the page shows a pulsing skeleton fallback. The contact row's EntityByIdBlock
 * resolves each contact_id asynchronously via the resolve.id + EveImage path (offline from the
 * DB — a factory CharacterInfo carries its own CharacterAffiliation, so its name renders without
 * ESI). A user always sees their OWN characters' contacts, so no permission is granted.
 * Provisioning comes from the suite helper actingAsCharacter() (core tests/Pest.php).
 */

uses(RefreshDatabase::class);

it('loads a character’s contacts via the fetch-swap POST (no 422)', function (string $device) {
    $character = actingAsCharacter();
    $contact = makeCharacterContact($character);

    $page = deviceVisit($device, '/character/contacts');
    $page->assertNoSmoke();
    $page->waitForText('Character Contacts');

    // The card header renders the owning character straight from the page prop (no async).
    $page->waitForText($character->name);

    // The rows arrive via the deferred `contacts` prop (partial reload after first paint). The
    // contact's resolved name appearing proves the load succeeded end-to-end
    // (Inertia::defer → ContactResource → deferred prop → EntityByIdBlock resolve).
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

    // …and each card renders its own contact row once the deferred prop lands. Contact names resolve lazily
    // (EntityByIdBlock's IntersectionObserver, threshold 1 — only fires when the block is *fully*
    // visible), so on the short iPhone viewport a below-the-fold card never resolves. Scroll each
    // card's own scroll container fully into view, settling between, so every block passes through
    // full visibility; once resolved the name stays in the DOM regardless of the final scroll pos.
    foreach (['0', '1'] as $cardIndex) {
        $page->script("(document.querySelectorAll('.max-h-96.overflow-y-auto')[{$cardIndex}] ?? document.body).scrollIntoView({ block: 'center' })");
        $page->waitForEvent('networkidle');
    }
    $page->waitForText($mainContact->name);
    $page->waitForText($secondContact->name);

    snap($page, "character-contacts-multiple-characters-{$device}");
})->with(['desktop', 'iphone']);
