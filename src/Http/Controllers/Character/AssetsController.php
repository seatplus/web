<?php


namespace Seatplus\Web\Http\Controllers\Character;


use Inertia\Inertia;
use Seatplus\Eveapi\Models\Assets\CharacterAsset;
use Seatplus\Web\Http\Controllers\Controller;
use Seatplus\Eveapi\Http\Resources\CharacterAsset as CharacterAssetResource;

class AssetsController extends Controller
{
    public function index()
    {

        //dd(auth()->user()->getAffiliatedCharacterIdsByPermission('character.assets'));

        $query = CharacterAsset::with('type.group', 'location.locatable', 'content.type.group', 'content.owner', 'content.content.*', 'owner')
            ->Affiliated()
            ->whereIn('location_flag', ['Hangar', 'AssetSafety', 'Deliveries'])
            ->orderBy('location_id','desc');

        $assets = CharacterAssetResource::collection(
            $query->paginate()
        );

        return Inertia::render('Character/Assets', [
            'assets' => $assets,
            'number_of_owner' => $query->pluck('character_id')->unique()->count()
        ]);

    }

}
