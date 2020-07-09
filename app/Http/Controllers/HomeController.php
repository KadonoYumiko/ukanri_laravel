<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Result;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        // 提出課題を取得
        $res = DB::table('results')->where('name', Auth::user()->name)
            ->orderBy('kadno', 'asc')
            ->get();

        $data['res'] = '';

        $kadlist = array("01_1", "01_2", "02_1", "02_2", "02_3", "03_1", "03_2", "03_3", "03_4", "04_1", "04_2", "04_3", "05_1", "05_2", "06_1", "06_2", "07_1", "07_2", "07_3", "08_1", "08_2", "08_3");
        foreach ($kadlist as $param) {
            $i = array_search('k_' . $param . '.sh', array_column($res->all(),'kadno'));
            if ( $i !== false ){
                $score = $res[$i]->score;
            }else{
                $score = '未提出';
            }
            $data['res'] .= '<tr><td><label><input type="radio" name="kad" id="kad" value="' . $param . '">k_' . $param . '</label></td><td>' . $score . '</td></tr>';
        }
        return view('home', $data);
    }
}
