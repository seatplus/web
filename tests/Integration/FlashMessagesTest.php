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

use Inertia\Inertia;
use Inertia\Testing\Assert;

beforeEach(function () {
    Inertia::share([
        'flash' => function () {
            return [
                'success' => session()->pull('success'),
                'info'    => session()->pull('info'),
                'warning' => session()->pull('warning'),
                'error'   => session()->pull('error'),
            ];
        },
    ]);
});

test('see success flash messages', function () {
    $response = test()->withSession(['success', 'SuccessFlashMessage'])
        ->get('auth/login');

    $response->assertInertia(fn (Assert $page) => $page->has('flash.success'));
});

test('see error flash messages', function () {
    $response = test()->withSession(['error', 'ErrorFlashMessage'])
        ->get('auth/login');

    $response->assertInertia(fn (Assert $page) => $page->has('flash.error'));
});

test('see warning flash messages', function () {
    $response = test()->withSession(['warning', 'WarningFlashMessage'])
        ->get('auth/login');

    $response->assertInertia(fn (Assert $page) => $page->has('flash.warning'));
});

test('see info flash messages', function () {
    $response = test()->withSession(['info', 'InfoFlashMessage'])
        ->get('auth/login');

    $response->assertInertia(fn (Assert $page) => $page->has('flash.info'));
});
