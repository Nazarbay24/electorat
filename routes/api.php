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

Route::post('login', [\App\Http\Controllers\AuthController::class, 'login']);

Route::group(['middleware' => 'auth:sanctum'], function () {

    Route::get('main-page', [\App\Http\Controllers\MainPageController::class, 'main']);

    Route::get('get-survey-data', [\App\Http\Controllers\SurveyController::class, 'getSurveyData']);

    Route::get('logout', [\App\Http\Controllers\AuthController::class, 'logout']);
});
