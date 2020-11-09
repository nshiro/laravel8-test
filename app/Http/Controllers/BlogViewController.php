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
            ->onlyOpen()  // ->where('status', Blog::OPEN)
            ->withCount('comments')
            ->orderByDesc('comments_count')
            ->get();

        return view('index', compact('blogs'));
    }

    public function show(Blog $blog)
    {
        // if ($blog->status == Blog::CLOSED) {
        //     abort(403);
        // }

        if ($blog->isClosed()) {
            abort(403);
        }

        return view('blog.show', compact('blog'));
    }
}
