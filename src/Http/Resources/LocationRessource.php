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
            'is_manual_location' => $this->whenLoaded('locatable', function () {
                return ! ($this->locatable instanceof Station || $this->locatable instanceof Structure);
            }, ! ($this->location_id === 2004)),
            // Resolve to a plain array (not a resource object): Inertia re-wraps nested JsonResources
            // in a `data` key, but the Vue side expects `assets` to be a plain array.
            'assets' => $this->whenLoaded('assets', fn (): array => AssetResource::collection($this->assets)->resolve()),
            'volume' => $this->whenLoaded('assets', fn () => $this->calculateVolume($this->assets)),
        ];
    }

    private function calculateVolume(Collection $assets): float|int
    {
        return $assets->reduce(fn (int|float $carry, Model $asset) => $carry + data_get($asset, 'type.volume', 0) * (int) $asset->getAttribute('quantity'), 0);
    }
}
