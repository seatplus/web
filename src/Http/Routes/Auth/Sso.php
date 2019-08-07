<?php

Route::get('/eve', [
    'as'   => 'auth.eve',
    'uses' => 'SsoController@redirectToProvider',
]);

Route::get('/eve/callback', [
    'as'   => 'auth.eve.callback',
    'uses' => 'SsoController@handleProviderCallback',
]);
