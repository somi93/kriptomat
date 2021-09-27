<?php

use Illuminate\Http\Request;
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

Route::get('/server-status', [\App\Http\Controllers\CryptoController::class, 'serverStatus']);


Route::prefix('coin')->group(function () {

    Route::get('/find', [\App\Http\Controllers\CoinController::class, 'find']);

    Route::get('/get-favorite', [\App\Http\Controllers\CoinController::class, 'allFavorites']);
    Route::get('/get-alert', [\App\Http\Controllers\CoinController::class, 'allAlerts']);

    Route::get('/{slug}', [\App\Http\Controllers\CoinController::class, 'show']);
    Route::post('/favorite', [\App\Http\Controllers\CoinController::class, 'favoriteStore']);
    Route::post('/alert', [\App\Http\Controllers\CoinController::class, 'alertStore']);

    Route::put('/favorite/{id}', [\App\Http\Controllers\CoinController::class, 'favoriteEdit']);
    Route::put('/alert/{id}', [\App\Http\Controllers\CoinController::class, 'alertEdit']);

});

Route::prefix('market')->group(function () {

    Route::get('/', [\App\Http\Controllers\MarketController::class, 'index']);
    Route::get('/sync', [\App\Http\Controllers\MarketController::class, 'syncMarket']);

});

Route::prefix('chart')->group(function () {

    Route::get('/{slug}', [\App\Http\Controllers\ChartController::class, 'show']);

});
