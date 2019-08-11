<?php

Route::get('status', [
    'as'   => 'horizon.status',
    'uses' => 'QueueController@getStatus',
]);
