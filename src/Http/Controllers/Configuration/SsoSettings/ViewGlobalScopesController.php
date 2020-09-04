<?php


namespace Seatplus\Web\Http\Controllers\Configuration\SsoSettings;


use Inertia\Inertia;
use Seatplus\Web\Http\Controllers\Controller;

class ViewGlobalScopesController extends Controller
{
    public function __invoke()
    {
        

        return Inertia::render('Configuration/Scopes/EditScopeSettings', [
            'available_scopes' => config('eveapi.scopes'),
            'entity' => collect(['selected_scopes' => setting('global_sso_scopes')]),
            'hasGlobalScopes' => true
        ]);
    }

}
