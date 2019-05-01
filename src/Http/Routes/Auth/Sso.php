<?php


Route::get('/eve', [
    'as'   => 'auth.eve',
    'uses' => 'SsoController@redirectToProvider',
]);

Route::get('/eve/callback', [
    'as'   => 'auth.eve.callback',
    'uses' => 'SsoController@handleProviderCallback',
]);

Route::get('/eve/email', [
    'as'   => 'auth.eve.email',
    'uses' => 'SsoController@getUserEmail',
]);

Route::post('/eve/email', [
    'as'   => 'auth.eve.email.set',
    'uses' => 'SsoController@postUpdateUserEmail',
]);

Route::get('/eve/confirm', [
    'as'   => 'auth.eve.confirmation.get',
    'uses' => 'SsoController@getSsoConfirmation',
]);

Route::post('/eve/confirm', [
    'as'   => 'auth.eve.confirmation.post',
    'uses' => 'SsoController@postSsoConfirmation',
]);
