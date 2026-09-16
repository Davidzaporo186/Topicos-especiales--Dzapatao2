<?php

use App\Http\Controllers\Api\ProductApiController;
use App\Http\Controllers\Api\ProductApiControllerV2;
use App\Http\Controllers\Api\ProductApiControllerV3;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

// A. API REST simple
Route::get('/products', 'App\Http\Controllers\Api\ProductApiController@index')->name('api.product.index');
Route::get('/products/{id}', 'App\Http\Controllers\Api\ProductApiController@show')->name('api.product.show');

// Actividad: crear producto vía POST
Route::post('/products', 'App\Http\Controllers\Api\ProductApiController@store')->name('api.product.store');

// B. API REST con Recursos (V2)
Route::get('/v2/products', 'App\Http\Controllers\Api\ProductApiControllerV2@index')->name('api.v2.product.index');
Route::get('/v2/products/{id}', 'App\Http\Controllers\Api\ProductApiControllerV2@show')->name('api.v2.product.show');

// C. API REST con Colecciones (V3)
Route::get('/v3/products', 'App\Http\Controllers\Api\ProductApiControllerV3@index')->name('api.v3.product.index');
Route::get('/v3/products/paginate', 'App\Http\Controllers\Api\ProductApiControllerV3@paginate')->name('api.v3.product.paginate');
