<?php


namespace Seatplus\Web\Models;


use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Seatplus\Auth\Models\User;
use Seatplus\Eveapi\Models\Universe\Location;
use Seatplus\Eveapi\Models\Universe\System;
use Seatplus\Web\database\factories\ManualLocationFactory;

class ManualLocation extends Model
{

    use HasFactory;

    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $guarded = [];

    protected static function newFactory()
    {
        return ManualLocationFactory::new();
    }

    public function location()
    {
        return $this->belongsTo(Location::class, 'location_id', 'location_id');
    }

    public function system()
    {
        return $this->belongsTo(System::class, 'solar_system_id', 'system_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }
}
