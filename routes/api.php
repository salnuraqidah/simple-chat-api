<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\GroupController;
use App\Http\Controllers\Api\MChatController;
use App\Http\Controllers\Api\UserController;
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

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});
Route::post('login', [AuthController::class, 'login']);

Route::group(['middleware'=>['auth:sanctum']], function () { 
    Route::get('contact', [UserController::class, 'index']);
    Route::post('chat', [MChatController::class, 'store']);
    Route::get('listchat/{id}', [MChatController::class, 'getListChat']);
    Route::post('group/{id}', [GroupController::class, 'store']);
    Route::get('chatgroup/{id}', [GroupController::class, 'getConversation']);
    Route::post('starchat', [MChatController::class, 'storeStarChat']);
    Route::get('liststarchat/{id}', [MChatController::class, 'getStarChat']);
    Route::post('logout', [AuthController::class, 'logout']);

});
