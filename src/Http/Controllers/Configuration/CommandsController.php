<?php

namespace Seatplus\Web\Http\Controllers\Configuration;

use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Redis as RedisManager;
use Seatplus\Web\Http\Controllers\Controller;

class CommandsController extends Controller
{
    public function clear()
    {

        RedisManager::flushall();

        Artisan::call('cache:clear');

        return response('Success');
    }
}
