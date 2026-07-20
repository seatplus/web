<?php

use Illuminate\Foundation\Testing\RefreshDatabase;
use Seatplus\Eveapi\Models\Character\CharacterInfo;

require_once __DIR__.'/helpers.php';

/*
 * Character contracts browser tests — run against the real assembled core app.
 *
 * Covers the modernized contracts view: per-character Inertia <InfiniteScroll> over a
 * scroll prop (contracts_<id>), and the assignee/acceptor entity blocks that resolve
 * asynchronously via the (axios/Ziggy-free) resolve.id + EveImage fetch path. A user
 * always sees their OWN characters' contracts, so no permission is granted.
 * Provisioning comes from the suite helper actingAsCharacter() (core tests/Pest.php).
 */

uses(RefreshDatabase::class);

it('merges the next contracts page in on scroll', function (string $device) {
    $character = actingAsCharacter();

    // 40 contracts → several paginator pages (default 15/page) for the contracts_<id> prop.
    makeCharacterContracts($character, 40);

    $rows = "#contracts-body-{$character->character_id} > *";

    $page = deviceVisit($device, '/character/contracts');
    $page->assertNoSmoke();
    $page->waitForText('Character Contracts');

    // Row <div>s come straight from the scroll prop data (no async needed to exist), so
    // the first page is present immediately.
    $before = (int) $page->script("document.querySelectorAll('{$rows}').length");
    expect($before)->toBeGreaterThan(0);

    // Re-scroll the list's own container to the bottom on every poll (comma expression)
    // so InfiniteScroll's end trigger fires; passes once the next page has merged in.
    $page->assertScript("(document.getElementById('contracts-body-{$character->character_id}').closest('.overflow-y-auto').scrollTo(0, 1e6), document.querySelectorAll('{$rows}').length > {$before})");

    snap($page, "character-contracts-infinite-scroll-{$device}");
})->with(['desktop', 'iphone']);

it('renders both the assignee and the acceptor for an accepted contract', function (string $device) {
    $character = actingAsCharacter();

    // A second, distinct character to act as the acceptor. A factory character carries its
    // own CharacterAffiliation, so resolve.id returns it offline (no ESI in the browser).
    $acceptor = CharacterInfo::factory()->create(['character_id' => realCharacterId()]);

    // Accepted contracts assigned to the main character but accepted by someone else — the
    // AssigneeComponent then renders a second entity block (acceptor_id !== 0 and != assignee).
    makeCharacterContracts($character, 3, [
        'acceptor_id' => $acceptor->character_id,
        'status' => 'in_progress',
    ]);

    $page = deviceVisit($device, '/character/contracts');
    $page->assertNoSmoke();
    $page->waitForText('Character Contracts');

    // The row entity blocks resolve asynchronously (IntersectionObserver → resolve.id →
    // EveImage). waitForText polls until both the assignee (the character itself) and the
    // acceptor blocks have loaded and rendered their names/images — i.e. give the images a
    // moment before asserting/screenshotting.
    $page->waitForText($character->name);
    $page->waitForText($acceptor->name);

    snap($page, "character-contracts-assignee-acceptor-{$device}");
})->with(['desktop', 'iphone']);

it('renders a contracts list for every character the user owns', function (string $device) {
    $mainCharacter = actingAsCharacter();
    $secondCharacter = attachOwnedCharacter($mainCharacter);

    // A handful of contracts per owned character so each list renders. The page emits one
    // contracts_<id> scroll prop / list per owned character, so both bodies must be present
    // — a single-character render would only emit the main character's.
    collect([$mainCharacter, $secondCharacter])
        ->each(fn (CharacterInfo $character) => makeCharacterContracts($character, 5));

    $page = deviceVisit($device, '/character/contracts');
    $page->assertNoSmoke();
    $page->waitForText('Character Contracts');

    $page->assertPresent("#contracts-body-{$mainCharacter->character_id}");
    $page->assertPresent("#contracts-body-{$secondCharacter->character_id}");

    snap($page, "character-contracts-multiple-characters-{$device}");
})->with(['desktop', 'iphone']);
