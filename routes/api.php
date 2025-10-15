<?php


use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\PostsController;

Route::post('/register',[AuthController::class,'register']);
Route::post('/login',[AuthController::class,'login']);


Route::middleware(['jwt'])->group(function(){
    Route::get('/userinfo',[AuthController::class,'userInfo']);
    Route::put('/editinfo',[AuthController::class,'editInfo']);
    Route::delete('/userdelete',[AuthController::class,'userDelete']);
    Route::post('/logout',[AuthController::class,'logout']);
    Route::get('/posts', [PostsController::class, 'index']);
    Route::post('/posts', [PostsController::class, 'store']);
    Route::get('/post/{id}', [PostsController::class, 'show']);
    Route::patch('/post/update/{id}', [PostsController::class, 'update']);
    Route::delete('/post/delete/{id}', [PostsController::class, 'destroy']);
});
