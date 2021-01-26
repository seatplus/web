<?php


namespace Seatplus\Web\Services;


use Illuminate\Support\Collection;
use Seatplus\Eveapi\Actions\Eseye\RetrieveEsiDataAction;
use Seatplus\Eveapi\Containers\EsiRequestContainer;


class GetNamesFromIdsService
{

    private Collection $result;

    public function __construct()
    {
        $this->result = collect();
    }

    public function execute(array $ids) : Collection
    {
        $ids_to_resolve = collect($ids)->filter(function ($id) {

            if(! cache()->has(sprintf('name:%s', $id)))
                return true;

            $this->result->push(cache(sprintf('name:%s', $id)));
            return false;
        })->filter();

        if($ids_to_resolve->isEmpty())
            return $this->result;

        $container = new EsiRequestContainer([
            'method' => 'post',
            'version' => 'v3',
            'endpoint' => '/universe/names/',
            'request_body' => [...$ids_to_resolve->toArray()],
        ]);

        $esi_results = (new RetrieveEsiDataAction)->execute($container);

        return collect($esi_results)->each(fn($esi_result) => cache([sprintf('name:%s', $esi_result->id) => $esi_result], now()->addDay()))
            ->merge($this->result);
    }

}
