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

//Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//    return $request->user();
//});

Route::get('/menu', [\App\Http\Controllers\MenuController::class, 'getAll']);
Route::post('/menu', [\App\Http\Controllers\MenuController::class, 'store']);
Route::delete('/menu', [\App\Http\Controllers\MenuController::class, 'delete']);
