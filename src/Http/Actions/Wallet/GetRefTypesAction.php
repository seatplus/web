<?php

namespace Seatplus\Web\Http\Actions\Wallet;


use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;
use Seatplus\Eveapi\Models\Wallet\WalletJournal;

class GetRefTypesAction
{
    public function execute(string $term): Collection
    {

        return $this->getRefTypes()
            ->filter(fn ($type) => Str::contains($type->ref_type, $term))
            ->map(fn ($group, $index) => [
                'id' => $this->alphabetToNumber($group->ref_type),
                'name' => $group->ref_type,
                'hasEveImage' => false
            ])
            ->values();

    }

    private function getRefTypes() : Collection
    {
        return Cache::remember('ref_types', now()->addDay(), fn () => WalletJournal::query()
            ->select('ref_type')
            ->groupBy('ref_type')
            ->get()
        );
    }

    private function alphabetToNumber($string): float {
        $string = strtoupper($string);
        $length = strlen($string);
        $number = 0;
        $level = 1;
        while ($length >= $level) {
            $char = $string[$length - $level];
            $c = ord($char) - 64;
            $number += $c * (26 ** ($level - 1));
            $level++;
        }

        return $number;
    }

}