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
use Seatplus\Auth\Models\User;
use Seatplus\Eveapi\Models\Character\CharacterInfo;

class HandleApplicationAction
{
    private User $user;

    private ?int $corporation_id = null;

    private ?int $character_id = null;

    public function __construct()
    {
        $this->user = auth()->user();
    }

    public function execute(array $data): void
    {
        $this->corporation_id = Arr::get($data, 'corporation_id');
        $this->character_id = Arr::get($data, 'character_id');

        $this->character_id ? $this->handleCharacterApplication() : $this->handleUserApplication();
    }

    private function handleUserApplication(): void
    {
        $this->user->application()->create(['corporation_id' => $this->corporation_id]);
    }

    private function handleCharacterApplication(): void
    {
        abort_unless($this->characterIdBelongsToUser(), 403, 'submitted character_id does not belong to user');

        CharacterInfo::find($this->character_id)->application()->create(['corporation_id' => $this->corporation_id]);
    }

    private function characterIdBelongsToUser(): bool
    {
        return in_array($this->character_id, $this->user->characters->pluck('character_id')->toArray());
    }
}
