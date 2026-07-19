<?php

namespace Seatplus\Web\Http\Controllers\Recruitment;

use Illuminate\Http\RedirectResponse;
use Seatplus\Web\Http\Actions\Recruitment\HandleApplicationAction;
use Seatplus\Web\Http\Controllers\Controller;
use Seatplus\Web\Http\Controllers\Request\ApplicationRequest;

/**
 * Apply to a posting from the central job portal. Reuses the existing HandleApplicationAction, so an
 * application is still a plain eveapi Application (account-wide or single-character) — the employment
 * lifecycle record is created later, on the hire hook.
 */
class ApplyController extends Controller
{
    public function __invoke(ApplicationRequest $request, HandleApplicationAction $action): RedirectResponse
    {
        $action->execute($request->validated());

        return back()->with('success', 'Application submitted');
    }
}
