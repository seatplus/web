<?php


namespace Seatplus\Web\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Seatplus\Auth\Services\BuildCharacterScopesArray;
use Seatplus\Eveapi\Models\Character\CharacterInfo;


class CharacterComplianceResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request
     * @return array
     */
    public function toArray($request)
    {
        $filter = $request->query('filter', false);

        $character_scopes_array = BuildCharacterScopesArray::get(CharacterInfo::find($this->character_id));

        return $filter
            ? $filter === 'renegades'
                ? (empty(collect($character_scopes_array)->get('missing_scopes')) ? [] : $character_scopes_array)
                : (!empty(collect($character_scopes_array)->get('missing_scopes')) ? [] : $character_scopes_array)
            : $character_scopes_array;
    }
}
