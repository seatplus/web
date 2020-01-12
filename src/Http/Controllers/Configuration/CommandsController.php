<?php

namespace Seatplus\Web\Http\Controllers\Configuration;

use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Redis as RedisHelper;
use Seatplus\Web\Http\Controllers\Controller;

class CommandsController extends Controller
{
    public function clear()
    {

        Cache::flush();
        RedisHelper::flushall();

        Artisan::call('cache:clear');

        return response('Success');
    }
}
