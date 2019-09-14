<?php

namespace Seatplus\Web\Models;

use Illuminate\Database\Eloquent\Model;

class CharacterUser extends Model
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
        'character_id', 'user_id', 'character_owner_hash',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
