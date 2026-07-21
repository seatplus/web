<?php

use Inertia\DeferProp;
use Seatplus\Eveapi\Models\Character\CharacterInfo;
use Seatplus\Eveapi\Models\Character\CorporationHistory;
use Seatplus\Eveapi\Models\Contracts\Contract;
use Seatplus\Eveapi\Models\Contracts\ContractItem;
use Seatplus\Eveapi\Models\Skills\Skill;
use Seatplus\Eveapi\Models\Universe\Category;
use Seatplus\Eveapi\Models\Universe\Group;
use Seatplus\Eveapi\Models\Universe\Location;
use Seatplus\Eveapi\Models\Universe\Station;
use Seatplus\Eveapi\Models\Universe\Type;
use Seatplus\Web\Services\CharacterInspectionScrollProps;

// Regression: reviewing an applicant (and observing an employee) reuse CharacterInspectionScrollProps
// for the shared tabs. Skills and Contacts render from page props, so the service must build them —
// otherwise those tabs are blank even though the data is in the DB (visible on the character pages).
it('builds resolvable skills and a wired contacts prop per inspected character', function () {
    $character = CharacterInfo::factory()->create();
    $skill = Skill::factory()->create(['character_id' => $character->character_id]);

    $props = app(CharacterInspectionScrollProps::class)->build([(int) $character->character_id], request());

    expect($props)->toHaveKeys(['skills', 'skillQueue', 'contacts', 'mailHeaders'])
        ->and($props['contacts'])->toBeInstanceOf(DeferProp::class);

    // Resolving the deferred skills prop yields the character's own skills, keyed by character_id.
    $skills = ($props['skills'])();

    expect($skills->get($character->character_id))->toHaveCount(1)
        ->and($skills->get($character->character_id)->first()->skill_id)->toBe($skill->skill_id);
});

// The Corporation History tab renders from a per-character scroll prop (migrated off the legacy
// axios InfiniteLoadingHelper), so the service must build a corporation_history_<id> prop that
// paginates the character's history.
it('builds a paginated corporation-history scroll prop per inspected character', function () {
    $character = CharacterInfo::factory()->create();
    $history = CorporationHistory::factory()->create(['character_id' => $character->character_id]);

    $props = app(CharacterInspectionScrollProps::class)->build([(int) $character->character_id], request());

    $key = "corporation_history_{$character->character_id}";

    expect($props)->toHaveKey($key);

    $paginator = ($props[$key])();

    expect($paginator->total())->toBe(1)
        ->and($paginator->first()->record_id)->toBe($history->record_id);
});

// The Contracts tab renders through native <InfiniteScroll> over per-character scroll props
// (migrated off the axios InfiniteLoadingHelper): an all-contracts prop always, plus a
// watchlist-filtered prop when the posting/observed corp defines a watchlist.
it('builds an all-contracts scroll prop and a watchlist-filtered one per inspected character', function () {
    $character = CharacterInfo::factory()->create();

    $location = Location::factory()->for(Station::factory(), 'locatable')->create();
    $contract = Contract::factory()->create([
        'start_location_id' => $location->location_id,
        'end_location_id' => $location->location_id,
        'assignee_id' => $character->character_id,
    ]);
    $character->contracts()->attach($contract);

    $category = Category::factory()->create();
    $group = Group::factory()->create(['category_id' => $category->category_id]);
    $type = Type::factory()->create(['group_id' => $group->group_id]);
    ContractItem::factory()->create(['contract_id' => $contract->contract_id, 'type_id' => $type->type_id]);

    $watchlist = ['categories' => [$category->category_id]];

    $props = app(CharacterInspectionScrollProps::class)->build([(int) $character->character_id], request(), $watchlist);

    $allKey = "contracts_{$character->character_id}";
    $watchlistKey = "watchlist_contracts_{$character->character_id}";

    expect($props)->toHaveKeys([$allKey, $watchlistKey]);

    // All-contracts prop returns the contract; the watchlist prop, filtered to its category, still does.
    expect(($props[$allKey])()->total())->toBe(1)
        ->and(($props[$watchlistKey])()->total())->toBe(1)
        ->and(($props[$watchlistKey])()->first()['contract_id'])->toBe($contract->contract_id);
});

it('omits the watchlist-contracts prop when the posting has no watchlist', function () {
    $character = CharacterInfo::factory()->create();

    $props = app(CharacterInspectionScrollProps::class)->build([(int) $character->character_id], request());

    expect($props)->toHaveKey("contracts_{$character->character_id}")
        ->and($props)->not->toHaveKey("watchlist_contracts_{$character->character_id}");
});
