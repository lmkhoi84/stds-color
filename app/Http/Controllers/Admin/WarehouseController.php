<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Structure;
use App\Models\Warehouse;
use Illuminate\Support\Facades\Session;

class WarehouseController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $warehouse = Warehouse::get();
        return view('Warehouse.List', ['warehouse' => $warehouse]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('Warehouse.Add');
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
        $data = $request->all();
        $this->validate($request, [
            'name' => 'required',
            'code' => 'required',
        ], [
            'name.required' => $alert['name_required'],
            'code.required' => $alert['code_required'],
        ]);
        $is_exist = Warehouse::where('code', $request->code)->first();
        if ($is_exist) {
            return redirect()->back()->with(['type' => 'danger', 'alert_messenge' => $alert['error'] . ' "' . $data['code'] . '" ' . $alert['is_exist']]);
        } else {
            $warehouse = new Warehouse();
            $warehouse->code = $data['code'];
            $warehouse->name = $data['name'];
            $warehouse->sort = $data['sort'];
            $warehouse->status = $data['status'];
            $warehouse->save();

            return redirect()->back()->with(['type' => 'success', 'alert_messenge' => $alert['success'] . ' "' . $data['name'] . '" ' . $alert['add_success']]);
        }
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
        $warehouse = Warehouse::find($id);
        if (empty($warehouse)){
            return redirect('warehouse')->with(['type' => 'danger', 'alert_messenge' => $alert['error'] . ' ' . $alert['data_error']]);
        }else{          
            return view('Warehouse.Edit',['warehouse' => $warehouse]);
        }
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
            'code' => 'required'
        ], [
            'name.required' => $alert['name_required'],
            'code.required' => $alert['code_required'],
        ]);
        $warehouse = Warehouse::find($id);
        if (!$warehouse){
            return redirect('warehouse')->with(['type' => 'danger', 'alert_messenge' => $alert['error'] . ' ' . $alert['data_error']]);
        }else{
            $checkNew = Warehouse::where("code",$request->code)->where("name",$request->name)->where("id","!=",$id)->first();
            if ($checkNew){
                return redirect("warehouse")->with(['type' => 'danger', 'alert_messenge' => $alert['error'] . ' "' . $request->name . '" ' . $alert['is_exist']]);
            }else{
                $warehouse->code = $request->code;
                $warehouse->name = $request->name;
                $warehouse->sort = $request->sort;
                $warehouse->status = $request->status;
                $warehouse->save();
                return redirect($request->previous_page)->with(['type' => 'success', 'alert_messenge' => $alert['success'] . ' "' . $request->name . '" ' . $alert['update_success']]);
            }
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $active_menu = Structure::where('structure_url',Session::get('active_menu'))->first();
        $alert = unserialize($active_menu->trans_page);
        if (url()->previous() != url('warehouse')) {
            return redirect('home');
        }
        
        if (!empty($id)){
            $warehouse = Warehouse::find($id);
            $name = $warehouse->name;
            if (!$warehouse) {
                return redirect()->back()->with(['type' => 'danger', 'alert_messenge' => $alert['error'].' "'.$name.'" '.$alert['data_error']]);
            } else {
                $warehouse->delete($id);
                return redirect()->back()->with(['type' => 'success', 'alert_messenge' => $alert['success'].' "'.$name.'" '.$alert['delete_success']]);
            }
        }
    }
    
    public function change_status($id)
    {
        $active_menu = Structure::where('structure_url', Session::get('active_menu'))->first();
        $alert = unserialize($active_menu->trans_page);
        $wh = Warehouse::find($id);
        if ($wh) {
            if ($wh->status == 0) $status = 1;
            else $status = 0;
            $wh->status = $status;
            $wh->save();
            return redirect()->back()->with(['type' => 'success', 'alert_messenge' => $alert['success'] . ' "' . $wh->name . '" ' . $alert['status_changed']]);
        } else {
            return redirect()->back()->with(['type' => 'danger', 'alert_messenge' => $alert['error'] . ' "' . $wh->name . '" ' . $alert['data_error']]);
        }
    }

    public static function WhList(){
        $wh = Warehouse::where('status',1)->get();
        return $wh;
    }
}
