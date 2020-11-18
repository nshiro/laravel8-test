@extends('layouts.index')

@section('content')


<h1>マイブログ一覧</h1>

<a href="/mypage/blogs/create">ブログ新規登録</a>
<hr>


<table>
    <tr>
        <th>ブログ名</th>
    </tr>

    @foreach($blogs as $blog)
    <tr>
        <td>
            {{ $blog->title }}
        </td>
    </tr>
    @endforeach
</table>


@endsection