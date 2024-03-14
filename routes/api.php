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

Route::group(['prefix' => '{locale}', 'where' => ['locale' => '[a-zA-Z]{2}'], 'middleware' => 'setlocale'], function()
{
    Route::post('login', [\App\Http\Controllers\AuthController::class, 'login']);

    Route::group(['middleware' => 'auth:sanctum'], function ()
    {
        Route::get('main-page', [\App\Http\Controllers\MainPageController::class, 'main']);

        Route::get('get-survey-data', [\App\Http\Controllers\SurveyController::class, 'getSurveyData']);

        Route::post('save-survey', [\App\Http\Controllers\SurveyController::class, 'saveSurvey']);

        //Route::get('get-geo-data', [\App\Http\Controllers\MainPageController::class, 'getGeoData']);

        Route::get('get-regions/{country_id}', [\App\Http\Controllers\MainPageController::class, 'getRegions']);

        Route::get('get-punkts/{region_id}', [\App\Http\Controllers\MainPageController::class, 'getPunkts']);

        Route::get('get-locals/{punkt_id}', [\App\Http\Controllers\MainPageController::class, 'getLocals']);

        Route::post('set-geo', [\App\Http\Controllers\MainPageController::class, 'setGeo']);

        Route::get('logout', [\App\Http\Controllers\AuthController::class, 'logout']);
    });
});
