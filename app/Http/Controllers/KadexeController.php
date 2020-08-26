<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use LengthException;
use App\Result;
use Exception;

class KadexeController extends Controller
{
    private static function cmdexec($filename, $kadno, $param, &$tblval, &$okcnt, &$tcnt)
    {
        //      ローカル環境用
        //        $cmd = '"C:\Program Files\Git\bin\sh"' . ' ../../kadans/k_' . $kadno . '.sh' . $param;
        $cmd = 'bash' . ' ../../kadans/k_' . $kadno . '.sh' . $param . ' 2>&1';
        exec($cmd, $opt, $ret);
        //        $cmd2 = 'bash' . ' ' . $filename . $param;
        $cmd2 = 'bash' . ' k_' . $kadno . '.sh' . $param . ' 2>&1';
        exec($cmd2, $opt2, $ret2);

        for ($i = 0; $i < count($opt); $i++) {
            $opt[$i] = mb_convert_kana($opt[$i],'kvrns');
            $opt2[$i] =mb_convert_kana($opt2[$i],'kvrns');

            $tblval[$tcnt] = '<tr>';
            if ($i < count($opt2) && $opt[$i] == $opt2[$i]) {
                $tblval[$tcnt] .= '<td>○</td>';
                $okcnt++;
            } else {
                $tblval[$tcnt] .= '<td style="color:red">×</td>';
            }
            $tblval[$tcnt] .= '<td>' . ($i + 1) . '</td><td>';
            if ($i < count($opt2)) {
                $tblval[$tcnt] .= $opt2[$i];
            }
            $tblval[$tcnt++] .= '</td><td>' . $opt[$i] . '</td></tr>';
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
    public function post(Request $request)
    {
        $tempfile = $_FILES['kadfile']['tmp_name'];    // 一時ファイル名
        $filename = '../../../../usr/' . Auth::user()->name . '/' . $_FILES['kadfile']['name'];        // 本来のファイル名
        //$filename = '../../usr/' . 'in3a01' . '/' . $_FILES['kadfile']['name'];        // 本来のファイル名

        //時刻入力欄のバリデーション。
        $validated_data = [
            'kad' => 'required',
            'kadfile' => 'required'
        ];
        $errormsg = [
            'kad.required' => 'アップロードする課題番号を選択してください',
            'kadfile.required' => 'アップロードするファイルを指定してください'
        ];
        $request->validate($validated_data, $errormsg);

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
        $param = [
            '01_1' => '', '01_2' => [' kad01', ' kad02'],
            '02_1' => '', '02_2' => '', '02_3' => [' 01', ' 02'],
            '03_1' => [' y', ' n', ' a'], '03_2' => '', '03_3' => [' k_03_1.sh', ' k_03_6.sh'], '03_4' => ['', '0', '13', ' 03'],
            '06_1' => [' 192.168.1.1', ' 10.201.10.13', ' 10.201.10.18'], '06_2' => 'noexec',
            '04_1' => 'noexec', '04_2' => ' k_04_1 k_04_2 k_04_4', '04_3' => 'noexec',
            //                    '06_1' => ' 192.168.1.1', '06_2' => 'noexec',
            '05_1' => 'noexec', '05_2' => ' k_05_1 k_05_2 k_05_3',
            '07_1' => ' id.lst', '07_2' => ['', ' data2.csv', ' data3.csv'], '07_3' => ['', ' aaa', ' phone.txt'],
            '08_1' =>  ' phone.txt', '08_2' => 'noexec', '08_3' => [' 10.201.10', ''],
            '09_1' =>   [' score.csv', ''], '09_2' => '',
            '10_1' => 'noexec', '10_2' => 'noexec',
            '11_1' => 'noexec', '11_2' => 'noexec', '11_3' => 'noexec',
            '12_1' => 'noexec', '12_2' => 'noexec'
        ];


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

        // 自分の課題提出ディレクトリに移動してから実行する
        chdir('../../../../usr/' . Auth::user()->name . '/');

        if (is_array($param[$kadno])) {
            foreach ($param[$kadno] as $param) {
                $tblval[$tcnt++] = '<tr><th colspan="4">パラメータ:' . $param . '</th></tr>';
                $cnt++;
                KadexeController::cmdexec($filename, $kadno, $param, $tblval, $okcnt, $tcnt);
            }
        } else {
            // パラメータが実行しないの場合は実行しない
            if ($param[$kadno] != 'noexec') {
                KadexeController::cmdexec($filename, $kadno, $param[$kadno], $tblval, $okcnt, $tcnt);
            }
        }

        //        $tblval[$tcnt] = '</table><h2>得点</h2><h2>' . (round($okcnt / ($tcnt - $cnt) * 100)) . '</h2>';

        $data['msg'] = $tblval;
        try {
            $score = round($okcnt / ($tcnt - $cnt) * 100);
        } catch (Exception $e) {
            $score = 0;
        }
        $data['score'] = '</table><h2>得点</h2><h2>' . $score . '</h2>';

        // 対象レコードがあればUpdate、無ければInsert
        Result::where('name', Auth::user()->name)
            ->where('kadno', 'k_' . $kadno . '.sh')
            ->updateOrInsert([], ['name' => Auth::user()->name, 'kadno' => 'k_' . $kadno . '.sh', 'score' => $score, 'result' => implode($tblval)]);

        // 提出課題を取得
        $res = DB::table('results')->where('name', Auth::user()->name)
            ->orderBy('kadno', 'asc')
            ->get();

        $data['res'] = '';

        $kadlist = array("01_1", "01_2", "02_1", "02_2", "02_3", "03_1", "03_2", "03_3", "03_4", "04_1", "04_2", "04_3", "05_1", "05_2", "06_1", "06_2", "07_1", "07_2", "07_3", "08_1", "08_2", "08_3", "09_1", "09_2", "10_1", "10_2", "11_1", "11_2", "11_3", "12_1", "12_2");
        foreach ($kadlist as $param) {
            $i = array_search('k_' . $param . '.sh', array_column($res->all(), 'kadno'));
            if ($i !== false) {
                $score = $res[$i]->score;
            } else {
                $score = '未提出';
            }
            $data['res'] .= '<tr><td><label><input type="radio" name="kad" id="kad" value="' . $param . '">k_' . $param . '</label></td><td>' . $score . '</td></tr>';
        }

        return view('home', $data);
    }
}
