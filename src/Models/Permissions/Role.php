<?php

namespace Seatplus\Web\Models\Permissions;

use Illuminate\Support\Arr;
use Seatplus\Web\Models\User;
use Spatie\Permission\Models\Role as SpatieRole;

class Role extends SpatieRole
{
    private $user;

    public function affiliations()
    {

        return $this->hasOne(Affiliation::class, 'role_id');
    }

    public function isAllowed(User $user)
    {
        $this->setUser($user);

        if($this->isInForbiddenAffiliation())
            return false;

        if($this->isInAllowedAffiliation())
            return true;

        if(! $this->isInInverseAffiliation())
            return true;

        return false;
    }

    /**
     * @param \Seatplus\Web\Models\User $user
     */
    private function setUser(User $user): void
    {

        $this->user = $user;
    }

    private function isInForbiddenAffiliation()
    {

        return $this->isAffiliated($this->affiliations->forbidden);
    }

    private function isInAllowedAffiliation()
    {

        return $this->isAffiliated($this->affiliations->allowed);
    }

    private function isInInverseAffiliation()
    {

        return $this->isAffiliated($this->affiliations->inverse);
    }

    /**
     * @param array|null $affiliations
     *
     * @return bool
     */
    private function isAffiliated(?Array $affiliations)
    {
        $resut = empty($affiliations) ?
            null :
            array_intersect(
                array_merge(
                    $this->user->characters->pluck('character_id')->toArray() ,
                    $this->user->characters->map(function ($char) {return $char->character;})
                        ->pluck('corporation_id')->toArray()
                ),
                Arr::flatten($affiliations)
            );

        return ! empty($resut);
    }
}
