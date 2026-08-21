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

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;
use Seatplus\Eveapi\Models\SsoScopes;
use Seatplus\Web\Http\Controllers\Controller;
use Seatplus\Web\Http\Controllers\Request\CreateSsoScopeSettingsValidation;
use Seatplus\Web\Services\SsoSettings\GetSsoScopeEntries;
use Seatplus\Web\Services\SsoSettings\UpdateOrCreateSsoSettings;

class SsoSettingsController extends Controller
{
    public function scopeSettings(?int $entity_id = null): Response
    {
        $available_scopes = config('eveapi.scopes');

        $sso_scopes_entries = fn () => (new GetSsoScopeEntries)->execute();

        return Inertia::render('Configuration/ScopeSettings', [
            'available_scopes' => $available_scopes,
            'entries' => $sso_scopes_entries,
            'active' => $entity_id,
        ]);
    }

    public function index(?int $entity_id = null): Response
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

    public function create(CreateSsoScopeSettingsValidation $validation): RedirectResponse
    {
        (new UpdateOrCreateSsoSettings($validation->all()))->execute();

        return redirect()->route('settings.scopes')->with('success', 'SSO Settings Saved');
    }

    public function deleteSsoScopeSetting(?int $entity_id = null): RedirectResponse
    {
        // Deleted one model at a time, never as a mass query: seatplus/auth's SsoScopeObserver flushes
        // every user's permission cache on the model's deleted event, and a query-builder delete fires
        // no model events at all.
        $this->rowsToDelete($entity_id)->each(fn (SsoScopes $row) => $row->delete());

        return redirect()->route('settings.scopes')->with('success', 'SSO Settings Deleted');
    }

    /**
     * @return Collection<int, SsoScopes>
     */
    private function rowsToDelete(?int $entity_id): Collection
    {
        // No id means the installation-wide entry, which is the row with no morphable — not merely
        // "typed global". SsoScopes::global() filters on the type alone, so it would also delete a
        // corporation or alliance row that happens to carry that type.
        if (is_null($entity_id)) {
            return SsoScopes::query()->where('type', 'global')->whereNull('morphable_id')->get();
        }

        // Still matched on the id alone, because that is all this route carries — a corporation and an
        // alliance sharing an id would both be deleted. Disambiguating needs the morphable type in the
        // route, which the single-screen rewrite introduces; not fixable here without changing the URL.
        return SsoScopes::query()
            ->where('morphable_id', $entity_id)
            ->whereNotNull('morphable_type')
            ->get();
    }

    private function getEntity(?int $entity_id = null): SsoScopes|\stdClass
    {
        if (is_null($entity_id)) {
            return SsoScopes::global()->first() ?? (object) [];
        }

        return SsoScopes::where('morphable_id', $entity_id)->with('morphable')->first();
    }
}
