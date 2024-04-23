<?php

namespace App\Http\Controllers\Admin;

use App\Models\Structure;
use App\Models\StructureTranslation;
use App\Models\Languages;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Auth;

class CategoriesController extends Controller
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
        $list = Structure::where('page_type','!=',3)->orderBy('sort')->get();
        $colors = ['', 'text-primary', 'text-success', 'text-danger', 'text-secondary', 'text-warning', 'text-info', 'text-dark', 'text-light'];
        if (Auth::user()->id != 1) $permissions = array_filter(Auth::user()->products_permission,function ($value){return !is_null($value) && $value != '';});
        else $permissions = [];
        //return $permissions;
        $tree = get_tree_view($list, $this->parent_id, $colors, $this->main_page,$permissions);
        return view('Categories.list', ['tree_view' => $tree]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create($parent_id = 0)
    {
        $langs = Languages::where('status',1)->get();
        if ($parent_id == 0) $parent_id = $this->parent_id;
        if (Auth::user()->id != 1) $permissions = array_filter(Auth::user()->products_permission,function ($value){return !is_null($value) && $value != '';});
        else $permissions = [];
        write_file_categories_json($this->path,$parent_id,0,$permissions);
        return view('Categories.add', ['parent_id' => $parent_id,'langs' => $langs]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $active_menu = Structure::where('structure_url',Session::get('active_menu'))->first();
        $alert = unserialize($active_menu->trans_page);
        $this->validate($request, [
            'structure_name_vi' => 'required',
            'parent_id' => 'required|numeric',
            'sort' => 'required|numeric',
        ], [
            'structure_name_vi.required' => $alert['structure_name_vi_required'],
            'parent_id.required' => $alert['parent_id_required'],
            'parent_id.numerc' => $alert['parent_id_numeric'],
            'sort.required' => $alert['sort_required'],
            'sort.numerc' => $alert['sort_numeric']
        ]);
        $data = $request->all();
        $menu_url = make_menu_url($request->structure_name_en);
        $is_exist = Structure::where('structure_url', $menu_url)->where('parent_id', $data['parent_id'])->first();
        if ($is_exist) {
            return redirect('categories')->with(['type' => 'danger', 'alert_messenge' => $alert['error'].' "'.$request->structure_name_en.'" '.$alert['dub_menu']]);
        } else {
            $parent = Structure::where('id', $data['parent_id'])->first();
            $parent_id = (int) $data['parent_id'];
            $menu = new Structure();
            $menu->page_type = 2;
            $level = $data['parent_id'] == 0 ? 1 : $parent->level + 1;
            $menu->structure_url = $menu_url;
            $menu->parent_id = $parent_id;
            $menu->sort = $data['sort'];
            $menu->level = $level;
            $menu->status = $data['status'];
            $menu->icon = '';
            $menu->created_user = Auth::user()->id;
            $menu->save();

            $langs = Languages::where('status',1)->get();
            foreach ($langs as $lang){
                $trans = new StructureTranslation();
                $trans->structure_id = $menu->id;
                $trans->structure_name = $data['structure_name_'.$lang->name];
                $trans->locale = $lang->name;
                $trans->save();
            }

            if (Auth::user()->id != 1) $permissions = array_filter(Auth::user()->products_permission,function ($value){return !is_null($value) && $value != '';});
            else $permissions = [];
            write_file_categories_json($this->path,$parent_id,0,$permissions);

            return redirect()->back()->with(['type' => 'success', 'alert_messenge' => $alert['success'].' "'.$request->structure_name_en.'" '.$alert['add_success']]);
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
        $langs = Languages::where('status',1)->get();
        $name = [];
        foreach($langs as $lang)
        {
            $menu_trans = StructureTranslation::where('structure_id',$id)->where('locale',$lang->name)->first();
                if($menu_trans){
                    $name[$lang->name] = $menu_trans->structure_name;
                }else{
                    $name[$lang->name] = '';
                }
        }
        $menu = Structure::find($id);
        if (Auth::user()->id != 1) $permissions = array_filter(Auth::user()->products_permission,function ($value){return !is_null($value) && $value != '';});
        else $permissions = [];
        write_file_categories_json($this->path, $this->parent_id, $menu->id,$permissions);
        return view('Categories.edit', ['menu' => $menu,'langs' => $langs,'name' => $name]);
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
        $active_menu = Structure::where('structure_url',Session::get('active_menu'))->first();
        $alert = unserialize($active_menu->trans_page);
        $this->validate($request, [
            'structure_name_vi' => 'required',
            'parent_id' => 'required|numeric',
            'sort' => 'required|numeric',
        ], [
            'structure_name_vi.required' => $alert['structure_name_vi_required'],
            'parent_id.required' => $alert['parent_id_required'],
            'parent_id.numerc' => $alert['parent_id_numeric'],
            'sort.required' => $alert['sort_required'],
            'sort.numerc' => $alert['sort_numeric']
        ]);
        $data = $request->all();
        $menu_url = make_menu_url($data['structure_name_en']);
        $parent = Structure::where('id', $data['parent_id'])->first();
        $is_exist = Structure::where('structure_url', $menu_url)->where('parent_id', $data['parent_id'])->where('id', '!=', $id)->first();

        if ($is_exist) {
            return redirect('categories')->with(['type' => 'danger', 'alert_messenge' => $alert['error'].' "'.$request->structure_name_en.'" '.$alert['dub_menu']]);
        } else {
            $menu = Structure::find($id);
            $level = $data['parent_id'] == 0 ? 1 : $parent->level + 1;
            $menu->structure_url = $menu_url;
            $menu->parent_id = (int) $data['parent_id'];
            $menu->sort = $data['sort'];
            $menu->level = $level;
            $menu->status = $data['status'];
            $menu->created_user = Auth::user()->id;
            $menu->save();

            $trans = StructureTranslation::where('structure_id', $id)->where('locale', Session::get('locale'))->first();
            $trans->structure_id = $id;
            $trans->structure_name = $request->structure_name;
            $trans->locale = Session::get('locale');
            $trans->save();

            $langs = Languages::where('status',1)->get();
            foreach ($langs as $lang){
                $trans = StructureTranslation::where('locale',$lang->name)->where('structure_id',$id)->first();
                if($trans){
                    $trans->structure_name = $data['structure_name_'.$lang->name];
                    $trans->locale = $lang->name;
                    $trans->save();
                }else{
                    $trans = new StructureTranslation();
                    $trans->structure_id = $id;
                    $trans->structure_name = $data['structure_name_'.$lang->name];
                    $trans->locale = $lang->name;
                    $trans->save();
                }
            }
            if (Auth::user()->id != 1) $permissions = array_filter(Auth::user()->products_permission,function ($value){return !is_null($value) && $value != '';});
            else $permissions = [];
            write_file_categories_json($this->path, 0,0,$permissions);
            return redirect('categories')->with(['type' => 'success', 'alert_messenge' => $alert['success'].' "'.$request->structure_name_en.'" '.$alert['update_success']]);
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
        if (url()->previous() != url('categories')) {
            return redirect('home');
        } else {
            $menu = Structure::where('id', $id)->first();
            $name = $menu->structure_name;
            if (!$menu) {
                return redirect()->back()->with(['type' => 'danger', 'alert_messenge' => $alert['error'].' "'.$name.'" '.$alert['data_error']]);
            } else {
                if (check_have_child($id)) {
                    return redirect()->back()->with(['type' => 'danger', 'alert_messenge' => $alert['error'].' "'.$name.'" '.$alert['delete_error']]);
                } else {
                    $menu->delete($id);
                    $trans = StructureTranslation::where('structure_id', $id)->get();
                    foreach ($trans as $lang) {
                        $trans->delete($lang->id);
                    }
                    return redirect()->back()->with(['type' => 'success', 'alert_messenge' => $alert['success'].' "'.$name.'" '.$alert['delete_success']]);
                }
            }
        }
    }
}
