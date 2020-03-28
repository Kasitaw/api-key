<?php

use Illuminate\Support\Facades\Route;

Route::get('/authorize/user', function () {
    return request()->user();
})->middleware('auth:api_key');

Route::get('/unauthorized/user', function () {
    return request()->user();
})->middleware('auth:api_key');
