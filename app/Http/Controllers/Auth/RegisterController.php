<?php

namespace App\Http\Controllers\Auth;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Models\Users;
use App\Http\Controllers\Admin\UsersController;
use App\Http\Controllers\SendMailController;

class RegisterController extends Controller
{
    public function index(){
        if (Auth::check()){
            return redirect('home');
        }else{
            return view('Login.register');
        }
    }

    public function create(Request $request){
        $this->validate($request, [
            'fullname' => 'required',
            'email' => ['required', 'email', 'ends_with:@stdsvn.com,@stds.vn'],
            'password' => 'required|min:8',
            'confirm_password' => 'required|min:8'
        ], [
            'fulname.required' => 'Vui lòng nhập đầy đủ họ tên !',
            'email.required' => 'Vui lòng nhập email !',
            'email.email' => 'Email không đúng định dạng !',
            'email.regex' => 'Chỉ chấp nhận email @stdsvn.com hoặc @stds.vn !',
            'password.required' => 'Vui lòng nhập mật khẩu !',
            'password.min' => 'Mật khẩu có ít nhất 8 ký tự !',
            'confirm_password.required' => 'Vui lòng nhập mật khẩu !',
            'confirm_password.min' => 'Mật khẩu có ít nhất 8 ký tự !'
        ]);
        if ($request->password != $request->confirm_password){
            return redirect()->back()->with(['type' => 'danger', 'alert_messenge' => 'Lỗi ! Mật khẩu không trùng khớp !']);
        }
        $user = Users::where('email',$request->email)->first();
        if ($user){
            return redirect()->back()->with(['type' => 'danger', 'alert_messenge' => 'Lỗi ! Email "'.$request->email.'" đã được sử dụng !']);
        }else{
            $data = $request->all();
            UsersController::register($request->fullname,$request->email,$request->password);
            SendMailController::RegisterMail($data);
            return redirect('login')->with(['type' => 'success', 'alert_messenge' => 'Thành công ! Đăng ký tài khoản thành công !']);
        }
    }
}
