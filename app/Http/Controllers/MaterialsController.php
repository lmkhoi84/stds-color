<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Structure;
use App\Models\Materials;
use App\Models\MaterialsTranslation;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

class MaterialsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

    public $main_page = 'materials';
    //public $permissions;
    public $langs;
    public $alert;
 
    public function __construct()
    {
        $this->langs = LanguagesController::getActiveLangs();
        $this->alert = $this->getTranslate();
        //$category = StructuresController::getStrucUrl($this->main_page);
        //$this->pid = $category->id;
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
        return view('materials.list', ['list' => $list,'add' => true,'search' => true, 'limit' => $limit, 'key' => $key]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function addNew()
    {
        $mList = $this->getAutocompleteList();
        $list = [];
            foreach ($mList as $item) {
                $list[] = "{
                    value: '" . $item["id"] . "',
                    label: '" . addslashes($item["crayola_code"]) . "',
                    desc: '" . addslashes($item["crayola_name"]) . "',
                    pcode: '" . addslashes($item["producer_code"]) . "',
                    pname: '" . addslashes($item["producer_name"]) . "',
                    unit: '" . $item["unit"] . "'
                }";
            }
            $m_list = "[" . implode(',', $list) . "]";
        return view('materials.add', ['list' => $m_list,'add' => false]);
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
            'crayolaName_vi' => 'required',
            'producerName_vi' => 'required',
            'crayolaCode' => 'required',
            'producerCode' => 'required',
            'unit_vi' => 'required',
        ], [
            'crayolaName_vi.required' => $this->alert['crayola_name_required'],
            'crayolaCode.required' => $this->alert['crayola_code_required'],
            'producerName_vi.required' => $this->alert['producer_name_required'],
            'producerCode.required' => $this->alert['producer_code_required'],
            'unit_vi.numeric' => $this->alert['unit_required']
        ]);
        $data = $request->all();
        $request->session()->put('data', $data);
        foreach ($data as $key => $value) {
            $request->session()->put($key, $value);
        }

        $checkExist = $this->getMaterialByCode($data["crayolaCode"],$data["producerCode"]);
        
        if ($checkExist){
            return redirect()->back()->with(['type' => 'danger', 'alert_messenge' => $this->alert['error'] .' '. $this->alert['code'].' "'.$data["crayolaCode"] .'" '. $this->alert['isExist']]);
        }
        
        $this->addMaterial($data);

        foreach ($data as $key => $value) {
            $request->session()->forget($key, $value);
        }
        return redirect()->back()->with(['type' => 'success', 'alert_messenge' => $this->alert['success'] . ' "' . $data["crayolaCode"] . '" ' . $this->alert['addSuccess']]);
        
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $item = $this->getMaterialById($id);
        if (!$item){
            return redirect('materials')->with(['type' => 'danger', 'alert_messenge' => $this->alert['error'] .' '. $this->alert['dataNotExist']]);
        }
        $mList = $this->getAutocompleteList();
        $list = [];
            foreach ($mList as $mItem) {
                $list[] = "{
                    value: '" . $item["id"] . "',
                    label: '" . addslashes($item["crayola_code"]) . "',
                    desc: '" . addslashes($item["crayola_name"]) . "',
                    pcode: '" . addslashes($item["producer_code"]) . "',
                    pname: '" . addslashes($item["producer_name"]) . "',
                    unit: '" . $item["unit"] . "'
                }";
            }
            $m_list = "[" . implode(',', $list) . "]";

        $langs = LanguagesController::getActiveLangs();
        foreach ($langs as $lang) {
            $menu_trans = $this->getNameTranslate($id,$lang->name);
            if ($menu_trans) {
                $crayolaName[$lang->name] = $menu_trans->crayola_name;
                $producerName[$lang->name] = $menu_trans->producer_name;
                $unit[$lang->name] = $menu_trans->unit;
            } else {
                $crayolaName[$lang->name] = '';
                $producerName[$lang->name] = '';
                $unit[$lang->name] = '';
            }
        }
        return view('materials.edit', ['item' => $item,'cName' => $crayolaName,'pName' => $producerName, 'unit' => $unit,'list' => $m_list,'add' => false]);
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

        $id = $data['materialId'];
        $item = $this->getMaterialById($id);
        if (!$item){
            return redirect()->back()->with(['type' => 'danger', 'alert_messenge' => $this->alert['error'] .' '. $this->alert['dataNotExist']]);
        }

        $this->validate($request, [
            'crayolaName_vi' => 'required',
            'producerName_vi' => 'required',
            'crayolaCode' => 'required',
            'producerCode' => 'required',
            'unit_vi' => 'required',
        ], [
            'crayolaName_vi.required' => $this->alert['crayola_name_required'],
            'crayolaCode.required' => $this->alert['crayola_code_required'],
            'producerName_vi.required' => $this->alert['producer_name_required'],
            'producerCode.required' => $this->alert['producer_code_required'],
            'unit_vi.numeric' => $this->alert['unit_required']
        ]);

        $checkDublicate = $this->getDublicate($id,$data["crayolaCode"],$data["producerCode"]);
        if ($checkDublicate){
            return redirect()->back()->with(['type' => 'danger', 'alert_messenge' => $this->alert['error'] .' '. $this->alert['code'].' "'.$data["crayolaCode"] .'" '. $this->alert['isExist']]);
        }

        $this->updateMaterial($id,$data);

        foreach ($data as $key => $value) {
            $request->session()->forget($key, $value);
        }

        return redirect($request->previous_page)->with(['type' => 'success', 'alert_messenge' => $this->alert['success'] . ' "' . $data['crayolaCode'] . '" ' . $this->alert['updateSuccess']]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $item = $this->getMaterialById($id);
        if (!$item){
            return redirect()->back()->with(['type' => 'danger', 'alert_messenge' => $this->alert['error'] .' '. $this->alert['dataNotExist']]);
        }

        $code = $item->crayola_code;
        //Nếu nguyên vật liệu đã được sử dụng -> không cho xóa
        $used = ProductsController::getUsedMaterialById($id);
        if($used){
            return redirect()->back()->with(['type' => 'danger', 'alert_messenge' => $this->alert['error']. ' "' . $code. '" ' . $this->alert['used']]);
        }
        //Chưa được sử dụng -> thực hiện xóa
        
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
        $list = Materials::select('materials.*','mt.crayola_name','mt.producer_name','mt.unit')
        ->leftJoin('materials_translations AS mt','mt.materials_id','=','materials.id')
        ->where('mt.locale',Session::get('locale'))
        ->where(function ($query) use ($key) {
            $query->orWhere('crayola_code', 'LIKE', '%' . $key . '%');
            $query->orWhere('producer_code', 'LIKE', '%' . $key . '%');
            $query->orWhere('crayola_name', 'LIKE', '%' . $key . '%');
            $query->orWhere('producer_name', 'LIKE', '%' . $key . '%');
            $query->orWhere('unit', 'LIKE', '%' . $key . '%');
        })
        ->orderBy('materials.id','DESC')
        ->paginate($limit);
        return $list;
    }

    private function getMaterialByCode($crayola_code,$producer_code){
        $material = Materials::where('crayola_code',$crayola_code)->where('producer_code',$producer_code)->first();
        return $material;
    }
    
    private function getDublicate($id,$crayola_code,$producer_code){
        $dub = Materials::where('id','!=',$id)->where('crayola_code',$crayola_code)->where('producer_code',$producer_code)->first();
        if ($dub) return true; else return false;
    }

    private function getNameTranslate($id,$lang){
        $item = MaterialsTranslation::where('materials_id', $id)->where('locale', $lang)->first();
        return $item;
    }

    // Add item, translation
    private function addMaterial($data){
        $item = new Materials();
        $item->crayola_code = $data['crayolaCode'];
        $item->producer_code = $data['producerCode'];
        $item->type = $data['type'];
        $item->status = $data["status"];
        $item->created_user = Auth::user()->id;
        $item->save();
        $this->addMaterialTranslation($item->id,$data);
    }

    private function addMaterialTranslation($material_id,$data){
        $langs = LanguagesController::getActiveLangs();
        foreach($langs as $lang){
            $item = new MaterialsTranslation();
            $item->materials_id = $material_id;
            $item->crayola_name = $data['crayolaName_'.$lang->name];
            $item->producer_name = $data['producerName_'.$lang->name];
            $item->unit = $data['unit_'.$lang->name];
            $item->locale = $lang->name;
            $item->save();
        }
    }

    //Update item, translation
    private function updateMaterial($id,$data){
        $item = $this->getMaterialById($id);
        $item->crayola_code = $data['crayolaCode'];
        $item->producer_code = $data['producerCode'];
        $item->type = $data['type'];
        $item->status = $data['status'];
        $item->updated_user = Auth::user()->id;
        $item->updated_at = date("Y-m-d h:m:s",time());
        $item->save();
        $this->updateMaterialTranslation($id,$data);
    }

    private function updateMaterialTranslation($material_id,$data){
        $langs = LanguagesController::getActiveLangs();
        foreach ($langs as $lang){
            $item = MaterialsTranslation::where('locale',$lang->name)->where('materials_id',$material_id)->first();
            if($item){
                $item->crayola_name = $data['crayolaName_'.$lang->name];
                $item->producer_name = $data['producerName_'.$lang->name];
                $item->unit = $data['unit_'.$lang->name];
                $item->save();
            }else{
                $item = new MaterialsTranslation();
                $item->materials_id = $material_id;
                $item->crayola_name = $data['crayolaName_'.$lang->name];
                $item->producer_name = $data['producerName_'.$lang->name];
                $item->unit = $data['unit_'.$lang->name];
                $item->locale = $lang->name;
                $item->save();
            }
            
        }
    }

    //Public function
    public static function getActiveList(){
        $list = Materials::select('materials.id','materials.crayola_code', 'materials.type', 'materials.status','mt.crayola_name','mt.unit')
        ->leftJoin('materials_translations AS mt','mt.materials_id','=','materials.id')
        ->where('mt.locale',Session::get('locale'))
        ->orderBy('materials.code')->orderBy('mt.name')
        ->get();
        return $list;
    }

    public static function getAutocompleteList(){
        $list = Materials::select('materials.id', 'materials.crayola_code','materials.producer_code','materials.type', 'mt.crayola_name', 'mt.producer_name', 'mt.unit')
        ->leftJoin('materials_translations AS mt','mt.materials_id','=','materials.id')
        ->where('mt.locale',Session::get('locale'))
        ->orderBy('materials.id','DESC')
        ->get();
        return $list;
    }

    public static function getMaterialById($id){
        return Materials::find($id);
    }
}
