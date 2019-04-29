<?php

// Namespace all of the routes for this package.
Route::group([
    'namespace'  => 'Seatplus\Web\Http\Controllers',
    'middleware' => 'web',
], function () {

    Route::get('/test', [
        'as'   => 'web.test',
        'middleware' => 'auth',
        'uses' => 'HomeController@test',
    ]);

    // Authentication & Registration Routes.
    Route::group([
        'namespace'  => 'Auth',
    ], function () {

        // Since Laravel 5.3, its recommended to use Auth::routes(),
        // for these. We use named routes though, so that does not
        // *really* work for us here.
        Route::group(['prefix' => 'auth'], function () {

            include __DIR__ . '/Routes/Auth/Auth.php';
        });

    });
});
