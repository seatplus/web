<?php

/*
 * MIT License
 *
 * Copyright (c) 2019, 2020 Felix Huber
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

namespace Seatplus\Web\Http\Controllers\Configuration;

use Illuminate\Support\Str;
use Inertia\Inertia;
use Seatplus\Auth\Models\User;
use Seatplus\Eveapi\Actions\Eseye\RetrieveEsiDataAction;
use Seatplus\Eveapi\Containers\EsiRequestContainer;
use Seatplus\Eveapi\Models\Corporation\CorporationInfo;
use Seatplus\Eveapi\Models\SsoScopes;
use Seatplus\Web\Http\Controllers\Controller;
use Seatplus\Web\Http\Controllers\Request\UpdateOrCreateSsoScopeSetting;
use Seatplus\Web\Http\Resources\UserRessource;
use Seatplus\Web\Services\SsoSettings\UpdateOrCreateSsoSettings;

class SeatPlusController extends Controller
{
    public function settings()
    {
        $validatedData = request()->validate([
            'search_param' => 'string',
        ]);

        $query = User::with('characters');

        if(request()->has('search_param'))
            $query = $query->search($validatedData['search_param']);

        $users = UserRessource::collection(
            $query->paginate()
        );

        return Inertia::render('Configuration/UserList', [
            'users' => $users,
        ]);
    }

    public function stopImpersonate()
    {
        // If there is no user set in the session, abort!
        if (! session()->has('impersonation_origin'))
            abort(404);

        // Login
        auth()->login(session('impersonation_origin'));

        // Clear the session value
        session()->forget('impersonation_origin');

        return back()->with('success', 'Stopped Impersonate');
    }

    public function impersonate($user_id)
    {

        // Store the original user in the session
        session(['impersonation_origin' => auth()->user()]);

        $impersonated_user = User::find($user_id);

        auth()->login($impersonated_user);

        return redirect()->route('home')->with('success', 'Impersonating ' . $impersonated_user->main_character->name);
    }

    public function scopeSettings(?int $entity_id = null)
    {
        $available_scopes = config('eveapi.scopes');

        $sso_scopes_entries = function () {

             return SsoScopes::with('morphable')->get()->map(function ($scope) {

                $selectedEntity = [
                    'id' => $scope->morphable_id,
                    'name' => optional($scope->morphable)->name,
                ];

                $selectedEntity = array_merge($selectedEntity, $scope->morphable_type === CorporationInfo::class
                    ? ['corporation_id' => $scope->morphable_id]
                    : ['alliance_id' => $scope->morphable_id]
                );

                return [
                    'selectedEntity' => $selectedEntity,
                    'selectedScopes' => $scope->selected_scopes,
                ];
            });
        };

        return Inertia::render('Configuration/ScopeSettings', [
            'available_scopes' => $available_scopes,
            'entries' => $sso_scopes_entries,
            'active' => $entity_id,
        ]);
    }

    public function searchAllianceCorporations($searchParam)
    {
        $action = new RetrieveEsiDataAction;

        if(Str::length($searchParam) < 3)
            return collect()->toJson();

        $search_request = new EsiRequestContainer([
            'method' => 'get',
            'version' => 'v2',
            'endpoint' => '/search/',
            'query_string' => [
                'categories' => ['alliance', 'corporation'],
                'search' => $searchParam,
            ],
        ]);

        $search_result = $action->execute($search_request);

        $name_post_request = new EsiRequestContainer([
            'method' => 'post',
            'version' => 'v3',
            'endpoint' => '/universe/names/',
            'request_body' => collect($search_result)->flatten()->toArray(),
        ]);

        $name_result = $action->execute($name_post_request);

        return collect($name_result)->toJson();
    }

    public function updateOrCreateSsoScopeSetting(UpdateOrCreateSsoScopeSetting $request)
    {

        (new UpdateOrCreateSsoSettings($request->all()))->execute();

        return redirect()->action([SeatPlusController::class, 'scopeSettings'])->with('success', 'SSO Settings Saved');
    }

    public function deleteSsoScopeSetting($entity_id)
    {

        SsoScopes::where('morphable_id', $entity_id)->delete();

        return redirect()->action([SeatPlusController::class, 'scopeSettings'])->with('success', 'SSO Settings Deleted');

    }
}
