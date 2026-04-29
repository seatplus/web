<?php

declare(strict_types=1);

namespace Seatplus\Web\Http\Controllers\AccessControl;

use Illuminate\Http\RedirectResponse;
use Seatplus\Auth\Http\Actions\Roles\OptIn\ManageOptInRoleAction;
use Seatplus\Web\Http\Controllers\Controller;
use Seatplus\Web\Http\Controllers\Request\ManageRoleRequest;

class UpdateOptInGroupController extends Controller
{
    public function __construct(
        private readonly ManageOptInRoleAction $action,
    ) {}

    public function __invoke(ManageRoleRequest $request, int $role_id): RedirectResponse
    {
        $this->action->execute($request);

        return redirect()->route('acl.detail', $role_id)->with('success', 'updated');
    }
}
