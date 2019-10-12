<?php

Route::get('/auth/login', 'AuthController@loginPage')->name('auth.login');
Route::post('/auth/login', 'AuthController@login')->name('auth.login');
Route::view('/unsupported-browser', 'sanjab::unsupported_browser')->name('unsupported-browser');
