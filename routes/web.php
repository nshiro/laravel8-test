<?php

use App\Http\Controllers\BlogViewController;
use App\Http\Controllers\Mypage\UserLoginController;
use App\Http\Controllers\SignUpController;
use Illuminate\Support\Facades\Route;

Route::get('/', [BlogViewController::class, 'index']);
Route::get('blogs/{blog}', [BlogViewController::class, 'show']);

Route::get('signup', [SignUpController::class, 'index']);
Route::post('signup', [SignUpController::class, 'store']);

Route::get('mypage/login', [UserLoginController::class, 'index']);
Route::post('mypage/login', [UserLoginController::class, 'login']);
