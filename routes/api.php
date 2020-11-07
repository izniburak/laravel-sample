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

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

Route::apiResource('leagues', \App\Http\Controllers\LeaguesController::class);
Route::post('leagues/{league}/fixture/generate', [\App\Http\Controllers\LeaguesController::class, 'generateFixture']);
Route::post('leagues/{league}/fixture/simulate', [\App\Http\Controllers\LeaguesController::class, 'simulate']);
Route::post('leagues/{league}/fixture/weekly-simulate', [\App\Http\Controllers\LeaguesController::class, 'simulateWeekly']);
Route::get('leagues/{league}/fixture', [\App\Http\Controllers\LeaguesController::class, 'fixture']);
Route::get('leagues/{league}/table', [\App\Http\Controllers\LeaguesController::class, 'table']);
Route::apiResource('leagues.teams', \App\Http\Controllers\TeamsController::class);
Route::post('leagues/{league}/teams/bulk', [\App\Http\Controllers\TeamsController::class, 'bulk']);
Route::apiResource('leagues.matches', \App\Http\Controllers\MatchesController::class);

