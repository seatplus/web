<?php

namespace Seatplus\Web\Http\Resources;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Seatplus\Eveapi\Models\Universe\Location;
use Seatplus\Eveapi\Models\Universe\Station;
use Seatplus\Eveapi\Models\Universe\Structure;

/**
 * @mixin Location
 */
class LocationRessource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'location_id' => $this->location_id,
            // when locatable loaded, get name
            'name' => $this->whenLoaded(
                'locatable',
                fn () => sprintf('%s - %s', data_get($this->locatable, 'system.name'), data_get($this->locatable, 'name'))
            ),
            // Not whenLoaded(): eager-loading a null-type morph marks `locatable` loaded-but-null,
            // and whenLoaded() returns null in that case — which would wrongly hide the "add
            // location information" affordance on genuinely unresolved locations. Gate on the load
            // state instead so an unresolved (or manual) location is still flagged manual, while a
            // resolved station/structure and asset-safety (2004) are not.
            'is_manual_location' => $this->when(
                $this->relationLoaded('locatable'),
                fn () => $this->location_id !== 2004 && ! ($this->locatable instanceof Station || $this->locatable instanceof Structure),
                ! ($this->location_id === 2004),
            ),
            'assets' => AssetResource::collection($this->whenLoaded('assets')),
            'volume' => $this->whenLoaded('assets', fn () => $this->calculateVolume($this->assets)),
        ];
    }

    private function calculateVolume(Collection $assets): float|int
    {
        return $assets->reduce(fn (int|float $carry, Model $asset) => $carry + data_get($asset, 'type.volume', 0) * (int) $asset->getAttribute('quantity'), 0);
    }
}
