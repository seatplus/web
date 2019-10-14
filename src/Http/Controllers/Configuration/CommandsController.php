<?php

namespace Seatplus\Web\Http\Controllers\Configuration;

use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Cache;
use Seatplus\Web\Http\Controllers\Controller;

class CommandsController extends Controller
{
    public function clear()
    {

        Cache::flush();

        Artisan::call('cache:clear');

        return response('Success');
    }
}
