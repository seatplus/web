<?php


namespace Seatplus\Web\Services;


use Illuminate\Support\Str;
use Seatplus\Eveapi\Models\Character\CharacterInfo;

class HasCharacterNecessaryRole
{
    public static function check(CharacterInfo $character, string | array $roles): bool
    {
        if($character->roles->hasRole('roles', 'Director'))
            return true;

        $character_roles = is_string($roles) ? explode('|', $roles) : $roles;

        foreach ($character_roles as $role) {
            if($character->roles->hasRole('roles', Str::ucfirst($role))) {
                return true;
            }
        }

        return false;
    }

}