<?php

namespace Seatplus\Web\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Seatplus\Eveapi\Models\Character\CharacterInfo;

class User extends Authenticatable
{

    /**
     * @var bool
     */
    public $incrementing = false;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'character_owner_hash',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function characters()
    {

        return $this->hasMany(CharacterUser::class, 'character_id', 'id');
    }
}
