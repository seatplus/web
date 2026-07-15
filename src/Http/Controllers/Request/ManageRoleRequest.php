<?php

declare(strict_types=1);

namespace Seatplus\Web\Http\Controllers\Request;

use Illuminate\Validation\Rule;
use Seatplus\Auth\Http\Requests\RoleRequest;
use Seatplus\Web\Support\AccessControl\AssignablePermissions;

class ManageRoleRequest extends RoleRequest
{
    protected function prepareForValidation(): void
    {
        $this->merge(['role_id' => (int) $this->route('role_id')]);
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return array_merge(parent::rules(), [
            'permissions' => 'nullable|array',
            'permissions.*' => ['string', Rule::in(AssignablePermissions::all()->all())],
        ]);
    }
}
