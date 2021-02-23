<?php


namespace Seatplus\Web\Models\Recruitment;


use Seatplus\Eveapi\Models\Recruitment\Enlistments;
use Seatplus\Eveapi\Models\Universe\Region;
use Seatplus\Eveapi\Models\Universe\System;

class Enlistment extends Enlistments
{

    public function systems()
    {
        return $this->morphedByMany(System::class, 'watchlistable', null, 'corporation_id');
    }

    public function regions()
    {
        return $this->morphedByMany(Region::class, 'watchlistable', null, 'corporation_id');
    }
}
