<?php


namespace Seatplus\Web\Http\Controllers\Corporation\Wallet;

use Seatplus\Eveapi\Jobs\Hydrate\Corporation\CorporationWalletHydrateBatch;
use Seatplus\Eveapi\Models\Corporation\CorporationDivision;
use Seatplus\Eveapi\Models\Wallet\WalletJournal;
use Seatplus\Eveapi\Models\Wallet\WalletTransaction;
use Seatplus\Web\Http\Controllers\Controller;

class CorporationWalletController extends Controller
{
    public function index() {

        return inertia('Corporation/Wallets/Wallet', [
            'dispatchTransferObject' => $this->buildDispatchTransferObject(),
            'corporationDivisions' => $this->getAffiliatedCorporateWalletDivisions(),
        ]);
    }

    public function journal(int $corporation_id, int $division_id)
    {
        return WalletJournal::where('wallet_journable_id', $corporation_id)
            ->where('division', $division_id)
            ->with('wallet_journable')
            ->orderByDesc('date')
            ->paginate();
    }

    public function balance(int $corporation_id, int $division_id)
    {
        $date_part = WalletJournal::query()
            ->whereBetween('date', [now(), now()->subDays(request()->get('days', 30))])
            ->orderByDesc('date')
            ->select(['date as x', 'balance as y']);

        return WalletJournal::query()
            ->limit(90)
            ->orderByDesc('date')
            ->union($date_part)
            ->where('wallet_journable_id', $corporation_id)
            ->where('division', $division_id)
            ->select(['date as x', 'balance as y'])
            ->paginate();
    }

    public function transaction(int $corporation_id, int $division_id)
    {
        return WalletTransaction::where('wallet_transactionable_id', $corporation_id)
            ->where('division', $division_id)
            ->with('type', 'location')
            ->orderByDesc('date')
            ->paginate();
    }

    private function buildDispatchTransferObject(): object
    {
        return (object) [
            'manual_job' => array_search(CorporationWalletHydrateBatch::class, config('web.jobs')),
            'permission' => $this->getPermission(),
            'required_scopes' => [...config('eveapi.scopes.corporation.wallet'), 'esi-characters.read_corporation_roles.v1'],
            'required_corporation_role' => 'Accountant|Junior_Accountant',
        ];
    }

    private function getPermission(): string
    {
        return config('eveapi.permissions.' . WalletJournal::class);
    }

    private function getAffiliatedCorporateWalletDivisions()
    {
        return CorporationDivision::whereIn('corporation_id', getAffiliatedIdsByPermission($this->getPermission()))
            ->where('division_type', 'wallet')
            ->get();
    }

}