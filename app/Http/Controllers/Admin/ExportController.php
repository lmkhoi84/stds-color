<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Export;
use App\Models\Structure;
use App\Models\Products;
use App\Models\ProductsDetails;
use App\Models\Staffs;
use App\Models\Customers;
use App\Models\ExportDetails;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

class ExportController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

    public function index()
    {
        if (isset($_GET['sd']) || isset($_GET['ed'])) {
            $start_date = $_GET['sd'];
            $end_date = $_GET['ed'];
        } else {
            $end_date = date('Y-m-d');
            $start_date = date('Y-m-d', strtotime('- 365days'));
        }

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
        $extend_ids = array_filter(explode(',',Auth::user()->extends_users));
        $extend_ids[] = Auth::user()->id;
        $export_list = Export::select('export.*','customers.name AS supplier_name')
        ->where('export.wh_id',Auth::user()->wh_id)
        ->whereIn('export.created_user',$extend_ids)
        ->where('date', '>=', $start_date)->where('date', '<=', $end_date)
        ->where(function($q)
        {
            $key = '';
            if (!isset($_GET['search']) || $_GET['search'] == '') {
                $key = '';
            } else {
                $key = $_GET['search'];
            }
            $q->where('customers.name','LIKE','%'.$key.'%')->orWhere('number','LIKE','%'.$key.'%');
        })
        ->join('customers','export.supplier','=','customers.id')
        ->orderBy('date','DESC')
        //->toSql();
        ->paginate($limit);
        //return $export_list;
        $export_list->appends(['show' => $limit, 'sd' => $start_date, 'ed' => $end_date]);
        return view('Export.list', ['export_list' => $export_list, 'limit' => $limit,'key' => $key]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        if (Auth::user()->id != 1){
            $permissions = array_filter(Auth::user()->products_permission,function ($value){return !is_null($value) && $value != '';});
            $products = Products::select('products.*', 'str.structure_name', 'pro.product_name', 'br.posision')
            ->whereIn('parent_id',$permissions)
            ->join('structure_translations as str', 'str.structure_id', '=', 'products.parent_id')
            ->where('str.locale', Session::get('locale'))
            ->join('products_translations as pro', 'products.id', '=', 'pro.products_id')
            ->where('pro.locale', Session::get('locale'))
            ->join('products_details as br', 'br.product_id', '=', 'products.id')
            ->where('br.wh_id',1)
            ->orderBy('products.parent_id', 'ASC')->orderBy('pro.product_name', 'ASC')
            ->get();
        }else{
            $permissions = [];
            $products = Products::select('products.*', 'str.structure_name', 'pro.product_name', 'br.posision')
            ->join('structure_translations as str', 'str.structure_id', '=', 'products.parent_id')
            ->where('str.locale', Session::get('locale'))
            ->join('products_translations as pro', 'products.id', '=', 'pro.products_id')
            ->where('pro.locale', Session::get('locale'))
            ->join('products_details as br', 'br.product_id', '=', 'products.id')
            ->where('br.wh_id',1)
            ->orderBy('products.parent_id', 'ASC')->orderBy('pro.product_name', 'ASC')
            ->get();
        }

        $pro_list = [];
        foreach ($products as $product) {
            $pro_list[] = "
            {value: '" . $product["id"] . "',
            label: '" . addslashes($product["product_name"]) . "',
            desc: '" . $product["sale_off_type"] . "',
            unit: '" . $product["product_unit"] . "',
            calType:'" . $product["calculate_type"] . "'}";
        }
        $products_list = "[".implode(',', $pro_list)."]";
        ////////
        $users = Staffs::select('id', 'staff_code', 'name')->where('status', 1)->get();
        $employee = [];
        foreach ($users as $user) {
            $employee[] = "{value: '" . $user["id"] . "',
                            label: '" . $user["name"] . " (" . addslashes($user["staff_code"]) . ")',
                            desc: '" . $user["name"] . "'}";
        }
        $staffs = "[".implode(',', $employee)."]";
        //////
        $customers = Customers::select('id','name')->get();
        $cus_arr = [];
        foreach ($customers as $customer){
            $cus_arr[] = "
                {value: '" . $customer["id"] . "',
                label: '" . addslashes($customer["name"]) . "'}";
        }
        $supplier = "[".implode(',', $cus_arr)."]";
        //////
        return view('Export.add', ['staffs' => $staffs, 'supplier' => $supplier, 'products' => $products_list]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $data = $request->all();
        $active_menu = Structure::where('structure_url', Session::get('active_menu'))->first();
        $alert = unserialize($active_menu->trans_page);
        $rows = $data['rows'];
        $request->session()->put('rows', $data['rows']);
        $request->session()->put('amountPrice', $data['amountPrice']);
        $request->session()->put('date', $data['date']);
        $request->session()->put('salesman', $data['salesman']);
        $request->session()->put('supplier', $data['supplier']);
        $request->session()->put('number', $data['number']);
        $request->session()->put('export_type', $data['export_type']);
        for ($i = 1; $i <= $rows; $i++) {
            if ($data['quantity_'.$i] != 0){
                if ($data['calculateType_' . $i] == 2) {
                    $request->session()->put('width_' . $i, $data['width_' . $i]);
                    $request->session()->put('length_' . $i, $data['length_' . $i]);
                    $request->session()->put('addLength_' . $i, $data['addLength_' . $i]);
                    $request->session()->put('finishedQty_' . $i, $data['finishedQty_' . $i]);
                } elseif ($data['calculateType_' . $i] == 1) {
                    $request->session()->put('width_' . $i, $data['width_' . $i]);
                    $request->session()->put('finishedQty_' . $i, $data['finishedQty_' . $i]);
                    if (isset($data['changeActualStock_' . $i])) $request->session()->put('changeActualStock_' . $i, $data['changeActualStock_' . $i]);
                }
                $request->session()->put('quantity_' . $i, $data['quantity_' . $i]);
                $request->session()->put('price_' . $i, $data['price_' . $i]);
                $request->session()->put('productId_' . $i, $data['productId_' . $i]);
                $request->session()->put('totalPrice_' . $i, $data['totalPrice_' . $i]);
                $request->session()->put('calculateType_' . $i, $data['calculateType_' . $i]);
                $request->session()->put('unitOfProduct_' . $i, $data['unitOfProduct_' . $i]);
                $request->session()->put('productName_' . $i, $data['productName_' . $i]);
            }
        }
        $this->validate($request, [
            'date' => 'required',
            'salesman' => 'required',
            'supplier' => 'required',
        ], [
            'date.required' => $alert['date_required'],
            'salesman.required' => $alert['salesman_required'],
            'supplier.required' => $alert['supplier_required'],
        ]);

        //Check Staff exist
        $staff = Staffs::find($data['salesman_id']);
        if (!$staff || ($staff['name'].' ('.$staff['staff_code'].')'  != $data['salesman'])) {
            return redirect()->back()->with(['type' => 'danger', 'alert_messenge' => $alert['error'] . ' ' . $alert['staff_not_exist']]);
        }
        //End check staff

        //Check supplier exist
        $supplier = Customers::find($data['supplier_id']);
        if (!$supplier || $supplier['name'] != $data['supplier']) {
            return redirect()->back()->with(['type' => 'danger', 'alert_messenge' => $alert['error'] . ' ' . $alert['supplier_not_exist']]);
        }
        //End check supplier

        $find_export = Export::where('number', $data['number'])->first();
        if ($find_export) {
            return redirect()->back()->with(['type' => 'danger', 'alert_messenge' => $alert['error'] . ' ' . $data['number'] . ' ' . $alert['number_is_exist']]);
        }

        $export = new Export();
        $export->wh_id = Auth::user()->wh_id;
        $export->date = format_Date($data['date']);
        $export->salesman = $data['salesman_id'];
        $export->supplier = $data['supplier_id'];
        $export->number = (!empty($data['number']) ? $data['number'] : '');
        $export->type = $data['export_type'];
        $export->note = (!empty($data['note']) ? $data['note'] : '');
        $export->items = $data['rows'];
        $export->created_user = Auth::user()->id;
        $export->save();

        $amount = 0;
        $count = 0;
        for ($i = 1; $i <= $rows; $i++) {
            if ($data["quantity_".$i] > 0) $qty = format_real_number($data['quantity_' . $i]);
            else $qty = 0;

            if ($qty != 0) {
                $export_detail = new ExportDetails();
                $export_detail->export_id = $export->id;
                $export_detail->product_id = $data['productId_' . $i];
                if ($data['calculateType_' . $i] == 2) {
                    $fQty = format_real_number($data['finishedQty_' . $i]);
                    if ($data['addLength_' . $i] > 0) $add_length = format_real_number($data['addLength_' . $i]);
                    else $add_length = 0;
                    $export_detail->width = format_real_number($data['width_' . $i]);
                    $export_detail->length = format_real_number($data['length_' . $i]);
                    $export_detail->add_length = $add_length;
                    $export_detail->finished_quantity = $fQty;
                }
                if ($data['calculateType_' . $i] == 1) {
                    $fQty = format_real_number($data['finishedQty_' . $i]);
                    $width = format_real_number($data['width_' . $i]);
                    $export_detail->finished_quantity = $fQty;
                    $export_detail->width = $width;
                    $export_detail->actual_quantity = $width."x".$fQty;
                }
                $price = (!empty($data['price_' . $i]) ? format_real_number($data['price_' . $i]) : 0);
                $export_detail->quantity = $qty;
                $export_detail->price = $price;
                $export_detail->save();
                
                if ($data['calculateType_' . $i] == 1 && isset($data['actualStock_' . $i])) {
                    $check = ProductsDetails::where('product_id',$data['productId_' . $i])->where('wh_id',Auth::user()->wh_id)->first();
                    if (!$check){
                        $product_detail = new ProductsDetails();
                        $product_detail->product_id = $data['productId_' . $i];
                        $product_detail->wh_id = Auth::user()->wh_id;
                        $product_detail->actual_quantity = $data['actualStock_' . $i];
                        $product_detail->save();
                    }else{
                        $check->actual_quantity = $data['actualStock_' . $i];
                        $check->save();
                    }
                    //DB::update("UPDATE products_details SET actual_quantity = '" . $data['actualStock_' . $i] . "',updated_at = '" . date('Y-m-d H:i:s') . "' where product_id = " . $data['productId_' . $i]);
                }

                if (isset($fQty)) {
                    $amount += $price * $fQty;
                } else {
                    $amount += $price * $qty;
                }

                $count++;
            } elseif ($qty == 0) {
                continue;
            }else{
                return redirect()->back()->with(['type' => 'danger', 'alert_messenge' => 'Lỗi dòng '.$i]);
            }

            Session::forget('productName_'.$i);
            Session::forget('name_' . $i);
            Session::forget('width_' . $i);
            Session::forget('length_' . $i);
            Session::forget('addLength_' . $i);
            Session::forget('finishedQty_' . $i);
            Session::forget('actualStock_' . $i);
            Session::forget('calculateType_' . $i);
            Session::forget('quantity_' . $i);
            Session::forget('price_' . $i);
            Session::forget('totalPrice_' . $i);
        }
        Session::forget('rows');
        Session::forget('amountPrice');
        Session::forget('date');
        Session::forget('salesman');
        Session::forget('supplier');
        Session::forget('number');
        Session::forget('export_type');
    $export = Export::find($export->id);
        if ($count != $rows){
            $export->items = $count;
        }
        $export->amount = $amount;
        $export->save();
        return redirect()->back()->with(['type' => 'success', 'alert_messenge' => $alert['success'] . ' "' . $data['number'] . '" ' . $alert['add_success']]);
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
        $extend_ids = array_filter(explode(',',Auth::user()->extends_users));
        $extend_ids[] = Auth::user()->id;
        if (Auth::user()->id != 1){
            $export = Export::select('export.*', 'staffs.staff_code', 'staffs.id AS staff_id', 'staffs.name','customers.id AS supplier_id','customers.name AS supplier_name')
            ->join('staffs', 'export.salesman', '=', 'staffs.id')
            ->join('customers', 'export.supplier', '=', 'customers.id')
            ->where('export.id', $id)
            ->whereIn('export.created_user',$extend_ids)
            ->where('wh_id',Auth::user()->wh_id)
            ->first();
        }else{
            $export = Export::select('export.*', 'staffs.staff_code', 'staffs.id AS staff_id', 'staffs.name','customers.id AS supplier_id','customers.name AS supplier_name')
            ->join('staffs', 'export.salesman', '=', 'staffs.id')
            ->join('customers', 'export.supplier', '=', 'customers.id')
            ->where('export.id', $id)->first();
        }
        if (!$export) {
            return redirect('export')->with(['type' => 'danger', 'alert_messenge' => $alert['error'] . ' ' . $alert['data_error']]);
        }

        $export_details = ExportDetails::select('export_details.*', 'pro.calculate_type', 'trans.product_name', 'trans.product_unit')
            ->join('products AS pro', 'pro.id', '=', 'export_details.product_id')
            ->join('products_translations as trans', 'pro.id', '=', 'trans.products_id')
            ->where('trans.locale', Session::get('locale'))
            ->where('export_id', $id)
            ->join('products_details as br', 'br.product_id', '=', 'pro.id')
            ->where('br.wh_id',Auth::user()->wh_id)
            ->orderBy('id')
            ->get();
        //return $export_details;
        
        if (Auth::user()->id != 1){
            $permissions = array_filter(Auth::user()->products_permission,function ($value){return !is_null($value) && $value != '';});
            $products = Products::select('products.*', 'str.structure_name', 'pro.product_name', 'br.posision')
            ->whereIn('parent_id',$permissions)
            ->join('structure_translations as str', 'str.structure_id', '=', 'products.parent_id')
            ->where('str.locale', Session::get('locale'))
            ->join('products_translations as pro', 'products.id', '=', 'pro.products_id')
            ->where('pro.locale', Session::get('locale'))
            ->join('products_details as br', 'br.product_id', '=', 'products.id')
            ->where('br.wh_id',1)
            ->orderBy('products.parent_id', 'ASC')->orderBy('pro.product_name', 'ASC')
            ->get();
        }else{
            $permissions = [];
            $products = Products::select('products.*', 'str.structure_name', 'pro.product_name', 'br.posision')
            ->join('structure_translations as str', 'str.structure_id', '=', 'products.parent_id')
            ->where('str.locale', Session::get('locale'))
            ->join('products_translations as pro', 'products.id', '=', 'pro.products_id')
            ->where('pro.locale', Session::get('locale'))
            ->join('products_details as br', 'br.product_id', '=', 'products.id')
            ->where('br.wh_id',1)
            ->orderBy('products.parent_id', 'ASC')->orderBy('pro.product_name', 'ASC')
            ->get();
        }

        $pro_list = [];
        foreach ($products as $product) {
            $pro_list[] = "
            {value: '" . $product["id"] . "',
            label: '" . addslashes($product["product_name"]) . "',
            desc: '" . $product["sale_off_type"] . "',
            unit: '" . $product["product_unit"] . "',
            calType:'" . $product["calculate_type"] . "'}";
        }
        $products_list = "[".implode(',', $pro_list)."]";
        ////////
        $users = Staffs::select('id', 'staff_code', 'name')->where('status', 1)->get();
        $employee = [];
        foreach ($users as $user) {
            $employee[] = "{value: '" . $user["id"] . "',
                            label: '" . $user["name"] . " (" . addslashes($user["staff_code"]) . ")',
                            desc: '" . $user["name"] . "'}";
        }
        $staffs = "[".implode(',', $employee)."]";
        //////
        $customers = Customers::select('id','name')->get();
        $cus_arr = [];
        foreach ($customers as $customer){
            $cus_arr[] = "
                {value: '" . $customer["id"] . "',
                label: '" . addslashes($customer["name"]) . "'}";
        }
        $supplier = "[".implode(',', $cus_arr)."]";
        return view('Export.edit', ['staffs' => $staffs, 'supplier' => $supplier, 'products' => $products_list, 'export' => $export, 'export_details' => $export_details]);
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
        $active_menu = Structure::where('structure_url', Session::get('active_menu'))->first();
        $alert = unserialize($active_menu->trans_page);
        $export_rows = $data['rows'];
        $export = Export::find($id);
        //Check export ID
        if (!$export) {
            return redirect('export')->with(['type' => 'danger', 'alert_messenge' => $alert['error'] . ' ' . $alert['export_not_exist']]);
        }
        //End Check export ID

        //Check duplicate export number
        $duplicate_export = Export::where('number', $data['number'])->where('id', '!=', $data['export_id'])->first();
        if ($duplicate_export) {
            return redirect()->back()->with(['type' => 'danger', 'alert_messenge' => $alert['error'] . ' ' . $data['number'] . ' ' . $alert['number_is_exist']]);
        }
        //End Check duplicate export number

        //Make data table
        $zero_rows = 0;
        for ($i = 1; $i <= $export_rows; $i++) {
            $request->session()->put('productId_' . $i, $data['productId_' . $i]);
            if ($data['calculateType_' . $i] == 2) {
                $request->session()->put('name_' . $i, $data['name_' . $i]);
                $request->session()->put('width_' . $i, $data['width_' . $i]);
                $request->session()->put('length_' . $i, $data['length_' . $i]);
                $request->session()->put('addLength_' . $i, $data['addLength_' . $i]);
                $request->session()->put('finishedQty_' . $i, $data['finishedQty_' . $i]);
            } elseif ($data['calculateType_' . $i] == 1) {
                $request->session()->put('width_' . $i, $data['width_' . $i]);
                $request->session()->put('finishedQty_' . $i, $data['finishedQty_' . $i]);
                if (isset($data['actualStock_' . $i])) {
                    $request->session()->put('actualStock_' . $i, $data['actualStock_' . $i]);
                } else {
                    $request->session()->put('actualStock_' . $i, '');
                }
            }
            $request->session()->put('quantity_' . $i, $data['quantity_' . $i]);
            $request->session()->put('price_' . $i, $data['price_' . $i]);
            
            if ($data['quantity_' . $i] != 0) {
                $qty = format_real_number($data['quantity_' . $i]);
            }else{
                $qty = $data['quantity_' . $i];
                $zero_rows++;
            }
        }
        //End make data table

        //Validate
        $this->validate($request, [
            'date' => 'required',
            'salesman' => 'required',
            'supplier' => 'required',
        ], [
            'date.required' => $alert['date_required'],
            'salesman.required' => $alert['salesman_required'],
            'supplier.required' => $alert['supplier_required'],
        ]);

        //End validate

        //Check Staff exist
        $staff = Staffs::find($data['salesman_id']);
        if (!$staff || ($staff['name'].' ('.$staff['staff_code'].')'  != $data['salesman'])) {
            return redirect()->back()->with(['type' => 'danger', 'alert_messenge' => $alert['error'] . ' ' . $alert['staff_not_exist']]);
        }
        //End check staff

        //Check supplier exist
        $supplier = Customers::find($data['supplier_id']);
        if (!$supplier || $supplier['name'] != $data['supplier']) {
            return redirect()->back()->with(['type' => 'danger', 'alert_messenge' => $alert['error'] . ' ' . $alert['supplier_not_exist']]);
        }
        //End check supplier

        //Find rows need updated
        if (empty($data['delete_rows'])) {
            $update_rows = $export->items;
        } else {
            $arr = explode(',', $data['delete_rows']);
            $del_rows = array_filter($arr, function ($k) {
                return $k != '';
            });
            $del_num = count($del_rows);
            $update_rows = $export->items - $del_num;
        }

        $amount = 0;
        $rowsDeleted = 0;
        for ($i = 1; $i <= $export_rows; $i++) {
            //Check product
            $product = Products::select('products.*', 'br.posision', 'pro.product_name')
                ->join('products_translations as pro', 'products.id', '=', 'pro.products_id')
                ->join('products_details as br', 'br.product_id', '=', 'products.id')
                ->where('pro.locale', Session::get('locale'))
                ->where('products.id', $data['productId_' . $i])
                ->first();
            if (!$product) {
                return redirect()->back()->with(['type' => 'danger', 'alert_messenge' => $alert['error'] . ' ' . $alert['name_error_1'] . ' ' . $i . ' ' . $alert['name_error_2']]);
            }
            //End check

            $price = (!empty($data['price_' . $i]) ? format_real_number($data['price_' . $i]) : 0);
            $qty = (!empty($data['quantity_' . $i]) ? format_real_number($data['quantity_' . $i]) : 0);
            //Update rows
            
            if ($i <= $update_rows) {
                if (empty($qty)) {
                    ExportDetails::where('id', $data['idDetail_' . $i])->delete();
                    $rowsDeleted++;
                } else {
                    $item = ExportDetails::find($data['idDetail_' . $i]);
                    if ($data['productId_'.$i] != $item->product_id){
                        $item->product_id = $data['productId_'.$i];
                    }
                    if ($data['calculateType_' . $i] == 2) {
                        $fQty = format_real_number($data['finishedQty_' . $i]);
                        $item->width = format_real_number($data['width_' . $i]);
                        $item->length = format_real_number($data['length_' . $i]);
                        $item->add_length = format_real_number($data['addLength_' . $i]);
                        $item->finished_quantity = $fQty;
                    }
                    if ($data['calculateType_' . $i] == 1) {
                        $fQty = format_real_number($data['finishedQty_' . $i]);
                        $width = format_real_number($data['width_' . $i]);
                        $item->finished_quantity = $fQty;
                        $item->width = $width;
                        $item->finished_quantity = $fQty;
                    }
                    $item->quantity = $qty;
                    $item->price = $price;
                    $item->save();
                    if (isset($fQty)) {
                        $amount += $price * $fQty;
                    } else {
                        $amount += $price * $qty;
                    }
                }
                //End Update
            } else {
                if ($qty != 0) {
                    $item = new ExportDetails();
                    $item->export_id = $export->id;
                    $item->product_id = $data['productId_' . $i];
                    if ($data['calculateType_' . $i] == 2) {
                        $fQty = format_real_number($data['finishedQty_' . $i]);
                        $item->width = format_real_number($data['width_' . $i]);
                        $item->length = format_real_number($data['length_' . $i]);
                        $item->add_length = format_real_number($data['addLength_' . $i]);
                        $item->finished_quantity = $fQty;
                    }
                    if ($data['calculateType_' . $i] == 1) {
                        $fQty = format_real_number($data['finishedQty_' . $i]);
                        $width = format_real_number($data['width_' . $i]);
                        $item->finished_quantity = $fQty;
                        $item->width = $width;
                        $item->finished_quantity = $fQty;
                    }
                    $item->quantity = $qty;
                    $item->price = $price;
                    $item->save();
                    if (isset($fQty)) {
                        $amount += $price * $fQty;
                    } else {
                        $amount += $price * $qty;
                    }
                }
            }
            // Update actual stock
            if ($data['calculateType_' . $i] == 1 && isset($data['actualStock_' . $i])) {
                $check = ProductsDetails::where('product_id',$data['productId_' . $i])->where('wh_id',Auth::user()->wh_id)->first();
                if (!$check){
                    $product_detail = new ProductsDetails();
                    $product_detail->product_id = $data['productId_' . $i];
                    $product_detail->wh_id = Auth::user()->wh_id;
                    $product_detail->actual_quantity = $data['actualStock_' . $i];
                    $product_detail->save();
                }else{
                    $check->actual_quantity = $data['actualStock_' . $i];
                    $check->save();
                }
                //DB::update("UPDATE products_details SET actual_quantity = '" . $data['actualStock_' . $i] . "',updated_at = '" . date('Y-m-d H:i:s') . "' where product_id = " . $data['productId_' . $i]);
            }
        }
        //return $del_rows;
        if (!empty($data['delete_rows'])) {
            foreach ($del_rows as $del_row) {
                ExportDetails::where('id', $del_row)->delete();
            }
        }

        $export_rows -= $rowsDeleted;
        $export->date = format_Date($data['date']);
        $export->salesman = $data['salesman_id'];
        $export->supplier = $data['supplier_id'];
        $export->number = (!empty($data['number']) ? $data['number'] : '');
        $export->type = $data['export_type'];
        $export->note = (!empty($data['note']) ? $data['note'] : '');
        $export->items = $export_rows;
        $export->created_user = Auth::user()->id;
        $export->amount = $amount;
        $export->save();
        //return $amount;
        for ($i = 1; $i <= $data['rows']; $i++) {
            Session::forget('name_' . $i);
            Session::forget('productId_' . $i);
            Session::forget('width_' . $i);
            Session::forget('length_' . $i);
            Session::forget('addLength_' . $i);
            Session::forget('finishedQty_' . $i);
            Session::forget('actualStock_' . $i);
            Session::forget('quantity_' . $i);
            Session::forget('price_' . $i);
        }

        return redirect($request->previous_page)->with(['type' => 'success', 'alert_messenge' => $alert['success'] . ' "' . $data['number'] . '" ' . $alert['update_success']]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, $id = '')
    {
        $active_menu = Structure::where('structure_url', Session::get('active_menu'))->first();
        $alert = unserialize($active_menu->trans_page);
        $extend_ids = array_filter(explode(',',Auth::user()->extends_users));
                $extend_ids[] = Auth::user()->id;
        if (url()->previous() != url('export')) {
            return redirect('home');
        } else {
            if (!empty($id)) {
                $export = Export::where('id',$id)->whereIn('created_user',$extend_ids)->first();
                if (empty($export)) {
                    return redirect()->back()->with(['type' => 'danger', 'alert_messenge' => $alert['error'] . ' ' . $alert['data_error']]);
                } else {
                    $name = $export->number;
                    //Xóa nhập chi tiết
                    ExportDetails::where('id', $id)->delete();
                    //Xóa phiếu nhập
                    $export->delete($id);
                }
                return redirect('export')->with(['type' => 'success', 'alert_messenge' => $alert['success'] . ' "' . $name . '" ' . $alert['delete_success']]);
            } else {
                $ids = $request->check_products;
                foreach ($ids as $id) {
                    $export = Export::where('id',$id)->whereIn('created_user',$extend_ids)->first();
                    if (empty($export)) {
                        return redirect()->back()->with(['type' => 'danger', 'alert_messenge' => $alert['error'] . ' ' . $alert['data_error']]);
                    } else {
                        $name = $export->number;
                        //Xóa nhập chi tiết
                        ExportDetails::where('id', $id)->delete();
                        //Xóa phiếu nhập
                        $export->delete($id);
                    }
                }
                return redirect('export')->with(['type' => 'success', 'alert_messenge' => $alert['success'] . ' ' . $alert['export_checked'] . ' ' . $alert['delete_success']]);
            }
        }
    }
}
