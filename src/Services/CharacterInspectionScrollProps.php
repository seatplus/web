<?php

declare(strict_types=1);

namespace Seatplus\Web\Services;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Seatplus\Eveapi\Models\Character\CharacterInfo;
use Seatplus\Eveapi\Models\Universe\Location;
use Seatplus\Eveapi\Models\Wallet\Balance;
use Seatplus\Eveapi\Models\Wallet\WalletJournal;
use Seatplus\Eveapi\Models\Wallet\WalletTransaction;
use Seatplus\Web\Http\Actions\Character\Asset\GetCharacterAssetLocationAction;
use Seatplus\Web\Http\Resources\LocationRessource;

/**
 * Builds the Inertia scroll/defer props the shared Asset and Wallet components read, for an arbitrary
 * set of character ids. Reused wherever we inspect a subject's characters — reviewing an applicant and
 * (later) observing an employee — so the recruitment/observation pages can feed the same components the
 * character pages use.
 */
class CharacterInspectionScrollProps
{
    public function __construct(
        private readonly GetCharacterAssetLocationAction $assetAction,
    ) {}

    /**
     * @param  array<int, int>  $characterIds
     * @return array<string, mixed>
     */
    public function build(array $characterIds, Request $request): array
    {
        return array_merge(
            $this->assetProps($characterIds, $request),
            $this->walletProps($characterIds),
        );
    }

    /**
     * @param  array<int, int>  $characterIds
     * @return array<string, mixed>
     */
    private function assetProps(array $characterIds, Request $request): array
    {
        $validated = $request->only(['search', 'systems', 'regions', 'types', 'groups', 'categories', 'only_unknown_locations']);
        $validated['character_ids'] = $characterIds;

        return [
            'assets' => Inertia::scroll(fn () => $this->assetAction->execute($validated)
                ->through(fn (Location $location): array => json_decode(
                    (string) json_encode((new LocationRessource($location))->resolve()),
                    true,
                ))),
        ];
    }

    /**
     * @param  array<int, int>  $characterIds
     * @return array<string, mixed>
     */
    private function walletProps(array $characterIds): array
    {
        $props = [];

        foreach ($characterIds as $characterId) {
            $props["journal_{$characterId}"] = Inertia::scroll(
                fn () => $this->journalQuery($characterId)->paginate(pageName: "journal_{$characterId}"),
            );
            $props["transaction_{$characterId}"] = Inertia::scroll(
                fn () => $this->transactionQuery($characterId)->paginate(pageName: "transaction_{$characterId}"),
            );
            $props["balance_{$characterId}"] = Inertia::defer(fn () => $this->balanceData($characterId));
        }

        return $props;
    }

    private function journalQuery(int $characterId): Builder
    {
        return WalletJournal::query()
            ->where('wallet_journable_id', $characterId)
            ->with('walletJournable')
            ->orderByDesc('date');
    }

    private function transactionQuery(int $characterId): Builder
    {
        return WalletTransaction::query()
            ->where('wallet_transactionable_id', $characterId)
            ->with('type', 'location')
            ->orderByDesc('date');
    }

    private function balanceData(int $characterId): Collection
    {
        $balancePart = Balance::query()
            ->whereHasMorph(
                'balanceable',
                CharacterInfo::class,
                fn (Builder $query) => $query->where('character_id', $characterId),
            )
            ->limit(1)
            ->select(DB::raw('DATE(updated_at) as x'), 'balance as y');

        $journalEntries = WalletJournal::query()
            ->select(DB::raw('DATE(date) as x'), DB::raw('AVG(balance) as y'))
            ->orderByDesc('x')
            ->where('wallet_journable_id', $characterId)
            ->groupBy('x')
            ->limit(30);

        return $balancePart->union($journalEntries)->limit(30)->get();
    }
}
