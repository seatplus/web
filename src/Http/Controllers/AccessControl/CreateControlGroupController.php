<?php

declare(strict_types=1);

namespace Seatplus\Web\Http\Controllers\AccessControl;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Seatplus\Auth\Models\Permissions\Role;

class CreateControlGroupController
{
    public function __invoke(Request $request): RedirectResponse
    {
        $name = $request->input('name');

        $role = Role::create(['name' => $name]);

        return redirect()
            ->route('acl.detail', $role->id)
            ->with('success', 'Role was created');
    }
}
