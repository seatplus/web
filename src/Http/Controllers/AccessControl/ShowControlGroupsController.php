<?php

declare(strict_types=1);

namespace Seatplus\Web\Http\Controllers\AccessControl;

use Inertia\Inertia;
use Inertia\Response;

class ShowControlGroupsController
{
    public function __invoke(): Response
    {
        return Inertia::render('AccessControl/ControlGroupsIndex', [
            'activeSidebarElement' => 'acl.groups',
        ]);
    }
}
