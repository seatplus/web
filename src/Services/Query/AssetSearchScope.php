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
        if (! $this->search_query) {
            return true;
        }

        if ($arg instanceof Builder) {
            return $this->handleBuilder($arg);
        }

        return $this->handleAsset($arg);
    }

    private function handleBuilder(Builder $query): bool
    {
        $query->when($this->search_query, function (Builder $query) {
            collect(str_getcsv($this->search_query, ' ', '"'))->filter()
                ->each(function (string $term) use ($query) {
                    $term = $term.'%';

                    // ilike: the *_normalized columns preserve the source casing (regexp_replace
                    // strips only non-alphanumerics, e.g. "SodiaTradealinassIbis"), so a
                    // case-sensitive `like` would never match a lower-cased search term. This
                    // mirrors handleAsset(), which lower-cases both sides.
                    $query->where('name_normalized', 'ilike', $term)
                        ->orWhere('type_name_normalized', 'ilike', $term)
                        ->orWhere('group_name_normalized', 'ilike', $term)
                        ->orWhere('category_name_normalized', 'ilike', $term);
                });
        });

        return true;
    }

    public function handleAsset(?Asset $item): bool
    {
        $terms = collect(str_getcsv($this->search_query, ' ', '"'))
            ->filter()
            ->map(fn (string $term) => Str::lower($term))
            ->toArray();

        return collect([
            $item->name_normalized,
            $item->type_name_normalized,
            $item->group_name_normalized,
            $item->category_name_normalized,
        ])->filter()
            ->map(fn (string $name) => Str::lower($name))
            ->filter(fn (string $name) => Str::startsWith($name, $terms))
            ->isNotEmpty();

    }
}
