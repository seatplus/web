<?php


namespace Seatplus\Web\Http\Actions\Recruitment;


use Illuminate\Support\Arr;
use Seatplus\Eveapi\Models\Character\CharacterInfo;
use Seatplus\Eveapi\Services\GetOwnedIds;

class HandleApplicationAction
{
    public function execute(array $data)
    {

        Arr::get($data,'character_id', false) ? $this->handleCharacterApplication($data) : $this->handleUserApplication($data);
    }

    private function handleUserApplication(array $application_request) : void
    {
        auth()->user()->application()->create(['corporation_id' => Arr::get($application_request,'corporation_id')]);
    }

    private function handleCharacterApplication(array $application_request) : void
    {

        $character_id = Arr::get($application_request,'character_id');

        $user_owns_character_id = in_array($character_id, (new GetOwnedIds)->execute());

        abort_unless($user_owns_character_id, 403, 'submitted character_id does not belong to user');

        CharacterInfo::find($character_id)->application()->create(['corporation_id' => Arr::get($application_request,'corporation_id')]);
    }

}
