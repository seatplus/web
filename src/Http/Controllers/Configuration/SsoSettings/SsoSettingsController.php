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

namespace Seatplus\Web\Http\Controllers\Configuration\SsoSettings;

use Inertia\Inertia;
use Seatplus\Eveapi\Models\SsoScopes;
use Seatplus\Web\Http\Controllers\Controller;
use Seatplus\Web\Http\Controllers\Request\CreateSsoScopeSettingsValidation;
use Seatplus\Web\Services\SsoSettings\GetSsoScopeEntries;
use Seatplus\Web\Services\SsoSettings\SearchCorporationOrAlliance;
use Seatplus\Web\Services\SsoSettings\UpdateOrCreateSsoSettings;

class SsoSettingsController extends Controller
{
    public function scopeSettings(?int $entity_id = null)
    {
        $available_scopes = config('eveapi.scopes');

        $sso_scopes_entries = fn() => (new GetSsoScopeEntries)->execute();

        return Inertia::render('Configuration/ScopeSettings', [
            'available_scopes' => $available_scopes,
            'entries' => $sso_scopes_entries,
            'active' => $entity_id,
        ]);
    }

    public function searchAllianceCorporations($searchParam): array
    {
        return (new SearchCorporationOrAlliance($searchParam))->search();
    }

    public function index(?int $entity_id = null)
    {
        $available_scopes = config('eveapi.scopes');

        return Inertia::render('Configuration/Scopes/ScopeSettings', [
            'available_scopes' => $available_scopes,
            'entity' => fn () => $this->getEntity($entity_id),
            'options' => [
                ['title' => 'default', 'description' => 'Only characters within the selected entity are required to fulfill the selected scopes'],
                ['title' => 'user', 'description' => 'All characters of a user within this corporation are required to met the required scopes'],
                ['title' => 'global', 'description' => 'Every character in this seat plus instance must met the requirements'],
            ],
        ]);
    }

    public function create(CreateSsoScopeSettingsValidation $validation)
    {
        (new UpdateOrCreateSsoSettings($validation->all()))->execute();

        return redirect()->route('settings.scopes')->with('success', 'SSO Settings Saved');
    }

    public function deleteSsoScopeSetting(?int $entity_id = null)
    {
        is_null($entity_id) ? SsoScopes::global()->delete() : SsoScopes::where('morphable_id', $entity_id)->delete();

        return redirect()->route('settings.scopes')->with('success', 'SSO Settings Deleted');
    }

    private function getEntity(?int $entity_id = null)
    {
        if (is_null($entity_id)) {
            return SsoScopes::global()->first() ?? (object) [];
        }

        return SsoScopes::where('morphable_id', $entity_id)->with('morphable')->first();
    }
}
