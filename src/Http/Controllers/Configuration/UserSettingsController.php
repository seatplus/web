<?php


namespace Seatplus\Web\Http\Controllers\Configuration;


use Inertia\Inertia;
use Seatplus\Web\Http\Resources\UserRessource;

class UserSettingsController
{
    public function index()
    {

        return Inertia::render('Configuration/UserSettings', [
            'user' => UserRessource::make(auth()->user())
        ]);
    }

    public function update_main_character()
    {
        $user = auth()->user();

        request()->validate([
            'character_id' => 'required|integer'
        ]);

        $user->main_character_id = request()->input('character_id');
        $user->save();

        return redirect()->action([UserSettingsController::class, 'index'])->with('success', 'Main character updated');

    }

}
