<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\TaskController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Tymon\JWTAuth\Facades\JWTAuth;


Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});
//--------------------------++++ Auth Routes ++++-----------------------------//
//---------------------------------------------------------------------------//

Route::prefix('auth')->group(function () {
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/login', [AuthController::class, 'login']);
    Route::post('/logout', [AuthController::class, 'logout']);
});

//--------------------------++++ Task Routes ++++-----------------------------//
//---------------------------------------------------------------------------//
Route::prefix('tasks')->group(function () {
    Route::get('/', [TaskController::class, 'index']);           
    Route::post('/', [TaskController::class, 'store']);          
    Route::put('/{task}', [TaskController::class, 'update']);    
    Route::delete('/{task}', [TaskController::class, 'destroy']); 
});




