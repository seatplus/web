<?php

declare(strict_types=1);

namespace Seatplus\Web\Support;

use Seatplus\Eveapi\Models\Alliance\AllianceInfo;
use Seatplus\Eveapi\Models\Corporation\CorporationInfo;

/**
 * Shapes a corporation (and its alliance) into the array the recruitment surfaces render. Centralised
 * so the typed relation access lives in one place.
 */
class CorporationShape
{
    /**
     * @return array<string, mixed>
     */
    public static function make(CorporationInfo $corporation): array
    {
        /** @var AllianceInfo|null $alliance */
        $alliance = $corporation->alliance;

        return [
            'corporation_id' => $corporation->corporation_id,
            'name' => $corporation->name,
            'ticker' => $corporation->ticker,
            'alliance' => $alliance instanceof AllianceInfo ? [
                'alliance_id' => $alliance->alliance_id,
                'name' => $alliance->name,
            ] : null,
        ];
    }
}
