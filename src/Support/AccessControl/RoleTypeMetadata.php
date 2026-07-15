<?php

declare(strict_types=1);

namespace Seatplus\Web\Support\AccessControl;

use Seatplus\Auth\Enums\RoleType;

/**
 * Web-side, presentational metadata for the four role types (the "join methods").
 *
 * The backend `RoleType` enum is a bare string enum with no labels; this is the single
 * source of truth for how each type is described and what it can do, so the UI never has
 * to re-derive capability rules from ad-hoc booleans. Labels/descriptions come from the
 * `web::access_control` translations; capability flags are static domain rules.
 */
class RoleTypeMetadata
{
    /**
     * @return array{key: string, label: string, description: string, supports_moderators: bool, uses_eligibility: bool, self_service: string, auto_assigned: bool}
     */
    public static function for(RoleType $type): array
    {
        return [
            'key' => $type->value,
            'label' => (string) trans("web::access_control.join_method.{$type->value}.label"),
            'description' => (string) trans("web::access_control.join_method.{$type->value}.description"),
            // Automatic roles cannot have moderators; managed/request/self-service can.
            'supports_moderators' => $type !== RoleType::AUTOMATIC,
            // Managed roles have no eligibility criteria (members are added by hand); the rest gate on criteria.
            'uses_eligibility' => $type !== RoleType::MANUAL,
            // How a member gets in on their own: request an approval, join instantly, or neither.
            'self_service' => match ($type) {
                RoleType::ON_REQUEST => 'apply',
                RoleType::OPT_IN => 'join',
                default => 'none',
            },
            'auto_assigned' => $type === RoleType::AUTOMATIC,
        ];
    }

    /**
     * Metadata for every role type, keyed by enum value — for the join-method picker.
     *
     * @return array<int, array{key: string, label: string, description: string, supports_moderators: bool, uses_eligibility: bool, self_service: string, auto_assigned: bool}>
     */
    public static function all(): array
    {
        return array_map(static fn (RoleType $type): array => self::for($type), RoleType::cases());
    }
}
