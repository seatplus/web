<?php

declare(strict_types=1);

namespace Seatplus\Web\Services;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Seatplus\Eveapi\Models\Alliance\AllianceInfo;
use Seatplus\Eveapi\Models\Character\CharacterAffiliation;
use Seatplus\Eveapi\Models\Character\CharacterInfo;
use Seatplus\Eveapi\Models\Character\CorporationHistory;
use Seatplus\Eveapi\Models\Contacts\Contact;
use Seatplus\Eveapi\Models\Corporation\CorporationInfo;
use Seatplus\Eveapi\Models\Mail\Mail;
use Seatplus\Eveapi\Models\Skills\Skill;
use Seatplus\Eveapi\Models\Skills\SkillQueue;
use Seatplus\Eveapi\Models\Universe\Location;
use Seatplus\Eveapi\Models\Wallet\Balance;
use Seatplus\Eveapi\Models\Wallet\WalletJournal;
use Seatplus\Eveapi\Models\Wallet\WalletTransaction;
use Seatplus\Web\Http\Actions\Character\Asset\GetCharacterAssetLocationAction;
use Seatplus\Web\Http\Resources\ContactResource;
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
            $this->skillProps($characterIds),
            $this->contactProps($characterIds),
            $this->mailProps($characterIds),
            $this->corporationHistoryProps($characterIds),
        );
    }

    /**
     * Corporation history per character (infinite scroll) — mirrors Character\CorporationHistoryController
     * so the shared CorporationHistoryComponent renders the recruit's/employee's history in-place instead
     * of fetching it through the legacy axios/Ziggy InfiniteLoadingHelper.
     *
     * @param  array<int, int>  $characterIds
     * @return array<string, mixed>
     */
    private function corporationHistoryProps(array $characterIds): array
    {
        $props = [];

        foreach ($characterIds as $characterId) {
            $props["corporation_history_{$characterId}"] = Inertia::scroll(
                fn () => CorporationHistory::query()
                    ->where('character_id', $characterId)
                    ->orderByDesc('record_id')
                    ->paginate(pageName: "corporation_history_{$characterId}"),
            );
        }

        return $props;
    }

    /**
     * Mail headers across the inspected characters as one infinite-scroll prop — mirrors
     * Character\MailsController so the shared MobileMailList renders (it throws when the prop is absent).
     *
     * @param  array<int, int>  $characterIds
     * @return array<string, mixed>
     */
    private function mailProps(array $characterIds): array
    {
        return [
            'mailHeaders' => Inertia::scroll(fn () => Mail::query()
                ->select('id', 'from', 'subject', 'timestamp')
                ->whereHas('recipients', fn (Builder $query) => $query->whereHasMorph(
                    'receivable',
                    CharacterInfo::class,
                    fn (Builder $query) => $query->whereIn('character_id', $characterIds),
                ))
                ->orderByDesc('timestamp')
                ->paginate()),
        ];
    }

    /**
     * Skills keyed by character_id (deferred) — mirrors Character\SkillsController so the shared
     * SkillsComponent renders the recruit's/employee's skills, not just the character pages'.
     *
     * @param  array<int, int>  $characterIds
     * @return array<string, mixed>
     */
    private function skillProps(array $characterIds): array
    {
        return [
            'skills' => Inertia::defer(fn () => collect($characterIds)->mapWithKeys(fn (int $characterId) => [
                $characterId => Skill::query()
                    ->with('type.group')
                    ->where('character_id', $characterId)
                    ->get(),
            ])),
            'skillQueue' => Inertia::defer(fn () => collect($characterIds)->mapWithKeys(fn (int $characterId) => [
                $characterId => SkillQueue::query()
                    ->with('type.group')
                    ->where('character_id', $characterId)
                    ->where(fn (Builder $query) => $query->where('finish_date', '>=', now())->orWhereNull('finish_date'))
                    ->orderBy('queue_position')
                    ->get(),
            ])),
        ];
    }

    /**
     * Contacts keyed by character_id (deferred) — mirrors Character\ContactsController, including the
     * per-character corp/alliance standings that ContactResource reads from the session.
     *
     * @param  array<int, int>  $characterIds
     * @return array<string, mixed>
     */
    private function contactProps(array $characterIds): array
    {
        return [
            'contacts' => Inertia::defer(fn () => CharacterInfo::query()
                ->whereIn('character_id', $characterIds)
                ->with('characterAffiliation')
                ->get()
                ->each->append('corporation_id')
                ->mapWithKeys(fn (CharacterInfo $character) => [
                    $character->character_id => $this->resolveContacts($character),
                ])),
        ];
    }

    /**
     * @return array<int, mixed>
     */
    private function resolveContacts(CharacterInfo $character): array
    {
        $affiliation = CharacterAffiliation::query()
            ->firstWhere('corporation_id', $character->corporation_id);

        $contactableIds = array_filter([
            $affiliation?->corporation_id,
            $affiliation?->alliance_id,
        ]);

        $corpAllianceStanding = Contact::query()
            ->whereIn('contactable_id', $contactableIds)
            ->get();

        request()->session()->now('contacts', [
            'corporation_contacts' => $corpAllianceStanding->filter(fn (Contact $contact) => $contact->contactable_type === CorporationInfo::class),
            'alliance_contacts' => $corpAllianceStanding->filter(fn (Contact $contact) => $contact->contactable_type === AllianceInfo::class),
        ]);

        $contacts = Contact::with(['labels', 'characterAffiliation', 'corporationAffiliation', 'allianceAffiliation', 'factionAffiliation'])
            ->where('contactable_id', $character->character_id)
            ->get();

        return ContactResource::collection($contacts)->resolve(request());
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
