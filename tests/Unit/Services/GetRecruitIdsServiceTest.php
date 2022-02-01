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

use Seatplus\Web\Services\GetRecruitIdsService;

beforeEach(fn () => assignPermissionToTestUser('superuser'));

it('returns recruit ids and caches values', function () {
    \Seatplus\Eveapi\Models\Application::factory()->count(5)->create([
        'applicationable_type' => \Seatplus\Auth\Models\User::class,
        'applicationable_id'   => \Seatplus\Auth\Models\User::factory(),
    ]);

    expect(test()->test_user)->can('superuser')->toBeTrue();

    test()->actingAs(test()->test_user);

    $cache_key = sprintf('can accept or deny applications:%s', test()->test_user->id);

    $recruit_ids = GetRecruitIdsService::get();

    expect($recruit_ids)->toHaveCount(5);
    expect(cache($cache_key))->toBe($recruit_ids);
});
