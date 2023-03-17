<?php

namespace Seatplus\Web\Http\Controllers\Configuration;

use Inertia\Inertia;
use Seatplus\Eveapi\Models\BatchStatistic;
use Seatplus\Web\Http\Controllers\Controller;

class PerformanceController extends Controller
{
    public function index()
    {
        return inertia('Configuration/Performance/Overview', [
            'data' => Inertia::lazy(fn () => BatchStatistic::query()->orderBy('finished_at', 'desc')->paginate(50)),
        ]);
    }
}
