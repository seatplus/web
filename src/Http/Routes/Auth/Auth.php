<?php

Route::get('login', [
    'as'   => 'auth.login',
    'uses' => 'LoginController@showLoginForm',
]);