<?php


namespace Seatplus\Web\Http\Controllers\Corporation\Recruitment;


use Inertia\Inertia;
use Seatplus\Auth\Models\User;
use Seatplus\Eveapi\Models\Character\CharacterInfo;
use Seatplus\Web\Http\Controllers\Controller;

class GetCharacterApplicationController extends Controller
{

    public function __invoke(int $character_id)
    {
        $character = CharacterInfo::find($character_id);

        $recruit = collect([
            'main_character' => $character,
            'characters' => [$character]
        ]);

        return inertia('Corporation/Recruitment/Application', [
            'recruit' => $recruit
        ]);

    }

}
