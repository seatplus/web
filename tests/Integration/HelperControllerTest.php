<?php


namespace Seatplus\Web\Tests\Integration;


use Mockery;
use Seat\Eseye\Containers\EsiResponse;
use Seatplus\Web\Tests\TestCase;

class HelperControllerTest extends TestCase
{

    /** @test */
    public function itStoresResolvedIdToCache() {

       $id = $this->test_character->character_id;

       $esi_mock_return_data = [
           'id' => $id,
           'name' => $this->test_character->name,
           'category' => 'character'
       ];

       $this->mockRetrieveEsiDataAction([$esi_mock_return_data]);

       $result = $this->actingAs($this->test_user)
           ->post(route('resolve.ids'), [$id]);

       $result->assertJson([
           $esi_mock_return_data
       ]);

       $cache_value = cache(sprintf('name:%s', $id));
       $this->assertEquals($this->test_character->name, $cache_value->name);
    }

    /** @test */
    public function itReturnsCachedValueForResolvedIds() {

        $id = $this->test_character->character_id;

        $cached_value = [
            'id' => $id,
            'name' => $this->test_character->name,
            'category' => 'character'
        ];

        cache([sprintf('name:%s', $id) => $cached_value], now()->addSeconds(2));

        $result = $this->actingAs($this->test_user)
            ->post(route('resolve.ids'), [$id]);

        $result->assertJson([
            $cached_value
        ]);
    }

    /** @test */
    public function itResolvesCharacterAffiliation() {

        $id = $this->test_character->character_id;

        $esi_mock_return_data = [
            'alliance_id' => 123,
            'character_id' => 456,
            'corporation_id' => 789,
            'faction_id' => null
        ];

        $this->mockRetrieveEsiDataAction([$esi_mock_return_data]);

        $result = $this->actingAs($this->test_user)
            ->post(route('resolve.character_affiliation'), [$id]);

        $result->assertJson([
            $esi_mock_return_data
        ]);
    }

    /** @test */
    public function itResolvesCorporationInfo() {

        $id = $this->test_character->corporation->corporation_id;

        $esi_mock_return_data = $this->test_character->corporation->toArray();

        $this->mockRetrieveEsiDataAction([$esi_mock_return_data]);

        $result = $this->actingAs($this->test_user)
            ->get(route('resolve.corporation_info', $id));

        $result->assertJson([
            $esi_mock_return_data
        ]);
    }

    private function mockRetrieveEsiDataAction(array $body) : void
    {

        $data = json_encode($body);

        $response = new EsiResponse($data, [], 'now', 200);

        $mock = Mockery::mock('overload:Seatplus\Eveapi\Actions\Eseye\RetrieveEsiDataAction');
        $mock->shouldReceive('execute')
            ->once()
            ->andReturn($response);
    }

}
