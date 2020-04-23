<?php


namespace Seatplus\Web\Http\Controllers\Configuration\SsoSettings;

use Inertia\Inertia;
use Seatplus\Web\Http\Controllers\Controller;
use Seatplus\Web\Services\SsoSettings\GetSsoScopeEntries;

class OverviewController extends Controller
{
    public function __invoke()
    {
        $available_scopes = config('eveapi.scopes');

        $sso_scopes_entries = function () {
            return (new GetSsoScopeEntries)->execute();
        };

        return Inertia::render('Configuration/Scopes/OverviewScopeSettings', [
            'available_scopes' => $available_scopes,
            'entries' => $sso_scopes_entries,
        ]);
    }

}
