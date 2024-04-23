<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use App\Http\Controllers\StructuresController;

function make_menu_url($str)
{
    $str = trim($str);
    $str = preg_replace("/(  )/", ' ', $str);
    $str = preg_replace("/(  )/", ' ', $str);
    $str = preg_replace("/(  )/", ' ', $str);
    $str = preg_replace("/(à|á|ạ|ả|ã|â|ầ|ấ|ậ|ẩ|ẫ|ă|ằ|ắ|ặ|ẳ|ẵ)/", 'a', $str);
    $str = preg_replace("/(è|é|ẹ|ẻ|ẽ|ê|ề|ế|ệ|ể|ễ)/", 'e', $str);
    $str = preg_replace("/(ì|í|ị|ỉ|ĩ)/", 'i', $str);
    $str = preg_replace("/(ò|ó|ọ|ỏ|õ|ô|ồ|ố|ộ|ổ|ỗ|ơ|ờ|ớ|ợ|ở|ỡ)/", 'o', $str);
    $str = preg_replace("/(ù|ú|ụ|ủ|ũ|ư|ừ|ứ|ự|ử|ữ)/", 'u', $str);
    $str = preg_replace("/(ỳ|ý|ỵ|ỷ|ỹ)/", 'y', $str);
    $str = preg_replace("/(đ)/", 'd', $str);
    $str = preg_replace("/(À|Á|Ạ|Ả|Ã|Â|Ầ|Ấ|Ậ|Ẩ|Ẫ|Ă|Ằ|Ắ|Ặ|Ẳ|Ẵ)/", 'a', $str);
    $str = preg_replace("/(È|É|Ẹ|Ẻ|Ẽ|Ê|Ề|Ế|Ệ|Ể|Ễ)/", 'e', $str);
    $str = preg_replace("/(Ì|Í|Ị|Ỉ|Ĩ)/", 'i', $str);
    $str = preg_replace("/(Ò|Ó|Ọ|Ỏ|Õ|Ô|Ồ|Ố|Ộ|Ổ|Ỗ|Ơ|Ờ|Ớ|Ợ|Ở|Ỡ)/", 'o', $str);
    $str = preg_replace("/(Ù|Ú|Ụ|Ủ|Ũ|Ư|Ừ|Ứ|Ự|Ử|Ữ)/", 'u', $str);
    $str = preg_replace("/(Ỳ|Ý|Ỵ|Ỷ|Ỹ)/", 'y', $str);
    $str = preg_replace("/(Đ)/", 'd', $str);
    $str = preg_replace("/(\&)/", 'and', $str);
    $str = preg_replace("/(\&|\"|\')/", '', $str);
    $str = preg_replace("/( - | -|- | \/|\/ |\/|, | ,|,)/", '-', $str);
    $str = preg_replace("/( )/", '-', $str);
    $str = preg_replace("/(\?|\"|\')/", '', $str);
    $str = strtolower($str);
    return $str;
}

function randString($num = 8){
    $randomString = '';
    $generalString = '!@#$%^&*+-0123456789qwertyuiopasdfghjklzxcvbnmQWERTYUIOPASDFGHJKLZXCVBNM';
    $length = strlen($generalString)-1;
    for($i=0;$i<=$num;$i++){
        $randomString .= $generalString[rand(0,$length)];
    }
    return $randomString;
}

function format_name($str)
{
    $str = trim($str);
    $str = preg_replace("/(  )/", ' ', $str);
    $str = preg_replace("/(  )/", ' ', $str);
    $str = preg_replace("/( )/", ' ', $str);
    //$str = ucwords($str);
    $str = mb_convert_case($str, MB_CASE_TITLE, "UTF-8");
    return $str;
}

function format_real_number($num)
{
    $num = trim($num);
    $num = str_replace(',', '', $num) * 1;
    return (float)$num;
}

function format_number($num)
{
    $str = ($num * 10000 / 10000) . '';
    if (strpos($str, '.') == true) {
        $i = strlen($str) - strpos($str, '.') - 1;
        return number_format($num, $i, '.', ',');
    } else {
        return number_format($num, 0, '.', ',');
    }
}

function format_date($date)
{
    $date = preg_replace("/(\/)/", '-', $date);
    $date = explode("-", $date);
    return $date[2] . "-" . $date[1] . "-" . $date[0];
}

function format_show_date($date)
{
    $date = preg_replace("/(\/)/", '-', $date);
    $date = explode("-", $date);
    return $date[2] . "/" . $date[1] . "/" . $date[0];
}

function check_date_dmy($date){
    $d = explode("/",$date);
    return checkdate($d[1],$d[0],$d[2]);
}

function read_file($path)
{
    // Kiểm tra tập tin tồn tại
    if (file_exists($path)) {
        $file = fopen($path, "r") or die('Can not open this file : ' . $path);
        // Đọc nội dung trong tập tin
        $content = '';
        while (!feof($file)) {
            $content .= fgets($file);
        }
        // Đóng tập tin
        fclose($file);
        return $content;
    } else {
        return false;
    }
}

function write_file($path, $content)
{
    $file = fopen($path, 'w') or die('Can not open this file : ' . $path);
    fwrite($file, $content);
    fclose($file);
}

function get_menu_tree($menus, $parent_id, $hidden_id = 0, $permissions) //hidden_id= 0 bỏ thư mục gốc, chỉ lấy từ danh sách sản phẩm
{
    $data = [];
    foreach ($menus as $menu) {
        if (Auth::user()->id != 1) {
            if (!in_array($menu->id, $permissions) || $hidden_id == $menu->id) {
                continue;
            }
        }
        if (Auth::user()->id == 1 && $hidden_id == $menu->id) {
            continue;
        }
        if ($menu->parent_id == $parent_id) {
            if (check_have_child($menu->id)) {
                $data[] = [
                    'id' => $menu->id,
                    'text' => $menu->structure_name,
                    'children' => get_menu_tree($menus, $menu->id, $hidden_id, $permissions)
                ];
            } else {
                $data[] = [
                    'id' => $menu->id,
                    'text' => $menu->structure_name
                ];
            }
        }
    }
    return $data;
}

function get_permissions_tree($menus, $parent_id, $checked)
{
    $data = [];
    foreach ($menus as $menu) {
        $arr = [];
        if ($menu->parent_id == $parent_id) {
            if (check_have_child($menu->id)) {
                $arr['id'] = $menu->id;
                $arr['text'] = $menu->structure_name;
                $arr['children'] = get_permissions_tree($menus, $menu->id, $checked);
                if (in_array($menu->id, $checked)) {
                    $arr['state'] = ['selected' => true];
                }
                $data[] = $arr;
            } else {
                $arr['id'] = $menu->id;
                $arr['text'] = $menu->structure_name;
                if (in_array($menu->id, $checked)) {
                    $arr['state'] = ['selected' => true];
                }
                $data[] = $arr;
            }
        }
    }
    return $data;
}

function makeMenu($menus, $menus_permissions)
{
    $list = '<ul class="menu-inner py-1">';
    foreach ($menus as $menu) {
        if (in_array($menu->id, $menus_permissions) || Auth::user()->id == 1) {
            if ($menu->parent_id == 0) {
                $active = "";
                if (Session::get('active_menu') == $menu->structure_url) $active = " active open";
                if (StructuresController::checkChildMenu($menu->id, 1)) {
                    $sub_menus = StructuresController::getSubStruc($menu->id,1);
                        $list .= '
                            <li class="menu-item'.$active.'">
                                <a href="javascript:void(0);" class="menu-link menu-toggle amenu">
                                    <i class="bx '.$menu->icon.'"></i>
                                    <div class="dmenu" data-i18n="'.$menu->structure_name.'">'.$menu->structure_name.'</div>
                                </a>
                        ';
                    $list .= '<ul class="menu-sub">';
                    foreach ($sub_menus as $sub_menu) {
                        if (Session::get('activeSubMenu') == $sub_menu->structure_url) $active = " active";
                        else $active = "";
                        if (in_array($sub_menu->id, $menus_permissions) || Auth::user()->id == 1) {
                            $list .= '
                                    <li class="menu-item'.$active.'">
                                        <a href="'.url($menu->structure_url . '/' . $sub_menu->structure_url).'" class="menu-link">
                                            <div data-i18n="'.$sub_menu->structure_name.'">'.$sub_menu->structure_name.'</div>
                                        </a>
                                    </li>
                                ';
                        }
                    }
                    $list .= '</ul></li>';
                } else {
                    $list .= '
                        <li class="menu-item'.$active.'">
                            <a href="'.url($menu->structure_url).'" class="menu-link amenu">
                                <i class="bx '.$menu->icon.'"></i>
                                <div class="dmenu" data-i18n="'.$menu->structure_name.'">'.$menu->structure_name.'</div>
                            </a>
                        </li>
                    ';
                }
            }
        }
    }
    $list .= '</ul>';
    return $list;
}

function get_tree_view($menus, $parent_id, $colors, $main_page, $permissions)
{
    $list = '';
    $menu_lang = StructuresController::getStrucUrl('structure');
    $trans = unserialize($menu_lang->trans_page);
    foreach ($menus as $menu) {
        if (Auth::user()->id != 1 && !in_array($menu->id, $permissions)) {
            continue;
        }
        if ($menu->parent_id == $parent_id) {
            if (StructuresController::checkChildMenu($menu->id)) {
                $list .= '<li>';
                $list .= '<div><b class="' . $colors[$menu->level] . '">' . $menu->structure_name . ' </b>[ <a href="' . url($main_page . '/add/' . $menu->id) . '">' . $trans['add'] . '</a> | <a href="' . url($main_page . '/edit/' . $menu->id) . '">' . $trans['edit'] . '</a> | <a onclick="post_delete(' . $menu->id . ')" href="#' . $menu->id . '">' . $trans['delete'] . '</a> ]</div>';
                $list .= '<ul>';
                $list .= get_tree_view($menus, $menu->id, $colors, $main_page, $permissions);
                $list .= '</ul>';
                $list .= '</li>';
            } else {
                $list .=
                    '<li>
                        <div><b class="' . $colors[$menu->level] . '">' . $menu->structure_name . ' </b>[ <a href="' . url($main_page . '/add/' . $menu->id) . '">' . $trans['add'] . '</a> | <a href="' . url($main_page . '/edit/' . $menu->id) . '">' . $trans['edit'] . '</a> | <a onclick="post_delete(' . $menu->id . ')" href="#' . $menu->id . '">' . $trans['delete'] . '</a> ]</div>
                    ' . ($parent_id == 0 ? '<ul></ul>' : '') . '
                    </li>';
            }
        }
    }
    return $list;
}

function check_have_child($id, $get_menu = 0)
{
    if ($get_menu == 1) $check = StructuresController::getSubStruc($id,1);
    else $check = StructuresController::checkHaveChild($id);
    return $check;
}

function check_have_action($url)
{
    $menu = StructuresController::getStrucUrl($url);
    $check = StructuresController::checkActionPage($menu->id,3);
    if ($check) {
        return true;
    } else {
        return false;
    }
}

function write_file_tree_json($path, $parent_id, $hidden_id = 0, $permissions)
{
    if ($parent_id != 0) $structure = StructuresController::getCategoriesList();
    else $structure = StructuresController::getSortList();
    if (count($structure) > 0) {
        if ($parent_id == 0) {
            $list = [
                'id' => 0,
                'text' => 'Root',
                'children' => get_menu_tree($structure, $parent_id, $hidden_id, $permissions)
            ];
        } else {
            $parent = StructuresController::getStrucById($parent_id);
            $list = [
                'id' => $parent->id,
                'text' => $parent->structure_name,
                'children' => get_menu_tree($structure, $parent->id, $hidden_id, $permissions)
            ];
        }
    } else {
        $list = [
            'id' => 0,
            'text' => 'Root'
        ];
    }

    $content = '[' . json_encode($list) . ']';

    write_file($path, $content);
}

function write_file_categories_json($path, $parent_id, $hidden_id = 0, $permissions)
{
    $structure = StructuresController::getCategoriesList($parent_id);
    if (count($structure) > 0) {
        $parent = StructuresController::getStrucById($parent_id);
        $list = [
            'id' => $parent->id,
            'text' => $parent->structure_name,
            'children' => get_menu_tree($structure, $parent->id, $hidden_id, $permissions)
        ];
    } else {
        $list = [
            'id' => 0,
            'text' => 'Root'
        ];
    }

    $content = '[' . json_encode($list) . ']';

    write_file($path, $content);
}

function write_file_permissions_tree_json($path, $list, $parent_id, $checked = [])
{
    $content = get_permissions_tree($list, $parent_id, $checked);

    $content = json_encode($content);

    write_file($path, $content);
}

function breadCrum($active_page){
    $n = count($active_page);
    $str = "";
    for($j = 1; $j <= $n;$j++){
        if ($j==1 && $n == 1) $str .= $active_page[$j]['name'];
        elseif ($j == 1 && $j < $n) $str .= '<span class="text-muted fw-light">'.$active_page[$j]['name'].'</span>';
        elseif ($j > 1 && $j < $n) $str .= '<span class="text-muted fw-light"> \\ '.$active_page[$j]['name'].'</span>';
        else $str .= " \\ ".$active_page[$j]['name'];
    }
    return $str;
}

function getPermissions(){
    if (Auth::user()->id != 1) $permissions = array_filter(Auth::user()->menus_permission, function ($value) {
        return !is_null($value) && $value != '';
    });
    else $permissions = [];
    return $permissions;
}
