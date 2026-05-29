<?php

namespace Seatplus\Web\Tests\Traits;

use Mockery;
use Seatplus\EsiClient\EsiClient;
use Seatplus\EsiSchema\Contracts\EsiRawResponse;

trait MockRetrieveEsiDataAction
{
    public function mockRetrieveEsiDataAction(array $body): void
    {
        $data = array_is_list($body)
            ? array_map(fn ($item) => (object) (array) $item, $body)
            : (object) $body;
        $response = new EsiRawResponse(data: $data, isCachedLoad: false, pages: 1);

        $mock = Mockery::mock(EsiClient::class);
        $mock->shouldReceive('withToken')->andReturnSelf();
        $mock->shouldReceive('invoke')->once()->andReturn($response);
        $this->app->instance(EsiClient::class, $mock);
    }

    public function assertRetrieveEsiDataIsNotCalled(): void
    {
        $mock = Mockery::mock(EsiClient::class);
        $mock->shouldReceive('invoke')->never();
        $this->app->instance(EsiClient::class, $mock);
    }

    public function mockEsiResponse(array $body): EsiRawResponse
    {
        if (! array_is_list($body)) {
            $data = (object) $body;
        } else {
            $data = array_map(fn ($item) => (object) (array) $item, $body);
        }

        return new EsiRawResponse(data: $data, isCachedLoad: false, pages: 1);
    }
}
