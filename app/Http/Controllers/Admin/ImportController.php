<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Import;
use App\Models\Structure;
use App\Models\Products;
use App\Models\Staffs;
use App\Models\Customers;
use App\Models\ImportDetails;
use App\Models\ProductsDetails;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\DB;

class ImportController extends Controller
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
        $import_list = Import::select('import.*','customers.name AS supplier_name')
        ->where('import.wh_id',Auth::user()->wh_id)
        ->whereIn('import.created_user',$extend_ids)
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
        ->join('customers','import.supplier','=','customers.id')
        ->orderBy('date','DESC')
        ->paginate($limit);
        //return $import_list;
        $import_list->appends(['show' => $limit, 'sd' => $start_date, 'ed' => $end_date]);
        return view('Import.list', ['import_list' => $import_list, 'limit' => $limit,'key' => $key]);
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
        return view('Import.add', ['staffs' => $staffs, 'supplier' => $supplier, 'products' => $products_list]);
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
        $request->session()->put('import_type', $data['import_type']);
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

        $find_import = Import::where('number', $data['number'])->first();
        if ($find_import) {
            return redirect()->back()->with(['type' => 'danger', 'alert_messenge' => $alert['error'] . ' ' . $data['number'] . ' ' . $alert['number_is_exist']]);
        }

        $import = new Import();
        $import->wh_id = Auth::user()->wh_id;
        $import->date = format_Date($data['date']);
        $import->salesman = $data['salesman_id'];
        $import->supplier = $data['supplier_id'];
        $import->number = (!empty($data['number']) ? $data['number'] : '');
        $import->type = $data['import_type'];
        $import->note = (!empty($data['note']) ? $data['note'] : '');
        $import->items = $data['rows'];
        $import->created_user = Auth::user()->id;
        $import->save();

        $amount = 0;
        $count = 0;
        for ($i = 1; $i <= $rows; $i++) {
            if ($data["quantity_".$i] > 0) $qty = format_real_number($data['quantity_' . $i]);
            else $qty = 0;

            if ($qty != 0) {
                $import_detail = new ImportDetails();
                $import_detail->import_id = $import->id;
                $import_detail->product_id = $data['productId_' . $i];
                if ($data['calculateType_' . $i] == 2) {
                    $fQty = format_real_number($data['finishedQty_' . $i]);
                    if ($data['addLength_' . $i] > 0) $add_length = format_real_number($data['addLength_' . $i]);
                    else $add_length = 0;
                    $import_detail->width = format_real_number($data['width_' . $i]);
                    $import_detail->length = format_real_number($data['length_' . $i]);
                    $import_detail->add_length = $add_length;
                    $import_detail->finished_quantity = $fQty;
                }
                if ($data['calculateType_' . $i] == 1) {
                    $fQty = format_real_number($data['finishedQty_' . $i]);
                    $width = format_real_number($data['width_' . $i]);
                    $import_detail->finished_quantity = $fQty;
                    $import_detail->width = $width;
                    $import_detail->actual_quantity = $width."x".$fQty; 
                }
                $price = (!empty($data['price_' . $i]) ? format_real_number($data['price_' . $i]) : 0);
                $import_detail->quantity = $qty;
                $import_detail->price = $price;
                $import_detail->save();
                
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
        Session::forget('import_type');

        $import = Import::find($import->id);
        if ($count != $rows){
            $import->items = $count;
        }
        $import->amount = $amount;
        $import->save();
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
        //return $extend_ids;
        if (Auth::user()->id != 1){
            $import = Import::select('import.*', 'staffs.staff_code', 'staffs.id AS staff_id', 'staffs.name','customers.id AS supplier_id','customers.name AS supplier_name')
            ->join('staffs', 'import.salesman', '=', 'staffs.id')
            ->join('customers', 'import.supplier', '=', 'customers.id')
            ->where('import.id', $id)
            ->whereIn('import.created_user',$extend_ids)
            ->where('wh_id',Auth::user()->wh_id)
            ->first();
        }else{
            $import = Import::select('import.*', 'staffs.staff_code', 'staffs.id AS staff_id', 'staffs.name','customers.id AS supplier_id','customers.name AS supplier_name')
            ->join('staffs', 'import.salesman', '=', 'staffs.id')
            ->join('customers', 'import.supplier', '=', 'customers.id')
            ->where('import.id', $id)
            ->first();
        }

        if (!$import) {
            return redirect('import')->with(['type' => 'danger', 'alert_messenge' => $alert['error'] . ' ' . $alert['data_error']]);
        }

        $import_details = ImportDetails::select('import_details.*', 'pro.calculate_type', 'trans.product_name', 'trans.product_unit','br.actual_quantity')
            ->join('products AS pro', 'pro.id', '=', 'import_details.product_id')
            ->join('products_translations as trans', 'pro.id', '=', 'trans.products_id')
            ->where('trans.locale', Session::get('locale'))
            ->where('import_id', $id)
            ->join('products_details as br', 'br.product_id', '=', 'pro.id')
            ->where('br.wh_id',Auth::user()->wh_id)
            ->get();

        if (Auth::user()->id != 1){
            $permissions = array_filter(Auth::user()->products_permission,function ($value){return !is_null($value) && $value != '';});
            $products = Products::select('products.*', 'pro.product_name')
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
            $products = Products::select('products.*', 'pro.product_name')
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
        return view('Import.edit', ['staffs' => $staffs, 'supplier' => $supplier, 'products' => $products_list, 'import' => $import, 'import_details' => $import_details]);
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
        $import_rows = $data['rows'];
        $import = Import::find($id);
        //Check import ID
        if (!$import) {
            return redirect('import')->with(['type' => 'danger', 'alert_messenge' => $alert['error'] . ' ' . $alert['import_not_exist']]);
        }
        //End Check import ID

        //Check duplicate import number
        $duplicate_import = Import::where('number', $data['number'])->where('id', '!=', $data['import_id'])->first();
        if ($duplicate_import) {
            return redirect()->back()->with(['type' => 'danger', 'alert_messenge' => $alert['error'] . ' ' . $data['number'] . ' ' . $alert['number_is_exist']]);
        }
        //End Check duplicate import number

        //Make data table
        $zero_rows = 0;
        for ($i = 1; $i <= $import_rows; $i++) {
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
            $update_rows = $import->items;
        } else {
            $arr = explode(',', $data['delete_rows']);
            $del_rows = array_filter($arr, function ($k) {
                return $k != '';
            });
            $del_num = count($del_rows);
            $update_rows = $import->items - $del_num;
        }

        $amount = 0;
        $rowsDeleted = 0;
        for ($i = 1; $i <= $import_rows; $i++) {
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
                    ImportDetails::where('id', $data['idDetail_' . $i])->delete();
                    $rowsDeleted++;
                } else {
                    $item = ImportDetails::find($data['idDetail_' . $i]);
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
                    $item = new ImportDetails();
                    $item->import_id = $import->id;
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
                ImportDetails::where('id', $del_row)->delete();
            }
        }

        $import_rows -= $rowsDeleted;
        $import->date = format_Date($data['date']);
        $import->salesman = $data['salesman_id'];
        $import->supplier = $data['supplier_id'];
        $import->number = (!empty($data['number']) ? $data['number'] : '');
        $import->type = $data['import_type'];
        $import->note = (!empty($data['note']) ? $data['note'] : '');
        $import->items = $import_rows;
        $import->created_user = Auth::user()->id;
        $import->amount = $amount;
        $import->save();
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
        if (url()->previous() != url('import')) {
            return redirect('home');
        } else {
            if (!empty($id)) {
                $import = Import::where('id',$id)->whereIn('created_user',$extend_ids)->first();
                if (empty($import)) {
                    return redirect()->back()->with(['type' => 'danger', 'alert_messenge' => $alert['error'] . ' ' . $alert['data_error']]);
                } else {
                    $name = $import->number;
                    //Xóa nhập chi tiết
                    ImportDetails::where('id', $id)->delete();
                    //Xóa phiếu nhập
                    $import->delete($id);
                }
                return redirect('import')->with(['type' => 'success', 'alert_messenge' => $alert['success'] . ' "' . $name . '" ' . $alert['delete_success']]);
            } else {
                $ids = $request->check_products;
                foreach ($ids as $id) {
                    $import = Import::where('id',$id)->whereIn('created_user',$extend_ids)->first();
                    if (empty($import)) {
                        return redirect()->back()->with(['type' => 'danger', 'alert_messenge' => $alert['error'] . ' ' . $alert['data_error']]);
                    } else {
                        $name = $import->number;
                        //Xóa nhập chi tiết
                        ImportDetails::where('id', $id)->delete();
                        //Xóa phiếu nhập
                        $import->delete($id);
                    }
                }
                return redirect('export')->with(['type' => 'success', 'alert_messenge' => $alert['success'] . ' ' . $alert['import_checked'] . ' ' . $alert['delete_success']]);
            }
        }
    }

}
