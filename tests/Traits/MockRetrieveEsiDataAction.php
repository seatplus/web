<?php


namespace Seatplus\Web\Tests\Traits;


use Facades\Seatplus\Eveapi\Services\Esi\RetrieveEsiData;
use Seat\Eseye\Containers\EsiResponse;

trait MockRetrieveEsiDataAction
{
    public function mockRetrieveEsiDataAction(array $body) : void
    {

        $data = json_encode($body);

        $response = new EsiResponse($data, [], 'now', 200);

        RetrieveEsiData::shouldReceive('execute')
            ->once()
            ->andReturn($response);

    }

    public function assertRetrieveEsiDataIsNotCalled() : void
    {
        RetrieveEsiData::shouldReceive('execute')->never();
    }

}