<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Input;
use App\Models\InputDetails;
use App\Models\Structure;
use App\Models\Products;
use App\Models\ProductsTranslation;
use App\Models\ProductsDetails;
use App\Models\Staffs;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use phpDocumentor\Reflection\Types\This;
use Symfony\Component\VarDumper\Cloner\Data;

class InputController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

     public $main_page = 'input';
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
    public function index()
    {
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
        return view('input.list', ['list' => $list,'add' => true,'search' => true, 'limit' => $limit, 'key' => $key]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function addNew()
    {   
        $sList = StaffsController::getAutocompleteList();
        $list = [];
            foreach ($sList as $item) {
                $list[] = "{
                    value: '" . $item["id"] . "',
                    label: '" . addslashes($item["name"])." (". addslashes($item["code"]).")',
                }";
            }
        $sList = "[" . implode(',', $list) . "]";

        $cList = CustomersController::getAutocompleteList();
        $list = [];
            foreach ($cList as $item) {
                $list[] = "{
                    value: '" . $item["id"] . "',
                    label: '" . addslashes($item["name"]) . "',
                    add: '" . addslashes($item["address"]) . "',
                }";
            }
        $cList = "[" . implode(',', $list) . "]";

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
        $mlist = "[" . implode(',', $list) . "]";

        return view('input.add', ['mList' => $mlist,'sList' => $sList,'cList' => $cList,'add' => false]);
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
            'date' => 'required|date_format:d/m/Y',
            'staff' => 'required',
            'customer' => 'required',
            'number' => 'required',
        ], [
            'date.required' => $this->alert['date_required'],
            'date.date_format' => $this->alert['date_format'],
            'staff.required' => $this->alert['staff_required'],
            'customer.required' => $this->alert['customer_required'],
            'number.required' => $this->alert['number_required']
        ]);

        $data = $request->all();
        $request->session()->put('data', $data);
        foreach ($data as $key => $value) {
            $request->session()->put($key, $value);
        }

        //Kiểm tra nhân viên
        $check = StaffsController::getStaffById($data['staff_id']);
        if (empty($check)){
            return redirect()->back()->with(['type' => 'danger', 'alert_messenge' => $this->alert['error'] .' '. $this->alert['staff'].' "'.$data["staff"] .'" '. $this->alert['isNotExist']]);
        }

        //Kiểm tra nhà cung cấp
        $check = CustomersController::getCustomerById($data['customer_id']);
        if (empty($check)){
            return redirect()->back()->with(['type' => 'danger', 'alert_messenge' => $this->alert['error'] .' '. $this->alert['customer'].' "'.$data["customer"] .'" '. $this->alert['isNotExist']]);
        }

        //Kiểm tra số chứng từ
        $check = $this->getInputByNumber($data['number']);
        if ($check){
            return redirect()->back()->with(['type' => 'danger', 'alert_messenge' => $this->alert['error'] .' '. $this->alert['number'].' "'.$data["number"] .'" '. $this->alert['isExist']]);
        }

        $countZeroQuantity = 0;
        for($i=1; $i<=$data['items']; $i++){
            $check = MaterialsController::getMaterialById($data['materialId_'.$i]);
            $notExistId = [];
            if(empty($check)){
                $notExistId[] = $i;
            }
            else if(empty($data['quantity_'.$i])){
                $countZeroQuantity++;
            }
        }
        if ($countZeroQuantity == $data['items']){
            return redirect()->back()->with(['type' => 'danger', 'alert_messenge' => $this->alert['error'] .' '. $this->alert['number'].' "'.$data["number"] .'" '. $this->alert['unsaved']]);
        }
        if (count($notExistId) > 0){
            return redirect()->back()->with(['type' => 'danger', 'alert_messenge' => $this->alert['error'] .' '. $this->alert['material'].' "['.implode(', ',$notExistId) .']" '. $this->alert['isNotExist']]);
        }

        $this->addInput($data);

        foreach ($data as $key => $value) {
            $request->session()->forget($key, $value);
        }
        return redirect()->back()->with(['type' => 'success', 'alert_messenge' => $this->alert['success'] . ' "' . $data["number"] . '" ' . $this->alert['addSuccess']]);   
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
        $item = $this->getInputById($id);
        if (!$item){
            return redirect('products')->with(['type' => 'danger', 'alert_messenge' => $this->alert['error'] .' '. $this->alert['dataNotExist']]);
        }
        $staff = StaffsController::getStaffById($item->staff_id);
        $customer = CustomersController::getCustomerById($item->customer_id);
        $item->date = format_show_date($item->date);
        $item->staff = $staff->name." (".$staff->code.")";
        $item->customer = $customer->name;
        $itemDetails = $this->getInputDetails($id);

        $sList = StaffsController::getAutocompleteList();
        $list = [];
            foreach ($sList as $sItem) {
                $list[] = "{
                    value: '" . $item["id"] . "',
                    label: '" . addslashes($sItem["name"])." (". addslashes($sItem["code"]).")',
                }";
            }
        $sList = "[" . implode(',', $list) . "]";

        $cList = CustomersController::getAutocompleteList();
        $list = [];
            foreach ($cList as $cItem) {
                $list[] = "{
                    value: '" . $cItem["id"] . "',
                    label: '" . addslashes($cItem["name"]) . "',
                    add: '" . addslashes($cItem["address"]) . "',
                }";
            }
        $cList = "[" . implode(',', $list) . "]";

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
        $mlist = "[" . implode(',', $list) . "]";

        return view('input.edit', ['item'=>$item,'itemDetails' => $itemDetails,'mList' => $mlist,'sList' => $sList,'cList' => $cList,'add' => false]);
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
        $item = $this->getInputById($id);
        if (!$item){
            return redirect()->back()->with(['type' => 'danger', 'alert_messenge' => $this->alert['error'] .' '. $this->alert['dataNotExist']]);
        }

        $this->validate($request, [
            'date' => 'required|date_format:d/m/Y',
            'staff' => 'required',
            'customer' => 'required',
            'number' => 'required',
        ], [
            'date.required' => $this->alert['date_required'],
            'date.date_format' => $this->alert['date_format'],
            'staff.required' => $this->alert['staff_required'],
            'customer.required' => $this->alert['customer_required'],
            'number.required' => $this->alert['number_required']
        ]);

        //Kiểm tra nhân viên
        $check = StaffsController::getStaffById($data['staff_id']);
        if (empty($check)){
            return redirect()->back()->with(['type' => 'danger', 'alert_messenge' => $this->alert['error'] .' '. $this->alert['staff'].' "'.$data["staff"] .'" '. $this->alert['isNotExist']]);
        }

        //Kiểm tra nhà cung cấp
        $check = CustomersController::getCustomerById($data['customer_id']);
        if (empty($check)){
            return redirect()->back()->with(['type' => 'danger', 'alert_messenge' => $this->alert['error'] .' '. $this->alert['customer'].' "'.$data["customer"] .'" '. $this->alert['isNotExist']]);
        }

        //Kiểm tra số chứng từ
        $check = $this->getDublicateNumber($id,$data['number']);
        if ($check){
            return redirect()->back()->with(['type' => 'danger', 'alert_messenge' => $this->alert['error'] .' '. $this->alert['number'].' "'.$data["number"] .'" '. $this->alert['isExist']]);
        }

        $countZeroQuantity = 0;
        for($i=1; $i<=$data['items']; $i++){
            $check = MaterialsController::getMaterialById($data['materialId_'.$i]);
            $notExistId = [];
            if(empty($check)){
                $notExistId[] = $i;
            }
            else if(empty($data['quantity_'.$i])){
                $countZeroQuantity++;
            }
        }
        if ($countZeroQuantity == $data['items']){
            return redirect()->back()->with(['type' => 'danger', 'alert_messenge' => $this->alert['error'] .' '. $this->alert['number'].' "'.$data["number"] .'" '. $this->alert['unsaved']]);
        }
        if (count($notExistId) > 0){
            return redirect()->back()->with(['type' => 'danger', 'alert_messenge' => $this->alert['error'] .' '. $this->alert['material'].' "['.implode(', ',$notExistId) .']" '. $this->alert['isNotExist']]);
        }

        $this->updateInput($id,$data);

        foreach ($data as $key => $value) {
            $request->session()->forget($key, $value);
        }
        return redirect($request->previous_page)->with(['type' => 'success', 'alert_messenge' => $this->alert['success'] .' '. $this->alert['number'].' "'. $data["number"] . '" ' . $this->alert['updateSuccess']]);

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }

    //private function

    private function getFullList($key,$limit){
        $list = Input::select('input.id','input.date','input.number','input.customer_id','input.amount','input.note','customers.name')
        ->leftJoin('customers','customers.id','=','input.customer_id')
        ->where(function ($query) use ($key) {
            $query->orWhere('input.date', 'LIKE', '%' . $key . '%');
            $query->orWhere('input.number', 'LIKE', '%' . $key . '%');
            $query->orWhere('customers.name', 'LIKE', '%' . $key . '%');
        })
        ->orderBy('id','DESC')
        ->paginate($limit);
        return $list;
    }

    private function getInputById($id){
        return Input::find($id);
    }

    private function getInputByNumber($number){
        return Input::where('number',$number)->first();
    }

    private function getInputDetails($id){
        $list = InputDetails::select('input_details.*','mt.crayola_name','mt.unit','m.crayola_code','m.type')
        ->leftJoin('materials AS m','m.id','=','input_details.material_id')
        ->leftJoin('materials_translations as mt','mt.materials_id','=','input_details.material_id')
        ->where('input_details.input_id',$id)
        ->where('mt.locale',Session::get('locale'))
        ->get();
        return $list;
    }

    private function getDublicateNumber($id,$number){
        return Input::where('id','!=',$id)->where('number',$number)->first();
    }

    private function getItems($inputId){
        return InputDetails::where('input_id',$inputId)->count('material_id');
    }

    private function addInput($data){
        $item = new Input();
        $item->date = format_date($data['date']);
        $item->staff_id = $data['staff_id'];
        $item->customer_id = $data['customer_id'];
        $item->number = $data['number'];
        $item->note = !empty($data['note'])?$data['note']:'';
        $item->type = 1;
        $item->created_user = Auth::user()->id;
        $item->save();
        $this->addInputDetails($item->id,$data);
    }

    private function addInputDetails($inputId,$data){
        $count = 0;
        $amount = 0;
        for ($i = 1; $i <= $data['items'];$i++){
            if(empty($data['materialId_'.$i]) || empty($data['quantity_'.$i]) ){
                continue;
            }
            else{
                $item = new InputDetails();
                $item->input_id = $inputId;
                $item->material_id = $data['materialId_'.$i];
                $item->quantity = format_real_number($data['quantity_'.$i]);
                $item->price = format_real_number($data['price_'.$i]);
                $item->created_user = Auth::user()->id;
                $item->save();
                $amount += (format_real_number($data['quantity_'.$i]) * format_real_number($data['price_'.$i]));
                $count++;
            }
        }
        $input = $this->getInputById($inputId);
        $input->items = $count;
        $input->amount = $amount;
        $input->save();
    }

    private function updateInput($id,$data){
        $item = Input::find($id);
        $item->date = format_date($data['date']);
        $item->staff_id = $data['staff_id'];
        $item->customer_id = $data['customer_id'];
        $item->number = $data['number'];
        $item->note = !empty($data['note'])?$data['note']:'';
        $item->type = 1;
        $item->updated_user = Auth::user()->id;
        $item->updated_at = date("Y-m-d h:m:s",time());
        $item->save();
        $this->updateInputDetails($item->id,$data);
    }

    private function updateInputDetails($inputId,$data){
        $amount = 0;
        for ($i = 1; $i <= $data['items'];$i++){
            if (empty($data['idDetail_'.$i])){
                if( empty($data['materialCode_'.$i]) || empty($data['materialId_'.$i]) || empty($data['quantity_'.$i]) ){
                    continue;
                }
                else{
                    $item = new InputDetails();
                    $item->input_id = $inputId;
                    $item->material_id = $data['materialId_'.$i];
                    $item->quantity = format_real_number($data['quantity_'.$i]);
                    $item->price = format_real_number($data['price_'.$i]);
                    $item->created_user = Auth::user()->id;
                    $item->save();
                    $amount += (format_real_number($data['quantity_'.$i]) * format_real_number($data['price_'.$i]));
                }
            }else{
                $item = InputDetails::find($data['idDetail_'.$i]);
                if ($item){
                    if(empty($data['quantity_'.$i]) ){
                        $item->delete();
                        $amount -= ($data['quantity_'.$i] * $data['price_'.$i]);
                    }else{
                        $item->input_id = $inputId;
                        $item->material_id = $data['materialId_'.$i];
                        $item->quantity = $data['quantity_'.$i];
                        $item->price = $data['price_'.$i];
                        $item->updated_user = Auth::user()->id;
                        $item->updated_at = date("Y-m-d h:m:s",time());
                        $item->save();
                        $amount += (format_real_number($data['quantity_'.$i]) * format_real_number($data['price_'.$i]));
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
                    InputDetails::where('id',$del_items[$i])->delete();
                }
            }
        }

        $count = $this->getItems($inputId);
        $input = $this->getInputById($inputId);
        $input->items = $count;
        $input->amount = $amount;
        $input->save();
    }

    //static function
}
