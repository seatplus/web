<?php


namespace Seatplus\Web\Services\ACL;


use Illuminate\Support\Arr;
use Seatplus\Auth\Models\Permissions\Affiliation;
use Seatplus\Auth\Models\Permissions\Role;

class SyncRoleAffiliations
{
    /**
     * @var \Seatplus\Auth\Models\Permissions\Role
     */
    private $role;

    private $current_affiliations;

    /**
     * @var \Illuminate\Support\Collection
     */
    private $target_affiliations;

    public function __construct(Role $role)
    {

        $this->role = $role;
        $this->current_affiliations = $role->affiliations;
        $this->target_affiliations = collect();
    }

    public function sync(array $validated_data)
    {
        foreach (['allowed', 'inverse', 'forbidden'] as $type) {
            if(Arr::has($validated_data, $type) && $validated_data[$type]){
                $this->addToTarget($type,$validated_data[$type]);
            }
        }

        $this->removeUnassignedAffiliations();

    }

    private function addToTarget(string $type, array $affiliations)
    {
        foreach ($affiliations as $affiliation)

            $this->target_affiliations->push(
                Affiliation::firstOrCreate([
                    'role_id' => $this->role->id,
                    'character_id' => $affiliation['character_id'] ?? null,
                    'corporation_id' => $affiliation['corporation_id'] ?? null,
                    'alliance_id' => $affiliation['alliance_id'] ?? null,
                    'type' => $type
                ])
            );
    }

    private function createAffiliations()
    {
        $this->target_affiliations = $this->target_affiliations->map(function ($affiliation) {
            return Affiliation::firstOrCreate([
                'role_id' => $this->role->id,
                'character_id' => $affiliation['character_id'],
                'corporation_id' => $affiliation['corporation_id'],
                'alliance_id' => $affiliation['alliance_id'],
                'type' => $affiliation['type']
            ]);
        });
    }

    private function removeUnassignedAffiliations()
    {
        $this->current_affiliations->reject(function ($current_affiliation) {
            return $this->target_affiliations->contains($current_affiliation);
        })->each(function ($affiliation) {
            Affiliation::where([
                'role_id' => $affiliation->role_id,
                'character_id' => $affiliation->character_id,
                'corporation_id' => $affiliation->corporation_id,
                'alliance_id' => $affiliation->alliance_id,
                'type' => $affiliation->type
            ])->delete();
        });
    }

}
