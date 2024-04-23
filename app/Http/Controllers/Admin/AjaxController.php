<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Products;
use App\Models\ProductsDetails;
use Illuminate\Support\Facades\DB;

class AjaxController extends Controller
{
    public function import_export(Request $request)
    {
        if ($request->act == 'stock') {
            $id = $request->product_id;
            $check = ProductsDetails::where('product_id',$id)->where('wh_id',$request->wh)->first();
            if(empty($check)){
                $pro = new ProductsDetails();
                $pro->product_id = $id;
                $pro->wh_id = $request->wh;
                $pro->save();
            }
            $product = Products::select('products.*', 'str.structure_name', 'pro.product_name', 'br.posision','br.actual_quantity','br.2022 as begin',
            DB::raw('(SELECT SUM(quantity) FROM import_details AS imd,import AS im WHERE imd.product_id = products.id AND im.id = imd.import_id AND im.wh_id = '.$request->wh.') as import'),
            DB::raw('(SELECT SUM(quantity) FROM export_details AS exd,export AS ex WHERE exd.product_id = products.id AND ex.id = exd.export_id AND ex.wh_id = '.$request->wh.') as export')
            )
            ->join('structure_translations as str', 'str.structure_id', '=', 'products.parent_id')
            ->where('str.locale', $request->lang)
            ->join('products_translations as pro', 'products.id', '=', 'pro.products_id')
            ->where('pro.locale', $request->lang)
            ->join('products_details as br', 'br.product_id', '=', 'products.id')
            ->where('br.wh_id',$request->wh)
            ->where('products.id',$id)//->toSql();
            ->first();
            $stock = $product->begin + $product->import - $product->export;
            $actual = $product->actual_quantity;      
            echo json_encode(array('calculateType' => $product->calculate_type,'stock' => $stock, 'actual' => $actual));
        }

        /////
        if ($request->act == 'calculate_qty') {
            if ($request->calType == 2) {
                $width = (!empty($request->width) ? format_real_number($request->width) * 1 : 0);
                $length = (!empty($request->length) ? format_real_number($request->length) * 1 : 0);
                $addLength = (!empty($request->addLength) ? format_real_number($request->addLength) * 1 : 0);
                $fQty = (!empty($request->fQty) ? format_real_number($request->fQty) * 1 : 0);
                $width = (!empty($request->width) ? format_real_number($request->width) * 1 : 0);
                $price = (!empty($request->price) ? format_real_number($request->price) * 1 : 0);

                $qty = $width * ($length + $addLength) * $fQty / 1000000;
                $total = $price * $fQty;
                echo json_encode(array(
                    'qty' => (!empty($qty) ? format_number($qty) : ""),
                    'width' => (!empty($width) ? format_number($width) : ""),
                    'length' => (!empty($length) ? format_number($length) : ""),
                    'addLength' => (!empty($addLength) ? format_number($addLength) : ""),
                    'fQty' => (!empty($fQty) ? format_number($fQty) : ""),
                    'price' => (!empty($price) ? format_number($price) : ""),
                    'total' => (!empty($total) ? format_number($total) : ""),
                ));
            } elseif ($request->calType == 1) {
                $unit = (!empty($request->unit) ? $request->unit : '');
                $width = (!empty($request->width) ? format_real_number($request->width) * 1 : 0);
                $fQty = (!empty($request->fQty) ? format_real_number($request->fQty) * 1 : 0);
                $price = (!empty($request->price) ? format_real_number($request->price) * 1 : 0);
                $actStock = $request->actStock;
                if (strtolower($unit) == 'inch') {
                    $qty = $width / 25 * $fQty;
                }
                else {
                    $qty = $width * $fQty;
                }
                
                $total = $fQty * $price;
                echo json_encode(array(
                    'unit' => $unit,
                    'actStock' => $actStock,
                    'fQty' => (!empty($fQty) ? format_number($fQty) : ""),
                    'qty' => (!empty($qty) ? format_number($qty) : ""),
                    'price' => (!empty($price) ? format_number($price) : ""),
                    'total' => (!empty($total) ? format_number($total) : ""),
                ));
            } else {
                $price = (!empty($request->price) ? format_real_number($request->price) : 0);
                $qty = (!empty($request->qty) ? format_real_number($request->qty) : 0);
                $total = $price * $qty;
                echo json_encode(array(
                    'qty' => (!empty($qty) ? format_number($qty) : ""),
                    'price' => (!empty($price) ? format_number($price) : ""),
                    'total' => (!empty($total) ? format_number($total) : "")
                ));
            }
        }
        ///////////
        if ($request->act == 'total_amount') {
            $amount = format_number($request->amount);
            echo json_encode(array('amount' => $amount));
        }
        //////////
    }
}
