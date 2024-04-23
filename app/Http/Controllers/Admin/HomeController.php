<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Import;
use App\Models\Export;
use App\Models\Warehouse;

class HomeController extends Controller
{
    public function index(){
        $month = date('m');
        $year = date('Y');
        $sell=[];
        $warehouse = Warehouse::get();
        foreach ($warehouse as $wh){
            $return = [];
            $sell = [];
            for($i=1; $i<=$month;$i++){
                $imports = Import::where('type',3)->where('wh_id',$wh->id)->where('date','>=',$year.'-'.$i.'-01')->where('date','<=',$year.'-'.$i.'-31')->get();
                $value = $imports->count();
                $return[] = $value;
            }
            $data[$wh->code]['return'] = $return;
            for($i=1; $i<=$month;$i++){
                $exports = Export::where('type',1)->where('wh_id',$wh->id)->where('date','>=',$year.'-'.$i.'-01')->where('date','<=',$year.'-'.$i.'-31')->get();
                $value = $exports->count();
                $sell[] = $value;
            }
            $data[$wh->code]['sell'] = $sell;
        }

        //echo "<pre>";
        //print_r($data['HCM']);
        //$data = json_encode($data['HCM']['sell']);
        $hcm = $data['HCM']['sell'];
        //print_r($data);
        //echo "</pre>";
        //exit;
        
        // $dataPoints1 = array(
        //     array("label"=> "2010", "y"=> 36.12),
        //     array("label"=> "2011", "y"=> 34.87),
        //     array("label"=> "2012", "y"=> 40.30),
        //     array("label"=> "2013", "y"=> 35.30),
        //     array("label"=> "2014", "y"=> 39.50),
        //     array("label"=> "2015", "y"=> 50.82),
        //     array("label"=> "2016", "y"=> 74.70)
        // );
        // echo json_encode($dataPoints1, JSON_NUMERIC_CHECK);exit;
        // $dataPoints2 = array(
        //     array("label"=> "2010", "y"=> 64.61),
        //     array("label"=> "2011", "y"=> 70.55),
        //     array("label"=> "2012", "y"=> 72.50),
        //     array("label"=> "2013", "y"=> 81.30),
        //     array("label"=> "2014", "y"=> 63.60),
        //     array("label"=> "2015", "y"=> 69.38),
        //     array("label"=> "2016", "y"=> 98.70)
        // );
        return view('Home.index',['data'=>$data,'hcm'=>$hcm]);
    }
}
