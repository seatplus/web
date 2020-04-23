<?php


namespace Seatplus\Web\Http\Controllers\Configuration\SsoSettings;


use Inertia\Inertia;
use Seatplus\Eveapi\Models\SsoScopes;
use Seatplus\Web\Http\Controllers\Controller;

class EditController extends Controller
{
    public function __invoke(int $entity_id)
    {
        $entity = SsoScopes::where('morphable_id', $entity_id)->with('morphable')->first();

        $available_scopes = config('eveapi.scopes');

        return Inertia::render('Configuration/Scopes/EditScopeSettings', [
            'available_scopes' => $available_scopes,
            'entity' => $entity
        ]);


    }

}
