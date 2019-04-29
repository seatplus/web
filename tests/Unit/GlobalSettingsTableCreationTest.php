<?php


namespace Seatplus\Web\Tests;


use Seatplus\Web\Models\Settings\GlobalSettings;

class GlobalSettingsTableCreationTest extends TestCase
{
    /** @test */
    public function migartionWorked()
    {

        $global_settings = factory(GlobalSettings::class)->create();

        $this->assertDatabaseHas('global_settings', [
            'name' => $global_settings->name,
            'value' => $global_settings->value
        ]);

    }

}