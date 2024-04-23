<?php

namespace App\Http\Middleware;

use App\Models\Languages;
use Illuminate\Support\Facades\Session;
use App\Models\Structure;
use App\Models\Users_Group;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Auth;
use Closure;

class CheckLogin
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
            
            return $next($request);
        }else{
            return \redirect('login');
        }
    }
}
