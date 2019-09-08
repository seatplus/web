<?php

Route::get('/settings', 'SeatPlusController@settings')->name('settings');

Route::post('/cache/clear', 'CommandsController@clear')->name('cache.clear');
