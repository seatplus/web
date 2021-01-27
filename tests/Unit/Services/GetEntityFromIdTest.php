<?php


namespace Seatplus\Web\Tests\Unit\Services;


use Mockery;
use Seat\Eseye\Containers\EsiResponse;
use Seatplus\Eveapi\Models\Alliance\AllianceInfo;
use Seatplus\Eveapi\Models\Character\CharacterAffiliation;
use Seatplus\Eveapi\Models\Character\CharacterInfo;
use Seatplus\Eveapi\Models\Corporation\CorporationInfo;
use Seatplus\Web\Services\GetEntityFromId;
use Seatplus\Web\Services\GetNamesFromIdsService;
use Seatplus\Web\Tests\TestCase;

class GetEntityFromIdTest extends TestCase
{
    /** @test  */
    public function happyPath()
    {
        $character_affiliation = factory(CharacterAffiliation::class)->create([
            'character_id' => factory(CharacterInfo::class),
            'corporation_id' => factory(CorporationInfo::class),
            'alliance_id' => factory(AllianceInfo::class)
        ]);

        $expected_result = [
            'id' => $character_affiliation->character_id,
            'character_id' => $character_affiliation->character_id,
            'name' => $character_affiliation->character->name,
            'corporation' => [
                'name' => $character_affiliation->corporation->name
            ],
            'alliance' => [
                'name' => $character_affiliation->alliance->name
            ]
        ];

        $cache_key =  sprintf('entityById:%s', $character_affiliation->character_id);
        $this->assertNull(cache($cache_key));

        $service = new GetEntityFromId($character_affiliation->character_id);

        $result = $service->execute();

        $this->assertEquals($expected_result, $result);

        $this->assertEquals($expected_result, cache($cache_key));
    }

    /** @test  */
    public function happyPathWithoutAlliance()
    {
        $character_affiliation = factory(CharacterAffiliation::class)->create([
            'character_id' => factory(CharacterInfo::class),
            'corporation_id' => factory(CorporationInfo::class),
            'alliance_id' => null
        ]);

        $expected_result = [
            'id' => $character_affiliation->character_id,
            'character_id' => $character_affiliation->character_id,
            'name' => $character_affiliation->character->name,
            'corporation' => [
                'name' => $character_affiliation->corporation->name
            ]
        ];

        $service = new GetEntityFromId($character_affiliation->character_id);

        $result = $service->execute();

        $this->assertEquals($expected_result, $result);
    }

    /** @test  */
    public function happyPathViaCorporationId()
    {
        $character_affiliation = factory(CharacterAffiliation::class)->create([
            'character_id' => factory(CharacterInfo::class),
            'corporation_id' => factory(CorporationInfo::class),
            'alliance_id' => factory(AllianceInfo::class)
        ]);

        $expected_result = [
            'id' => $character_affiliation->corporation_id,
            'corporation_id' => $character_affiliation->corporation_id,
            'name' => $character_affiliation->corporation->name,
            'alliance' => [
                'name' => $character_affiliation->alliance->name
            ]
        ];

        $service = new GetEntityFromId($character_affiliation->corporation_id);

        $result = $service->execute();

        $this->assertEquals($expected_result, $result);
    }

    /** @test  */
    public function happyPathViaAllianceId()
    {
        $character_affiliation = factory(CharacterAffiliation::class)->create([
            'character_id' => factory(CharacterInfo::class),
            'corporation_id' => factory(CorporationInfo::class),
            'alliance_id' => factory(AllianceInfo::class)
        ]);

        $expected_result = [
            'id' => $character_affiliation->alliance_id,
            'alliance_id' => $character_affiliation->alliance_id,
            'name' => $character_affiliation->alliance->name
        ];

        $service = new GetEntityFromId($character_affiliation->alliance_id);

        $result = $service->execute();

        $this->assertEquals($expected_result, $result);
    }

    /** @test  */
    public function unknownCharacterId()
    {

        $alliance = factory(AllianceInfo::class)->create();
        $corporation = factory(CorporationInfo::class)->create([
            'alliance_id' => $alliance->alliance_id
        ]);
        $character = factory(CharacterInfo::class)->make([
            'corporation_id' => $corporation->corporation_id
        ]);

        $esi_mock_return_data = [
            'alliance_id' => $alliance->alliance_id,
            'character_id' => $character->character_id,
            'corporation_id' => $corporation->corporation_id,
            'faction_id' => null
        ];

        $this->mockRetrieveEsiDataAction([
            array_merge($esi_mock_return_data, [
                'id' => $character->character_id,
                'name' => $character->name,
                'category' => 'character'
            ])
        ]);


        $expected_result = [
            'id' => $character->character_id,
            'character_id' => $character->character_id,
            'name' => $character->name,
            'corporation' => [
                'name' => $corporation->name
            ],
            'alliance' => [
                'name' => $alliance->name
            ]
        ];

        $service = new GetEntityFromId($character->character_id);

        $result = $service->execute();

        $this->assertEquals($expected_result, $result);
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
