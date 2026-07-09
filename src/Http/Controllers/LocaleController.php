<?php

namespace Seatplus\Web\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Seatplus\Web\Support\Locales;

class LocaleController
{
    public function update(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'locale' => ['required', 'string', Rule::in(Locales::available())],
        ]);

        // Remember the choice for this session — covers guests and applies immediately.
        session(['locale' => $validated['locale']]);

        // Persist to the account so it follows an authenticated user across devices/sessions.
        if (auth()->check()) {
            auth()->user()->update(['locale' => $validated['locale']]);
        }

        return redirect()->back();
    }
}
