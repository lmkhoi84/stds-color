<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Staffs;
use App\Models\Structure;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

class StaffsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
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

        $staffsList = Staffs::where('name','LIKE','%'.$key.'%')
        ->orWhere('staff_code','LIKE','%'.$key.'%')
        ->orWhere('email','LIKE','%'.$key.'%')
        ->orWhere('address','LIKE','%'.$key.'%')
        ->orWhere('phone','LIKE','%'.$key.'%')
        ->orWhere('area','LIKE','%'.$key.'%')
        ->orWhere('cmnd_cccd','LIKE','%'.$key.'%')
        ->orderBy("created_at","DESC")->orderBy("staff_code","DESC")->paginate($limit);
        $staffsList->appends(['show' => $limit,'search' => $key]);
        return view('Staffs.List', ['staffs' => $staffsList,'limit'=>$limit,'key' => $key]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('Staffs.Add');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $active_menu = Structure::where('structure_url', Session::get('active_menu'))->first();
        $alert = unserialize($active_menu->trans_page);
        $this->validate($request, [
            'name' => 'required',
            'staff_code' => 'required',
            'email'  => 'required|email',
        ], [
            'name.required' => $alert['name_required'],
            'staff_code.required' => $alert['staff_code_required'],
            'email.required' => $alert['email_required'],
            'email.email' => $alert['email_email'],
        ]);
        $staff_code = Staffs::where("staff_code",$request->staff_code)->first();
        if (!empty($staff_code)){
            return redirect()->back()->with(['type' => 'danger', 'alert_messenge' => $alert['error'] . ' "' . $request->staff_code . '" ' . $alert['is_exist']]);
        }

        $email_extend = explode("@",$request->email);
        if ($email_extend[1] != "stdsvn.com" && $email_extend[1] != "stds.vn"){
            return redirect()->back()->with(['type' => 'danger', 'alert_messenge' => $alert['error'] . ' "' . $request->email . '" ' . $alert['is_denied']]);
        }
        
        $staff_email = Staffs::where("email",$request->email)->first();
        if (!empty($staff_email)){
            return redirect()->back()->with(['type' => 'danger', 'alert_messenge' => $alert['error'] . ' "' . $request->email . '" ' . $alert['is_exist']]);
        }

        if (check_date_dmy($request->date_of_birth) == false){
            return redirect()->back()->with(['type' => 'danger', 'alert_messenge' => $alert['error']. " " . $alert['date_format_holder']]);
        }

        $newStaff = new Staffs();
        $newStaff->name = $request->name;
        $newStaff->staff_code = $request->staff_code;
        $newStaff->date_of_birth = format_date($request->date_of_birth);
        $newStaff->address = $request->address;
        $newStaff->email = $request->email;
        $newStaff->cmnd_cccd = $request->cmnd_cccd;
        $newStaff->phone = $request->phone;
        $newStaff->area = $request->area;
        $newStaff->created_user = Auth::user()->id;
        $newStaff->status = 1;
        $newStaff->save();
        return redirect()->back()->with(['type' => 'success', 'alert_messenge' => $alert['success'] . ' "' . $request->name . '" ' . $alert['add_success']]);
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
        $active_menu = Structure::where('structure_url', Session::get('active_menu'))->first();
        $alert = unserialize($active_menu->trans_page);
        $staff = Staffs::find($id);
        if (!$staff){
            return redirect('staffs')->with(['type' => 'danger', 'alert_messenge' => $alert['error'] . ' ' . $alert['data_error']]);
        }
        if (!empty($staff->date_of_birth)) $staff->date_of_birth = format_show_date($staff->date_of_birth);
        return view('Staffs.Edit',['staff' => $staff]);
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
        $active_menu = Structure::where('structure_url', Session::get('active_menu'))->first();
        $alert = unserialize($active_menu->trans_page);
        $this->validate($request, [
            'name' => 'required',
            'staff_code' => 'required',
            'email'  => 'required|email',
        ], [
            'name.required' => $alert['name_required'],
            'staff_code.required' => $alert['staff_code_required'],
            'email.required' => $alert['email_required'],
            'email.email' => $alert['email_email'],
        ]);
        $staff = Staffs::find($id);
        if (!$staff){
            return redirect('staffs')->with(['type' => 'danger', 'alert_messenge' => $alert['error'] . ' ' . $alert['data_error']]);
        }

        $staff_code = Staffs::where("staff_code",$request->staff_code)->where('id','!=',$id)->first();
        if (!empty($staff_code)){
            return redirect()->back()->with(['type' => 'danger', 'alert_messenge' => $alert['error'] . ' "' . $request->staff_code . '" ' . $alert['is_exist']]);
        }

        $email_extend = explode("@",$request->email);
        if ($email_extend[1] != "stdsvn.com" && $email_extend[1] != "stds.vn"){
            return redirect()->back()->with(['type' => 'danger', 'alert_messenge' => $alert['error'] . ' "' . $request->email . '" ' . $alert['is_denied']]);
        }
        
        $staff_email = Staffs::where("email",$request->email)->where('id','!=',$id)->first();
        if (!empty($staff_email)){
            return redirect()->back()->with(['type' => 'danger', 'alert_messenge' => $alert['error'] . ' "' . $request->email . '" ' . $alert['is_exist']]);
        }

        if (!empty($request->date_of_birth) && check_date_dmy($request->date_of_birth) == false){
            return redirect()->back()->with(['type' => 'danger', 'alert_messenge' => $alert['error']. " " . $alert['date_format_holder']]);
        }


        $staff->name = $request->name;
        $staff->staff_code = $request->staff_code;
        $staff->date_of_birth = (!empty($request->date_of_birth)?format_date($request->date_of_birth):null);
        $staff->address = $request->address;
        $staff->email = $request->email;
        $staff->cmnd_cccd = $request->cmnd_cccd;
        $staff->phone = $request->phone;
        $staff->area = $request->area;
        $staff->status = 1;
        $staff->save();
        return redirect($request->previous_page)->with(['type' => 'success', 'alert_messenge' => $alert['success'] . ' "' . $request->name . '" ' . $alert['update_success']]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, $id = '')
    {
        $active_menu = Structure::where('structure_url',Session::get('active_menu'))->first();
        $alert = unserialize($active_menu->trans_page);
        if (url()->previous() != url('staffs')) {
            return redirect('home');
        }
        
        if (!empty($id)){
            $staff = Staffs::find($id);
            $name = $staff->name;
            if (!$staff) {
                return redirect()->back()->with(['type' => 'danger', 'alert_messenge' => $alert['error'].' "'.$name.'" '.$alert['data_error']]);
            } else {
                    $staff->delete($id);
                    return redirect()->back()->with(['type' => 'success', 'alert_messenge' => $alert['success'].' "'.$name.'" '.$alert['delete_success']]);
            }
        }else{
            $ids = $request->check_staffs;
            foreach ($ids as $id){
                $staff = Staffs::find($id);
                $name = $staff->name;
                if (!$staff) {
                    return redirect()->back()->with(['type' => 'danger', 'alert_messenge' => $alert['error'].' "'.$name.'" '.$alert['data_error']]);
                } else {
                    $staff->delete($id);
                }
            }
            return redirect()->back()->with(['type' => 'success', 'alert_messenge' => $alert['success'].' '.$alert['checked_staffs'].' '.$alert['delete_success']]);
        }
    }

    public function change_status($id)
    {
        $active_menu = Structure::where('structure_url', Session::get('active_menu'))->first();
        $alert = unserialize($active_menu->trans_page);
        $staff = Staffs::find($id);
        if ($staff) {
            if ($staff->status == 0) $status = 1;
            else $status = 0;
            $staff->status = $status;
            $staff->save();
            return redirect()->back()->with(['type' => 'success', 'alert_messenge' => $alert['success'] . ' "' . $staff->name . '" ' . $alert['status_changed']]);
        } else {
            return redirect()->back()->with(['type' => 'danger', 'alert_messenge' => $alert['error'] . ' "' . $staff->name . '" ' . $alert['data_error']]);
        }
    }
}
