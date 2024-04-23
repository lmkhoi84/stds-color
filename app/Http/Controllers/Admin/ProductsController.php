<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\ProductsDetails;
use App\Models\Structure;
use App\Models\Products;
use App\Models\ProductsTranslation;
use App\Models\Languages;
use App\Models\Warehouse;
use Illuminate\Support\Facades\Session;
use function Opis\Closure\unserialize;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Auth;

class ProductsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

    public $main_page = 'categories';
    public $parent_id;
    public $path = 'json_data/categories.json';

    public function __construct()
    {
        $category = Structure::where('structure_url', $this->main_page)->first();
        $this->parent_id = $category->id;
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
        if (Auth::user()->id != 1) {
            $permissions = array_filter(Auth::user()->products_permission, function ($value) {
                return !is_null($value) && $value != '';
            });
            $products = Products::select('products.*', 'str.structure_name', 'pro.product_name', 'br.posision')
                ->whereIn('parent_id', $permissions)
                ->join('structure_translations as str', 'str.structure_id', '=', 'products.parent_id')
                ->where('str.locale', Session::get('locale'))
                ->join('products_translations as pro', 'products.id', '=', 'pro.products_id')
                ->where('pro.locale', Session::get('locale'))
                ->join('products_details as br', 'br.product_id', '=', 'products.id')
                ->where('br.wh_id', 1)
                ->where(function ($query) use ($key) {
                    $query->orWhere('pro.product_name', 'LIKE', '%' . $key . '%');
                    $query->orWhere('br.posision', 'LIKE', '%' . $key . '%');
                    $query->orWhere('str.structure_name', 'LIKE', '%' . $key . '%');
                })
                ->orderBy('products.parent_id', 'ASC')->orderBy('pro.product_name', 'ASC')
                ->paginate($limit);
        } else {
            $permissions = [];

            $products = Products::select('products.*', 'str.structure_name', 'pro.product_name', 'br.posision')
                ->join('structure_translations as str', 'str.structure_id', '=', 'products.parent_id')
                ->where('str.locale', Session::get('locale'))
                ->join('products_translations as pro', 'products.id', '=', 'pro.products_id')
                ->where('pro.locale', Session::get('locale'))
                ->join('products_details as br', 'br.product_id', '=', 'products.id')
                ->where('br.wh_id', 1)
                ->where(function ($query) use ($key) {
                    $query->where('pro.product_name', 'LIKE', '%' . $key . '%');
                    $query->orWhere('br.posision', 'LIKE', '%' . $key . '%');
                    $query->orWhere('str.structure_name', 'LIKE', '%' . $key . '%');
                })
                ->orderBy('products.parent_id', 'ASC')->orderBy('pro.product_name', 'ASC')
                ->paginate($limit);
        }
        $products->appends(['show' => $limit, 'search' => $key]);
        return view('Products.list', ['products' => $products, 'limit' => $limit, 'key' => $key]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $langs = Languages::where('status', 1)->get();
        if (Auth::user()->id != 1) {
            $permissions = array_filter(Auth::user()->products_permission, function ($value) {
                return !is_null($value) && $value != '';
            });
            $products = Products::select('products.*', 'str.structure_name', 'pro.product_name', 'br.posision')
                ->whereIn('parent_id', $permissions)
                ->join('structure_translations as str', 'str.structure_id', '=', 'products.parent_id')
                ->where('str.locale', Session::get('locale'))
                ->join('products_translations as pro', 'products.id', '=', 'pro.products_id')
                ->where('pro.locale', Session::get('locale'))
                ->join('products_details as br', 'br.product_id', '=', 'products.id')
                ->where('br.wh_id', 1)
                ->orderBy('products.parent_id', 'ASC')->orderBy('pro.product_name', 'ASC')
                ->get();
        } else {
            $permissions = [];

            $products = Products::select('products.*', 'str.structure_name', 'pro.product_name', 'br.posision')
                ->join('structure_translations as str', 'str.structure_id', '=', 'products.parent_id')
                ->where('str.locale', Session::get('locale'))
                ->join('products_translations as pro', 'products.id', '=', 'pro.products_id')
                ->where('pro.locale', Session::get('locale'))
                ->join('products_details as br', 'br.product_id', '=', 'products.id')
                ->where('br.wh_id', 1)
                ->orderBy('products.parent_id', 'ASC')->orderBy('pro.product_name', 'ASC')
                ->get();
        }
        write_file_categories_json($this->path, $this->parent_id, 0, $permissions);

        $pro_list = [];
        foreach ($products as $product) {
            $pro_list[] = "{
                value: '" . $product["id"] . "',
                label: '" . addslashes($product["product_name"]) . "',
                unit: '" . $product["product_unit"] . "'
            }";
        }
        $products_list = "[" . implode(',', $pro_list) . "]";
        //return $products_list;
        return view('Products.add', ['parent_id' => $this->parent_id, 'langs' => $langs, 'products' => $products_list]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $lang = Session::get('locale');
        $active_menu = Structure::where('structure_url', Session::get('active_menu'))->first();
        $alert = unserialize($active_menu->trans_page);
        $this->validate($request, [
            'product_name_vi' => 'required',
            'parent_id' => 'required|numeric',
            'unit_vi' => 'required',
        ], [
            'product_name_vi.required' => $alert['product_name_vi_required'],
            'parent_id.required' => $alert['parent_id_required'],
            'parent_id.numeric' => $alert['parent_id_numeric'],
            'unit_vi.required' => $alert['unit_vi_required'],
        ]);

        $langs = Languages::where('status', 1)->get();

        if ($request->processing == 1) {
            foreach ($langs as $lang) {
                $this->validate($request, [
                    'processing_unit_' . $lang->name => 'required',
                ], [
                    'processing_unit_vi' . $lang->name . '.required' => $alert['processing_unit_required'],
                ]);
            }
        }

        $data = $request->all();
        $files = $request->file('images');
        $filesName = array();
        $path = 'images/products';
        //$path = 'public/images/products';
        if ($request->hasFile('images')) {
            foreach ($files as $file) {
                if (!File::isDirectory($path)) {
                    File::makeDirectory($path, 0777, true, true);
                }

                $allowExtension = ['jpeg', 'png', 'jpg', 'gif'];

                if ($file->isValid()) {
                    $extension = $file->getClientOriginalExtension();

                    if (!in_array($extension, $allowExtension)) {
                        return redirect()->back()->with(['type' => 'danger', 'alert_messenge' => $alert['error'] . ' ' . $alert['error_extension']]);
                    }
                    $have_files = true;
                } else {
                    return redirect()->back()->with(['type' => 'danger', 'alert_messenge' => $alert['error'] . ' ' . $alert['error_image']]);
                }
            }
        } else {
            $have_files = false;
        }

        $product = new Products();
        $product->product_url = make_menu_url($request->product_name_en);
        $product->parent_id = $request->parent_id;
        $product->sort = $request->sort;
        //$product->images = $files_name;
        $product->status = $request->status;
        $product->sale_off_type = $request->sale_off_type;
        $product->show_in_stock = $request->show_in_stock;
        $product->calculate_type = $request->calculate_type;
        $product->processing = $request->processing;
        $product->created_user = Auth::user()->id;
        $product->save();

        $wh = count(Warehouse::get());
        for ($i = 1; $i <= $wh; $i++) {
            $product_detail = new ProductsDetails();
            $product_detail->product_id = $product->id;
            $product_detail->wh_id = $i;
            if ($i == Auth::user()->wh_id) {
                $product_detail->posision = $request->posision;
                $product_detail->actual_quantity = $request->actual_quantity;
            }
            $product_detail->save();
        }

        $id = $product->id;
        foreach ($langs as $lang) {
            $translate = new ProductsTranslation();
            $translate->products_id = $id;
            $translate->product_name = $data['product_name_' . $lang->name];
            $translate->product_unit = $data['unit_' . $lang->name];
            $translate->processing_unit = ($request->processing == 1 ? $data['processing_unit_' . $lang->name] : '');
            $translate->locale = $lang->name;
            $translate->save();
        }

         if (!File::isDirectory($path)) {
            File::makeDirectory($path, 0777, true, true);
        }

        if ($have_files) {
            foreach ($files as $file) {
                $file_name = make_menu_url($id . '-' . $file->getClientOriginalName());
                $filesName[] = $file_name;
                $file->move($path, $file_name);
            }
            $files_name = implode('|', $filesName);
        } else {
            $files_name = '';
        }
        $update_product = Products::find($id);
        $update_product->images = $files_name;
        $update_product->save();
        //return "ok";
        return redirect()->back()->with(['type' => 'success', 'alert_messenge' => $alert['success'] . ' "' . $request->product_name_vi . '" ' . $alert['add_success'], 'parent_id' => $request->parent_id]);
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
        $langs = Languages::where('status', 1)->get();
        foreach ($langs as $lang) {
            $menu_trans = ProductsTranslation::where('products_id', $id)->where('locale', $lang->name)->first();
            if ($menu_trans) {
                $name[$lang->name] = $menu_trans->product_name;
                $unit[$lang->name] = $menu_trans->product_unit;
                $processing_unit[$lang->name] = $menu_trans->processing_unit;
            } else {
                $name[$lang->name] = '';
                $unit[$lang->name] = '';
                $processing_unit[$lang->name] = '';
            }
        }
        if (Auth::user()->id != 1) $permissions = array_filter(Auth::user()->products_permission, function ($value) {
            return !is_null($value) && $value != '';
        });
        else {
            $permissions = [];
        }
        write_file_categories_json($this->path, $this->parent_id, 0, $permissions);

        $product = Products::select('products.*', 'br.posision', 'pro.product_name','br.actual_quantity')
            ->join('products_translations as pro', 'products.id', '=', 'pro.products_id')
            ->join('products_details as br', 'br.product_id', '=', 'products.id')
            ->where('pro.locale', Session::get('locale'))
            ->where('products.id', $id)
            ->first();
        if (!$product) {
            return redirect('products')->with(['type' => 'danger', 'alert_messenge' => $alert['error'] . ' ' . $alert['data_error']]);
        } else {
            if (Auth::user()->id != 1) {
                $permissions = array_filter(Auth::user()->products_permission, function ($value) {
                    return !is_null($value) && $value != '';
                });
                $products = Products::select('products.*', 'str.structure_name', 'pro.product_name', 'br.posision')
                    ->whereIn('parent_id', $permissions)
                    ->join('structure_translations as str', 'str.structure_id', '=', 'products.parent_id')
                    ->where('str.locale', Session::get('locale'))
                    ->join('products_translations as pro', 'products.id', '=', 'pro.products_id')
                    ->where('pro.locale', Session::get('locale'))
                    ->join('products_details as br', 'br.product_id', '=', 'products.id')
                    ->where('br.wh_id', 1)
                    ->orderBy('products.parent_id', 'ASC')->orderBy('pro.product_name', 'ASC')
                    ->get();
            } else {
                $permissions = [];

                $products = Products::select('products.*', 'str.structure_name', 'pro.product_name', 'br.posision')
                    ->join('structure_translations as str', 'str.structure_id', '=', 'products.parent_id')
                    ->where('str.locale', Session::get('locale'))
                    ->join('products_translations as pro', 'products.id', '=', 'pro.products_id')
                    ->where('pro.locale', Session::get('locale'))
                    ->join('products_details as br', 'br.product_id', '=', 'products.id')
                    ->where('br.wh_id', 1)
                    ->orderBy('products.parent_id', 'ASC')->orderBy('pro.product_name', 'ASC')
                    ->get();
            }
            $pro_list = [];
            foreach ($products as $pro) {
                $pro_list[] = "{
                    value: '" . $pro["id"] . "',
                    label: '" . addslashes($pro["product_name"]) . "',
                    desc: '" . $pro["sale_off_type"] . "',
                    unit: '" . $pro["product_unit"] . "'
                }";
            }
            $products_list = "[" . implode(',', $pro_list) . "]";
            if (!empty($product->images)){
                $array = explode('|', $product->images);
                $images = [];
                for ($i = 0; $i < count($array); $i++) {
                    //$images[] = array('id' => $array[$i], 'src' => url('public/images/products/' . $array[$i]));
                    $images[] = array('id' => $array[$i], 'src' => url('images/products/' . $array[$i]));
                }
                
            }else{
                $images = [];
            }
            
            $images = json_encode($images);

            return view('Products.edit', ['product' => $product, 'products' => $products_list, 'langs' => $langs, 'name' => $name, 'unit' => $unit, 'processing_unit' => $processing_unit, 'images' => $images]);
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
        $lang = Session::get('locale');
        $active_menu = Structure::where('structure_url', Session::get('active_menu'))->first();
        $alert = unserialize($active_menu->trans_page);
        $this->validate($request, [
            'product_name_vi' => 'required',
            'parent_id' => 'required|numeric',
            'unit_vi' => 'required',
        ], [
            'product_name_vi.required' => $alert['product_name_vi_required'],
            'parent_id.required' => $alert['parent_id_required'],
            'parent_id.numeric' => $alert['parent_id_numeric'],
            'unit_vi.required' => $alert['unit_vi_required'],
        ]);

        $langs = Languages::where('status', 1)->get();

        if ($request->processing == 1) {
            foreach ($langs as $lang) {
                $this->validate($request, [
                    'processing_unit_' . $lang->name => 'required',
                ], [
                    'processing_unit_vi' . $lang->name . '.required' => $alert['processing_unit_required'],
                ]);
            }
        }

        $product = Products::select('products.*', 'br.posision', 'pro.product_name')
            ->join('products_translations as pro', 'products.id', '=', 'pro.products_id')
            ->join('products_details as br', 'br.product_id', '=', 'products.id')
            ->where('pro.locale', Session::get('locale'))
            ->where('products.id', $id)
            ->first();


        if (!$product) {
            return redirect('products')->with(['type' => 'danger', 'alert_messenge' => $alert['error'] . ' ' . $alert['data_error']]);
        } else {
            $data = $request->all();
            $files = $request->file('images');
            $old_files = array_filter(explode('|', $product->images));
            $new_files = array();
            $filesName = [];
            $path = 'images/products';
            //$path ='public/images/products/';
            if (!$request->old_images) $request->old_images = [];

            if ($request->hasFile('images')) {
                foreach ($files as $file) {
                    if (!File::isDirectory($path)) {
                        File::makeDirectory($path, 0777, true, true);
                    }

                    $allowExtension = ['jpeg', 'png', 'jpg', 'gif'];

                    if ($file->isValid()) {
                        $extension = $file->getClientOriginalExtension();

                        if (!in_array($extension, $allowExtension)) {
                            return redirect()->back()->with(['type' => 'danger', 'alert_messenge' => $alert['error'] . ' ' . $alert['error_extension']]);
                        }

                        //$old_file = public_path($path . '/' . $product->images);
                        //File::delete($old_file);
                        $file_name = make_menu_url($product->id . '-' . $file->getClientOriginalName());

                        if (!in_array($file_name, $old_files)) {
                            $filesName[] = $file_name;
                            $new_files[] = $file_name;
                        } else {
                            continue;
                        }
                        $have_files = true;
                    } else {
                        return redirect()->back()->with(['type' => 'danger', 'alert_messenge' => $alert['error'] . ' ' . $alert['error_image']]);
                    }
                }

                $files_name = implode('|', $filesName);
                $have_files = true;
            } else {
                $files_name = '';
                $have_files = false;
            }

            if (count($request->old_images) > 0) {
                $oldImages = implode("|", $request->old_images);
                if ($have_files){
                    $files_name = $oldImages.'|'.$files_name;
                }else{
                    $files_name = $oldImages;
                }
            }
             for ($i = 0; $i < count($old_files); $i++) {
                    if (!in_array($old_files[$i], $request->old_images)) {
                        $deleted_file = $path.$old_files[$i];
                        File::delete($deleted_file);
                    }
                }
            
            if (count($new_files) == 0) $have_files = false;

            $product->product_url = make_menu_url($request->product_name_vi);
            $product->parent_id = $request->parent_id;
            $product->sort = $request->sort;
            $product->images = $files_name;
            $product->status = $request->status;
            $product->sale_off_type = $request->sale_off_type;
            $product->show_in_stock = $request->show_in_stock;
            $product->calculate_type = $request->calculate_type;
            $product->processing = $request->processing;
            $product->save();
            $products_detail = ProductsDetails::where('product_id', $product->id)->where('wh_id', Auth::user()->wh_id)->first();

            $products_detail->posision = ($request->posision?$request->posision:null);
            $products_detail->actual_quantity = $request->actual_quantity;
            $products_detail->save();

            foreach ($langs as $lang) {
                $translate = ProductsTranslation::where('products_id', $id)->where('locale', $lang->name)->first();
                $translate->products_id = $product->id;
                $translate->product_name = $data['product_name_' . $lang->name];
                $translate->product_unit = $data['unit_' . $lang->name];
                $translate->processing_unit = ($request->processing == 1 ? $data['processing_unit_' . $lang->name] : '');
                $translate->locale = $lang->name;
                $translate->save();
            }
            //return $new_files;
            if ($have_files) {
                foreach ($files as $file) {
                    $file_name = make_menu_url($id . '-' . $file->getClientOriginalName());
                    $file->move($path, $file_name);
                }
            }
            return redirect($request->previous_page)->with(['type' => 'success', 'alert_messenge' => $alert['success'] . ' "' . $request->product_name_vi . '" ' . $alert['update_success']]);
        }
    }

    public function change_status($id)
    {
        $active_menu = Structure::where('structure_url', Session::get('active_menu'))->first();
        $alert = unserialize($active_menu->trans_page);
        $product = Products::find($id);
        if ($product) {
            if ($product->status == 0) $status = 1;
            else $status = 0;
            $product->status = $status;
            $product->save();
            return redirect()->back()->with(['type' => 'success', 'alert_messenge' => $alert['success'] . ' "' . $product->product_name . '" ' . $alert['status_changed']]);
        } else {
            return redirect()->back()->with(['type' => 'danger', 'alert_messenge' => $alert['error'] . ' "' . $product->product_name . '" ' . $alert['data_error']]);
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
        $active_menu = Structure::where('structure_url', Session::get('active_menu'))->first();
        $alert = unserialize($active_menu->trans_page);
        if (url()->previous() != url('products')) {
            return redirect('home');
        } else {
            if (!empty($id)) {
                $product = Products::select('products.*', 'br.posision', 'pro.product_name')
                    ->join('products_translations as pro', 'products.id', '=', 'pro.products_id')
                    ->join('products_details as br', 'br.product_id', '=', 'products.id')
                    ->where('pro.locale', Session::get('locale'))
                    ->where('products.id', $id)
                    ->first();
                if (!$product) {
                    return redirect()->back()->with(['type' => 'danger', 'alert_messenge' => $alert['error'] . ' ' . $alert['data_error']]);
                } else {
                    $name = $product->product_name;
                    $products_details = ProductsDetails::where('product_id', $product->id)->get();
                    foreach ($products_details as $prod) {
                        $prod->delete('product_id', $product->id);
                    }
                    $images = explode("|", $product->images);
                    foreach ($images as $image) {
                        $old_file = public_path('public/images/products/' . $image);
                        File::delete($old_file);
                    }
                    $product->delete($id);
                    return redirect('products')->with(['type' => 'success', 'alert_messenge' => $alert['success'] . ' "' . $name . '" ' . $alert['delete_success']]);
                }
            } else {
                $ids = $request->check_products;
                foreach ($ids as $id) {
                    $product = Products::select('products.*', 'br.posision', 'pro.product_name')
                        ->join('products_translations as pro', 'products.id', '=', 'pro.products_id')
                        ->join('products_' . $this->wh . ' as br', 'br.product_id', '=', 'products.id')
                        ->where('pro.locale', Session::get('locale'))
                        ->where('products.id', $id)
                        ->first();
                    if (!$product) {
                        return redirect()->back()->with(['type' => 'danger', 'alert_messenge' => $alert['error'] . ' ' . $alert['data_error']]);
                    } else {
                        $name = $product->product_name;
                        $products_details = ProductsDetails::where('product_id', $product->id)->get();
                        foreach ($products_details as $prod) {
                            $prod->delete('product_id', $product->id);
                        }
                        $images = explode("|", $product->images);
                        foreach ($images as $image) {
                            $old_file = public_path('public/images/products/' . $image);
                            File::delete($old_file);
                        }
                        $product->delete($id);
                    }
                }
                return redirect('products')->with(['type' => 'success', 'alert_messenge' => $alert['success'] . ' "' . $alert['products_checked'] . '" ' . $alert['delete_success']]);
            }
        }
    }
}
