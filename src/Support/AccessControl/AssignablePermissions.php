<?php

declare(strict_types=1);

namespace Seatplus\Web\Support\AccessControl;

use Illuminate\Support\Collection;
use Seatplus\Auth\Models\Permissions\Permission;

/**
 * The permissions a control group may grant. The canonical set is declared in
 * `config/web.permissions.php`; any permissions already persisted (e.g. created by other packages
 * or the AssignSuperuser command) are merged in so nothing assignable is hidden.
 */
class AssignablePermissions
{
    /**
     * @return Collection<int, string>
     */
    public static function all(): Collection
    {
        return collect(config('web.permissions', []))
            ->merge(Permission::query()->pluck('name'))
            ->unique()
            ->sort()
            ->values();
    }
}
