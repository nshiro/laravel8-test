<?php

use App\Http\Controllers\BlogViewController;
use App\Http\Controllers\SignUpController;
use Illuminate\Support\Facades\Route;

Route::get('/', [BlogViewController::class, 'index']);
Route::get('blogs/{blog}', [BlogViewController::class, 'show']);

Route::get('signup', [SignUpController::class, 'index']);
Route::post('signup', [SignUpController::class, 'store']);
