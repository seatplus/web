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

namespace Seatplus\Web\Services\ACL;

use Illuminate\Support\Arr;
use Seatplus\Auth\Models\Permissions\Affiliation;
use Seatplus\Auth\Models\Permissions\Role;
use Seatplus\Eveapi\Models\Alliance\AllianceInfo;
use Seatplus\Eveapi\Models\Character\CharacterInfo;
use Seatplus\Eveapi\Models\Corporation\CorporationInfo;

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
                $this->addToTarget($type, $validated_data[$type]);
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
                    'affiliatable_id' => $affiliation['id'],
                    'affiliatable_type' => $this->getAffiliatableType($affiliation),
                    'type' => $type,
                ])
            );
    }

    private function removeUnassignedAffiliations()
    {
        $this->current_affiliations->reject(function ($current_affiliation) {
            return $this->target_affiliations->contains($current_affiliation);
        })->each(function ($affiliation) {
            Affiliation::where([
                'role_id' => $affiliation->role_id,
                'affiliatable_id' => $affiliation->affiliatable_id,
                'type' => $affiliation->type,
            ])->delete();
        });
    }

    private function getAffiliatableType($affiliation)
    {
        return Arr::has($affiliation, 'character_id')
            ? CharacterInfo::class
            : (Arr::has($affiliation, 'corporation_id') ? CorporationInfo::class : AllianceInfo::class);
    }
}
