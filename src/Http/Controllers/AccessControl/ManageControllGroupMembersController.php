<?php


namespace Seatplus\Web\Http\Controllers\AccessControl;


use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Inertia\Inertia;
use Seatplus\Auth\Models\Permissions\Role;
use Seatplus\Auth\Models\User;
use Spatie\Permission\PermissionRegistrar;

class ManageControllGroupMembersController
{
    public function index($role_id)
    {
        $users = User::all()->map(function ($user) {
            return [
                'id' => $user->id,
                'name' => $user->main_character->name,
                'character_id' => $user->main_character->character_id,
                'characters' => $user->characters->filter(function ($character) use ($user){
                    return $character->character_id !== $user->main_character->character_id;
                })
            ];
        });

        return Inertia::render('AccessControl/ManageControlGroup', [
            'users' => $users,
            'members' => Role::findById($role_id)->users()->get()->map(function ($user) {
                return [
                    'id' => $user->id,
                    'name' => $user->main_character->name,
                    'character_id' => $user->main_character->character_id,
                    'characters' => $user->characters->filter(function ($character) use ($user){
                        return $character->character_id !== $user->main_character->character_id;
                    })
                ];
            })
        ]);
    }

    public function update(Request $request, $role_id)
    {

        $validated_data = $request->validate([
            'selectedValues.*.id' => 'bail|integer|exists:users,id'
        ]);

        $role = Role::findById($role_id);

        $users_should_have_role = empty($validated_data) ? collect() : collect($validated_data['selectedValues'])->map(function ($vaule) {
            return Arr::get($vaule, 'id');
        });

        $role->users()->sync($users_should_have_role->toArray());

        //Update Cache
        app()->make(PermissionRegistrar::class)->forgetCachedPermissions();

        return redirect()
            ->action([ManageControllGroupMembersController::class, 'index'], $role_id)
            ->with('Success', 'Control Group updated');

    }

}
