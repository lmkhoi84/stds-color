<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Users;
use App\Models\Users_Group;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\File;

class UsersController extends Controller
{
    public $main_page = 'users';
    public $path_menus = 'json_data/user_menus.json';
    public $path_classes = 'json_data/user_classes.json';
    public $langs;
    public $alert;

    public function __construct()
    {
        $this->langs = LanguagesController::getActiveLangs();
        $this->alert = StructuresController::getPageTranslate($this->main_page);
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if (!isset($_GET['show'])) {
            $limit = 30;
        } elseif ($_GET['show'] == 0) {
            if (!isset($_GET['search']) || $_GET['search'] == '') {
                $limit = 100;
            } else {
                $limit = 9999999999;
            }
        } else {
            $limit = $_GET['show'];
        }

        if (!isset($_GET['search']) || $_GET['search'] == '') {
            $key = '';
        } else {
            $key = $_GET['search'];
        }

        $list = $this->getUserList($key,$limit);
        $list->appends(['show' => $limit,'search' => $key]);
        return view('Users.list',['itemsList' => $list,'add' => true,'search' => true, 'limit' => $limit, 'key' => $key]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function addNew()
    {
        $groups = $this->getActiveUserGroup();
        $menus = StructuresController::getMenusPermission();
        write_file_permissions_tree_json($this->path_menus, $menus, 0);
        return view('Users.add',['groups' => $groups]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $data = $request->all();
        if($request->status) $data['status'] = 0;

        session(['addUser' => $request->all()]);

        $this->validate($request, [
            'full_name' => 'required',
            'email' => 'required|email',
            'password' => 'required|min:8',
            'user_group' => 'required',
            'default_language' => 'required'
        ], [
            'full_name.required' => $this->alert['full_name_required'],
            'email.required' => $this->alert['email_required'],
            'email.email' => $this->alert['email_email'],
            'password.required' => $this->alert['password_required'],
            'password.min' => $this->alert['password_min'],
        ]);

        $item = $this->getUserByEmail($data['email']);

        if ($item){
            return redirect()->back()->with(['type' => 'danger', 'alert_messenge' => $this->alert['error'] .' '.$data['email'] .' '. $this->alert['has_been_used']]);
        } else {

            $imagePath = 'images/users';
            if (!File::isDirectory($imagePath)) {
                File::makeDirectory($imagePath, 0777, true, true);
            }
            $allowExtension = ['jpeg', 'png', 'jpg', 'gif'];
            $file = $request->file('profile_picture');
            if ($request->hasFile('profile_picture')) {
                if ($file->isValid()) {
                    $extension = $file->getClientOriginalExtension();

                    if (!in_array($extension, $allowExtension)) {
                        return redirect()->back()->with(['type' => 'danger', 'alert_messenge' => $this->alert['error'] . ' ' . $this->alert['error_extension']]);
                    }
                    $file_name = $data['email'].'.'.$extension;
                    $have_file = true;
                } else {
                    return redirect()->back()->with(['type' => 'danger', 'alert_messenge' => $this->alert['error'] . ' ' . $this->alert['error_image']]);
                }
            } else {
                $file_name = '';
                $have_file = false;
            }

            $data['profile_picture'] = $file_name;

            // $extends = array_filter(explode(', ', $request->extends_id));
            // $extends_id = '';
            // for ($i = 0; $i < count($extends); $i++) {
            //     $u = User::where('email', $extends[$i])->first();
            //     if ($u) {
            //         $extends_id .= $u->id . ",";
            //     }
            // }
        }

        $this->addUser($data);

        if ($have_file) $file->move($imagePath, $file_name);
        Session::forget('addUser');
        return redirect($this->main_page.'/users-list')->with(['type' => 'success', 'alert_messenge' => $this->alert['success'] . ' "' . $data["email"] . '" ' . $this->alert['add_success']]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $item = $this->getUserById($id);
        if (!$item){
            return redirect('users/users-list')->with(['type' => 'danger', 'alert_messenge' => $this->alert['error'] .' '. $this->alert['dataNotExist']]);
        }
        if (empty($item->profile_picture)) $item->profile_picture = 'no-image.jpg';
        $groups = $this->getActiveUserGroup();
        $menus = StructuresController::getMenusPermission();
        $menus_permission = explode(',', $item->menus_permission);
        write_file_permissions_tree_json($this->path_menus, $menus, 0, $menus_permission);
        return view('Users.edit',['item' => $item,"groups" => $groups]);
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
        $data = $request->all();
        $file = $request->file('profile_picture');
        if(!$request->status) $data['status'] = 0;
        session(['updateUser' => $request->except('profile_picture')]);

        $this->validate($request, [
            'full_name' => 'required',
            'email' => 'required|email',
            
            'user_group' => 'required',
            'default_language' => 'required'
        ], [
            'full_name.required' => $this->alert['full_name_required'],
            'email.required' => $this->alert['email_required'],
            'email.email' => $this->alert['email_email'],
            
        ]);

        if ($request->password){
            $this->validate($request,[
                'password' => 'required|min:8',
            ],[
                'password.required' => $this->alert['password_required'],
                'password.min' => $this->alert['password_min'],
            ]);
        }

        if ($id != $data['user_id']){
            return redirect($data['previous_page'])->with(['type' => 'danger', 'alert_messenge' => $this->alert['error'] .' '. $this->alert['dataNotExist']]);
        }

        $item = $this->getUserById($data['user_id']);
        if (!$item){
            return redirect($data['previous_page'])->with(['type' => 'danger', 'alert_messenge' => $this->alert['error'] .' '. $this->alert['dataNotExist']]);
        }

        if ($this->checkDublicateEmail($data['user_id'],$data['email'])){
            return redirect()->back()->with(['type' => 'danger', 'alert_messenge' => $this->alert['error'] .' '.$data['email'] .' '. $this->alert['has_been_used']]);
        }

        $imagePath = 'images/users';
        if (!File::isDirectory($imagePath)) {
            File::makeDirectory($imagePath, 0777, true, true);
        }
        $allowExtension = ['jpeg', 'png', 'jpg', 'gif'];
        //$file = $request->file('profile_picture');
        if ($request->hasFile('profile_picture')) {
            if ($file->isValid()) {
                $extension = $file->getClientOriginalExtension();

                if (!in_array($extension, $allowExtension)) {
                    return redirect()->back()->with(['type' => 'danger', 'alert_messenge' => $this->alert['error'] . ' ' . $this->alert['error_extension']]);
                }
                $file_name = $data['email'].'-'.time().'.'.$extension;
                $have_file = true;
                $old_file = $item->profile_picture;
            } else {
                return redirect()->back()->with(['type' => 'danger', 'alert_messenge' => $this->alert['error'] . ' ' . $this->alert['error_image']]);
            }
        } else {
            $file_name = '';
            $have_file = false;
        }

        $data['profile_picture'] = $file_name;

        // $extends = array_filter(explode(', ', $request->extends_id));
        // $extends_id = '';
        // for ($i = 0; $i < count($extends); $i++) {
        //     $u = User::where('email', $extends[$i])->first();
        //     if ($u) {
        //         $extends_id .= $u->id . ",";
        //     }
        // }

        $this->updateUser($data);

        if ($have_file) {
            $file->move($imagePath, $file_name);
            if (!empty($old_file)){
                File::delete(public_path($imagePath.'/' . $old_file));
            }
        }
        
        Session::forget('updateUser');
        return redirect($data['previous_page'])->with(['type' => 'success', 'alert_messenge' => $this->alert['success'] . ' "' . $data["email"] . '" ' . $this->alert['update_success']]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $item = $this->getUserById($id);
        if (!$item){
            return redirect()->back()->with(['type' => 'danger', 'alert_messenge' => $this->alert['error'] .' '. $this->alert['dataNotExist']]);
        }else{
            $profile_picture = public_path('images/users/' .$item->profile_picture);
            $email = $item->email;
            File::delete($profile_picture);
            $item->delete();
            return redirect('users/users-list')->with(['type' => 'success', 'alert_messenge' => $this->alert['success'] . ' "' . $email . '" ' . $this->alert['delete_success']]);
        }
    }

    public function changeStatus($id){
        $item = $this->getUserById($id);
        if ($item && $item->id != 1) {
            if ($item->status == 0) $status = 1;
            else $status = 0;
            $item->status = $status;
            $item->save();
            return redirect()->back()->with(['type' => 'success', 'alert_messenge' => $this->alert['success'] . ' "' . $item->email . '" ' . $this->alert['status_changed']]);
        } else {
            return redirect()->back()->with(['type' => 'danger', 'alert_messenge' => $this->alert['error'] . ' "' . $item->email . '" ' . $this->alert['data_error']]);
        }
    }

    
    //End Users List

    /* Account */

        /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function account()
    {
        $item = Session::get('user');
        if (empty($item->profile_picture)) $item->profile_picture = 'no-image.jpg';
        return view('Users.profile',['item' => $item]);
    }

    public function updateAccount(Request $request,$id)
    {
        $data = $request->all();
        $file = $request->file('profile_picture');
        if(!$request->status) $data['status'] = 0;
        session(['updateAccount' => $request->except('profile_picture')]);

        $this->validate($request, [
            'full_name' => 'required',
            'email' => 'required|email',
            'default_language' => 'required'
        ], [
            'full_name.required' => $this->alert['full_name_required'],
            'email.required' => $this->alert['email_required'],
            'email.email' => $this->alert['email_email'],
            
        ]);

        if ($request->password){
            $this->validate($request,[
                'password' => 'required|min:8',
            ],[
                'password.required' => $this->alert['password_required'],
                'password.min' => $this->alert['password_min'],
            ]);
        }

        if ($id != $data['user_id']){
            return redirect()->back()->with(['type' => 'danger', 'alert_messenge' => $this->alert['error'] .' '. $this->alert['dataNotExist']]);
        }

        $item = $this->getUserById($data['user_id']);
        if (!$item){
            return redirect()->back()->with(['type' => 'danger', 'alert_messenge' => $this->alert['error'] .' '. $this->alert['dataNotExist']]);
        }

        if ($this->checkDublicateEmail($data['user_id'],$data['email'])){
            return redirect()->back()->with(['type' => 'danger', 'alert_messenge' => $this->alert['error'] .' '.$data['email'] .' '. $this->alert['has_been_used']]);
        }

        $imagePath = 'images/users';
        if (!File::isDirectory($imagePath)) {
            File::makeDirectory($imagePath, 0777, true, true);
        }
        $allowExtension = ['jpeg', 'png', 'jpg', 'gif'];
        //$file = $request->file('profile_picture');
        if ($request->hasFile('profile_picture')) {
            if ($file->isValid()) {
                $extension = $file->getClientOriginalExtension();

                if (!in_array($extension, $allowExtension)) {
                    return redirect()->back()->with(['type' => 'danger', 'alert_messenge' => $this->alert['error'] . ' ' . $this->alert['error_extension']]);
                }
                $file_name = $data['email'].'-'.time().'.'.$extension;
                $have_file = true;
                $old_file = $item->profile_picture;
            } else {
                return redirect()->back()->with(['type' => 'danger', 'alert_messenge' => $this->alert['error'] . ' ' . $this->alert['error_image']]);
            }
        } else {
            $file_name = '';
            $have_file = false;
        }

        $data['profile_picture'] = $file_name;

        $this->updateProfile($data);

        if ($have_file) {
            $file->move($imagePath, $file_name);
            if (!empty($old_file)){
                File::delete(public_path($imagePath.'/'.$old_file));
            }
        }

        $item = $this->getUserById($id);
        Session::put('user',$item);
        Session::forget('updateAccount');
        return redirect()->back()->with(['type' => 'success', 'alert_messenge' => $this->alert['success'] . ' "' . $data["email"] . '" ' . $this->alert['update_success']]);
    }

    /* End Account */

    /* User Group */

    public function listGroup(){
        if (!isset($_GET['show'])) {
            $limit = 30;
        } elseif ($_GET['show'] == 0) {
            if (!isset($_GET['search']) || $_GET['search'] == '') {
                $limit = 100;
            } else {
                $limit = 9999999999;
            }
        } else {
            $limit = $_GET['show'];
        }

        if (!isset($_GET['search']) || $_GET['search'] == '') {
            $key = '';
        } else {
            $key = $_GET['search'];
        }

        $list = $this->getGroupList($key,$limit);
        $list->appends(['show' => $limit,'search' => $key]);
        return view('UsersGroup.list',['itemsList' => $list,'add' => true,'search' => true, 'limit' => $limit, 'key' => $key]);
    }

    public function addNewGroup(){
        $menus = StructuresController::getMenusPermission();
        write_file_permissions_tree_json($this->path_menus, $menus, 0);
        return view('UsersGroup.add',['add' => false,'search' => false]);
    }

    public function storeUsersGroup(Request $request){
        $data = $request->all();
        session(['updateAccount' => $request->all()]);
        if (!$request->status) $data['status'] = 0;

        $this->validate($request, [
            'name' => 'required',
        ], [
            'name.required' => $this->alert['name_required'],
        ]);

        $item = $this->getUsersGroupByName($data['name']);
        if ($item){
            return redirect()->back()->with(['type' => 'danger', 'alert_messenge' => $this->alert['error'] .' '.$data['name'] .' '. $this->alert['has_been_used']]);
        }
        
        $this->addUsersGroup($data);

        Session::forget('addUsersGroup');
        return redirect($this->main_page.'/users-group')->with(['type' => 'success', 'alert_messenge' => $this->alert['success'] . ' "' . $data["name"] . '" ' . $this->alert['add_success']]);
    }

    public function editUsersGroup($id){
        $item = $this->getUsersGroupById($id);
        if (!$item){
            return redirect('users/users-group')->with(['type' => 'danger', 'alert_messenge' => $this->alert['error'] .' '. $this->alert['dataNotExist']]);
        }
        $menus = StructuresController::getMenusPermission();
        $menus_permission = explode(',', $item->menus_permission);
        write_file_permissions_tree_json($this->path_menus, $menus, 0, $menus_permission);
        return view('UsersGroup.edit',['item' => $item]);
    }

    public function updateUsersGroup(Request $request,$id){
        $data = $request->all();
        session(['updateAccount' => $request->all()]);
        if (!$request->status) $data['status'] = 0;

        $this->validate($request, [
            'name' => 'required',
        ], [
            'name.required' => $this->alert['name_required'],
        ]);

        if ($id != $data['group_id']){
            return redirect()->back()->with(['type' => 'danger', 'alert_messenge' => $this->alert['error'] .' '. $this->alert['data_error']]);
        }

        $item = $this->checkDublicateUsersGroupByName($id,$data['name']);
        if ($item){
            return redirect()->back()->with(['type' => 'danger', 'alert_messenge' => $this->alert['error'] .' '.$data['name'] .' '. $this->alert['has_been_used']]);
        }
        
        $this->updateGroup($data);

        Session::forget('addUsersGroup');
        return redirect($this->main_page.'/users-group')->with(['type' => 'success', 'alert_messenge' => $this->alert['success'] . ' "' . $data["name"] . '" ' . $this->alert['add_success']]);
    }

    public function changeGroupStatus($id){
        $item = $this->getUsersGroupById($id);
        if ($item) {
            if ($item->status == 0) $status = 1;
            else $status = 0;
            $item->status = $status;
            $item->save();
            return redirect()->back()->with(['type' => 'success', 'alert_messenge' => $this->alert['success'] . ' "' . $item->name . '" ' . $this->alert['status_changed']]);
        } else {
            return redirect()->back()->with(['type' => 'danger', 'alert_messenge' => $this->alert['error'] . ' ' . $this->alert['data_error']]);
        }
    }

    public function destroyGroup($id){
        $item = $this->getUsersGroupById($id);
        if (!$item){
            return redirect()->back()->with(['type' => 'danger', 'alert_messenge' => $this->alert['error'] . ' ' . $this->alert['data_error']]);
        }
        $name = $item->name;
        $item->delete($id);
        return redirect('users/users-group')->with(['type' => 'success', 'alert_messenge' => $this->alert['success'] . ' "' . $name . '" ' . $this->alert['delete_success']]);
    }
    /* End User Group */

    ///////////////////////////////
    public function getLogin(){
        if (Auth::check()){
            return redirect('home');
        }else{
            return view('Auth.login',['web_title'=>'Scolor']);
        }
    }

    public function postLogin(Request $request){
        //Kiểm tra ngoại lệ
        $this->validate($request, [
            'email' => 'required|email',
            'password' => 'required|min:8'
        ], [
            'email.required' => 'Vui lòng nhập email !',
            'email.email' => 'Email không đúng định dạng !',
            'password.required' => 'Vui lòng nhập mật khẩu !',
            'password.min' => 'Mật khẩu có ít nhất 8 ký tự !'
        ]);
        //Kiểm tra đăng nhập
        $email = $request->email;
        $password = $request->password;
        //return $password;
        if(Auth::attempt(['email' => $email,'password' => $password])){
            $user = Users::where('email',$email)->first();
            Session::put('user',$user);
            return redirect()->intended('home');
        }else{
            return redirect()->back()->with(['type'=> 'danger', 'msg' => 'Kiểm tra email hoặc mật khẩu !']);
        }
    }

    public function getLogout(){
        Auth::logout();
        Session::flush();
        return redirect('login');
    }

    /* User List*/
    private function getUserList($key,$limit){
        $list =  Users::select('users.*','gr.name AS group_name','gr.menus_permission AS gr_menus_permission')
        ->leftJoin('users_group AS gr','gr.id','=','users.user_group')
        ->where(function ($query) use ($key) {
            $query->orWhere('users.full_name', 'LIKE', '%' . $key . '%');
            $query->orWhere('users.email', 'LIKE', '%' . $key . '%');
            $query->orWhere('users.address', 'LIKE', '%' . $key . '%');
            $query->orWhere('users.phone_number', 'LIKE', '%' . $key . '%');
            $query->orWhere('users.id_card', 'LIKE', '%' . $key . '%');
            $query->orWhere('gr.name', 'LIKE', '%' . $key . '%');
        })
        ->orderBy('email')
        ->paginate($limit);
        return $list;
    }

    private function getUserById($id){
        return Users::find($id);
    }

    private function checkDublicateEmail($id,$email){
        return Users::where('id','!=',$id)->where('email',$email)->first();
    }

    private function addUser($data){
        $item = new Users();
        $item->full_name = $data['full_name'];
        $item->email = $data['email'];
        $item->password = Hash::make($data['password']);
        $item->address = $data['address'];
        $item->phone_number = $data['phone_number'];
        $item->id_card = $data['id_card'];
        $item->user_group = $data['user_group'];
        $item->status = $data['status'];
        $item->profile_picture = $data['profile_picture'];
        $item->default_language = $data['default_language'];
        $item->menus_permission = $data['menus_permission'];
        $item->save();
    }

    private function updateUser($data){
        $item = Users::find($data['user_id']);
        $item->full_name = $data['full_name'];
        $item->email = $data['email'];
        if (!empty($data['password'])){
            $item->password = Hash::make($data['password']);
        }
        $item->address = $data['address'];
        $item->phone_number = $data['phone_number'];
        $item->id_card = $data['id_card'];
        $item->user_group = $data['user_group'];
        $item->status = $data['status'];
        if (!empty($data['profile_picture'])){
            $item->profile_picture = $data['profile_picture'];
        }
        $item->default_language = $data['default_language'];
        $item->menus_permission = $data['menus_permission'];
        $item->save();
    }

    private function updateProfile($data){
        $item = Users::find($data['user_id']);
        $item->full_name = $data['full_name'];
        $item->email = $data['email'];
        if (!empty($data['password'])){
            $item->password = Hash::make($data['password']);
        }
        $item->address = $data['address'];
        $item->phone_number = $data['phone_number'];
        $item->id_card = $data['id_card'];
        if (!empty($data['profile_picture'])){
            $item->profile_picture = $data['profile_picture'];
        }
        $item->default_language = $data['default_language'];
        $item->save();
    }

    /* Users Group*/
    private function getGroupList($key,$limit){
        $list = Users_Group::where('name', 'LIKE', '%' . $key . '%')
        ->orderBy('sort')
        ->paginate($limit);
        return $list;
    }

    private function getUsersGroupById($id){
        return Users_Group::find($id);
    }

    private function getUsersGroupByName($name){
        return Users_Group::where('name',$name)->first();
    }

    private function checkDublicateUsersGroupByName($id,$name){
        return Users_Group::where('id','!=',$id)->where('name',$name)->first();
    }

    private function getActiveUserGroup(){
        return Users_Group::where('status',1)->orderBy('sort')->get();
    }

    private function addUsersGroup($data){
        $item = new Users_Group();
        $item->name = $data['name'];
        $item->sort = $data['sort'];
        $item->status = $data['status'];
        $item->menus_permission = $data['menus_permission'];
        $item->save();
    }

    private function updateGroup($data){
        $item = $this->getUsersGroupById($data['group_id']);
        $item->name = $data['name'];
        $item->sort = $data['sort'];
        $item->status = $data['status'];
        $item->menus_permission = $data['menus_permission'];
        $item->save();
    }

    /* End Users Group*/

    //Public staic function
    public static function getUserByEmail($email){
        return Users::where('email',$email)->first();
    }
}
