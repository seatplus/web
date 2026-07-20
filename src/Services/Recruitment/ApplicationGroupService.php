<?php

declare(strict_types=1);

namespace Seatplus\Web\Services\Recruitment;

use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Seatplus\Eveapi\Models\Application;
use Seatplus\Web\Models\Recruitment\ApplicationGroupMember;

/**
 * A multi-character application is stored as one eveapi Application per covered character (so auth's
 * per-character compliance stays correct) tied together by a shared group_id. This service resolves
 * and creates those groups; an application with no group row is treated as a group of itself, so
 * legacy single-character and whole-account applications keep working unchanged.
 */
class ApplicationGroupService
{
    /**
     * The applications reviewed together with the given one (its group members). Falls back to a
     * singleton [self] when the application has no group.
     *
     * @return Collection<int, Application>
     */
    public function groupFor(Application $application): Collection
    {
        $groupId = $this->groupIdFor($application);

        if ($groupId === null) {
            return collect([$application]);
        }

        $applicationIds = ApplicationGroupMember::query()
            ->where('group_id', $groupId)
            ->pluck('application_id');

        return Application::query()
            ->whereIn('id', $applicationIds)
            ->get();
    }

    /**
     * Link the given applications under a fresh group and return its id.
     *
     * @param  array<int, string>  $applicationIds
     */
    public function create(array $applicationIds): string
    {
        $groupId = (string) Str::uuid();

        foreach ($applicationIds as $applicationId) {
            ApplicationGroupMember::query()->create([
                'group_id' => $groupId,
                'application_id' => $applicationId,
            ]);
        }

        return $groupId;
    }

    private function groupIdFor(Application $application): ?string
    {
        /** @var ApplicationGroupMember|null $member */
        $member = ApplicationGroupMember::query()
            ->where('application_id', (string) $application->id)
            ->first();

        return $member ? (string) $member->group_id : null;
    }
}
