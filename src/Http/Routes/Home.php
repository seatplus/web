<?php

Route::get('/home', [
    'as'   => 'home',
    'uses' => 'HomeController@getHome',
]);