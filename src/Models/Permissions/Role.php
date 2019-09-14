<?php

namespace Seatplus\Web\Models\Permissions;

use Illuminate\Support\Arr;
use Seatplus\Web\Models\User;
use Spatie\Permission\Models\Role as SpatieRole;

class Role extends SpatieRole
{
    public function affiliations()
    {

        return $this->hasOne(Affiliation::class, 'role_id');
    }

    public function hasAffiliation(User $user)
    {

        if(! empty($this->affiliations->forbidden) && in_array($user->id, Arr::flatten($this->affiliations->forbidden)))
            return false;

        if($this->isAllowed($user))
            return true;
        /*if(in_array($user->id, Arr::flatten($this->affiliations->allowed)))
            return true;*/

        if(! in_array($user->id, Arr::flatten($this->affiliations->inverse)))
            return true;

        return false;
    }

    private function isAllowed(User $user)
    {
        $resut = array_intersect(
            $user->characters->pluck('character_id')->toArray(),
            Arr::flatten($this->affiliations->allowed)
        );

        return ! empty($resut);
    }
}
