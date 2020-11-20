<?php

use App\Http\Controllers\BlogViewController;
use App\Http\Controllers\Mypage\BlogMypageController;
use App\Http\Controllers\Mypage\UserLoginController;
use App\Http\Controllers\SignUpController;
use Illuminate\Support\Facades\Route;

Route::get('/', [BlogViewController::class, 'index']);
// Route::get('blogs/{blog}', [BlogViewController::class, 'show'])->middleware('blog.show.limit');
Route::get('blogs/{blog}', [BlogViewController::class, 'show']);

Route::get('signup', [SignUpController::class, 'index']);
Route::post('signup', [SignUpController::class, 'store']);

Route::get('mypage/login', [UserLoginController::class, 'index'])->name('login');
Route::post('mypage/login', [UserLoginController::class, 'login']);

Route::middleware('auth')->group(function () {
    Route::post('mypage/logout', [UserLoginController::class, 'logout']);

    Route::get('mypage/blogs', [BlogMypageController::class, 'index']);
    Route::get('mypage/blogs/create', [BlogMypageController::class, 'create']);
    Route::post('mypage/blogs/create', [BlogMypageController::class, 'store']);
    Route::get('mypage/blogs/edit/{blog}', [BlogMypageController::class, 'edit'])->name('mypage.blog.edit');
    Route::post('mypage/blogs/edit/{blog}', [BlogMypageController::class, 'update'])->name('mypage.blog.update');
    Route::delete('mypage/blogs/delete/{blog}', [BlogMypageController::class, 'destroy'])->name('mypage.blog.delete');

});
