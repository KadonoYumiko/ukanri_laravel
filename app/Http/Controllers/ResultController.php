<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Result;

class ResultController extends Controller
{
    public function index()
    {
        // 提出課題を取得
        $res = DB::table('results')
            ->get();

        // ユーザ名の配列を作成
        $user = $res->pluck("name")->all();
        // 重複を削除して並び替える
        $user = array_unique($user);
        asort($user);

        // ユーザ名の数だけ繰り返す
        foreach ( $user as $us ){
            $ures = array_keys($res->all(),$us);
        }


        var_dump($user);
    }
}
