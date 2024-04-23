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
        $list = $this->getUserList();
        return view('Users.list',['itemsList' => $list,'add' => true,'search' => true]);
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
        if($request->status) $data['status'] = 1;

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
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show()
    {
        $user = session('user');
        if (empty($user->avatar)) $user->avatar = 'no-image.jpg';
        return view('Users.profile',['item' => $user]);
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
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }

    public function changeStatus($id){
        $user = $this->getUserById($id);
        if ($user && $user->id != 1) {
            if ($user->status == 0) $status = 1;
            else $status = 0;
            $user->status = $status;
            $user->save();
            return redirect()->back()->with(['type' => 'success', 'alert_messenge' => $this->alert['success'] . ' "' . $user->email . '" ' . $this->alert['status_changed']]);
        } else {
            return redirect()->back()->with(['type' => 'danger', 'alert_messenge' => $this->alert['error'] . ' "' . $user->email . '" ' . $this->alert['data_error']]);
        }
    }

    
    //End User List

    /* User Group */




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

    /////////////////////////////////
    private function getUserList(){
        return Users::select('users.*','gr.name AS group_name','gr.menus_permission AS gr_menus_permission')
        ->leftJoin('users_group AS gr','gr.id','=','users.user_group')
        ->orderBy('id')->paginate(20);
    }

    private function getUserById($id){
        return Users::find($id);
    }

    private function getUserByEmail($email){
        return Users::where('email',$email)->first();
    }

    private function getActiveUserGroup(){
        return Users_Group::where('status',1)->orderBy('sort')->get();
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
}
