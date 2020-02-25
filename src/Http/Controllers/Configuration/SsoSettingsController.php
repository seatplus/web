<?php


namespace Seatplus\Web\Http\Controllers\Configuration;


use Inertia\Inertia;
use Seatplus\Eveapi\Models\SsoScopes;
use Seatplus\Web\Http\Controllers\Controller;
use Seatplus\Web\Http\Controllers\Request\UpdateOrCreateSsoScopeSetting;
use Seatplus\Web\Services\SsoSettings\GetSsoScopeEntries;
use Seatplus\Web\Services\SsoSettings\SearchCorporationOrAlliance;
use Seatplus\Web\Services\SsoSettings\UpdateOrCreateSsoSettings;

class SsoSettingsController extends Controller
{
    public function scopeSettings(?int $entity_id = null)
    {
        $available_scopes = config('eveapi.scopes');

        $sso_scopes_entries = function () {
            return (new GetSsoScopeEntries)->execute();
        };

        return Inertia::render('Configuration/ScopeSettings', [
            'available_scopes' => $available_scopes,
            'entries' => $sso_scopes_entries,
            'active' => $entity_id,
        ]);
    }

    public function searchAllianceCorporations($searchParam) : string
    {
        return (new SearchCorporationOrAlliance($searchParam))->search();
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
