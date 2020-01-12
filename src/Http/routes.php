<?php

Route::namespace('Seatplus\Web\Http\Controllers')
    ->middleware('web')
    ->group(function () {

        Route::middleware('auth')
            ->group(function () {
                Route::get('/home', 'HomeController@home')->name('home');

                Route::namespace('Queue')
                    ->prefix('queue')
                    ->group(function () {
                        include __DIR__ . '/Routes/Queue/Queue.php';
                    });

                Route::namespace('Configuration')
                    ->prefix('configuration')
                    ->group(function () {
                        include __DIR__ . '/Routes/Configuration/Configuration.php';
                    });

                Route::namespace('Character')
                    ->prefix('character')
                    ->group(function () {
                        include __DIR__ . '/Routes/Character/View.php';
                    });
            });

    });
