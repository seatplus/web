<?php


namespace Seatplus\Web\Http\Resources;


use Illuminate\Http\Resources\Json\JsonResource;

class ContactResource extends JsonResource
{
    private array $character_affiliation = [];

    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request
     * @return array
     */
    public function toArray($request)
    {

        return [
            'contact_id' => $this->contact_id,
            'contact_type' => $this->contact_type,
            'is_blocked' => $this->is_blocked,
            'is_watched' => $this->is_watched,
            'standing' => $this->standing,
            'labels' => $this->labels,
            'affiliation' => $this->affiliations
        ];
    }

}
