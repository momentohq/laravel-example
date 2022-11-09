<?php

use Momento\LaravelExample\Controllers\WeatherController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::get('/weather/{city}', [WeatherController::class, 'city']);
Route::get('/weather/{zipcode}/{countryCode}', [WeatherController::class, 'zipcode']);
Route::get('/weather/id/{cityId}', [WeatherController::class, 'cityId']);
