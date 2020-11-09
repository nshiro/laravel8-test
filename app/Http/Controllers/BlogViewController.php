<?php

namespace App\Http\Controllers;

use App\Models\Blog;
use Illuminate\Http\Request;

class BlogViewController extends Controller
{
    public function index()
    {
        // $blogs = Blog::get();
        $blogs = Blog::with('user')
            ->withCount('comments')
            ->orderByDesc('comments_count')
            ->get();

        return view('index', compact('blogs'));
    }
}
