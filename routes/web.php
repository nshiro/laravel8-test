<?php

use App\Http\Controllers\BlogViewController;
use Illuminate\Support\Facades\Route;

Route::get('/', [BlogViewController::class, 'index']);