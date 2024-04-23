<?php

namespace App\Http\Controllers;

use App\Models\Customers;
use App\Models\Structure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CustomersController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

    public $main_page = 'customers';
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
        return view('customers.list', ['list' => $list,'add' => true,'search' => true, 'limit' => $limit, 'key' => $key]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function addNew()
    {
        $cList = $this->getAutocompleteList();
        $list = [];
            foreach ($cList as $item) {
                $list[] = "{
                    value: '" . $item["id"] . "',
                    label: '" . addslashes($item["name"]) . "',
                    add: '" . addslashes($item["address"]) . "',
                }";
            }
            $cList = "[" . implode(',', $list) . "]";
        return view('customers.add', ['list' => $cList,'add' => false]);
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
            'name' => 'required',
            'address' => 'required',
        ], [
            'name.required' => $this->alert['name_required'],
            'address.required' => $this->alert['address_required'],
        ]);
        $data = $request->all();
        $request->session()->put('data', $data);
        foreach ($data as $key => $value) {
            $request->session()->put($key, $value);
        }

        $checkExist = $this->getCustomerByNameAddress($data["name"],$data["address"]);
        
        if ($checkExist){
            return redirect()->back()->with(['type' => 'danger', 'alert_messenge' => $this->alert['error'] .' '. $this->alert['name'].' "'.$data["name"] .'" '. $this->alert['isExist']]);
        }
        
        $this->addCustomer($data);

        foreach ($data as $key => $value) {
            $request->session()->forget($key, $value);
        }
        return redirect()->back()->with(['type' => 'success', 'alert_messenge' => $this->alert['success'] . ' "' . $data["name"] . '" ' . $this->alert['addSuccess']]);
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
        $item = $this->getCustomerById($id);
        if (!$item){
            return redirect('customers')->with(['type' => 'danger', 'alert_messenge' => $this->alert['error'] .' '. $this->alert['dataNotExist']]);
        }
        $cList = $this->getAutocompleteList();
        $list = [];
            foreach ($cList as $citem) {
                $list[] = "{
                    value: '" . $citem["id"] . "',
                    label: '" . addslashes($citem["name"]) . "',
                    desc: '" . addslashes($citem["address"]) . "',
                }";
            }
            $cList = "[" . implode(',', $list) . "]";


        return view('customers.edit', ['item' => $item,'cList' => $cList,'add' => false]);
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
        $item = $this->getCustomerById($id);
        if (!$item){
            return redirect()->back()->with(['type' => 'danger', 'alert_messenge' => $this->alert['error'] .' '. $this->alert['dataNotExist']]);
        }

        $this->validate($request, [
            'name' => 'required',
            'address' => 'required',
        ], [
            'name.required' => $this->alert['name_required'],
            'address.required' => $this->alert['address_required'],
        ]);

        $checkDublicate = $this->getDublicate($id,$data["name"],$data["address"]);
        if ($checkDublicate){
            return redirect()->back()->with(['type' => 'danger', 'alert_messenge' => $this->alert['error'] .' '. $this->alert['name'].' "'.$data["name"] .'" '. $this->alert['isExist']]);
        }

        $this->updateCustomer($id,$data);

        foreach ($data as $key => $value) {
            $request->session()->forget($key, $value);
        }

        return redirect($request->previous_page)->with(['type' => 'success', 'alert_messenge' => $this->alert['success'] . ' "' . $data['name'] . '" ' . $this->alert['updateSuccess']]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $item = $this->getCustomerById($id);
        if (!$item){
            return redirect()->back()->with(['type' => 'danger', 'alert_messenge' => $this->alert['error'] .' '. $this->alert['dataNotExist']]);
        }

        $name = $item->name;
        //Nếu khách hàng đã được sử dụng -> không cho xóa
        //$used = ProductsController::getUsedMaterialById($id);
        $used = false;
        if($used){
            return redirect()->back()->with(['type' => 'danger', 'alert_messenge' => $this->alert['error']. ' "' . $name. '" ' . $this->alert['used']]);
        }
        //Chưa được sử dụng -> thực hiện xóa
        
        $item->delete();
        return redirect($this->main_page)->with(['type' => 'success', 'alert_messenge' => $this->alert['success'] . ' "' . $name. '" ' . $this->alert['deleted']]);
    }

    
    //private function
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

    private function getFullList($key,$limit){
        $list = Customers::select('id','name','address','tax_code','status')
        ->where(function ($query) use ($key) {
            $query->orWhere('name', 'LIKE', '%' . $key . '%');
            $query->orWhere('address', 'LIKE', '%' . $key . '%');
            $query->orWhere('tax_code', 'LIKE', '%' . $key . '%');
            $query->orWhere('contact_name', 'LIKE', '%' . $key . '%');
            $query->orWhere('contact_phone', 'LIKE', '%' . $key . '%');
            $query->orWhere('contact_email', 'LIKE', '%' . $key . '%');
            $query->orWhere('consignee_name', 'LIKE', '%' . $key . '%');
            $query->orWhere('consignee_phone', 'LIKE', '%' . $key . '%');
            $query->orWhere('delivery_address', 'LIKE', '%' . $key . '%');
        })
        ->orderBy('id','DESC')
        ->paginate($limit);
        return $list;
    }

    private function getCustomerByNameAddress($name,$address){
        return Customers::where('name',$name)->where('address',$address)->first();
    }

    private function getDublicate($id,$name,$address){
       return Customers::where('id','!=',$id)->where('name',$name)->where('address',$address)->first();
    }

    private function addCustomer($data){
        $item = new Customers();
        $item->name = $data['name'];
        $item->address = $data['address'];
        $item->tax_code = $data['tax_code'];
        $item->contact_name = $data['contact_name'];
        $item->contact_phone = $data['contact_phone'];
        $item->contact_email = $data['contact_email'];
        $item->consignee_name = $data['consignee_name'];
        $item->consignee_phone = $data['consignee_phone'];
        $item->delivery_address = $data['delivery_address'];
        $item->status = $data['status'];
        $item->created_user = Auth::user()->id;
        $item->save();
    }

    private function updateCustomer($id,$data){
        $item = Customers::find($id);
        $item->name = $data['name'];
        $item->address = $data['address'];
        $item->tax_code = $data['tax_code'];
        $item->contact_name = $data['contact_name'];
        $item->contact_phone = $data['contact_phone'];
        $item->contact_email = $data['contact_email'];
        $item->consignee_name = $data['consignee_name'];
        $item->consignee_phone = $data['consignee_phone'];
        $item->delivery_address = $data['delivery_address'];
        $item->status = $data['status'];
        $item->updated_user = Auth::user()->id;
        $item->updated_at = date("Y-m-d h:m:s",time());
        $item->save();
    }

    //static function
    public static function getAutocompleteList(){
        $list = Customers::select('id','name', 'address')
        ->orderBy('name','DESC')
        ->get();
        return $list;
    }

    public static function getCustomerById($id){
        return Customers::find($id);
    }

}
