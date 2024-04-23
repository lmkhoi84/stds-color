<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Structure;
use App\Models\Products;
use App\Models\ProductsTranslation;
use App\Models\ProductsDetails;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

class ProductsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

    public $main_page = 'products';
    //public $permissions;
    public $langs;
    public $alert;

    public function __construct()
    {
        $this->langs = LanguagesController::getActiveLangs();
        $this->alert = $this->getTranslate();
    }
    
    public function index()
    {
        // if (isset($_GET['sd']) || isset($_GET['ed'])) {
        //     $start_date = $_GET['sd'];
        //     $end_date = $_GET['ed'];
        // } else {
        //     $end_date = date('Y-m-d');
        //     $start_date = date('2022-01-01');
        // }

        if (!isset($_GET['show'])) {
            $limit = 30;
        } elseif ($_GET['show'] == 0) {
            if (!isset($_GET['search']) || $_GET['search'] == '') {
                $limit = 100;
            } else {
                $limit = 9999999999;
            }
        } else {
            $limit = $_GET['show'];
        }

        if (!isset($_GET['search']) || $_GET['search'] == '') {
            $key = '';
        } else {
            $key = $_GET['search'];
        }

        $list = $this->getFullList($key,$limit);
        $list->appends(['show' => $limit,'search' => $key]);
        return view('products.list', ['list' => $list,'add' => true,'search' => true, 'limit' => $limit, 'key' => $key]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function addNew()
    {
        $pList = $this->getAutocompleteList();
        $list = [];
        foreach ($pList as $pItem) {
            $list[] = "{
                value: '" . $pItem["id"] . "',
                label: '" . addslashes($pItem["code"]) . "',
                desc: '" . addslashes($pItem["name"]) . "',
                unit: '" . $pItem["unit"] . "'
            }";
        }
        $p_list = "[" . implode(',', $list) . "]";
        
        $mList = MaterialsController::getAutocompleteList();
        $list = [];
        foreach ($mList as $mItem) {
            $list[] = "{
                value: '" . $mItem["id"] . "',
                label: '" . addslashes($mItem["crayola_code"]) . " / ". addslashes($mItem["crayola_name"]) ."',
                pcode: '" . addslashes($mItem["producer_code"]). " / ". addslashes($mItem["producer_name"]) ."',
                type: '" . ($mItem["type"]==1?$this->alert['powder']:$this->alert['liquid']) . "',
                unit: '" . $mItem["unit"] . "'
            }";
        }
        $m_list = "[" . implode(',', $list) . "]";
        return view('products.add', ['pList' => $p_list,'mList' => $m_list,'add' => false]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $this->validate($request, [
            'productName_vi' => 'required',
            'code' => 'required',
            'unit_vi' => 'required',
        ], [
            'productName_vi.required' => $this->alert['name_required'],
            'code.required' => $this->alert['code_required'],
            'unit_vi.numeric' => $this->alert['unit_required']
        ]);
        $data = $request->all();
        $request->session()->put('data', $data);
        foreach ($data as $key => $value) {
            $request->session()->put($key, $value);
        }

        $checkExist = $this->getProductCode($data['code']);
        if ($checkExist){
            return redirect()->back()->with(['type' => 'danger', 'alert_messenge' => $this->alert['error'] .' '. $this->alert['code'].' "'.$data["code"] .'" '. $this->alert['isExist']]);
        }
        $totalPercent = 0;
        for($i=1;$i<=$data['items'];$i++){
            $totalPercent += $data['percentage_'.$i]*1;
        }
        if($totalPercent != 100){
            return redirect()->back()->with(['type' => 'danger', 'alert_messenge' => $this->alert['error'] .' '. $this->alert['totalPercent']]);
        }

        $checkDublicatematerial = $this->checkDublicateMaterial($data);
        if ($checkDublicatematerial){
            return redirect()->back()->with(['type' => 'danger', 'alert_messenge' => $this->alert['error'] .' '. $this->alert['haveDubMaterial']]);
        }

        $this->addProduct($data);

        foreach ($data as $key => $value) {
            $request->session()->forget($key, $value);
        }
        return redirect()->back()->with(['type' => 'success', 'alert_messenge' => $this->alert['success'] . ' "' . $data["code"] . '" ' . $this->alert['addSuccess']]);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $item = $this->getProductById($id);
        if (!$item){
            return redirect('products')->with(['type' => 'danger', 'alert_messenge' => $this->alert['error'] .' '. $this->alert['dataNotExist']]);
        }

        $itemDetails = $this->getProductComposion($id);

        $pList = $this->getAutocompleteList();
        $list = [];
        foreach ($pList as $pItem) {
            $list[] = "{
                value: '" . $pItem["id"] . "',
                label: '" . addslashes($pItem["code"]) . "',
                desc: '" . addslashes($pItem["name"]) . "',
                unit: '" . $pItem["unit"] . "'
            }";
        }
        $p_list = "[" . implode(',', $list) . "]";
        
        $mList = MaterialsController::getAutocompleteList();
        $list = [];
        foreach ($mList as $mItem) {
            $list[] = "{
                value: '" . $mItem["id"] . "',
                label: '" . addslashes($mItem["crayola_code"]) . " / ". addslashes($mItem["crayola_name"]) ."',
                pcode: '" . addslashes($mItem["producer_code"]). " / ". addslashes($mItem["producer_name"]) ."',
                type: '" . ($mItem["type"]==1?$this->alert['powder']:$this->alert['liquid']) . "',
                unit: '" . $mItem["unit"] . "'
            }";
        }
        $m_list = "[" . implode(',', $list) . "]";

        $langs = LanguagesController::getActiveLangs();
        foreach ($langs as $lang) {
            $menu_trans = $this->getNameTranslate($id,$lang->name);
            if ($menu_trans) {
                $name[$lang->name] = $menu_trans->name;
                $unit[$lang->name] = $menu_trans->unit;
            } else {
                $name[$lang->name] = '';
                $unit[$lang->name] = '';
            }
        }
        return view('products.show', ['item' => $item, 'itemDetails' => $itemDetails,'pList' => $p_list,'mList' => $m_list,'name' =>$name,'unit' => $unit,'add' => false]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $item = $this->getProductById($id);
        if (!$item){
            return redirect('products')->with(['type' => 'danger', 'alert_messenge' => $this->alert['error'] .' '. $this->alert['dataNotExist']]);
        }

        $itemDetails = $this->getProductComposion($id);

        $pList = $this->getAutocompleteList();
        $list = [];
        foreach ($pList as $pItem) {
            $list[] = "{
                value: '" . $pItem["id"] . "',
                label: '" . addslashes($pItem["code"]) . "',
                desc: '" . addslashes($pItem["name"]) . "',
                unit: '" . $pItem["unit"] . "'
            }";
        }
        $p_list = "[" . implode(',', $list) . "]";
        
        $mList = MaterialsController::getAutocompleteList();
        $list = [];
        foreach ($mList as $mItem) {
            $list[] = "{
                value: '" . $mItem["id"] . "',
                label: '" . addslashes($mItem["crayola_code"]) . " / ". addslashes($mItem["crayola_name"]) ."',
                pcode: '" . addslashes($mItem["producer_code"]). " / ". addslashes($mItem["producer_name"]) ."',
                type: '" . ($mItem["type"]==1?$this->alert['powder']:$this->alert['liquid']) . "',
                unit: '" . $mItem["unit"] . "'
            }";
        }
        $m_list = "[" . implode(',', $list) . "]";

        $langs = LanguagesController::getActiveLangs();
        foreach ($langs as $lang) {
            $menu_trans = $this->getNameTranslate($id,$lang->name);
            if ($menu_trans) {
                $name[$lang->name] = $menu_trans->name;
                $unit[$lang->name] = $menu_trans->unit;
            } else {
                $name[$lang->name] = '';
                $unit[$lang->name] = '';
            }
        }
        return view('products.edit', ['item' => $item, 'itemDetails' => $itemDetails,'pList' => $p_list,'mList' => $m_list,'name' =>$name,'unit' => $unit,'add' => false]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $data = $request->all();
        $request->session()->put('data', $data);
        foreach ($data as $key => $value) {
            $request->session()->put($key, $value);
        }

        $id = $data['item_id'];
        $item = $this->getProductById($id);
        if (!$item){
            return redirect()->back()->with(['type' => 'danger', 'alert_messenge' => $this->alert['error'] .' '. $this->alert['dataNotExist']]);
        }

        $this->validate($request, [
            'productName_vi' => 'required',
            'code' => 'required',
            'unit_vi' => 'required',
        ], [
            'productName_vi.required' => $this->alert['name_required'],
            'code.required' => $this->alert['code_required'],
            'unit_vi.numeric' => $this->alert['unit_required']
        ]);

        $checkDublicate = $this->getDublicate($id,$data["code"]);
        if ($checkDublicate){
            return redirect()->back()->with(['type' => 'danger', 'alert_messenge' => $this->alert['error'] .' '. $this->alert['code'].' "'.$data["code"] .'" '. $this->alert['isExist']]);
        }

        $totalPercent = 0;
        for($i=1; $i<=$data['items']; $i++){
            if( empty($data['materialCode_'.$i]) || empty($data['materialId_'.$i]) || empty($data['percentage_'.$i]) ){
                continue;
            }else{
                $totalPercent += $data['percentage_'.$i];
            }
        }

        $checkDublicatematerial = $this->checkDublicateMaterial($data);
        if ($checkDublicatematerial){
            return redirect()->back()->with(['type' => 'danger', 'alert_messenge' => $this->alert['error'] .' '. $this->alert['haveDubMaterial']]);
        }

        if ($totalPercent != 100){
            return redirect()->back()->with(['type' => 'danger', 'alert_messenge' => $this->alert['error'] .' '. $this->alert['totalPercent']]);
        }       

        $this->updateProduct($id,$data);

        foreach ($data as $key => $value) {
            $request->session()->forget($key, $value);
        }

        return redirect($request->previous_page)->with(['type' => 'success', 'alert_messenge' => $this->alert['success'] . ' "' . $data['code'] . '" ' . $this->alert['updateSuccess']]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $item = $this->getProductById($id);
        if (!$item){
            return redirect()->back()->with(['type' => 'danger', 'alert_messenge' => $this->alert['error'] .' '. $this->alert['dataNotExist']]);
        }

        $code = $item->code;
        //Nếu sản phẩm đã từng được pha chế -> không cho xóa
        $used = false;
        if($used){
            return redirect()->back()->with(['type' => 'danger', 'alert_messenge' => $this->alert['error']. ' "' . $code. '" ' . $this->alert['used']]);
        }
        //Chưa được sử dụng (mới tạo, nhập sai) -> thực hiện xóa
        
        $item->delete();
        return redirect($this->main_page)->with(['type' => 'success', 'alert_messenge' => $this->alert['success'] . ' "' . $code. '" ' . $this->alert['deleted']]);
    
    }

    //Private function
    private function getTranslate()
    {
        $struct = $this->getStrucUrlByActive();
        return unserialize($struct->trans_page);
    }

    private function getStrucUrlByActive()
    {
        $strucUrl = Structure::where('structure_url', $this->main_page)->first();
        return $strucUrl;
    }
    
    // Get list,item
    private function getFullList($key,$limit){
        $list = Products::select('products.id','products.code', 'products.status','products.items','pt.name','pt.unit')
        ->leftJoin('products_translations AS pt','pt.products_id','=','products.id')
        ->where('pt.locale',Session::get('locale'))
        ->where(function ($query) use ($key) {
            $query->orWhere('code', 'LIKE', '%' . $key . '%');
            $query->orWhere('name', 'LIKE', '%' . $key . '%');
            $query->orWhere('unit', 'LIKE', '%' . $key . '%');
        })
        ->orderBy('products.code')->orderBy('pt.name')
        ->paginate($limit);
        return $list;
    }

    private function getProductComposion($id){
        $list = ProductsDetails::select('products_details.*','mt.crayola_name','mt.unit','m.crayola_code','m.type')
        ->leftJoin('materials AS m','m.id','=','products_details.materials_id')
        ->leftJoin('materials_translations as mt','mt.materials_id','=','products_details.materials_id')
        ->where('products_details.products_id',$id)
        ->where('mt.locale',Session::get('locale'))
        ->get();
        return $list;
    }

    private function getProductCode($code){
        $item = Products::where('code',$code)->first();
        return $item;
    }
    
    private function getDublicate($id,$code){
        $dub = Products::where('id','!=',$id)->where('code',$code)->first();
        if ($dub) return true; else return false;
    }

    private function getProductById($id){
        $item = Products::find($id);
        return $item;
    }

    private function getNameTranslate($id,$lang){
        $item = ProductsTranslation::where('products_id', $id)->where('locale', $lang)->first();
        return $item;
    }

    private function getAutocompleteList(){
        $list = Products::select('products.id', 'products.code', 'pt.name', 'pt.unit')
        ->leftJoin('products_translations AS pt','pt.products_id','=','products.id')
        ->where('pt.locale',Session::get('locale'))
        ->orderBy('id','DESC')
        ->get();
        return $list;
    }

    private function getItems($productId){
        return ProductsDetails::where('products_id',$productId)->count('materials_id');
    }

    private function checkDublicateMaterial($data){
        for( $i=0; $i<$data['items']; $i++){
            $arr[$i] = $data['materialId_'.($i + 1)];
        }
        if(count(array_unique($arr)) < $data['items'] ) return true;
        else return false;
    }

    // Add item, translation
    private function addProduct($data){
        $item = new Products();
        $item->code = $data["code"];
        $item->status = $data["status"];
        $item->items = $data['items'];
        $item->formula = $data['formula'];
        $item->created_user = Auth::user()->id;
        $item->save();
        $this->addProductTranslation($item->id,$data);
        $this->addProductDetails($item->id,$data);
    }

    private function addProductTranslation($products_id,$data){
        $langs = LanguagesController::getActiveLangs();
        foreach($langs as $lang){
            $item = new ProductsTranslation();
            $item->products_id = $products_id;
            $item->name = $data["productName_".$lang->name];
            $item->unit = $data["unit_".$lang->name];
            $item->locale = $lang->name;
            $item->save();
        }
    }

    private function addProductDetails($products_id,$data){
        $count = 0;
        for ($i = 1; $i <= $data['items'];$i++){
            if( empty($data['materialCode_'.$i]) || empty($data['materialId_'.$i]) || empty($data['percentage_'.$i]) ){
                continue;
            }
            else{
                $item = new ProductsDetails();
                $item->products_id = $products_id;
                $item->materials_id = $data['materialId_'.$i];
                $item->percentage = $data['percentage_'.$i];
                $item->accuracy = $data['accuracy_'.$i];
                $item->created_user = Auth::user()->id;
                $item->save();
                $count ++;
            }
        }
        $pro = $this->getProductById($products_id);
        $pro->items = $count;
        $pro->save();
    }

    //Update item, translation
    private function updateProduct($id,$data){
        $item = $this->getProductById($id);
        $item->code = $data['code'];
        $item->status = $data['status'];
        $item->updated_user = Auth::user()->id;
        $item->updated_at = date("Y-m-d h:m:s",time());
        $item->save();
        $this->updateProductTranslation($id,$data);
        $this->updateProductDetails($id,$data);

        $num = $this->getItems($id);
        $item = $this->getProductById($id);
        $item->items = $num;
        $item->save();
    }

    private function updateProductTranslation($product_id,$data){
        $langs = LanguagesController::getActiveLangs();
        foreach ($langs as $lang){
            $item = ProductsTranslation::where('locale',$lang->name)->where('products_id',$product_id)->first();
            $item->name = $data['productName_'.$lang->name];
            $item->unit = $data['unit_'.$lang->name];
            $item->save();
        }
    }

    private function updateProductDetails($product_id,$data){
        for ($i = 1; $i <= $data['items'];$i++){
            if (empty($data['idDetail_'.$i])){
                if( empty($data['materialCode_'.$i]) || empty($data['materialId_'.$i]) || empty($data['percentage_'.$i]) ){
                    continue;
                }
                else{
                    $item = new ProductsDetails();
                    $item->products_id = $product_id;
                    $item->materials_id = $data['materialId_'.$i];
                    $item->percentage = $data['percentage_'.$i];
                    $item->accuracy = $data['accuracy_'.$i];
                    $item->created_user = Auth::user()->id;
                    $item->save();
                }
            }else{
                $item = ProductsDetails::find($data['idDetail_'.$i]);
                if ($item){
                    if( empty($data['materialCode_'.$i]) || empty($data['percentage_'.$i]) ){
                        $item->delete();
                    }else{
                        $item->products_id = $product_id;
                        $item->materials_id = $data['materialId_'.$i];
                        $item->percentage = $data['percentage_'.$i];
                        $item->accuracy = $data['accuracy_'.$i];
                        $item->updated_user = Auth::user()->id;
                        $item->updated_at = date("Y-m-d h:m:s",time());
                        $item->save();
                    }
                }
            }
        }      
        
        $del_items = array_filter(explode(',',$data['delete_items']),function ($k) {
            return $k != '';
        });

        if (count($del_items) > 0){
            for($i = 0; $i < count($del_items); $i++){
                if (!empty($del_items[$i])){
                    ProductsDetails::where('id',$del_items[$i])->delete();
                }
            }
        }
    }

    //public function
    public static function getUsedMaterialById($material_id){
        $check = ProductsDetails::where('materials_id',$material_id)->first();
        if ($check) return true;
        else return false;
    }
}
