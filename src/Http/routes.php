<?php

// Namespace all of the routes for this package.
Route::group([
    'namespace'  => 'Seatplus\Web\Http\Controllers',
], function () {

    Route::get('/test', [
        'as'   => 'web.test',
        'uses' => 'HomeController@test',
    ]);
});
