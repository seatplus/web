<?php

namespace Seatplus\Web\Http\Controllers\Character;

use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Inertia\Inertia;
use Seatplus\Eveapi\Http\Resources\CharacterAsset as CharacterAssetResource;
use Seatplus\Eveapi\Models\Assets\CharacterAsset;
use Seatplus\Eveapi\Models\Universe\Region;
use Seatplus\Web\Http\Controllers\Controller;

class AssetsController extends Controller
{
    public function index(Request $request)
    {

        $validatedData = $request->validate([
            'character_id' => [Rule::in(auth()->user()->getAffiliatedCharacterIdsByPermission(CharacterAsset::class)), 'numeric'],
            'region_id' => ['exists:universe_regions,region_id', 'numeric'],
            'search_param' => 'string'
        ]);

        $character_id = $request->has('character_id') ? $validatedData['character_id'] : null;

        $query = CharacterAsset::Affiliated($character_id)
            //TODO get rid of with
            ->with('location.locatable', 'owner')
            ->whereIn('location_flag', ['Hangar', 'AssetSafety', 'Deliveries'])
            ->orderBy('location_id', 'desc');

        if($request->has('region_id'))
            $query = $query->inRegion($validatedData['region_id']);

        if($request->has('search_param'))
            $query = $query->search($validatedData['search_param']);

        $filters = function () {

            $affiliated_characters = getAffiliatedCharacters(CharacterAsset::class);
            $owned_character_ids = auth()->user()->characters->pluck('character_id');

            return [
                'regions' => Region::all(),
                'affiliated_characters' => $affiliated_characters->filter(function ($character_info) use ($owned_character_ids){
                    return !in_array($character_info->character_id, $owned_character_ids->toArray());
                }),
                'owned_characters' => $affiliated_characters->filter(function ($character_info) use ($owned_character_ids){
                    return in_array($character_info->character_id, $owned_character_ids->toArray());
                })
            ];
        };

        $assets = CharacterAssetResource::collection(
            $query->paginate()
        );

        return Inertia::render('Character/Assets', [
            'filters' => $filters,
            'assets' => $assets,
        ]);

    }
}
