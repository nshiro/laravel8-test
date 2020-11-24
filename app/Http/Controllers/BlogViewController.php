<?php

namespace App\Http\Controllers;

use Facades\Illuminate\Support\Str;
use App\Models\Blog;
use App\StrRandom;
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

        // $random = Str::random(10);

        // $random = (new StrRandom)->random(10);
        $random = app(StrRandom::class)->random(10);  // resolve()


        return view('blog.show', compact('blog', 'random'));
    }
}
