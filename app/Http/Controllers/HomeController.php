<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Structure;

class HomeController extends Controller
{
    public function index(){
        return view('Home.list');
    }
}
