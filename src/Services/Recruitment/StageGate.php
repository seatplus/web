<?php

declare(strict_types=1);

namespace Seatplus\Web\Services\Recruitment;

use Seatplus\Auth\Models\Permissions\Role;
use Seatplus\Auth\Models\User;
use Seatplus\Web\Models\Recruitment\EnlistmentReviewRound;

/**
 * Decides whether a user may review a given recruitment round. A round gates on its control-group
 * (role) membership; a round with no control-group (role_id null) falls back to the flat
 * 'can accept or deny applications' permission — the legacy, pre-multi-stage behaviour.
 */
class StageGate
{
    final public const string RECRUITER_PERMISSION = 'can accept or deny applications';

    public function allowsRound(User $user, EnlistmentReviewRound $round): bool
    {
        return $this->allows($user, $round->role_id);
    }

    public function allows(User $user, ?int $roleId): bool
    {
        if ($user->can('superuser')) {
            return true;
        }

        if (is_null($roleId)) {
            return $user->can(self::RECRUITER_PERMISSION);
        }

        $role = Role::find($roleId);

        return $role !== null && $user->hasRole($role);
    }
}
