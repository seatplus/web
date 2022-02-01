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

use Illuminate\Support\Facades\Event;
use Inertia\Testing\Assert;
use Seatplus\Auth\Models\CharacterUser;

it('has user settings', function () {
    $response = test()->actingAs(test()->test_user)
        ->get(route('user.settings'));

    //$response->assertInertia('Configuration/UserSettings');
    $response->assertInertia(fn (Assert $page) => $page->component('Configuration/UserSettings'));
});

test('one can update main character', function () {
    $secondary_character = Event::fakeFor(fn () => CharacterUser::factory()->make());

    test()->test_user->character_users()->save($secondary_character);

    test()->assertNotEquals(test()->test_user->main_character, $secondary_character->character);

    test()->actingAs(test()->test_user)
        ->json('POST', route('change.main_character'), [
            'character_id' => $secondary_character->character_id,
        ]);

    expect($secondary_character->character)->toEqual(test()->test_user->refresh()->main_character);
});
