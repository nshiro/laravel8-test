@extends('layouts.index')

@section('content')


<h1>ブログ一覧</h1>

<ul>
    @foreach($blogs as $blog)
    <li>{{ $blog->title }}　{{ $blog->user->name }}　（{{ $blog->comments_count }}件のコメント）</li>
    @endforeach
</ul>


@endsection