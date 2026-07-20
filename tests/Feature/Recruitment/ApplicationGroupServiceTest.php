<?php

use Illuminate\Support\Facades\Event;
use Seatplus\Auth\Models\User;
use Seatplus\Eveapi\Models\Application;
use Seatplus\Eveapi\Models\Corporation\CorporationInfo;
use Seatplus\Web\Models\Recruitment\ApplicationGroupMember;
use Seatplus\Web\Services\Recruitment\ApplicationGroupService;

function makeApplication(?int $corporationId = null): Application
{
    $applicant = Event::fakeFor(fn () => User::factory()->create());

    return Application::factory()->create([
        'corporation_id' => $corporationId ?? CorporationInfo::factory()->create()->corporation_id,
        'applicationable_type' => User::class,
        'applicationable_id' => $applicant->getKey(),
    ]);
}

it('resolves an ungrouped application to a singleton group', function () {
    $application = makeApplication();

    $group = (new ApplicationGroupService)->groupFor($application);

    expect($group)->toHaveCount(1)
        ->and((string) $group->first()->id)->toBe((string) $application->id);
});

it('creates a group and resolves all its members from any member', function () {
    $corp = CorporationInfo::factory()->create();

    $applications = collect(range(1, 3))->map(fn () => makeApplication($corp->corporation_id));
    $coveredIds = $applications->take(2)->pluck('id')->map(fn ($id) => (string) $id);

    $service = new ApplicationGroupService;
    $groupId = $service->create($coveredIds->all());

    expect(ApplicationGroupMember::query()->where('group_id', $groupId)->count())->toBe(2);

    // From either grouped member we resolve exactly the 2 covered applications — not the 3rd.
    $resolved = $service->groupFor($applications->first())
        ->pluck('id')->map(fn ($id) => (string) $id)->sort()->values();

    expect($resolved->all())->toEqual($coveredIds->sort()->values()->all());

    // The ungrouped 3rd application stays a singleton.
    expect($service->groupFor($applications->last()))->toHaveCount(1);
});
