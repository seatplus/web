<?php


namespace Seatplus\Web\Http\Controllers\Character;

use Seatplus\Eveapi\Models\Assets\CharacterAsset;
use Seatplus\Eveapi\Http\Resources\CharacterAsset as CharacterAssetResource;
use Seatplus\Web\Http\Controllers\Request\GetCharacterAssets;

class PostAssetsController
{
    public function __invoke(GetCharacterAssets $request)
    {

        $query = CharacterAsset::Affiliated($request->character)
            ->with('location', 'location.locatable', 'owner', 'type', 'type.group', 'content')
            ->whereIn('location_flag', ['Hangar', 'AssetSafety', 'Deliveries'])
            ->orderBy('location_id', 'asc');

        if($request->has('region'))
            $query = $query->inRegion($request->region);

        if($request->has('search'))
            $query = $query->search($request->search);

        return CharacterAssetResource::collection(
            $query->paginate()
        );

    }
}
