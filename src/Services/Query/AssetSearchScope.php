<?php

namespace Seatplus\Web\Services\Query;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Str;
use Seatplus\Eveapi\Models\Assets\Asset;

class AssetSearchScope
{
    private ?string $search_query = null;

    public function __construct(array $request)
    {
        $this->search_query = $request['search'] ?? null;
    }

    public function __invoke(Builder|Asset $arg): bool
    {
        if(! $this->search_query) {
            return true;
        }

        if($arg instanceof Builder) {
            return $this->handleBuilder($arg);
        }

        return $this->handleAsset($arg);
    }

    private function handleBuilder(Builder $query): bool
    {
        $query->when($this->search_query, function ($query) {
            collect(str_getcsv($this->search_query, ' ', '"'))->filter()
                ->each(function ($term) use ($query) {
                    $term = $term.'%';

                    $query->where('name_normalized', 'like', $term)
                        ->orWhere('type_name_normalized', 'like', $term)
                        ->orWhere('group_name_normalized', 'like', $term)
                        ->orWhere('category_name_normalized', 'like', $term);
                });
        });

        return true;
    }

    /**
     * @param Asset|null $item
     * @return bool
     */
    public function handleAsset(?Asset $item): bool
    {
        $terms = collect(str_getcsv($this->search_query, ' ', '"'))
            ->filter()
            ->map(fn ($term) => Str::lower($term))
            ->toArray();

        return collect([
            $item->name_normalized,
            $item->type_name_normalized,
            $item->group_name_normalized,
            $item->category_name_normalized,
        ])->filter()
            ->map(fn ($name) => Str::lower($name))
            ->filter(fn ($name) => Str::startsWith($name, $terms))
            ->isNotEmpty();

    }
}
