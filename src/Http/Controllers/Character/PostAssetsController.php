<?php


namespace Seatplus\Web\Http\Controllers\Character;

use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Seatplus\Eveapi\Models\Assets\CharacterAsset;
use Seatplus\Eveapi\Http\Resources\CharacterAsset as CharacterAssetResource;

class PostAssetsController
{
    public function __invoke(Request $request)
    {
        $validatedData = $request->validate([
            'character_id' => [Rule::in(auth()->user()->getAffiliatedCharacterIdsByPermission(CharacterAsset::class)), 'numeric'],
            'region_id' => ['exists:universe_regions,region_id', 'numeric'],
            'search_param' => 'string',
        ]);

        $character_id = $request->has('character_id') ? $validatedData['character_id'] : null;

        $query = CharacterAsset::Affiliated($character_id)
            ->with('location', 'location.locatable', 'owner', 'type', 'type.group', 'content')
            ->whereIn('location_flag', ['Hangar', 'AssetSafety', 'Deliveries'])
            ->orderBy('location_id', 'asc');

        if($request->has('region_id'))
            $query = $query->inRegion($validatedData['region_id']);

        if($request->has('search_param'))
            $query = $query->search($validatedData['search_param']);

        return CharacterAssetResource::collection(
            $query->paginate()
        );

    }
}
