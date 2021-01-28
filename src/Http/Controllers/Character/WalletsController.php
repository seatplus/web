<?php

/*
 * MIT License
 *
 * Copyright (c) 2019, 2020 Felix Huber
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

namespace Seatplus\Web\Http\Controllers\Character;

use Seatplus\Eveapi\Jobs\Hydrate\Character\WalletHydrateBatch;
use Seatplus\Eveapi\Models\Wallet\WalletJournal;
use Seatplus\Eveapi\Models\Wallet\WalletTransaction;
use Seatplus\Web\Http\Controllers\Controller;

class WalletsController extends Controller
{
    public function index()
    {
        $ids = request()->has('character_ids')
            ? request()->get('character_ids')
            : auth()->user()->characters->pluck('character_id')->toArray();


        return inertia('Character/Wallet/Index', [
            'dispatch_transfer_object' => $this->buildDispatchTransferObject(),
            'character_ids' => collect($ids)->map( fn ($id) => (int) $id),
        ]);
    }

    public function journal(int $character_id)
    {
        return WalletJournal::where('wallet_journable_id', $character_id)
            ->with('wallet_journable')
            ->orderByDesc('date')
            ->paginate();
    }

    public function transaction(int $character_id)
    {
        return WalletTransaction::where('wallet_transactionable_id', $character_id)
            ->with('type', 'location')
            ->orderByDesc('date')
            ->paginate();
    }

    private function buildDispatchTransferObject(): object
    {
        return (object) [
            'manual_job' => array_search(WalletHydrateBatch::class, config('web.jobs')),
            'permission' => config('eveapi.permissions.' . WalletJournal::class),
            'required_scopes' => config('eveapi.scopes.character.wallet'),
            'required_corporation_role' => '',
        ];
    }
}
