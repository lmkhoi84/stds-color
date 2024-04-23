<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Structure;
use App\Models\Staffs;
use Illuminate\Support\Facades\Auth;

class StaffsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

    public $main_page = 'staffs';
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
        return view('staffs.list', ['list' => $list,'add' => true,'search' => true, 'limit' => $limit, 'key' => $key]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function addNew()
    {
        $sList = $this->getAutocompleteList();
        $list = [];
            foreach ($sList as $item) {
                $list[] = "{
                    value: '" . $item["id"] . "',
                    label: '" . addslashes($item["code"]) . "',
                    name: '" . addslashes($item["name"]) . "',
                    email: '" . addslashes($item["email"]) . "',
                    phone: '" . addslashes($item["phone"]) . "',
                }";
            }
            $sList = "[" . implode(',', $list) . "]";
        return view('staffs.add', ['list' => $sList,'add' => false]);
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
            'code' => 'required',
            'phone' => 'required',
            'email' => 'required|email'
        ], [
            'name.required' => $this->alert['name_required'],
            'code.required' => $this->alert['code_required'],
            'phone.required' => $this->alert['phone_required'],
            'email.required' => $this->alert['email_required'],
            'email.email' => $this->alert['email_type']
        ]);
        $data = $request->all();
        $request->session()->put('data', $data);
        foreach ($data as $key => $value) {
            $request->session()->put($key, $value);
        }

        $checkExist = $this->getStaffByNameCode($data["name"],$data["code"]);
        
        if ($checkExist){
            return redirect()->back()->with(['type' => 'danger', 'alert_messenge' => $this->alert['error'] .' '. $this->alert['name'].' "'.$data["name"] .'" '. $this->alert['isExist']]);
        }
        
        $this->addStaff($data);

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
        $item = $this->getStaffById($id);
        if (!$item){
            return redirect('customers')->with(['type' => 'danger', 'alert_messenge' => $this->alert['error'] .' '. $this->alert['dataNotExist']]);
        }
        $item->birthday = !empty($item->birthday)?format_show_date($item->birthday):'';
        $sList = $this->getAutocompleteList();
        $list = [];
            foreach ($sList as $sitem) {
                $list[] = "{
                    value: '" . $sitem["id"] . "',
                    label: '" . addslashes($sitem["code"]) . "',
                    name: '" . addslashes($sitem["name"]) . "',
                    email: '" . addslashes($sitem["email"]) . "',
                    phone: '" . addslashes($sitem["phone"]) . "',
                }";
            }
            $sList = "[" . implode(',', $list) . "]";

        return view('staffs.edit', ['item' => $item,'sList' => $sList,'add' => false]);
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
        $item = $this->getStaffById($id);
        if (!$item){
            return redirect()->back()->with(['type' => 'danger', 'alert_messenge' => $this->alert['error'] .' '. $this->alert['dataNotExist']]);
        }

        $this->validate($request, [
            'name' => 'required',
            'code' => 'required',
            'phone' => 'required',
            'email' => 'required|email'
        ], [
            'name.required' => $this->alert['name_required'],
            'code.required' => $this->alert['code_required'],
            'phone.required' => $this->alert['phone_required'],
            'email.required' => $this->alert['email_required'],
            'email.email' => $this->alert['email_type']
        ]);

        $checkDublicate = $this->getDublicate($id,$data["code"]);
        if ($checkDublicate){
            return redirect()->back()->with(['type' => 'danger', 'alert_messenge' => $this->alert['error'] .' '. $this->alert['code'].' "'.$data["code"] .'" '. $this->alert['isExist']]);
        }

        $this->updateStaff($id,$data);

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
        $item = $this->getStaffById($id);
        if (!$item){
            return redirect()->back()->with(['type' => 'danger', 'alert_messenge' => $this->alert['error'] .' '. $this->alert['dataNotExist']]);
        }

        $code = $item->code;
        //Nếu nhân viên đã được sử dụng -> không cho xóa
        //$used = ProductsController::getUsedMaterialById($id);
        $used = false;
        if($used){
            return redirect()->back()->with(['type' => 'danger', 'alert_messenge' => $this->alert['error']. ' "' . $code. '" ' . $this->alert['used']]);
        }
        //Chưa được sử dụng -> thực hiện xóa
        
        $item->delete();
        return redirect($this->main_page)->with(['type' => 'success', 'alert_messenge' => $this->alert['success'] . ' "' . $code. '" ' . $this->alert['deleted']]);
    }

    //private function

    private function getFullList($key,$limit){
        $list = Staffs::select('id','name','phone','code','email','status')
        ->where(function ($query) use ($key) {
            $query->orWhere('name', 'LIKE', '%' . $key . '%');
            $query->orWhere('address', 'LIKE', '%' . $key . '%');
            $query->orWhere('code', 'LIKE', '%' . $key . '%');
            $query->orWhere('phone', 'LIKE', '%' . $key . '%');
            $query->orWhere('email', 'LIKE', '%' . $key . '%');
            $query->orWhere('citizen_identity_card', 'LIKE', '%' . $key . '%');
            $query->orWhere('birthday', 'LIKE', '%' . $key . '%');
        })
        ->orderBy('id','DESC')
        ->paginate($limit);
        return $list;
    }

    private function getStaffByNameCode($name,$code){
        return Staffs::where('name',$name)->where('code',$code)->first();
    }

    private function getDublicate($id,$code){
        return Staffs::where('id','!=',$id)->where('code',$code)->first();
    }

    private function addStaff($data){
        $item = new Staffs();
        $item->name = $data['name'];
        $item->code = $data['code'];
        $item->phone = $data['phone'];
        $item->email = $data['email'];
        $item->citizen_identity_card = $data['citizen_identity_card'];
        $item->address = $data['address'];
        $item->birthday = isset($data['birthday'])?format_date($data['birthday']):null;
        $item->area = 1;
        $item->status = $data['status'];
        $item->created_user = Auth::user()->id;
        $item->save();
    }

    private function updateStaff($id,$data){
        $item = Staffs::find($id);
        $item->name = $data['name'];
        $item->code = $data['code'];
        $item->phone = $data['phone'];
        $item->email = $data['email'];
        $item->citizen_identity_card = $data['citizen_identity_card'];
        $item->address = $data['address'];
        $item->birthday = isset($data['birthday'])?format_date($data['birthday']):null;
        $item->area = 1;
        $item->status = $data['status'];
        $item->created_user = Auth::user()->id;
        $item->updated_at = date("Y-m-d h:m:s",time());
        $item->save();
    }

    //static function
    public static function getAutocompleteList(){
        $list = Staffs::select('id','name', 'code','email','phone')
        ->orderBy('name','DESC')
        ->get();
        return $list;
    }

    public static function getStaffById($id){
        return Staffs::find($id);
    }

}
