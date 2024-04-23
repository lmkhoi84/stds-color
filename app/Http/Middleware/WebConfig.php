<?php

namespace App\Http\Middleware;

use App\Http\Controllers\LanguagesController;
use App\Http\Controllers\StructuresController;
use Closure;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\View;
use App\Models\Structure;
use App\Models\Users_Group;
use Illuminate\Support\Facades\Session;

class WebConfig
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if (Auth::check()){
            $menu_temp = StructuresController::getStrucUrl('structure');
            $menu_temp->trans_page = $alert = unserialize($menu_temp->trans_page);
            $menus_permission = [];
            $products_permission = [];
            if (Auth::user()->id != 1){
                $group = Users_Group::find(Auth::user()->group);
                $menus_permission = array_merge(explode(',',Auth::user()->menus_permission),explode(',',$group->menus_permission));
                $menus_permission = array_unique($menus_permission);
                sort($menus_permission);
                Auth::user()->menus_permission = $menus_permission;
    
                $products_permission = array_merge(explode(',',Auth::user()->products_permission),explode(',',$group->products_permission));
                $products_permission = array_unique($products_permission);
                sort($products_permission);
                Auth::user()->products_permission = $products_permission;
            }
            $siteName = "Scolor";
            $SiteURL = env('APP_URL');
            $CurrentURL = url()->current();
            $uri = substr($CurrentURL, strlen($SiteURL . '/'));
            $uri = explode('/', $uri);
            $num = count($uri);
            if (is_numeric($uri[$num - 1])) $num--;
            $active_page = [];
            $parent = 0;
            $active_url = [];
            for ($i = 0; $i < $num; $i++) {
                $menu = Structure::where('structure_url', $uri[$i])->where('parent_id', $parent)->first();
                if (in_array($menu->id, $menus_permission) || Auth::user()->id == 1) {
                    if ($menu->page_type == 1) $active_url[$i] = $menu->structure_url;
                    $active_page[$menu->level] = [
                        'url' => $menu->structure_url,
                        'name' => $menu->structure_name,
                        'full_url' => implode('/', $active_url),
                        'page_type' => $menu->page_type
                    ];
                    $parent = $menu->id;
                }else{
                    return redirect('home')->with(['type' => 'danger', 'alert_messenge' => $alert['error'] .' '. $alert['error_permission']]);
                }
            }
    
            $active_url = implode('/', $active_url);

            $page_bread = breadCrum($active_page);

            if ($i == 1) $title_page = $active_page[$i]['name'];
            else $title_page = $active_page[$i - 1]['name'];
            
            $structure = StructuresController::getActiveStrucList();
            $lang = Session::get('locale');
            if ($lang == 'en') $change_lang = 'vi';
            else $change_lang = 'en';
            Session::put('active_menu', $active_page[1]['url']);
            Session::put('activeSubMenu',$active_page[count($active_page)]['url']);
            Session::put('active_url', $active_url);
            $translate = StructuresController::getStrucUrl(Session::get('active_menu'));
            $translate->trans_page = unserialize($translate->trans_page);
            $colors = array("", "#24b70b", "#2e0cbf", "#c00822", "#b80498", "#b8bf04", "#0d8f78", "#5a3e27", "#f07c1e", "#938f8c", "#00000");
            $addNew = '<div class="buy-now"><a href="'.$CurrentURL.'/add" class="btn btn-danger btn-buy-now">'.$translate->trans_page['create_new'].'</a></div>';
            $add = false;
            $search = false;
            $langs = LanguagesController::getActiveLangs();
            // echo"<pre>";
            // print_r($langs);
            // echo"</pre>";exit;
            $avatar = Auth::user()->profile_picture;
            if ($avatar == "") $avatar = 'no-image.jpg';
            View::share([
                'web_title' => 'Scolor',
                'active_page' => $active_page,
                'active_url' => $active_url,
                'current_url' => $CurrentURL,
                'title_page' => $title_page . " | ".$siteName,
                'colors' => $colors,
                'structure' => $structure,
                'change_lang' => $change_lang,
                'translate' => $translate,
                'menu_temp' => $menu_temp,
                'menus_permissions' => $menus_permission,
                'products_permission' => $products_permission,
                'page_bread' => $page_bread,
                'addNew' => $addNew,
                'add' => $add,
                'search' => $search,
                'langs' => $langs,
                'avatar' => $avatar
                ]);
            return $next($request);
        }else{
            return redirect('login');
        }
    }
}
