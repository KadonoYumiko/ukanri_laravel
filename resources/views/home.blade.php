@extends('layouts.app')

@section('content')
    <form action="check" method="POST" enctype="multipart/form-data">
        <!-- CSRF保護 -->
        @csrf
        <h2>チェックする課題を選択</h2>
        <p><label><input type="radio" name="kad" id="kad" value="01_1">k_01_1.sh</label></p>
        <p><label><input type="radio" name="kad" id="kad" value="01_2">k_01_2.sh</label></p>
        <h2>自分の課題ファイルをアップロード</h2>
        <p><input type="file" name="kadfile" id="kadfile"></p>
        <p><input type="submit" value="課題チェック"></p>
    </form>
    @isset($score)
        <h2>{!!$score!!}</h2>
    @endisset
    <h2>実行結果</h2>
    @isset($msg)
        @foreach($msg as $ca)
            {!!$ca!!}
        @endforeach
    @endisset
@endsection
