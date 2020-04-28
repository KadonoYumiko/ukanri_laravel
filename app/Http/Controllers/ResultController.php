<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Result;

class ResultController extends Controller
{
    public function index()
    {
        // 結果テーブル「results」から対象のデータを取得
        $ressult = Result::where('name', Auth::user()->name)
            ->get();

    }
}
