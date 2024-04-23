<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Structure;
use App\Models\StructureTranslation;
use App\Models\Languages;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Auth;
use function Opis\Closure\unserialize;

class StructureController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

    public $main_page = 'structure';
    public $path = 'json_data/structure.json';

    public function index($id = 0)
    {
        $list = Structure::orderBy('sort')->get();
        $colors = ['', 'text-primary', 'text-success', 'text-danger', 'text-secondary', 'text-warning', 'text-info', 'text-dark', 'text-light'];
        if (Auth::user()->id != 1) $permissions = array_filter(Auth::user()->menus_permission,function ($value){return !is_null($value) && $value != '';});
        else $permissions = [];
        $tree = get_tree_view($list, $id, $colors, $this->main_page,$permissions);
        return view('Structures.list', ['tree_view' => $tree]);
    }

    /**
     * Show the form for creating a new resource,
     *
     * @return \Illuminate\Http\Response
     */
    public function create($parent_id = 0)
    {
        $langs = Languages::where('status',1)->get();
        $menu = Structure::where('structure_url',Session::get('active_menu'))->first();
        $menu->trans_page = unserialize($menu->trans_page);
        if (Auth::user()->id != 1) $permissions = array_filter(Auth::user()->menus_permission,function ($value){return !is_null($value) && $value != '';});
        else $permissions = [];
        write_file_tree_json($this->path,$parent_id,0,$permissions);
        return view('Structure.add', ['parent_id' => $parent_id,'menu' => $menu,'langs' => $langs]);
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
            'parent_id.numeric' => $alert['parent_id_numeric'],
            'sort.required' => $alert['sort_required'],
            'sort.numeric' => $alert['sort_numeric']
        ]);
        $data = $request->all();
        $menu_url = make_menu_url($request->structure_name_en);
        $is_exist = Structure::where('structure_url', $menu_url)->where('parent_id', $data['parent_id'])->first();
        if ($is_exist) {
            return redirect()->back()->with(['type' => 'danger', 'alert_messenge' => $alert['error'].' "'.$request->structure_name_en.'" '.$alert['dub_menu']]);
        } else {
            $parent = Structure::where('id', $data['parent_id'])->first();
            $menu = new Structure();
            $menu->page_type = $data['page_type'];
            $level = $data['parent_id'] == 0 ? 1 : $parent->level + 1;
            $menu->structure_url = $menu_url;
            $menu->parent_id = (int) $data['parent_id'];
            $menu->sort = $data['sort'];
            $menu->level = $level;
            $menu->status = $data['status'];
            $menu->icon = ($data['icon'] ? $data['icon'] : '');
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
            if (Auth::user()->id != 1) $permissions = array_filter(Auth::user()->menus_permission,function ($value){return !is_null($value) && $value != '';});
            else $permissions = [];
            write_file_tree_json($this->path,$request->parent_id,0,$permissions,0);
            Session::put('parent_id',$request->parent_id);
            Session::put('sort',$request->sort);
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
        $menu->structure_name_en = $name['en'];
        $menu->trans_page = unserialize($menu->trans_page);
        if (Auth::user()->id != 1) $permissions = array_filter(Auth::user()->menus_permission,function ($value){return !is_null($value) && $value != '';});
        else $permissions = [];
        write_file_tree_json($this->path, 0, $menu->id,$permissions);
        return view('Structure.edit', ['menu' => $menu,'langs' => $langs,'name' => $name]);
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
            return redirect()->back()->with(['type' => 'danger', 'alert_messenge' => $alert['error'].' "'.$request->structure_name_en.'" '.$alert['dub_menu']]);
        } else {
            $menu = Structure::find($id);
            $menu->page_type = $data['page_type'];
            $level = $data['parent_id'] == 0 ? 1 : $parent->level + 1;
            $menu->structure_url = $menu_url;
            $menu->parent_id = (int) $data['parent_id'];
            $menu->sort = $data['sort'];
            $menu->level = $level;
            $menu->status = $data['status'];
            $menu->icon = ($data['icon'] ? $data['icon'] : '');
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
            if (Auth::user()->id != 1) $permissions = array_filter(Auth::user()->menus_permission,function ($value){return !is_null($value) && $value != '';});
            else $permissions = [];
            write_file_tree_json($this->path,0,0,$permissions);
            return redirect('structure')->with(['type' => 'success', 'alert_messenge' => $alert['success'].' "'.$request->structure_name_en.'" '.$alert['update_success']]);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, $id)
    {
        $active_menu = Structure::where('structure_url',Session::get('active_menu'))->first();
        $alert = unserialize($active_menu->trans_page);
        if (url()->previous() != url('structure')) {
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
