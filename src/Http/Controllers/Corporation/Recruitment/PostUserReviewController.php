<?php


namespace Seatplus\Web\Http\Controllers\Corporation\Recruitment;


use Illuminate\Http\Request;
use Seatplus\Auth\Models\User;
use Seatplus\Web\Http\Controllers\Controller;

class PostUserReviewController extends Controller
{
    public function __invoke(Request $request, User $recruit)
    {
        $request->validate([
            'decision' => 'required',
            'explanation' => 'required_if:decision,rejected'
        ]);

        $recruit->application()->update([
            'status' => $request->get('decision'),
            'comment' => $request->get('explanation', '')
        ]);


        return redirect()->route('corporation.recruitment')->with('success', sprintf('User %s', $request->get('decision')));

    }
}
