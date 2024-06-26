<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\TasksController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;


Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});



///PUBLIC ROUTES
Route::post('/login', [AuthController::class, 'login']);
Route::post('/register', [AuthController::class, 'register']);
Route::get('/tasks/get-all-tasks', [TasksController::class, 'getAllTasks']);




///PROTECTED ROUTES

Route::group(['middleware' => ['auth:sanctum']], function () {
    Route::resource('/tasks', TasksController::class);
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/task/get-others-tasks', [TasksController::class, 'getOthersTask']);
    // Route::patch('/update-task/{id}', [TasksController::class, 'update']);
});
