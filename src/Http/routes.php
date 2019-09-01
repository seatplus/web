<?php

// Namespace all of the routes for this package.
Route::group([
    'namespace'  => 'Seatplus\Web\Http\Controllers',
    'middleware' => 'web', //TODO add locale
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

    // Authentication & Registration Routes.
    Route::group([
        'namespace'  => 'Queue',
    ], function () {

        Route::group(['prefix' => 'queue'], function () {

            include __DIR__ . '/Routes/Queue/Queue.php';
        });

    });

    Route::get('/home', [
        'middleware' => 'auth',
        'as' => 'home',
        'uses' => 'HomeController@home',
    ]);

    /*Route::get('/inertia', [
        'uses' => 'HomeController@inertia',
    ]);*/
});
