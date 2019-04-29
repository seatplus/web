<?php


namespace Seatplus\Web\Tests;


class GlobalSettingsTest extends TestCase
{
    /** @test */
    public function setGlobalSetting()
    {
        setting(['test','settingTest'], true);

        $this->assertDatabaseHas('global_settings', [
            'name' => 'test',
            'value' => 'settingTest'
        ]);

    }

    /** @test */
    public function getGlobalSetting()
    {

        $testing_value = bin2hex(random_bytes(10));

        // 1. try to get a non set setting, returning null
        $value = setting('test', true);

        $this->assertNull($value);

        // 2. set setting and expect the setting to return the previously set value.

        $value = setting(['test', $testing_value], true);

        $this->assertNotNull($value);

        $this->assertEquals($value,$testing_value);

        $this->assertDatabaseHas('global_settings', [
            'name' => 'test',
            'value' => $testing_value
        ]);



    }

}