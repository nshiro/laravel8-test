<?php

namespace App\Http\Controllers\Mypage;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class BlogMypageController extends Controller
{
    public function index()
    {
        return view('mypage.blog.index');
    }
}
