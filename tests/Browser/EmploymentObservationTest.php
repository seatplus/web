<?php

use Illuminate\Foundation\Testing\RefreshDatabase;

require_once __DIR__.'/helpers.php';

/*
 * Browser tests for observation during employment (Personnel → Observation), run against the
 * assembled core app. A director observes their corporation's members (compliance + activity) and
 * inspects one across the shared tabs. Real logged-in character comes from core's actingAsCharacter().
 */

uses(RefreshDatabase::class);

// ═══ Observation ═══════════════════════════════════════════════════════════════════════════════

it('observation — a director sees a corporation\'s members with compliance and activity', function (string $device) {
    $character = actingAsCharacter();
    makeObserverOfCorporation($character);
    seedObservableCorporation($character);
    cache()->flush();

    $page = deviceVisit($device, '/employment');
    $page->assertNoSmoke();

    $page->waitForText('Observation');
    // Members are fetched asynchronously per corporation — wait for the member row to resolve.
    $page->waitForText('characters compliant');

    snap($page, "observation-{$device}");
})->with(['desktop', 'iphone']);

it('observation — a director inspects a member', function (string $device) {
    $character = actingAsCharacter();
    $user = makeObserverOfCorporation($character);
    seedObservableCorporation($character);
    cache()->flush();

    $page = deviceVisit($device, "/employment/{$character->corporation_id}/member/{$user->getKey()}");
    $page->assertNoSmoke();

    // The inspect header names the member; the shared inspection tabs render below it. (The Skills /
    // Contacts / Mails tab data is wired up on the multi-character branch, so only the default view is
    // asserted here.)
    $page->waitForText('Inspect');

    snap($page, "observation-inspect-{$device}");
})->with(['desktop', 'iphone']);
