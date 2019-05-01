<?php

// Namespace all of the routes for this package.
Route::group([
    'namespace'  => 'Seatplus\Web\Http\Controllers',
    'middleware' => 'web',
], function () {

    // Authentication & Registration Routes.
    Route::group([
        'namespace'  => 'Auth',
    ], function () {

        Route::group(['prefix' => 'auth'], function () {

            include __DIR__ . '/Routes/Auth/Auth.php';
            include __DIR__ . '/Routes/Auth/Sso.php';
        });

    });

    Route::get('/home', [
        'middleware' => 'auth',
        'uses' => 'HomeController@home',
    ]);
});
