<?php

use App\Http\Controllers\Api\TaskController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});
Route::prefix('api/v1')->group(function () {
    Route::apiResource('tasks', TaskController::class);
});




