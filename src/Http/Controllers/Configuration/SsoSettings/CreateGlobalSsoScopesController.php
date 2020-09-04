<?php


namespace Seatplus\Web\Http\Controllers\Configuration\SsoSettings;


use Seatplus\Web\Http\Controllers\Controller;
use Seatplus\Web\Http\Controllers\Request\CreateGlobalSsoScopeValidation;

class CreateGlobalSsoScopesController extends Controller
{
    public function __invoke(CreateGlobalSsoScopeValidation $global_sso_scope_validation_request)
    {

        setting(['global_sso_scopes', collect($global_sso_scope_validation_request->get('selectedScopes'))]);

        return redirect()->route('settings.scopes')->with('success', 'Global SSO Settings Saved');
    }

}
