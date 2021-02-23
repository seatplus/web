<?php

/*
 * MIT License
 *
 * Copyright (c) 2019, 2020, 2021 Felix Huber
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in all
 * copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
 * SOFTWARE.
 */

namespace Seatplus\Web\Http\Actions\Recruitment;

use Illuminate\Support\Arr;
use Seatplus\Eveapi\Models\Character\CharacterInfo;
use Seatplus\Eveapi\Services\GetOwnedIds;

class HandleApplicationAction
{
    public function execute(array $data)
    {
        Arr::get($data, 'character_id', false) ? $this->handleCharacterApplication($data) : $this->handleUserApplication($data);
    }

    private function handleUserApplication(array $application_request): void
    {
        auth()->user()->application()->create(['corporation_id' => Arr::get($application_request, 'corporation_id')]);
    }

    private function handleCharacterApplication(array $application_request): void
    {
        $character_id = Arr::get($application_request, 'character_id');

        $user_owns_character_id = in_array($character_id, (new GetOwnedIds)->execute());

        abort_unless($user_owns_character_id, 403, 'submitted character_id does not belong to user');

        CharacterInfo::find($character_id)->application()->create(['corporation_id' => Arr::get($application_request, 'corporation_id')]);
    }
}
