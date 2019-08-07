<?php

Route::get('login', [
    'as'   => 'auth.login',
    'uses' => 'LoginController@showLoginForm',
]);

Route::get('logout', [
    'as'   => 'auth.logout',
    'uses' => 'LoginController@logout',
]);
