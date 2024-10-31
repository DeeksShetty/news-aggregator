<?php

use App\Http\Controllers\ArticleController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserController;

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
Route::post('/password/reset-request', [AuthController::class, 'sendPasswordResetLink'])->middleware('throttle:2,1');
Route::post('/password/reset', [AuthController::class, 'passwordReset'])->name('password.reset');

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);

    Route::group(['prefix' => 'article'],function(){
        Route::get('/list',[ArticleController::class,'getArticleList']);
        Route::get('/detail/{id}',[ArticleController::class,'getArticleDetail']);
        Route::get('/user/prefered-list',[ArticleController::class,'getUserArticleList']);
    });
    Route::group(['prefix' => 'user'],function(){
        Route::post('/article/set-preference',[UserController::class,'setUserArticlePreference']);
        Route::get('/article/get-preference',[UserController::class,'getUserArticlePreference']);
    });
});