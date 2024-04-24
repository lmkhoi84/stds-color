<?php

namespace App\Http\Controllers\Auth;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Controllers\UsersController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

class LoginController extends Controller
{
    public function getLogin()
    {
        if (Auth::check()) {
            return redirect('home');
        } else {
            return view('Login.login');
        }
    }

    public function postLogin(Request $request)
    {
        $this->validate($request, [
            'email' => 'required|email',
            'password' => 'required|min:8'
        ], [
            'email.required' => 'Vui lòng nhập email !',
            'email.email' => 'Email không đúng định dạng !',
            'password.required' => 'Vui lòng nhập mật khẩu !',
            'password.min' => 'Mật khẩu có ít nhất 8 ký tự !'
        ]);

        $email = $request->email;
        $password = $request->password;
        if (Auth::attempt(['email' => $email, 'password' => $password])) {
            $user = UsersController::getUserByEmail($request->email);
            $user->last_login = date('Y-m-d H:i:s');
            $user->save();
            $request->session()->put('user', $user);
            return redirect()->intended('home');
        } else {
            return redirect()->back()->with(['type' => 'danger', 'alert_messenge' => 'Email hoặc mật khẩu không đúng !']);
        }
    }

    public function getLogout()
    {
        Auth::logout();
        //\Session::forget('user');
        Session::flush();
        return redirect('login');
    }
}
