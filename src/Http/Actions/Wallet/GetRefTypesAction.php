<?php

/*
 * MIT License
 *
 * Copyright (c) 2019, 2020, 2021 Felix Huber
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in all
 * copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
 * SOFTWARE.
 */

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
                'hasEveImage' => false,
            ])
            ->values();
    }

    private function getRefTypes(): Collection
    {
        return Cache::remember(
            'ref_types',
            now()->addDay(),
            fn () => WalletJournal::query()
            ->select('ref_type')
            ->groupBy('ref_type')
            ->get()
        );
    }

    private function alphabetToNumber($string): float
    {
        $string = strtoupper((string) $string);
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
