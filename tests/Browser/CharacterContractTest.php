<?php

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Collection;
use Seatplus\Auth\Models\CharacterUser;
use Seatplus\Eveapi\Models\Character\CharacterInfo;
use Seatplus\Eveapi\Models\Contracts\Contract;
use Seatplus\Eveapi\Models\Corporation\CorporationInfo;
use Seatplus\Eveapi\Models\Universe\Location;

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

if (! function_exists('makeCharacterContracts')) {
    /**
     * Create $count contracts owned by (attached to) $character and return them. Defaults
     * model a personal, unaccepted (outstanding) contract issued to and assigned to the
     * character itself, so every id the row renders (issuer/assignee) resolves offline
     * from the DB. Override e.g. acceptor_id/status for the accepted case. Guarded so each
     * Browser file can define it standalone without colliding when the suite loads several.
     *
     * @param  array<string, mixed>  $overrides
     * @return Collection<int, Contract>
     */
    function makeCharacterContracts(CharacterInfo $character, int $count, array $overrides = []): Collection
    {
        // Share supporting records across the whole batch. Left to its defaults the Contract
        // factory spins up a fresh Location→Station→System and a CorporationInfo per contract,
        // and those random universe/corp ids collide (e.g. universe_systems_pkey) once enough
        // rows are created. Create them once and reuse the ids.
        $contracts = Contract::factory()->count($count)->create(array_merge([
            'issuer_id' => $character->character_id,
            'assignee_id' => $character->character_id,
            'for_corporation' => false,
            'acceptor_id' => 0,
            'status' => 'outstanding',
            'issuer_corporation_id' => CorporationInfo::factory()->create()->corporation_id,
            'start_location_id' => Location::factory()->withStation()->create()->location_id,
            'end_location_id' => Location::factory()->withStation()->create()->location_id,
        ], $overrides));

        $character->contracts()->syncWithoutDetaching($contracts->pluck('contract_id')->all());

        return $contracts;
    }
}

if (! function_exists('attachOwnedCharacter')) {
    /**
     * Attach an additional owned character to the same user that owns $existing, so a
     * browser test can exercise the multi-character case — character-scoped pages render
     * one list per every character the logged-in user owns, not just the main. Returns the
     * newly attached character. Guarded so each Browser file can define it standalone
     * without colliding when the suite loads several.
     */
    function attachOwnedCharacter(CharacterInfo $existing): CharacterInfo
    {
        $user = CharacterUser::query()
            ->where('character_id', $existing->character_id)
            ->firstOrFail()
            ->user;

        $character = CharacterInfo::factory()->create();

        CharacterUser::create([
            'user_id' => $user->getKey(),
            'character_id' => $character->character_id,
            'character_owner_hash' => sha1((string) $character->character_id),
        ]);

        return $character;
    }
}

it('merges the next contracts page in on scroll', function () {
    $character = actingAsCharacter();

    // 40 contracts → several paginator pages (default 15/page) for the contracts_<id> prop.
    makeCharacterContracts($character, 40);

    $rows = "#contracts-body-{$character->character_id} > *";

    $page = visit('/character/contracts');
    $page->assertNoSmoke();
    $page->waitForText('Character Contracts');

    // Row <div>s come straight from the scroll prop data (no async needed to exist), so
    // the first page is present immediately.
    $before = (int) $page->script("document.querySelectorAll('{$rows}').length");
    expect($before)->toBeGreaterThan(0);

    // Re-scroll the list's own container to the bottom on every poll (comma expression)
    // so InfiniteScroll's end trigger fires; passes once the next page has merged in.
    $page->assertScript("(document.getElementById('contracts-body-{$character->character_id}').closest('.overflow-y-auto').scrollTo(0, 1e6), document.querySelectorAll('{$rows}').length > {$before})");

    $page->screenshot(true, 'character-contracts-infinite-scroll');
});

it('renders both the assignee and the acceptor for an accepted contract', function () {
    $character = actingAsCharacter();

    // A second, distinct character to act as the acceptor. A factory character carries its
    // own CharacterAffiliation, so resolve.id returns it offline (no ESI in the browser).
    $acceptor = CharacterInfo::factory()->create();

    // Accepted contracts assigned to the main character but accepted by someone else — the
    // AssigneeComponent then renders a second entity block (acceptor_id !== 0 and != assignee).
    makeCharacterContracts($character, 3, [
        'acceptor_id' => $acceptor->character_id,
        'status' => 'in_progress',
    ]);

    $page = visit('/character/contracts');
    $page->assertNoSmoke();
    $page->waitForText('Character Contracts');

    // The row entity blocks resolve asynchronously (IntersectionObserver → resolve.id →
    // EveImage). waitForText polls until both the assignee (the character itself) and the
    // acceptor blocks have loaded and rendered their names/images — i.e. give the images a
    // moment before asserting/screenshotting.
    $page->waitForText($character->name);
    $page->waitForText($acceptor->name);

    $page->screenshot(true, 'character-contracts-assignee-acceptor');
});

it('renders a contracts list for every character the user owns', function () {
    $mainCharacter = actingAsCharacter();
    $secondCharacter = attachOwnedCharacter($mainCharacter);

    // A handful of contracts per owned character so each list renders. The page emits one
    // contracts_<id> scroll prop / list per owned character, so both bodies must be present
    // — a single-character render would only emit the main character's.
    collect([$mainCharacter, $secondCharacter])
        ->each(fn (CharacterInfo $character) => makeCharacterContracts($character, 5));

    $page = visit('/character/contracts');
    $page->assertNoSmoke();
    $page->waitForText('Character Contracts');

    $page->assertPresent("#contracts-body-{$mainCharacter->character_id}");
    $page->assertPresent("#contracts-body-{$secondCharacter->character_id}");

    $page->screenshot(true, 'character-contracts-multiple-characters');
});
