<?php


namespace Seatplus\Web\Http\Controllers\Corporation\Recruitment;


use Illuminate\Http\Request;
use Seatplus\Auth\Models\User;
use Seatplus\Eveapi\Models\Character\CharacterInfo;
use Seatplus\Web\Http\Controllers\Controller;

class PostCharacterReviewController extends Controller
{
    public function __invoke(Request $request, int $character_id)
    {
        $request->validate([
            'decision' => 'required',
            'explanation' => 'required_if:decision,rejected'
        ]);

        CharacterInfo::find($character_id)->application()->update([
            'status' => $request->get('decision'),
            'comment' => $request->get('explanation', '')
        ]);


        return redirect()->route('corporation.recruitment')->with('success', sprintf('Character %s', $request->get('decision')));

    }
}
