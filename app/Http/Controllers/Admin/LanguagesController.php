<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Languages;
use App\Models\LanguagesTranslation;
use App\Models\Structure;
use App\Models\StructureTranslation;
use Illuminate\Support\Facades\Session;

class LanguagesController extends Controller
{
    public function LangIndex()
    {
        $languages = Languages::get();
        return view('Languages.list', ['languages' => $languages]);
    }

    public function LangCreate()
    {
        $langs = Languages::where('status', 1)->get();
        return view('Languages.add', ['langs' => $langs]);
    }

    public function LangStore(Request $request)
    {
        $active_menu = Structure::where('structure_url', Session::get('active_menu'))->first();
        $alert = unserialize($active_menu->trans_page);
        $this->validate($request, [
            'name_en' => 'required',
            'code' => 'required',
        ], [
            'name_en.required' => $alert['name_en_required'],
            'code.required' => $alert['language_code_required'],
        ]);
        $is_exist = Languages::where('name', $request->code)->first();
        if ($is_exist) {
            return redirect()->back()->with(['type' => 'danger', 'alert_messenge' => $alert['error'] . ' "' . $request->name_en . '" ' . $alert['is_exist']]);
        } else {
            $data = $request->all();
            $new_lang = new Languages();
            $new_lang->name = $data['code'];
            $new_lang->status = $data['status'];
            $new_lang->save();

            $langs = Languages::where('status', 1)->get();
            foreach ($langs as $lang) {
                $trans = new LanguagesTranslation();
                $trans->languages_id = $new_lang->id;
                $trans->languages_name = $data['name_' . $lang->name];
                $trans->locale = $lang->name;
                $trans->save();
            }

            return redirect()->back()->with(['type' => 'success', 'alert_messenge' => $alert['success'] . ' "' . $data['name_en'] . '" ' . $alert['add_success']]);
        }
    }

    public function LangShow($id)
    {
        //
    }

    public function LangEdit($id)
    {
        $active_menu = Structure::where('structure_url', Session::get('active_menu'))->first();
        $alert = unserialize($active_menu->trans_page);
        $lang_edit = Languages::find($id);
        if (!$lang_edit) {
            return redirect('languages')->with(['type' => 'danger', 'alert_messenge' => $alert['error'] . ' ' . $alert['data_error']]);
        } else {
            $langs = Languages::where('status', 1)->get();
            $name = [];
            foreach ($langs as $lang) {
                $lang_trans = LanguagesTranslation::where('languages_id', $id)->where('locale', $lang->name)->first();
                if ($lang_trans) {
                    $name[$lang->name] = $lang_trans->languages_name;
                } else {
                    $name[$lang->name] = '';
                }
            }
            return view('Languages.edit', ['lang_edit' => $lang_edit, 'langs' => $langs, 'name' => $name]);
        }
    }

    public function LangUpdate(Request $request, $id)
    {
        $active_menu = Structure::where('structure_url', Session::get('active_menu'))->first();
        $alert = unserialize($active_menu->trans_page);
        $this->validate($request, [
            'name_en' => 'required',
            'code' => 'required',
        ], [
            'name_en.required' => $alert['name_en_required'],
            'code.required' => $alert['language_code_required'],
        ]);

        $lang_edit = Languages::where('id', $id)->first();
        if (!$lang_edit) {
            return redirect('languages')->with(['type' => 'danger', 'alert_messenge' => $alert['error'] . ' ' . $alert['data_error']]);
        }elseif ($request->code == $lang_edit->name){
            $check_exist = Languages::where([['id','!=',$id],['name',$request->code]])->first();
            if ($check_exist) {
                return redirect()->back()->with(['type' => 'danger', 'alert_messenge' => $alert['error'] . ' "' . $request->code . '" ' . $alert['is_exist']]);
            } else {
                $data = $request->all();
                $lang_edit->name = $data['code'];
                $lang_edit->status = $data['status'];
                $lang_edit->save();

                $langs = Languages::where('status', 1)->get();
                foreach ($langs as $lang) {
                    $trans = LanguagesTranslation::where('languages_id', $id)->where('locale', $lang->name)->first();
                    if ($trans) {
                        $trans->languages_name = $data['name_' . $lang->name];
                        $trans->locale = $lang->name;
                        $trans->save();
                    } else {
                        $trans = new LanguagesTranslation();
                        $trans->languages_id = $id;
                        $trans->languages_name = $data['name_' . $lang->name];
                        $trans->locale = $lang->name;
                        $trans->save();
                    }
                }

                return redirect()->back()->with(['type' => 'success', 'alert_messenge' => $alert['success'] . ' "' . $request->code . '" ' . $alert['update_success']]);
            }
        }
    }

    public function LangDestroy($id)
    {
        $active_menu = Structure::where('structure_url', Session::get('active_menu'))->first();
        $alert = unserialize($active_menu->trans_page);
        $lang = Languages::where('id', $id)->first();
        $name = $lang->languages_name;
        if (!$lang) {
            return redirect()->back()->with(['type' => 'danger', 'alert_messenge' => $alert['error'] . ' "' . $name . '" ' . $alert['data_error']]);
        } else {
            $lang->delete($id);
            $trans = LanguagesTranslation::where('languages_id', $id)->get();
            foreach ($trans as $lang) {
                $trans->delete($lang->id);
            }
            return redirect()->back()->with(['type' => 'success', 'alert_messenge' => $alert['success'] . ' "' . $name . '" ' . $alert['delete_success']]);
        }
    }

    public function change_status($id)
    {
        $active_menu = Structure::where('structure_url', Session::get('active_menu'))->first();
        $alert = unserialize($active_menu->trans_page);
        $lang = Languages::find($id);
        if ($lang) {
            if ($lang->status == 0) $status = 1;
            else $status = 0;
            $lang->status = $status;
            $lang->save();
            return redirect()->back()->with(['type' => 'success', 'alert_messenge' => $alert['success'] . ' "' . $lang->languages_name . '" ' . $alert['status_changed']]);
        } else {
            return redirect()->back()->with(['type' => 'danger', 'alert_messenge' => $alert['error'] . ' "' . $lang->languages_name . '" ' . $alert['data_error']]);
        }
    }


    //Translations Controller

    public function TransIndex()
    {
        $list = Structure::where('parent_id', 0)->where('page_type',1)->get();
        return view('Translations.list', ['list' => $list]);
    }

    public function TransEdit($id)
    {
        $menu = Structure::find($id);
        include_once \app_path('Includes/lang/' . $menu->structure_url . '.php');
        $datas = StructureTranslation::where('structure_id', $id)->get();
        $langs = Languages::where('status', 1)->get();
        $lang_data = [];
        foreach ($langs as $lang) {
            foreach ($datas as $data) {
                if ($data->locale == $lang->name) {
                    $lang_data[$lang->name] = unserialize($data->trans_page);
                }
            }
        }

        $data = [
            'keys_lang' => $key_lang,
            'langs' => $langs,
            'data' => $lang_data,
            'menu' => $menu
        ];
        return view('Translations.edit', $data);
    }

    public function TransUpdate(Request $request, $id)
    {
        $active_menu = Structure::where('structure_url', Session::get('active_menu'))->first();
        $alert = unserialize($active_menu->trans_page);
        $data = $request->all();
        $trans = StructureTranslation::where('structure_id', $id)->where('locale', $request->lang)->first();
        if (!$trans) { } else {
            $trans->trans_page = \serialize($data);
            $trans->save();
        }
        //return redirect()->back()->with(['type' => 'success', 'alert_messenge' => $alert['success'] . ' "' . $lang->languages_name . '" ' . $alert['status_changed']]);
        return redirect()->back();
    }
}
