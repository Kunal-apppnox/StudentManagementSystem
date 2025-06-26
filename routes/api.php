<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\StudentController;

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

Route::get('/teachers', [AuthController::class, 'index']);
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
Route::get('/teachers/{id}', [AuthController::class, 'show']);
Route::put('/teachers/{id}', [AuthController::class, 'update']);
Route::delete('/teachers/{id}', [AuthController::class, 'delete']);


Route::middleware('auth:api')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::middleware('role:teacher')->group(function () {
        Route::get('/students', [StudentController::class, 'index']);
        Route::post('/students', [StudentController::class, 'store']);
        Route::put('/students/{id}', [StudentController::class, 'update']);
        Route::get('/student/{id}', [StudentController::class, 'show']);
        Route::delete('/students/{id}', [StudentController::class, 'destroy']);
    });

    Route::middleware('role:student,teacher')->get('/students/{id}', [StudentController::class, 'show']);
});

