<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Models\Structure;
use App\Models\StructureTranslation;
use Illuminate\Support\Facades\Session;

class StructuresController extends Controller
{
    public $main_page = 'structure';
    public $path = 'json_data/structure.json';
    public $permissions;
    public $langs;
    public $alert;

    public function __construct()
    {
        $this->langs = LanguagesController::getActiveLangs();
        $this->alert = $this->getTranslate();
    }

    // List
    public function index($id = 0)
    {
        $this->permissions = getPermissions();
        $list = $this->getStrucListOrderBySort();
        $colors = ['', 'text-primary', 'text-success', 'text-danger', 'text-secondary', 'text-warning', 'text-info', 'text-dark', 'text-light'];
        $tree = get_tree_view($list, $id, $colors, $this->main_page, $this->permissions);
        return view('Structures.list', ['tree_view' => $tree, 'add' => true]);
    }
    // End list

    // Add new
    public function addNew($pid = 0)
    {
        write_file_tree_json($this->path, $pid, 0, $this->permissions);
        return view('Structures.add', ['parent_id' => $pid, 'langs' => $this->langs]);
    }

    public function store(Request $request)
    {
        $this->validate($request, [
            'strucName_vi' => 'required',
            'parent_id' => 'required|numeric',
            'sort' => 'required|numeric',
        ], [
            'strucName_vi.required' => $this->alert['structure_name_vi_required'],
            'parent_id.required' => $this->alert['parent_id_required'],
            'parent_id.numeric' => $this->alert['parent_id_numeric'],
            'sort.required' => $this->alert['sort_required'],
            'sort.numeric' => $this->alert['sort_numeric']
        ]);
        $data = $request->all();
        $check_dublicate = $this->checkDublicateStruc(make_menu_url($request->strucName_en), $request->parent_id);

        if ($check_dublicate) {
            return redirect()->back()->with(['type' => 'danger', 'alert_messenge' => $this->alert['error'] . ' "' . $data['strucName_'.session('locale')] . '" ' . $this->alert['is_exist']]);
        }
        
        $parent = $this->getStrucParent($request->parent_id);
        $data['level'] = $request->parent_id == 0 ? 1 : $parent->level + 1;
        $data['url'] = make_menu_url($request->strucName_en);
        $newItem = $this->addNewItem($data);
        
        foreach ($this->langs as $lang) {
            $tData = ['item_id' => $newItem->id,'name' => $data['strucName_'.$lang->name],'lang' => $lang->name];
            $this->addNewItemTranslate($tData);
        }

        write_file_tree_json($this->path, $request->parent_id, 0, $this->permissions, 0);
        Session::put('parent_id', $request->parent_id);
        Session::put('sort', $request->sort);
        return redirect($this->main_page)->with(['type' => 'success', 'alert_messenge' => $this->alert['success'] . ' "' . $data['strucName_'.session('locale')] . '" ' . $this->alert['add_success']]);
    }
    // end add

    //Edit
    public function edit($id)
    {
        if (is_nan($id) || $id <= 0) {
            return redirect($this->main_page)->with(['type' => 'danger', 'alert_messenge' => $this->alert['error'] . ' ' . $this->alert['data_error']]);
        }

        $item = $this->getStrucInfo($id);

        if (!$item) {
            return redirect($this->main_page)->with(['type' => 'danger', 'alert_messenge' => $this->alert['error'] . ' ' . $this->alert['data_error']]);
        }

        $name = [];
        foreach ($this->langs as $lang) {
            $menu_trans = $this->getTranslateItem($id,$lang->name);
            $menu_trans = StructureTranslation::where('structure_id', $id)->where('locale', $lang->name)->first();
            if ($menu_trans) {
                $name[$lang->name] = $menu_trans->structure_name;
            } else {
                $name[$lang->name] = '';
            }
        }
        write_file_tree_json($this->path, 0, $item->id, $this->permissions);
        return view('Structures.edit', ['item' => $item, 'langs' => $this->langs, 'name' => $name]);
    }

    public function update(Request $request)
    {
        $this->validate($request, [
            'strucName_vi' => 'required',
            'parent_id' => 'required|numeric',
            'sort' => 'required|numeric',
        ], [
            'strucName_vi.required' => $this->alert['structure_name_vi_required'],
            'parent_id.required' => $this->alert['parent_id_required'],
            'parent_id.numerc' => $this->alert['parent_id_numeric'],
            'sort.required' => $this->alert['sort_required'],
            'sort.numerc' => $this->alert['sort_numeric']
        ]);
        $data = $request->all();

        $check_dublicate = $this->checkDublicateStrucOrtherId($request->item_id,make_menu_url($request->strucName_en),$request->parent_id);
        if ($check_dublicate){
            return redirect()->back()->with(['type' => 'danger', 'alert_messenge' => $this->alert['error'] . ' "' . $data['strucName_'.session('locale')]. '" ' . $this->alert['is_exist']]);
        }
        $parent = $this->getStrucParent($request->parent_id);
        //print_r($parent);exit;
        $data['level'] = ($request->parent_id == 0 ? 1 : $parent->level + 1);
        $data['url'] = make_menu_url($request->strucName_en);

        $this->updateItem($data);
        
        foreach ($this->langs as $lang){
            $tData = ['item_id' => $data['item_id'],'name' => $data['strucName_'.$lang->name],'lang' => $lang->name];
            $this->updateItemTraslate($tData);
        }

        write_file_tree_json($this->path,0,0,$this->permissions);
        return redirect($this->main_page)->with(['type' => 'success', 'alert_messenge' => $this->alert['success'].' "'.$data['strucName_'.session('locale')].'" '.$this->alert['update_success']]);
    }
    //End edit

    //Delete
    public function destroy(Request $request, $id){
        if (url()->previous() != url($this->main_page)) {
            return redirect('home');
        } else {
            $menu = $this->getStrucById($id);
            if (!$menu) {
                return redirect()->back()->with(['type' => 'danger', 'alert_messenge' => $this->alert['error'].' '.$this->alert['data_error']]);
            } else {
                $name = $menu->structure_name;
                if (check_have_child($id)) {
                    return redirect()->back()->with(['type' => 'danger', 'alert_messenge' => $this->alert['error'].' "'.$name.'" '.$this->alert['delete_error']]);
                } else {
                    $items = $this->getTranslateByItemId($id);
                    foreach ($items as $item) {
                        $item->delete();
                    }
                    $menu->delete();
                    return redirect()->back()->with(['type' => 'success', 'alert_messenge' => $this->alert['success'].' "'.$name.'" '.$this->alert['delete_success']]);
                }
            }
        }
    }
    //End Delete

    //static funtion
    public static function getActiveStrucList(){
        return Structure::where('status', 1)->where('page_type', 1)->orderBy('sort')->get();
    }

    public static function checkChildMenu($parentID, $pageType = 0)
    {
        if ($pageType == 1) {
            $check = Structure::where('parent_id', $parentID)->where('page_type', 1)->first();
        } else {
            $check = Structure::where('parent_id', $parentID)->first();
        }
        return $check;
    }

    public static function getStrucUrl($url){
        return Structure::where('structure_url', $url)->first();
    }

    public static function getPageTranslate($url){
        $str = StructuresController::getStrucUrl($url);
        return unserialize($str->trans_page);
    }

    public static function getSubStruc($id){
        return Structure::where('parent_id', $id)->where('page_type','!=',3)->get();
    }

    public static function checkHaveChild($id){
        return Structure::where('parent_id', $id)->first();
    }

    public static function getStrucById($id){
        return Structure::find($id);
    }

    public static function getSortList(){
        return Structure::where('status',1)->orderBy('sort')->get();
    }

    public static function checkActionPage($id,$type){
        return Structure::where('parent_id', $id)->where('page_type', $type)->orderBy('sort')->first();
    }

    public static function getMenuList($id,$type){
        return Structure::where('parent_id', $id)->where('page_type', $type)->orderBy('sort')->get();
    }

    public static function getCategoriesList(){
        return Structure::where('page_type','!=', 3)->where('status',1)->orderBy('sort')->get();

    }

    public static function getTranslateByStructId($item_id){
        return StructureTranslation::where('structure_id',$item_id)->get();
    }

    public static function getTranslateByStructIdLocale($item_id,$lang){
        return StructureTranslation::where('structure_id',$item_id)->where('locale',$lang)->first();
    }

    public static function getMenusPermission(){
        return Structure::where('id','!=',2)->where('page_type', '!=', 2)->where('status', 1)->orderBy('sort')->get();
    }
    public static function getClassPermission(){
        return Structure::where('page_type', 2)->where('status', 1)->orderBy('sort')->get();
    }

    //private function
    private function addNewItem($data){
        $newItem = new Structure();
            $newItem->page_type = $data['page_type'];
            $newItem->structure_url = $data['url'];
            $newItem->parent_id = (int) $data['parent_id'];
            $newItem->sort = $data['sort'];
            $newItem->level = $data['level'];
            $newItem->status = $data['status'];
            $newItem->icon = $data['icon'] ? $data['icon'] : '';
            $newItem->created_user = Auth::user()->id;
        $newItem->save();
        return $newItem;
    }

    private function addNewItemTranslate($data){
        $newTranslate = new StructureTranslation();
            $newTranslate->structure_id = $data['item_id'];
            $newTranslate->structure_name = $data['name'];
            $newTranslate->locale = $data['lang'];
        $newTranslate->save();
    }

    private function updateItem($data){

        $item = $this->getStrucById($data['item_id']);
            $item->page_type = $data['page_type'];
            $item->structure_url = $data['url'];
            $item->parent_id = (int) $data['parent_id'];
            $item->sort = $data['sort'];
            $item->level = $data['level'];
            $item->status = $data['status'];
            $item->icon = $data['icon'] ? $data['icon'] : '';
            $item->created_user = Auth::user()->id;
        $item->save();
    }

    private function updateItemTraslate($data){
        $translateItem = $this->getTranslateItem($data['item_id'],$data['lang']);
        if ($translateItem){
            $translateItem->structure_name = $data['name'];
            $translateItem->save();
        }else{
            $tData = ['item_id' => $data['item_id'],'name' => $data['name'],'lang' => $data['lang']];
            $this->addNewItemTranslate($tData);
        }
    }

    private function getStrucListOrderBySort()
    {
        $strucList = Structure::orderBy('sort')->get();
        return $strucList;
    }

    private function getStrucUrlByActive()
    {
        $strucUrl = Structure::where('structure_url', $this->main_page)->first();
        return $strucUrl;
    }

    private function getStrucInfo($id)
    {
        return Structure::find($id);
    }

    private function checkDublicateStruc($menu_url, $parent_id)
    {
        $struc = Structure::where('structure_url', $menu_url)->where('parent_id', $parent_id)->first();
        if ($struc) return true;
        else return false;
    }

    private function checkDublicateStrucOrtherId($id,$menu_url, $parent_id)
    {
        $struc = Structure::where('structure_url', $menu_url)->where('parent_id', $parent_id)->where('id','!=',$id)->first();
        if ($struc) return true;
        else return false;
    }

    private function getStrucParent($parent_id)
    {
        $struc = Structure::where('id', $parent_id)->first();
        return $struc;
    }

    private function getTranslate()
    {
        $struct = $this->getStrucUrlByActive();
        return unserialize($struct->trans_page);
    }

    //private Structure Translations
    private function getTranslateItem($item_id,$lang){
        return StructureTranslation::where('locale',$lang)->where('structure_id',$item_id)->first();
    }

    private function getTranslateByItemId($item_id){
        return StructureTranslation::where('structure_id',$item_id)->get();
    }
}
