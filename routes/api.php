<?php

use App\Http\Controllers\GetGameController;
use App\Http\Controllers\MakeAMoveController;
use App\Http\Controllers\NewGameController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::post('/get-game', GetGameController::class);
Route::post('/new-game', NewGameController::class);
Route::post('/move', MakeAMoveController::class);
