<?php

namespace Seatplus\Web\Http\Resources;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Seatplus\Eveapi\Models\Universe\Station;
use Seatplus\Eveapi\Models\Universe\Structure;

class LocationRessource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'location_id' => $this->location_id,
            // when locatable loaded, get name
            'name' => $this->whenLoaded(
                'locatable',
                fn () => sprintf('%s - %s', $this->locatable?->system->name, $this->locatable->name)
            ),
            'is_manual_location' => $this->whenLoaded('locatable', function () {
                return ! ($this->locatable instanceof Station || $this->locatable instanceof Structure);
            }, ! ($this->location_id === 2004)),
            'assets' => AssetResource::collection($this->whenLoaded('assets')),
            'volume' => $this->whenLoaded('assets', fn () => $this->calculateVolume($this->assets)),
        ];
    }

    private function calculateVolume(Collection $assets): float|int
    {
        return $assets->reduce(fn (int|float $carry, Model $asset) => $carry + data_get($asset, 'type.volume', 0) * $asset->quantity, 0);
    }
}
