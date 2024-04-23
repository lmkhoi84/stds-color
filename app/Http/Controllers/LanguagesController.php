<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Languages;
use App\Models\LanguagesTranslation;
use App\Http\Controllers\StructuresController;
use Illuminate\Support\Facades\Auth;

class LanguagesController extends Controller
{
    public $main_page = 'multi-languages';
    public $langs;
    public $alert;

    public function __construct()
    {
        $this->langs = LanguagesController::getActiveLangs();
        $this->alert = StructuresController::getPageTranslate($this->main_page);
    }

    //Language
    public function index()
    {
        $list = $this->getLangList();
        return view('languages.list', ['itemsList' => $list, 'add' => true,'search' => true]);
    }

    public function addNew()
    {
        return view('Languages.add', ['langs' => $this->langs]);
    }

    public function store(Request $request)
    {
        $this->validate($request, [
            'name_en' => 'required',
            'code' => 'required',
        ], [
            'name_en.required' => $this->alert['name_en_required'],
            'code.required' => $this->alert['code_required'],
        ]);
        $is_exist = Languages::where('name', $request->code)->first();
        if ($is_exist) {
            return redirect()->back()->with(['type' => 'danger', 'alert_messenge' => $this->alert['error'] . ' "' . $request->name_en . '" ' . $this->alert['is_exist']]);
        } else {
            $data = $request->all();
            $newItem = $this->addNewItem($data);

            foreach ($this->langs as $lang) {
                $tData = ['item_id' => $newItem->id, 'name' => $data['name_' . $lang->name], 'lang' => $lang->name];
                $this->addNewItemTranslate($tData);
            }
            return redirect()->back()->with(['type' => 'success', 'alert_messenge' => $this->alert['success'] . ' "' . $data['name_en'] . '" ' . $this->alert['add_success']]);
        }
    }

    public function edit($id)
    {
        $item = $this->getInfo($id);
        if (!$item) {
            return redirect('languages')->with(['type' => 'danger', 'alert_messenge' => $this->alert['error'] . ' ' . $this->alert['data_error']]);
        } else {
            $name = [];
            foreach ($this->langs as $lang) {
                $lang_trans = LanguagesTranslation::where('languages_id', $id)->where('locale', $lang->name)->first();
                if ($lang_trans) {
                    $name[$lang->name] = $lang_trans->languages_name;
                } else {
                    $name[$lang->name] = '';
                }
            }
            return view('Languages.edit', ['item' => $item, 'langs' => $this->langs, 'name' => $name]);
        }
    }

    public function update(Request $request)
    {
        $this->validate($request, [
            'name_en' => 'required',
            'code' => 'required',
        ], [
            'name_en.required' => $this->alert['name_en_required'],
            'code.required' => $this->alert['code_required'],
        ]);

        $item = $this->getInfo($request->item_id);
        if (!$item) {
            return redirect('languages')->with(['type' => 'danger', 'alert_messenge' => $this->alert['error'] . ' ' . $this->alert['data_error']]);
        }
        $check_exist = $this->checkDublicateOtherId($request->item_id,$item->name);
        if ($check_exist) {
            return redirect()->back()->with(['type' => 'danger', 'alert_messenge' => $this->alert['error'] . ' "' . $request->code . '" ' . $this->alert['is_exist']]);
        }
        $data = $request->all();
        //echo"<pre>";print_r($data);echo"</pre>";exit;
        $this->updateItem($data);

        foreach ($this->langs as $lang) {
            $tData = ['item_id' => $data['item_id'], 'name' => $data['name_' . $lang->name], 'lang' => $lang->name];
            $this->updateItemTraslate($tData);
        }
        return redirect($this->main_page.'/languages')->with(['type' => 'success', 'alert_messenge' => $this->alert['success'] . ' "' . $request->code . '" ' . $this->alert['update_success']]);
    }

    public function changeStatus($id)
    {
        $lang = $this->getInfo($id);
        if ($lang) {
            if ($lang->status == 0) $status = 1;
            else $status = 0;
            $lang->status = $status;
            $lang->save();
            return redirect()->back()->with(['type' => 'success', 'alert_messenge' => $this->alert['success'] . ' "' . $lang->languages_name . '" ' . $this->alert['status_changed']]);
        } else {
            return redirect()->back()->with(['type' => 'danger', 'alert_messenge' => $this->alert['error'] . ' "' . $lang->languages_name . '" ' . $this->alert['data_error']]);
        }
    }

    public function destroy(Request $request,$id){
        $lang = $this->getInfo($id);
        $name = $lang->languages_name;
        if (!$lang) {
            return redirect()->back()->with(['type' => 'danger', 'alert_messenge' => $this->alert['error'] . ' "' . $name . '" ' . $this->alert['data_error']]);
        } else {
            $trans = $this->getTranslateByLang($lang->id);
            foreach ($trans as $item) {
                $item->delete();
            }
            $lang->delete();
            return redirect()->back()->with(['type' => 'success', 'alert_messenge' => $this->alert['success'] . ' "' . $name . '" ' . $this->alert['delete_success']]);
        }
    }

    //End languages

    //Translations
    public function translateList(){
        $list = StructuresController::getMenuList(0,1);
        return view('Translations.list', ['list' => $list,'search' => true]);
    }

    public function translateEdit($id){
        $menu = StructuresController::getStrucById($id);
        include_once \app_path('Includes/lang/' . $menu->structure_url . '.php');
        $datas = StructuresController::getTranslateByStructId($id);
        $lang_data = [];
        foreach ($this->langs as $lang) {
            foreach ($datas as $data) {
                if ($data->locale == $lang->name) {
                    $lang_data[$lang->name] = unserialize($data->trans_page);
                }
            }
        }

        $data = [
            'item_id' => $id,
            'key_lang' => $key_lang,
            'langs' => $this->langs,
            'data' => $lang_data,
            'menu' => $menu
        ];
        return view('Translations.edit', $data);
    }

    public function translateUpdate(Request $request){
        $menu = StructuresController::getStrucById($request->item_id);
        include_once \app_path('Includes/lang/' . $menu->structure_url . '.php');
        $data = $request->all();
        $arr = [];
        foreach ($this->langs as $lang){
            $trans = StructuresController::getTranslateByStructIdLocale($data['item_id'],$lang->name);
            foreach($key_lang as $key => $value){
                if (is_array($data[$value]))
                    $arr[$lang->id][$value] = $data[$value][$lang->id];
            $trans->trans_page = serialize($arr[$lang->id]);
            $trans->save();
            }
        }
        return redirect()->back()->with(['type' => 'success', 'alert_messenge' => $this->alert['success'] . ' "' . $this->alert['translate_page'] . '" ' . $this->alert['update_success']]);
    }
    //End Translations

    //private function
    private function getLangList()
    {
        return Languages::get();
    }

    private function getInfo($id)
    {
        return Languages::find($id);
    }

    private function getTranslateInfo($lang_id,$lang){
        return LanguagesTranslation::where('languages_id',$lang_id)->where('locale',$lang)->first();
    }

    private function getTranslateByLang($lang_id){
        return LanguagesTranslation::where('languages_id',$lang_id)->get();
    }

    private function addNewItem($data)
    {
        $newItem = new Languages();
        $newItem->name = $data['code'];
        $newItem->status = $data['status'];
        $newItem->sort = (int)$data['sort'];
        $newItem->save();
        return $newItem;
    }

    private function addNewItemTranslate($data)
    {
        $newTranslate = new LanguagesTranslation();
        $newTranslate->languages_id = $data['item_id'];
        $newTranslate->languages_name = $data['name'];
        $newTranslate->locale = $data['lang'];
        $newTranslate->save();
    }

    private function updateItem($data)
    {
        $item = $this->getInfo($data['item_id']);
        $item->name = $data['code'];
        $item->status = $data['status'];
        $item->sort = $data['sort'];
        $item->save();
    }

    private function updateItemTraslate($data)
    {
        $translateItem = $this->getTranslateInfo($data['item_id'], $data['lang']);
        if ($translateItem) {
            $translateItem->languages_name = $data['name'];
            $translateItem->save();
        } else {
            $tData = ['item_id' => $data['item_id'], 'name' => $data['name'], 'lang' => $data['lang']];
            $this->addNewItemTranslate($tData);
        }
    }

    private function checkDublicateOtherId($id,$name){
        return Languages::where('id','!=',$id)->where('name',$name)->first();
    }
    //end private

    public static function getActiveLangs()
    {
        $langs = Languages::where('status', 1)->get();
        return $langs;
    }
}
