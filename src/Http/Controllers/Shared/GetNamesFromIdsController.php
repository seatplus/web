<?php


namespace Seatplus\Web\Http\Controllers\Shared;


use Seatplus\Eveapi\Actions\Eseye\RetrieveEsiDataAction;
use Seatplus\Eveapi\Containers\EsiRequestContainer;
use Seatplus\Web\Http\Controllers\Controller;

class GetNamesFromIdsController extends Controller
{
    public function __invoke()
    {
        $container = new EsiRequestContainer([
            'method' => 'post',
            'version' => 'v3',
            'endpoint' => '/universe/names/',
            'request_body' => request()->all(),
        ]);

        $result = (new RetrieveEsiDataAction)->execute($container);

        return collect($result)->toJson();
    }

}
