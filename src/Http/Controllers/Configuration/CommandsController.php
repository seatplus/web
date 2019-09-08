<?php


namespace Seatplus\Web\Http\Controllers\Configuration;


use Illuminate\Support\Facades\Redis;
use Seatplus\Web\Http\Controllers\Controller;

class CommandsController extends Controller
{
    public function clear()
    {

        $this->clear_redis_cache();

        return back()->with('success', 'year post');
    }

    /**
     * Flush all keys in Redis.
     */
    public function clear_redis_cache()
    {
        $redis_host = config('database.redis.default.host');
        $redis_port = config('database.redis.default.port');

        clock()->info('Clearing the Redis Cache at: ' . $redis_host . ':' . $redis_port);

        try {

            Redis::flushall();

        } catch (Exception $e) {

            clock()->error('Failed to clear the Redis Cache. Error: ' . $e->getMessage());
        }
    }


}
