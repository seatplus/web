<?php


namespace Seatplus\Web\Http\Controllers\Configuration\SsoSettings;


use Seatplus\Web\Http\Controllers\Controller;

class DeleteGlobalSsoScopesController extends Controller
{
    public function __invoke()
    {
        setting(['global_sso_scopes', '']);

        return redirect()->route('settings.scopes')->with('success', 'Global SSO Settings Deleted');
    }

}
