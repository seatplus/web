<?php

use Illuminate\Testing\Fluent\AssertableJson;
use Seatplus\Eveapi\Models\Wallet\WalletJournal;

it('has journalType autosuggest endpoint', function () {
    $journal = WalletJournal::factory()->create();

    assignPermissionToTestUser('superuser');

    test()->actingAs(test()->test_user)
        ->get(route('wallet.journalTypes', ['search' => substr($journal->ref_type, 0, -5)]))
        ->assertJson(
            fn (AssertableJson $json) => $json
            ->has(1)
            ->first(
                fn (AssertableJson $json) => $json
                ->where('name', $journal->ref_type)
                ->etc()
            )
            ->etc()
        );
});
