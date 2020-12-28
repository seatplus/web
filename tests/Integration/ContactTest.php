<?php


namespace Seatplus\Web\Tests\Integration;


use Seatplus\Eveapi\Models\Assets\CharacterAsset;
use Seatplus\Eveapi\Models\Contacts\Contact;
use Seatplus\Web\Tests\TestCase;

class ContactTest extends TestCase
{


    /** @test */
    public function see_component()
    {

        $response = $this->actingAs($this->test_user)
            ->get(route('character.contacts'));

        $response->assertInertia('Character/Contact/Index');
    }

    /** @test */
    public function it_has_details()
    {

        $this->test_character->contacts()->create(Contact::factory()->make()->toArray());

        $contact = $this->test_character->contacts->first();

        $response = $this->actingAs($this->test_user)
            ->get(route('character.contacts.detail', $this->test_character->character_id));

        $response->assertOk();

    }

}
