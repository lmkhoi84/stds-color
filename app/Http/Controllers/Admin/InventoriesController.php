<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Structure;
use Illuminate\Support\Facades\Session;

class InventoriesController extends Controller
{
    public function index($wh){
        $page = Structure::where('menu_url',$wh)->first();
        if (!$page) return redirect('home');
        if (Session::get('locale')=='vi') $name = $page->menu_name_vi;
        else $name = $page->menu_name_en;
        return view('Inventories.list',['wh' => $name]);
    }
}
