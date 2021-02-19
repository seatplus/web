<?php


namespace Seatplus\Web\Tests\Unit\Models;


use Illuminate\Support\Facades\Event;
use Seatplus\Web\Models\ManualLocation;
use Seatplus\Web\Tests\TestCase;

class ManualLocationTest extends TestCase
{
    /** @test */
    public function manual_location_has_location_relationship()
    {

        $manual_location = Event::fakeFor( fn() => ManualLocation::factory()->create());

        $this->assertNotNull($manual_location);
    }



}
