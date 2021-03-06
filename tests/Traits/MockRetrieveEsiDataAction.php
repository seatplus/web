<?php


namespace Seatplus\Web\Tests\Traits;


use Seatplus\Eveapi\Services\Facade\RetrieveEsiData;
use Seat\Eseye\Containers\EsiResponse;

trait MockRetrieveEsiDataAction
{
    public function mockRetrieveEsiDataAction(array $body) : void
    {

        $response = $this->mockEsiResponse($body);

        RetrieveEsiData::shouldReceive('execute')
            ->once()
            ->andReturn($response);

    }

    public function assertRetrieveEsiDataIsNotCalled() : void
    {
        RetrieveEsiData::shouldReceive('execute')->never();
    }

    public function mockEsiResponse(array $body) : EsiResponse
    {
        $data = json_encode($body);

        return new EsiResponse($data, [], 'now', 200);
    }

}