<?php

declare(strict_types=1);

namespace Seatplus\Web\Http\Controllers\Request;

use Seatplus\Auth\Http\Requests\RoleRequest;

class ManageRoleRequest extends RoleRequest
{
    protected function prepareForValidation(): void
    {
        $this->merge(['role_id' => (int) $this->route('role_id')]);
    }
}
