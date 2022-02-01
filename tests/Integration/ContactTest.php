<?php


use Illuminate\Testing\Fluent\AssertableJson;
use Inertia\Testing\Assert;
use Seatplus\Eveapi\Models\Character\CharacterAffiliation;
use Seatplus\Eveapi\Models\Character\CharacterInfo;
use Seatplus\Eveapi\Models\Contacts\Contact;
use Seatplus\Eveapi\Models\Corporation\CorporationInfo;

test('see component', function () {
    $response = test()->actingAs(test()->test_user)
        ->get(route('character.contacts'));

    $response->assertInertia(fn (Assert $page) => $page->component('Character/Contact/Index'));
});

it('has details', function () {
    test()->test_character->contacts()->create(Contact::factory()->make()->toArray());

    $contact = test()->test_character->contacts->first();

    $response = test()->actingAs(test()->test_user)
        ->post(route('character.contacts.detail', test()->test_character->character_id), [
            'corporation_id' => test()->test_character->corporation->corporation_id,
        ]);

    $response->assertOk();
});

it('has corporation standing', function (string $contact_type, string $corp_contact_level) {
    $affiliation = CharacterAffiliation::factory()->create([
        'alliance_id' => faker()->numberBetween(99000000, 100000000),
        'faction_id' => faker()->numberBetween(500000, 1000000),
    ]);

    $contact_id = match ($contact_type) {
        'character' => $affiliation->character_id,
        'corporation' => $affiliation->corporation_id,
        'alliance' => $affiliation->alliance_id,
        'faction' => $affiliation->faction_id,
    };

    $contact = Contact::factory()->create([
        'contact_id' => $contact_id,
        'contact_type' => $contact_type,
        'standing' => 10.0,
        'contactable_id' => $this->test_character->character_id,
        'contactable_type' => CharacterInfo::class,
    ]);

    expect($contact)->affiliation->not()->toBeNull();

    $getCorpStanding = function (CharacterAffiliation $affiliation) use ($corp_contact_level) : float {
        $corp_standing = null;

        $contacts_array = match ($corp_contact_level) {
            'alliance' => ['alliance', 'corporation', 'faction', 'character'],
            'corporation' => ['corporation', 'faction', 'character'],
            'faction' => ['faction', 'character'],
            'character' => ['character'],
        };

        $getIdFromAffiliationByType = fn (string $type) => match ($type) {
            'character' => $affiliation->character_id,
            'corporation' => $affiliation->corporation_id,
            'alliance' => $affiliation->alliance_id,
            'faction' => $affiliation->faction_id,
        };

        foreach ($contacts_array as $contact_type) {
            $contact = Contact::factory()->create([
                'contact_id' => $getIdFromAffiliationByType($contact_type),
                'contact_type' => $contact_type,
                'standing' => round(faker()->randomFloat(1, -10.0, 10.0), 1),
                'contactable_id' => $this->test_character->corporation->corporation_id,
                'contactable_type' => CorporationInfo::class,
            ]);

            if ($contact->contact_type === $corp_contact_level) {
                $corp_standing = $contact->standing;
            }
        }

        $contacts = Contact::query()->where('contactable_id', $this->test_character->corporation->corporation_id)->get();

        expect($contacts)->toHaveCount(count($contacts_array));
        expect($corp_standing)->toBeFloat();

        return $corp_standing;
    };

    $corp_standing = $getCorpStanding($affiliation);

    test()->actingAs(test()->test_user)
        ->post(route('character.contacts.detail', test()->test_character->character_id), [
            'corporation_id' => test()->test_character->corporation->corporation_id,
        ])
        ->assertJson(
            fn (AssertableJson $json) =>
            $json->has(3)
                ->has('data', 1)
                ->has(
                    'data.0',
                    fn (AssertableJson $json) =>
                    $json->where('corporation_standing', json_decode(json_encode($corp_standing)))
                    ->etc()
                )
                ->etc()
        );
})->with(fn () => collect(['character', 'corporation', 'alliance', 'faction'])->crossJoin(['alliance', 'corporation', 'faction', 'character'])->toArray());
