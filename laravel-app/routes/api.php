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

// Route::middleware('auth:api')->get('/user', function (Request $request) {
//     return $request->user();
// });

Route::post('tests', [App\Http\Controllers\TestController::class, 'createTest']);
Route::get('tests', [App\Http\Controllers\TestController::class, 'getTest']);
Route::post('results', [App\Http\Controllers\TestController::class, 'addResult']);
