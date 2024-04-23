<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Products;
use App\Models\ProductsDetails;
use Illuminate\Support\Facades\DB;

class AjaxController extends Controller
{
    public function input_output(Request $request)
    {
        /////
        if ($request->act == 'calculate_qty') {
            $price = (!empty($request->price) ? format_real_number($request->price) : 0);
            $quantity = (!empty($request->quantity) ? format_real_number($request->quantity) : 0);
            $total = $price * $quantity;
            echo json_encode(array(
                'quantity' => (!empty($quantity) ? format_number($quantity) : ""),
                'price' => (!empty($price) ? format_number($price) : ""),
                'total' => (!empty($total) ? format_number($total) : "")
            ));
        }
        ///////////
        if ($request->act == 'total_amount') {
            $amount = format_number($request->amount);
            echo json_encode(array('amount' => $amount));
        }
        //////////
    }
}
