<?php

if (! function_exists('setting')) {

    /**
     * Work with settings.
     *
     * Providing a string argument will retrieve a setting.
     * Providing an array argument will set a setting.
     *
     * @param      $name
     * @param bool $global
     *
     * @return mixed
     * @throws \Seatplus\Web\Exceptions\SettingException
     */
    function setting($name, bool $global = false)
    {

        // If we received an array, it means we want to set.
        if (is_array($name)) {

            // Check that we have at least 2 keys.
            if (count($name) < 2)
                throw new \Seatplus\Web\Exceptions\SettingException(
                    'Must provide a name and value when setting a setting.');

            // If we have a third element in the array, set it.
            //$for_id = $name[2] ?? null;

            if ($global)
                return \Seatplus\Web\Models\Settings\GlobalSettings::updateOrCreate(
                    ['name' => $name[0]],
                    ['value' => $name[1]]
                )
                    ->value;

            return \Seatplus\Services\Settings\Profile::set($name[0], $name[1], $for_id);
        }

        // If we just got a string, it means we want to get.
        if ($global)
            return optional(\Seatplus\Web\Models\Settings\GlobalSettings::where('name', $name[0])
                ->first())
                ->value;

        //return \Seat\Services\Settings\Profile::get($name);
        return ''; //TODO: return user-settings

    }
}

if (! function_exists('number_roman')) {
    /**
     * Converts an integer to a roman numberal representation.
     *
     * @param int $number
     *
     * @return string
     */
    function number_roman($number)
    {

        $map = [
            'M'  => 1000, 'CM' => 900, 'D' => 500, 'CD' => 400, 'C' => 100, 'XC' => 90, 'L' => 50,
            'XL' => 40, 'X' => 10, 'IX' => 9, 'V' => 5, 'IV' => 4, 'I' => 1,
        ];

        $returnValue = '';

        while ($number > 0) {

            foreach ($map as $roman => $int) {

                if ($number >= $int) {
                    $number -= $int;
                    $returnValue .= $roman;
                    break;
                }
            }
        }

        return $returnValue;
    }
}

if (! function_exists('carbon')) {

    /**
     * A helper to get a fresh instance of Carbon.
     *
     * @param null $data
     *
     * @return \Carbon\Carbon
     */
    function carbon($data = null)
    {

        if (! is_null($data))
            return new \Carbon\Carbon($data);

        return new \Carbon\Carbon;
    }
}
