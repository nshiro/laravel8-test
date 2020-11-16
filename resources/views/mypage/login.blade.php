@extends('layouts.index')

@section('content')


<h1>ログイン画面</h1>

<form method="post">
@csrf

@include('inc.error')

@include('inc.status')


メールアドレス：<input type="text" name="email" value="{{ old('email') }}">
<br>
パスワード：<input type="password" name="password">
<br><br>
<input type="submit" value="　送信する　">

</form>


@endsection