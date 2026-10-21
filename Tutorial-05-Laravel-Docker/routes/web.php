<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return response()->view('welcome');
});

Route::get('/salud', function () {
    return response()->json([
        'estado' => 'ok',
        'proyecto' => 'Tutorial 05 - Laravel con Docker',
    ]);
});
