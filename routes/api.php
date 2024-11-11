<?php

use App\Http\Controllers\API\AuthController;
use App\Http\Controllers\API\UrlController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::post('/signup', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

Route::middleware('auth:sanctum')->group(function() {
    Route::get('/hello', function () {
        return "hello world";
    });
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::post('/long-url', [UrlController::class, 'insertLongUrl']);
    Route::get('/urls/{user}', [UrlController::class, 'getUrlsByUser']);
});
