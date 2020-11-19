<?php


namespace Seatplus\Web\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;


class UserComplianceResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request
     * @return array
     */
    public function toArray($request)
    {
        $characters = collect(CharacterComplianceResource::collection($this->characters))->filter();

        return $characters->isEmpty() ? [] : [
            'main_character' => $this->main_character,
            'characters' => $characters
        ];
    }
}
