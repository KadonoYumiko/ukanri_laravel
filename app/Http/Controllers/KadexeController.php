<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use LengthException;
use App\Result;

class KadexeController extends Controller
{
    private static function cmdexec($filename, $kadno, $param, &$tblval, &$okcnt, &$tcnt)
    {
        $cmd = 'sh' . ' ' . $filename . $param;
        exec($cmd, $opt, $ret);
        $cmd2 = 'sh' . ' ../../kadans/k_' . $kadno . '.sh' . $param;
        exec($cmd2, $opt2, $ret2);

        for ($i = 0; $i < count($opt); $i++) {
            $tblval[$tcnt] = '<tr>';
            if ($opt[$i] == $opt2[$i]) {
                $tblval[$tcnt] .= '<td>○</td>';
                $okcnt++;
            } else {
                $tblval[$tcnt] .= '<td style="color:red">×</td>';
            }
            $tblval[$tcnt++] .= '<td>' . ($i + 1) . '</td><td>' . $opt[$i] . '</td><td>' . $opt2[$i] . '</td></tr>';
        }
    }

    public function index()
    {
        //         //テンプレートに渡すデータ
        //         $cmd = 'sh ../kadans/k_01_2.sh kad01';
        // //        $cmd = '@cd';
        //         exec($cmd, $opt, $ret);
        //         $data['msg'] = $opt;
        // //        $data = ['msg'=>'Hello World!!'];
        //         return view('kadexe', $data);
        return view('kadexe');
    }
    public function post()
    {
        $tempfile = $_FILES['kadfile']['tmp_name'];    // 一時ファイル名
        $filename = '../../usr/' . Auth::user()->name . '/' . $_FILES['kadfile']['name'];        // 本来のファイル名
        //$filename = '../../usr/' . 'in3a01' . '/' . $_FILES['kadfile']['name'];        // 本来のファイル名

        if (is_uploaded_file($tempfile)) {
            if (move_uploaded_file($tempfile, $filename)) {
                //                echo $filename . "をアップロードしました。";
            } else {
                //                echo "ファイルをアップロードできません。";
            }
        } else {
            //            echo "ファイルが選択されていません。";
        }

        //パラメータセット
        $param = ['01_1' => '', '01_2' => [' kad01', ' kad02']];


        //シェルスクリプト実行
        $kadno = $_POST['kad'];   //課題番号

        // 結果表示文字列
        $tblval[0] = '<table border="1"><tr><th>○×</th><th>行数</th><th>解答</th><th>正答</th></tr>';
        // 正解数カウント
        $okcnt = 0;
        // 結果の行数
        $tcnt = 1;
        // 正解に関係ない行数
        $cnt = 1;

        if (is_array($param[$kadno])) {
            foreach ($param[$kadno] as $param) {
                $tblval[$tcnt++] = '<tr><th colspan="4">パラメータ:' . $param . '</th></tr>';
                $cnt++;
                KadexeController::cmdexec($filename, $kadno, $param, $tblval, $okcnt, $tcnt);
            }
        } else {
            KadexeController::cmdexec($filename, $kadno, $param[$kadno], $tblval, $okcnt, $tcnt);
        }

        //        $tblval[$tcnt] = '</table><h2>得点</h2><h2>' . (round($okcnt / ($tcnt - $cnt) * 100)) . '</h2>';

        $data['msg'] = $tblval;
        $score = round($okcnt / ($tcnt - $cnt) * 100);
        $data['score'] = '</table><h2>得点</h2><h2>' . $score . '</h2>';

        // 対象レコードがあればUpdate、無ければInsert
        Result::where('name', Auth::user()->name)
            ->where('kadno', 'k_' . $kadno . '.sh')
            ->updateOrInsert([], ['name' => Auth::user()->name, 'kadno' => 'k_' . $kadno . '.sh', 'score' => $score, 'result' => implode($tblval)]);

        return view('home', $data);
    }
}
