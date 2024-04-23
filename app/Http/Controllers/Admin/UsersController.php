<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Structure;
use App\Models\Users;
use App\Models\Users_Group;
use App\Models\Warehouse;
use App\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Session;

class UsersController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

    public $path_menus = 'json_data/user_menus.json';
    public $path_products = 'json_data/user_products.json';

    public function index()
    {
        $users = Users::orderBy('id')->get();
        return view('Users_List.list', ['users' => $users]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $groups = Users_Group::where('status', 1)->get();
        $menus = Structure::where('page_type', '!=', 2)->where('status', 1)->orderBy('sort')->get();
        $products = Structure::where('page_type', 2)->where('status', 1)->orderBy('sort')->get();
        $warehouse = Warehouse::where('status', 1)->get();
        $users = Users::get();
        $Users = [];
        foreach ($users as $user) {
            $Users[] = "{value: '" . $user["id"] . "',
                            label: '" .  addslashes($user["email"]) . " (" . addslashes($user["full_name"]) . ")',
                            name: '" .  $user["email"] . "'
                        }";
        }
        $Users = "[" . implode(',', $Users) . "]";
        write_file_permissions_tree_json($this->path_menus, $menus, 0);
        write_file_permissions_tree_json($this->path_products, $products, 4);
        return view('Users_List.add', ['groups' => $groups, 'warehouse' => $warehouse, 'users' => $Users]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $active_menu = Structure::where('structure_url', Session::get('active_menu'))->first();
        $alert = unserialize($active_menu->trans_page);
        $this->validate($request, [
            'full_name' => 'required',
            'email' => 'required|email',
            'password' => 'required|min:8',
            'group' => 'required'
        ], [
            'full_name.required' => $alert['full_name_required'],
            'email.required' => $alert['email_required'],
            'email.email' => $alert['email_email'],
            'password.required' => $alert['password_required'],
            'password.min' => $alert['password_min'],
            'group.required' => $alert['group_required']
        ]);

        $user = Users::where('email', $request->email)->first();
        if ($user) {
            return redirect()->back()->with(['type' => 'danger', 'alert_messenge' => $alert['error'] . ' "' . $request->email . '" ' . $alert['is_exist']]);
        } else {
            $file = $request->file('avatar');
            $allowExtension = ['jpeg', 'png', 'jpg', 'gif'];
            if ($file) {
                if ($file->isValid()) {
                    $extension = $file->getClientOriginalExtension();
                    if (!in_array($extension, $allowExtension)) {
                        return redirect()->back()->with(['type' => 'danger', 'alert_messenge' => $alert['error'] . ' ' . $alert['error_extension']]);
                    }
                    $file_name = $request->email.'.'.$extension;
                    $have_file = 1;
                } else {
                    return redirect()->back()->with(['type' => 'danger', 'alert_messenge' => $alert['error'] . ' ' . $alert['error_image']]);
                }
            } else {
                $file_name = '';
                $have_file = 0;
            }
            $extends = array_filter(explode(', ', $request->extends_id));
            $extends_id = '';
            for ($i = 0; $i < count($extends); $i++) {
                $u = User::where('email', $extends[$i])->first();
                if ($u) {
                    $extends_id .= $u->id . ",";
                }
            }
            $create_user = new Users();
            $create_user->full_name = $request->full_name;
            $create_user->email = $request->email;
            $create_user->password = Hash::make($request->password);
            $create_user->avatar = $file_name;
            $create_user->status = $request->status;
            $create_user->group = $request->group;
            $create_user->wh_id = $request->warehouse;
            $create_user->extends_users = $extends_id;
            $create_user->menus_permission = $request->menus_permission;
            $create_user->products_permission = $request->products_permission;
            $create_user->save();
            if ($have_file == 1) $file->move('images/users', $file_name);
            return redirect()->back()->with(['type' => 'success', 'alert_messenge' => $alert['success'] . ' "' . $request->email . '" ' . $alert['add_success']]);
        }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $active_menu = Structure::where('structure_url', Session::get('active_menu'))->first();
        $alert = unserialize($active_menu->trans_page);
        $user = Users::find($id);
        if (empty($user)) {
            return redirect('users/users-list')->with(['type' => 'danger', 'alert_messenge' => $alert['error'] . ' ' . $alert['data_error']]);
        } else {
            $extends_id = array_filter(explode(',', $user->extends_users));
            $extends_name = '';
            $users = Users::where('id', '!=', $id)->get();
            $Users = [];
            foreach ($users as $User) {
                $Users[] = "{value: '" . $User["id"] . "',
                                label: '" .  addslashes($User["email"]) . " (" . addslashes($User["full_name"]) . ")',
                                name: '" .  $User["email"] . "'
                            }";
                for ($i = 0; $i < count($extends_id); $i++) {
                    if ($User->id == $extends_id[$i]) {
                        $extends_name .= $User->email . ", ";
                    }
                }
            }
            $Users = "[" . implode(',', $Users) . "]";

            $groups = Users_Group::where('status', 1)->get();
            $menus_permission = explode(',', $user->menus_permission);
            $products_permission = explode(',', $user->products_permission);
            $menus = Structure::where('page_type', '!=', 2)->where('status', 1)->orderBy('sort')->get();
            $products = Structure::where('page_type', 2)->where('status', 1)->orderBy('sort')->get();
            $warehouse = Warehouse::where('status', 1)->get();
            write_file_permissions_tree_json($this->path_menus, $menus, 0, $menus_permission);
            write_file_permissions_tree_json($this->path_products, $products, 4, $products_permission);
            return view('Users_List.edit', ['user' => $user, 'groups' => $groups, 'warehouse' => $warehouse, 'extends_name' => $extends_name, 'UserList' => $Users]);
        }
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
        $active_menu = Structure::where('structure_url', Session::get('active_menu'))->first();
        $alert = unserialize($active_menu->trans_page);
        $this->validate($request, [
            'full_name' => 'required',
            'email' => 'required|email',
            'password' => 'nullable|min:8',
            'group' => 'required'
        ], [
            'full_name.required' => $alert['full_name_required'],
            'email.required' => $alert['email_required'],
            'email.email' => $alert['email_email'],
            'password.min' => $alert['password_min'],
            'group.required' => $alert['group_required']
        ]);

        $user = Users::find($id);
        if (!$user) {
            return redirect()->back()->with(['type' => 'danger', 'alert_messenge' => $alert['error'] . ' ' . $alert['data_error']]);
        }

        $email_extend = explode("@", $request->email);
        if ($email_extend[1] != "stdsvn.com" && $email_extend[1] != "stds.vn") {
            return redirect()->back()->with(['type' => 'danger', 'alert_messenge' => $alert['error'] . ' Email "' . $request->email . '" ' . $alert['is_denied']]);
        }

        if ($user->email != $request->email) {
            $find_email = Users::where('email', $request->email)->where("id", "!=", $id)->first();
            if ($find_email) {
                return redirect()->back()->with(['type' => 'danger', 'alert_messenge' => $alert['error'] . ' "' . $request->email . '" ' . $alert['is_exist']]);
            }
        }
        $file = $request->file('avatar');
        $allowExtension = ['jpeg', 'png', 'jpg', 'gif'];
        if ($file) {
            if ($file->isValid()) {
                $extension = $file->getClientOriginalExtension();
                if (!in_array($extension, $allowExtension)) {
                    return redirect()->back()->with(['type' => 'danger', 'alert_messenge' => $alert['error'] . ' ' . $alert['error_extension']]);
                }
                $old_file = public_path('images/users/' . $user->avatar);
                File::delete($old_file);
                $file_name = $user->email.'.'.$extension;
                $have_file = 1;
            } else {
                return redirect()->back()->with(['type' => 'danger', 'alert_messenge' => $alert['error'] . ' ' . $alert['error_image']]);
            }
        } else {
            $file_name = $user->avatar;
            $have_file = 0;
        }

        $user->full_name = $request->full_name;
        $user->email = $request->email;
        if (empty($request->password)) {
            $password = $user->password;
        } else {
            $password = Hash::make($request->password);
        }
        $extends = array_filter(explode(',', $request->extends));
        $extends_id = [];
        for ($i = 0; $i < count($extends); $i++) {
            $u = User::where('email', trim($extends[$i]))->first();
            if ($u) {
                $extends_id[] = $u->id;
            }
        }
        $extends_id = implode(',',$extends_id);
        $user->email = $request->email;
        $user->avatar = $file_name;
        $user->password = $password;
        $user->status = $request->status;
        $user->group = $request->group;
        $user->wh_id = $request->warehouse;
        $user->extends_users = $extends_id;
        $user->menus_permission = $request->menus_permission;
        $user->products_permission = $request->products_permission;
        $user->save();
        if ($have_file == 1) $file->move('images/users', $file_name);
        return redirect($request->previous_page)->with(['type' => 'success', 'alert_messenge' => $alert['success'] . ' "' . $request->email . '" ' . $alert['update_success']]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, $id)
    {
        $active_menu = Structure::where('structure_url', Session::get('active_menu'))->first();
        $alert = unserialize($active_menu->trans_page);
        if (url()->previous() != url('users/users-list')) {
            return redirect('home');
        } else {
            $user = Users::find($id);
            if (!$user) {
                return redirect()->back()->with(['type' => 'danger', 'alert_messenge' => $alert['error'] . ' "' . $user->email . '" ' . $alert['data_error']]);
            } else {
                $old_file = public_path('images/users/' . $user->avatar);
                File::delete($old_file);
                $user->delete($id);
                return redirect('users/users-list')->with(['type' => 'success', 'alert_messenge' => $alert['success'] . ' "' . $user->email . '" ' . $alert['delete_success']]);
            }
        }
    }

    public function change_status($id)
    {
        $active_menu = Structure::where('structure_url', Session::get('active_menu'))->first();
        $alert = unserialize($active_menu->trans_page);
        $user = Users::find($id);
        if ($user && $user->id != 1) {
            if ($user->status == 0) $status = 1;
            else $status = 0;
            $user->status = $status;
            $user->save();
            return redirect()->back()->with(['type' => 'success', 'alert_messenge' => $alert['success'] . ' "' . $user->email . '" ' . $alert['status_changed']]);
        } else {
            return redirect()->back()->with(['type' => 'danger', 'alert_messenge' => $alert['error'] . ' "' . $user->email . '" ' . $alert['data_error']]);
        }
    }


    //
    ///////////////////////////////////// Users Group /////////////////////////////////////
    //
    public function GroupList()
    {
        $group = Users_Group::get();
        return view('Users_Group.list', ['users_group' => $group]);
    }

    public function CreateGroup()
    {
        $menus = Structure::where('page_type', '!=', 2)->where('status', 1)->orderBy('sort')->get();
        $products = Structure::where('page_type', 2)->where('status', 1)->orderBy('sort')->get();
        write_file_permissions_tree_json($this->path_menus, $menus, 0);
        write_file_permissions_tree_json($this->path_products, $products, 4);
        return view('Users_Group.add');
    }

    public function StoreGroup(Request $request)
    {
        $active_menu = Structure::where('structure_url', Session::get('active_menu'))->first();
        $alert = unserialize($active_menu->trans_page);
        $this->validate($request, [
            'name' => 'required',
        ], [
            'name.required' => $alert['name_required'],
        ]);
        $group = Users_Group::where('name', $request->name_en)->first();
        if ($group) {
            return redirect()->back()->with(['type' => 'danger', 'alert_messenge' => $alert['error'] . ' "' . $request->name . '" ' . $alert['is_exist']]);
        } else {
            $new_group = new Users_Group();
            $new_group->name = $request->name;
            $new_group->status = $request->status;
            $new_group->menus_permission = $request->menus_permission;
            $new_group->products_permission = $request->products_permission;
            $new_group->save();
            return redirect()->back()->with(['type' => 'success', 'alert_messenge' => $alert['success'] . ' "' . $request->name . '" ' . $alert['add_success']]);
        }
    }

    public function EditGroup($id)
    {
        $active_menu = Structure::where('structure_url', Session::get('active_menu'))->first();
        $alert = unserialize($active_menu->trans_page);
        $group = Users_Group::find($id);
        if (empty($group)) {
            return redirect('users/users-group')->with(['type' => 'danger', 'alert_messenge' => $alert['error'] . ' ' . $alert['data_error']]);
        } else {
            $menus_permission = explode(',', $group->menus_permission);
            $products_permission = explode(',', $group->products_permission);
            $menus = Structure::where('page_type', '!=', 2)->where('status', 1)->orderBy('sort')->get();
            $products = Structure::where('page_type', 2)->where('status', 1)->orderBy('sort')->get();
            write_file_permissions_tree_json($this->path_menus, $menus, 0, $menus_permission);
            write_file_permissions_tree_json($this->path_products, $products, 4, $products_permission);
            return view('Users_Group.edit', ['group' => $group]);
        }
    }

    public function UpdateGroup(Request $request, $id)
    {
        $active_menu = Structure::where('structure_url', Session::get('active_menu'))->first();
        $alert = unserialize($active_menu->trans_page);
        $this->validate($request, [
            'name' => 'required',
        ], [
            'name.required' => $alert['name_required'],
        ]);
        $group = Users_Group::find($id);
        if (!$group) {
            return redirect($request->previous_page)->with(['type' => 'danger', 'alert_messenge' => $alert['error'] . ' "' . $request->name . '" ' . $alert['data_error']]);
        } else {
            if ($group->name == $request->name) {
                $group->status = $request->status;
                $group->menus_permission = $request->menus_permission;
                $group->products_permission = $request->products_permission;
                $group->save();
                return redirect($request->previous_page)->with(['type' => 'success', 'alert_messenge' => $alert['success'] . ' "' . $request->name . '" ' . $alert['update_success']]);
            } else {
                $is_exist = Users_Group::where('name', $request->name)->where('id', '!=', $id)->first();
                if ($is_exist) {
                    return redirect()->back()->with(['type' => 'danger', 'alert_messenge' => $alert['error'] . ' "' . $request->name . '" ' . $alert['is_exist']]);
                } else {
                    $group->name = $request->name;
                    $group->status = $request->status;
                    $group->menus_permission = $request->menus_permission;
                    $group->products_permission = $request->products_permission;
                    $group->save();
                    return redirect($request->previous_page)->with(['type' => 'success', 'alert_messenge' => $alert['success'] . ' "' . $request->name . '" ' . $alert['update_success']]);
                }
            }
        }
    }

    public function DestroyGroup($id)
    {
        $active_menu = Structure::where('structure_url', Session::get('active_menu'))->first();
        $alert = unserialize($active_menu->trans_page);
        if (url()->previous() != url('users/users-group')) {
            return redirect('home');
        } else {
            $group = Users_Group::find($id);
            if (!$group) {
                return redirect()->back()->with(['type' => 'danger', 'alert_messenge' => $alert['error'] . ' "' . $group->name . '" ' . $alert['data_error']]);
            } else {
                $group->delete($id);
                return redirect()->back()->with(['type' => 'success', 'alert_messenge' => $alert['success'] . ' "' . $group->name . '" ' . $alert['delete_success']]);
            }
        }
    }

    public function change_group_status($id)
    {
        $active_menu = Structure::where('structure_url', Session::get('active_menu'))->first();
        $alert = unserialize($active_menu->trans_page);
        $group = Users_Group::find($id);
        if ($group) {
            if ($group->status == 0) $status = 1;
            else $status = 0;
            $group->status = $status;
            $group->save();
            return redirect()->back()->with(['type' => 'success', 'alert_messenge' => $alert['success'] . ' "' . $group->name . '" ' . $alert['status_changed']]);
        } else {
            return redirect()->back()->with(['type' => 'danger', 'alert_messenge' => $alert['error'] . ' "' . $group->name . '" ' . $alert['data_error']]);
        }
    }

    //
    ///////////////////////////////////// Account /////////////////////////////////////
    //
    public function show()
    {
        $user = Users::where('email',Auth::user()->email)->first();
        if (empty($user->avatar)){
            $user->avatar = 'no-image.jpg';
        }
        return view('Account.show',['user'=>$user]);
    }

    public function EditAccount()
    {
        return view('Account.edit');
    }

    public function UpdateAccount(Request $request)
    {
        $active_menu = Structure::where('structure_url', Session::get('active_menu'))->first();
        $alert = unserialize($active_menu->trans_page);
        $this->validate($request, [
            'full_name' => 'required',
            'email' => 'required|email',
            'password' => 'nullable|min:8',
        ], [
            'full_name.required' => $alert['full_name_required'],
            'email.required' => $alert['email_required'],
            'email.email' => $alert['email_email'],
            'password.min' => $alert['password_min'],
        ]);

        $user = Users::find($request->id);
        if (!$user) {
            return redirect()->back()->with(['type' => 'danger', 'alert_messenge' => $alert['error'] . ' "' . $request->email . '" ' . $alert['not_exist']]);
        } else {
            if ($request->email != $user->email) {
                $check_exist = User::where('email', $request->email)->where('id', '!=', $request->id)->first();
                if ($check_exist) {
                    return redirect()->back()->with(['type' => 'danger', 'alert_messenge' => $alert['error'] . ' "' . $request->email . '" ' . $alert['email_used']]);
                }
            }

            $file = $request->file('profile_picture');
            $allowExtension = ['jpeg', 'png', 'jpg', 'gif'];
            if ($file) {
                if ($file->isValid()) {
                    $extension = $file->getClientOriginalExtension();
                    if (!in_array($extension, $allowExtension)) {
                        return redirect()->back()->with(['type' => 'danger', 'alert_messenge' => $alert['error'] . ' ' . $alert['error_extension']]);
                    }
                    $old_file = public_path('images/users/' . $user->avatar);
                    File::delete($old_file);
                    $file_name = $user->email.'.'.$extension;
                    $have_file = 1;
                } else {
                    return redirect()->back()->with(['type' => 'danger', 'alert_messenge' => $alert['error'] . ' ' . $alert['error_image']]);
                }
            } else {
                $file_name = $user->avatar;
                $have_file = 0;
            }

            $user->full_name = $request->full_name;
            $user->email = $request->email;
            if (empty($request->password)) {
                $password = $user->password;
            } else {
                $password = Hash::make($request->password);
            }
            $user->avatar = $file_name;
            $user->password = $password;
            $user->save();
            if ($have_file == 1) $file->move('images/users', $file_name);
            $request->session()->put('user', $user);
            return redirect('account')->with(['type' => 'success', 'alert_messenge' => $alert['success'] . ' "' . $request->email . '" ' . $alert['update_success']]);
        }
    }

    public static function checkUser($email){
        $user = self::getUser($email);
        if (!empty($user)){
            return true;
        }else{
            return false;
        }
    }

    public static function getUser($email){
        $user = Users::where('email',$email)->first();
        return $user;
    }

    public static function register($fullname,$email,$password){
        $user = new Users();
        $user->full_name = $fullname;
        $user->email = $email;
        $user->password = Hash::make($password);
        $user->group = 1;
        $user->status = 0;
        $user->save();
    }

    public static function getNewPassword($email){
        $check = self::checkUser($email);
        if ($check){
            $newPassword = randString(10);
            $user = Users::where('email',$email)->first();
            $user->password = Hash::make($newPassword);
            $user->save();
            return $newPassword;
        }else{
            return '';
        }
        
    }
}
