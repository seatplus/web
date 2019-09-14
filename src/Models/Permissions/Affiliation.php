<?php


namespace Seatplus\Web\Models\Permissions;


use Illuminate\Database\Eloquent\Model;
use Seatplus\Web\Models\User;

class Affiliation extends Model
{
    /**
     * @var bool
     */
    public $incrementing = false;

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'allowed' => 'array',
        'inverse' => 'array',
        'forbidden' => 'array'
    ];

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'allowed', 'inverse', 'forbidden',
    ];

}
