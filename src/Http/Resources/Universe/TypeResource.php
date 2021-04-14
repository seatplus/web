<?php


namespace Seatplus\Web\Http\Resources\Universe;


use Illuminate\Http\Resources\Json\JsonResource;

class TypeResource extends JsonResource
{

    public function toArray($request)
    {
        return [
            'type_id' => $this->type_id,
            'volume' => $this->volume,
            'name' => $this->name,
            'group' => GroupResource::make($this->whenLoaded('group')) ?? 'unknown',
        ];
    }

}