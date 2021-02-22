<?php

/*
 * MIT License
 *
 * Copyright (c) 2019, 2020, 2021 Felix Huber
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in all
 * copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
 * SOFTWARE.
 */

namespace Seatplus\Web\Http\Controllers\Shared;

use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Seatplus\Eveapi\Actions\Jobs\Universe\ResolveUniverseSystemsBySystemIdAction;
use Seatplus\Eveapi\Jobs\Universe\ResolveUniverseSystemBySystemIdJob;
use Seatplus\Web\Http\Controllers\Controller;
use Seatplus\Web\Models\ManualLocation;

class ManualLocationController extends Controller
{
    public function index()
    {
        return inertia('Configuration/ManualLocations/ManualLocation');
    }

    public function getSuggestions()
    {
        ManualLocation::query()->has('location')->delete();

        return ManualLocation::with('system', 'user.main_character', 'user.characters')
            ->orderByDesc('location_id')
            ->paginate();
    }

    public function acceptSuggestion(Request $request)
    {
        $validated = $request->validate([
            'location_id' => ['required'],
            'id' => ['required', Rule::in(ManualLocation::where('location_id', $request->get('location_id'))->pluck('id')->toArray())],
        ]);

        $suggestion = ManualLocation::find($validated['id']);
        $suggestion->selected = true;
        $suggestion->save();

        ManualLocation::where('location_id', $validated['location_id'])->where('id', '<>', $validated['id'])->delete();

        return redirect()->action([self::class, 'index']);
    }

    public function getLocation(int $location_id)
    {
        $this->getMissingSystem($location_id);

        $entries = ManualLocation::with('system')->where('location_id', $location_id)
            ->latest()
            ->get();

        if ($entries->isEmpty()) {
            return collect([
                'name' => sprintf('Unknown Structure (%s)', $location_id),
            ]);
        }

        $selected = $entries->firstWhere('selected');

        if ($selected) {
            return $selected;
        }

        return $entries->firstWhere('user_id', auth()->user()->getAuthIdentifier()) ?? $entries->first();
    }

    public function create(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string'],
            'location_id' => ['required', 'unique:universe_locations,location_id'],
            'solar_system_id' => ['required'],
        ]);

        // First create solar_system_entry
        $system = (new ResolveUniverseSystemsBySystemIdAction)->execute($validated['solar_system_id']);

        $location = ManualLocation::create([
            'location_id' => $validated['location_id'],
            'user_id' => auth()->user()->getAuthIdentifier(),
            'name' => $validated['name'],
            'solar_system_id' => $validated['solar_system_id'],
        ]);

        return redirect()->to(url()->previous());
    }

    private function getMissingSystem(int $location_id): void
    {
        $entries = ManualLocation::doesntHave('system')->where('location_id', $location_id)->get();

        if ($entries->isEmpty()) {
            return;
        }

        $job = new ResolveUniverseSystemBySystemIdJob;

        $entries->each(fn ($manual_location) => dispatch($job->setSystemId($manual_location->solar_system_id))->onQueue('low'));
    }
}