@extends('layouts.app')

@section('content')
    <form action="check" method="POST" enctype="multipart/form-data">
        <!-- CSRF保護 -->
        @csrf
        <h1>課題提出状況</h1>
        <h2>提出する課題のラジオボタンを選択してください。</h2>
        <table class="table">
        <tr><th>課題番号</th><th>得点</th></tr>
        @isset($res)
            {!!$res!!}
        @endisset
        </table>

        <h2>自分の課題ファイルをアップロード</h2>
        <p><input type="file" name="kadfile" id="kadfile"></p>
        <p><input type="submit" value="課題チェック"></p>
    </form>
    <!-- 入力値エラー時表示-->
    @if (count($errors) > 0)
        <span class="alert alert-danger">
                @foreach ($errors->all() as $error)
                    {{ $error }}
                @endforeach
          </span>
    @endif
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
