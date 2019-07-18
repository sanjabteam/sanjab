<?php

Route::get('/auth/login', 'AuthController@loginPage')->name('auth.login');
Route::post('/auth/login', 'AuthController@login')->name('auth.login');
