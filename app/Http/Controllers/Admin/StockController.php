<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Models\Structure;
use App\Models\Products;
use App\Models\Export;
use App\Models\ExportDetails;
use App\Models\Import;
use App\Models\ImportDetails;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Auth;

class StockController extends Controller
{
    public $main_page = 'categories';
    public $parent_id;
    public $path = 'json_data/categories.json';

    public function __construct()
    {
        $category = Structure::where('structure_url', $this->main_page)->first();
        $this->parent_id = $category->id;
    }
    
    public function index(){
        if (!isset($_GET['show'])){
            $limit = 30;
        }elseif ($_GET['show'] == 0){
            if (!isset($_GET['search']) || $_GET['search'] == ''){
                $limit = 100;
            }else{
                $limit = 9999999999;
            }
        }else{
            $limit = $_GET['show'];
        }

        if (!isset($_GET['search']) || $_GET['search'] == ''){
            $key = '';
        }else{
            $key = $_GET['search'];
        }

        $products = Products::select('products.*', 'str.structure_name', 'pro.product_name', 'br.posision','br.actual_quantity','br.2022 as begin',
            DB::raw('(SELECT SUM(quantity) FROM import_details AS imd WHERE imd.product_id = products.id) as import'),
            DB::raw('(SELECT SUM(quantity) FROM export_details AS exd WHERE exd.product_id = products.id) as export'),
            )
            ->join('structure_translations as str', 'str.structure_id', '=', 'products.parent_id')
            ->where('str.locale', Session::get('locale'))
            ->join('products_translations as pro', 'products.id', '=', 'pro.products_id')
            ->where('pro.locale', Session::get('locale'))->where('pro.product_name','LIKE','%'.$key.'%')
            ->join('products_details as br', 'br.product_id', '=', 'products.id')
            ->where('br.wh_id',1)
            ->orderBy('products.parent_id', 'ASC')->orderBy('pro.product_name', 'ASC')
            //->toSql();
            ->paginate($limit);
        //return $products;
        $products->appends(['show' => $limit,'search' => $key]);
        Session::put('stock_url',url()->full());
        return view('Stock.list', ['products' => $products,'limit'=>$limit,'key' => $key]);
    }

    public function stock($wh,$id){
        $product = Products::select('products.*', 'str.structure_name', 'pro.product_name', 'br.posision','br.actual_quantity','br.2022 as begin',
        DB::raw('(SELECT SUM(quantity) FROM import_details AS imd,import AS im WHERE imd.product_id = products.id AND im.id = imd.import_id AND im.wh_id = '.$wh->id.') as import'),
        DB::raw('(SELECT SUM(quantity) FROM export_details AS exd,export AS ex WHERE exd.product_id = products.id AND ex.id = exd.export_id AND ex.wh_id = '.$wh->id.') as export')
        )
        ->join('structure_translations as str', 'str.structure_id', '=', 'products.parent_id')
        ->where('str.locale',  Session::get('locale'))
        ->join('products_translations as pro', 'products.id', '=', 'pro.products_id')
        ->where('pro.locale',  Session::get('locale'))
        ->join('products_details as br', 'br.product_id', '=', 'products.id')
        ->where('br.wh_id',$wh->id)
        ->where('products.id',$id)
        ->first();
        $begin = (empty($product->begin)?0:$product->begin*1);
        $import = (empty($product->import)?0:$product->import*1);
        $export = (empty($product->export)?0:$product->export*1);
        $actual_quantity = (empty($product->actual_quantity)?'':$product->actual_quantity);
        $posision = (empty($product->posision)?'':$product->posision);
        $stock = $begin + $import - $export;
        $resutl = [
            'wh' => $wh->name,
            'begin' => $begin,
            'stock' => $stock,
            'import'=> $import,
            'export'=> $export,
            'posision' => $posision,
            'actual_quantity'=>$actual_quantity,
           ];
        return $resutl;
    }

    public function details($wh,$id){
        $product = Products::select('2022 AS begin')->join('products_details AS prod','products.id','=','prod.product_id')->where('products.id',$id)->where('wh_id',$wh)->first();
        $export = Export::select('date',DB::raw('1 AS "ie_type"'),'number','supplier','exd.quantity','exd.actual_quantity','exd.finished_quantity','exd.price','customers.name','export.id','export.wh_id')
        ->join('export_details AS exd','exd.export_id','=','export.id')
        ->join('customers','customers.id','=','export.supplier')
        ->where('wh_id',$wh)
        ->where('exd.product_id',$id)
        ->orderBy('date')->get();
        $import = Import::select('date',DB::raw('0 AS "ie_type"'),'number','supplier','imd.quantity','imd.actual_quantity','imd.finished_quantity','imd.price','customers.name','import.id','import.wh_id')
        ->join('import_details AS imd','imd.import_id','=','import.id')
        ->join('customers','customers.id','=','import.supplier')
        ->where('wh_id',$wh)
        ->where('imd.product_id',$id)
        ->orderBy('date')->get();
        $import = json_decode(json_encode($import),true);
        $export = json_decode(json_encode($export),true);
        $details = array_merge($import,$export);
        asort($details);
        if (!empty($product->begin)){
            $begin = $product->begin;
        }else{
            $begin = 0;
        }
        Session::put('detail_url',url()->current());
        //echo "<pre>";print_r($details);echo "</pre>";exit;
        return view('Stock.details',['details' => $details,'begin'=>$begin]);
    }

    public function ie_detail($wh,$type,$id){
        $active_menu = Structure::where('structure_url', Session::get('active_menu'))->first();
        $alert = unserialize($active_menu->trans_page);
        $extend_ids = array_filter(explode(',',Auth::user()->extends_users));
        $extend_ids[] = Auth::user()->id;
        //return $extend_ids;
        if ($type == 0){
            $ie = Import::select('import.*', 'staffs.staff_code', 'staffs.id AS staff_id', 'staffs.name','customers.id AS supplier_id','customers.name AS supplier_name')
            ->join('staffs', 'import.salesman', '=', 'staffs.id')
            ->join('customers', 'import.supplier', '=', 'customers.id')
            ->where('import.id', $id)
            ->first();

            $ie_details = ImportDetails::select('import_details.*', 'pro.calculate_type', 'trans.product_name', 'trans.product_unit','br.actual_quantity')
            ->join('products AS pro', 'pro.id', '=', 'import_details.product_id')
            ->join('products_translations as trans', 'pro.id', '=', 'trans.products_id')
            ->where('trans.locale', Session::get('locale'))
            ->where('import_id', $id)
            ->join('products_details as br', 'br.product_id', '=', 'pro.id')
            ->where('br.wh_id', $wh)
            ->get();
        }else{
            $ie = Export::select('export.*', 'staffs.staff_code', 'staffs.id AS staff_id', 'staffs.name','customers.id AS supplier_id','customers.name AS supplier_name')
            ->join('staffs', 'export.salesman', '=', 'staffs.id')
            ->join('customers', 'export.supplier', '=', 'customers.id')
            ->where('export.id', $id)->first();

            $ie_details = ExportDetails::select('export_details.*', 'pro.calculate_type', 'trans.product_name', 'trans.product_unit','br.actual_quantity')
            ->join('products AS pro', 'pro.id', '=', 'export_details.product_id')
            ->join('products_translations as trans', 'pro.id', '=', 'trans.products_id')
            ->where('trans.locale', Session::get('locale'))
            ->where('export_id', $id)
            ->join('products_details as br', 'br.product_id', '=', 'pro.id')
            ->where('br.wh_id', $wh)
            ->orderBy('id')
            ->get();
        }
        $type_arr = [['buy','change_warehouse','return'],['sell','change_warehouse','destroy']];

        if (!$ie) {
            return redirect()->back()->with(['type' => 'danger', 'alert_messenge' => $alert['error'] . ' ' . $alert['data_error']]);
        }
        
        return view('Stock.import-export', ['ie' => $ie, 'ie_details' => $ie_details,'type'=>$type,'type_arr'=> $type_arr]);
    }
}
