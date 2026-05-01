<?php

declare(strict_types=1);

namespace Seatplus\Web\Http\Controllers\AccessControl;

use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Collection;
use Seatplus\Eveapi\Models\Alliance\AllianceInfo;
use Seatplus\Eveapi\Models\Character\CharacterInfo;
use Seatplus\Eveapi\Models\Corporation\CorporationInfo;
use Seatplus\Web\Services\SearchService;

class SearchAffiliatableController
{
    public function __invoke(): LengthAwarePaginator
    {
        $query = request()->get('query');

        $token = SearchService::getTokenFromCurrentUser();

        $result = $query
            ? (new SearchService)->execute($token, ['character', 'corporation', 'alliance'], $query)
            : $this->getFirstSelection();

        return $this->paginate(
            collect($result)
                ->flatMap(fn (array $result, string $category) => collect($result)
                    ->map(fn (int $res) => [
                        'id' => $res,
                        'category' => $category,
                    ]))
        );
    }

    private function paginate(array|Collection $items, int $perPage = 15, ?int $page = null, array $options = []): LengthAwarePaginator
    {
        $page = $page ?: (Paginator::resolveCurrentPage() ?: 1);

        $items = $items instanceof Collection ? $items : Collection::make($items);

        return new LengthAwarePaginator($items->forPage($page, $perPage), $items->count(), $perPage, $page, $options);
    }

    private function getFirstSelection(): array
    {
        $alliance_ids = AllianceInfo::query()->take(15)->inRandomOrder()->pluck('alliance_id');
        $corporation_ids = CorporationInfo::query()->take(15)->inRandomOrder()->pluck('corporation_id');
        $character_ids = CharacterInfo::query()->take(15)->inRandomOrder()->pluck('character_id');

        $ids = collect([...$alliance_ids, ...$corporation_ids, ...$character_ids])
            ->shuffle()
            ->take(15);

        return [
            'alliance' => $alliance_ids->intersect($ids)->toArray(),
            'corporation' => $corporation_ids->intersect($ids)->toArray(),
            'character' => $character_ids->intersect($ids)->toArray(),
        ];
    }
}
