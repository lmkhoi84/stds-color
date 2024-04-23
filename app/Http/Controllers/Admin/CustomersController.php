<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Customers;
use App\Models\Structure;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Auth;

class CustomersController extends Controller
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

        $customersList = Customers::where('name','LIKE','%'.$key.'%')->orderBy("created_at","DESC")->paginate($limit);
        $customersList->appends(['show' => $limit,'search' => $key]);
        return view('Customers.List', ['customers' => $customersList,'limit'=>$limit,'key' => $key]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $customersList = Customers::select('id','name','address')->get();
        $cus_arr = [];
        foreach ($customersList as $customer){
            $cus_arr[] = '
                {value: "' . $customer['id'] . '",
                label: "' . addslashes($customer['name']) . '",
                add: "' . $customer['address'] . '"
                }';
        }
        $customers = "[".implode(',', $cus_arr)."]";
        return view('Customers.Add',['customers' => $customers]);
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
            'address' => 'required'
        ], [
            'name.required' => $alert['name_required'],
            'address.required' => $alert['address_required'],
        ]);
        $customer = Customers::where("name",$request->name)->where("address",$request->address)->first();
        if (!empty($customer)){
            return redirect()->back()->with(['type' => 'danger', 'alert_messenge' => $alert['error'] . ' "' . $request->name . '" ' . $alert['is_exist']]);
        }else{
            $newCustomer = new Customers();
            $newCustomer->name = $request->name;
            $newCustomer->address = $request->address;
            $newCustomer->tax_code = $request->tax_code;
            $newCustomer->contact = $request->contact;
            $newCustomer->phone = $request->phone;
            $newCustomer->email = $request->email;
            $newCustomer->created_user = Auth::user()->id;
            $newCustomer->save();
            return redirect()->back()->with(['type' => 'success', 'alert_messenge' => $alert['success'] . ' "' . $request->name . '" ' . $alert['add_success']]);
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
        $Customer = Customers::find($id);
        if (empty($Customer)){
            return redirect('customers')->with(['type' => 'danger', 'alert_messenge' => $alert['error'] . ' ' . $alert['data_error']]);
        }else{
            $customersList = Customers::select('id','name','address')->get();
            $cus_arr = [];
            foreach ($customersList as $customer){
                $cus_arr[] = '
                    {value: "' . $customer['id'] . '",
                    label: "' . addslashes($customer['name']) . '",
                    add: "' . $customer['address'] . '"
                    }';
            }
            $customers = "[".implode(',', $cus_arr)."]";
            return view('Customers.Edit',['customers' => $customers,'customer' => $Customer]);
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
            'address' => 'required'
        ], [
            'name.required' => $alert['name_required'],
            'address.required' => $alert['address_required'],
        ]);
        $customer = Customers::find($id);
        if (!$customer){
            return redirect('customers')->with(['type' => 'danger', 'alert_messenge' => $alert['error'] . ' ' . $alert['data_error']]);
        }else{
            $checkNew = Customers::where("name",$request->name)->where("address",$request->address)->where("id","!=",$id)->first();
            if ($checkNew){
                return redirect("customers")->with(['type' => 'danger', 'alert_messenge' => $alert['error'] . ' "' . $request->email . '" ' . $alert['is_exist']]);
            }else{
                $customer->name = $request->name;
                $customer->address = $request->address;
                $customer->tax_code = $request->tax_code;
                $customer->contact = $request->contact;
                $customer->phone = $request->phone;
                $customer->email = $request->email;
                $customer->save();
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
    public function destroy(Request $request, $id = '')
    {
        $active_menu = Structure::where('structure_url',Session::get('active_menu'))->first();
        $alert = unserialize($active_menu->trans_page);
        if (url()->previous() != url('customers')) {
            return redirect('home');
        }
        
        if (!empty($id)){
            $customer = Customers::find($id);
            $name = $customer->name;
            if (!$customer) {
                return redirect()->back()->with(['type' => 'danger', 'alert_messenge' => $alert['error'].' "'.$name.'" '.$alert['data_error']]);
            } else {
                    $customer->delete($id);
                    return redirect()->back()->with(['type' => 'success', 'alert_messenge' => $alert['success'].' "'.$name.'" '.$alert['delete_success']]);
            }
        }else{
            $ids = $request->check_customers;
            foreach ($ids as $id){
                $customer = Customers::find($id);
                $name = $customer->name;
                if (!$customer) {
                    return redirect()->back()->with(['type' => 'danger', 'alert_messenge' => $alert['error'].' "'.$name.'" '.$alert['data_error']]);
                } else {
                    $customer->delete($id);
                }
            }
            return redirect()->back()->with(['type' => 'success', 'alert_messenge' => $alert['success'].' '.$alert['checked_customers'].' '.$alert['delete_success']]);
        }
    }
}