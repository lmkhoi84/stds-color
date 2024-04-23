<?php

namespace App\Http\Controllers\Auth;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use App\User;

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
            //$user = User::join('users_group AS g','g.id','=','users.group')->select('users.*','g.menus_permission AS m_p','products_permission AS p_p')->where('email', $request->email)->first();
            $user = User::where('email', $request->email)->first();
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
