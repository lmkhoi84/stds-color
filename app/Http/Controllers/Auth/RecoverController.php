<?php

namespace App\Http\Controllers\Auth;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Controllers\Admin\UsersController;
use App\Http\Controllers\SendMailController;

class RecoverController extends Controller
{
    function index(){
        return view('Login.recover');
    }

    function getPassword(Request $request){
        $this->validate($request, [
            'email' => ['required', 'email', 'ends_with:@stdsvn.com,@stds.vn'],
        ], [
            'email.required' => 'Vui lòng nhập email !',
            'email.email' => 'Email không đúng định dạng !',
            'email.regex' => 'Chỉ chấp nhận email @stdsvn.com hoặc @stds.vn !',
        ]);
        $newPassword = UsersController::getNewPassword($request->email);
        if (!empty($newPassword)){
            $user = UsersController::getUser($request->email);
            $data['email'] = $request->email;
            $data['fullname'] = $user->full_name;
            $data['password'] = $newPassword; 
            SendMailController::RecoverMail($data);
            return redirect('login')->with(['type' => 'success', 'alert_messenge' => 'Thành công ! Mật khẩu mới đã được gửi về email của bạn !']);
        }else{
            return redirect()->back()->with(['type' => 'danger', 'alert_messenge' => 'Email đăng ký không tồn tại !']);
        }
    }
}
