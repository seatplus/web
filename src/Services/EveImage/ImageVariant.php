<?php

declare(strict_types=1);

namespace Seatplus\Web\Services\EveImage;

class ImageVariant
{
    /** EVE inventory category ids whose types use a non-default image variation. */
    private const int SHIP = 6;

    private const int BLUEPRINT = 9;

    /**
     * The EVE Image Server variation for a type, derived from its inventory category.
     *
     * Ships get a 3D `render`, blueprints their `bp` art, everything else its `icon`.
     * `icon` exists for effectively every type, so unknown/unmapped categories are safe.
     * `bpc` (blueprint copies) and `relic` are per-instance / edge cases handled at the
     * call site (the `bpo` prop), not here.
     */
    public static function forCategory(?int $categoryId): string
    {
        return match ($categoryId) {
            self::SHIP => 'render',
            self::BLUEPRINT => 'bp',
            default => 'icon',
        };
    }
}
