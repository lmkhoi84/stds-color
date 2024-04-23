<?php

namespace App\Http\Middleware;

use App\Http\Controllers\LanguagesController;
use Closure;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;

class ChangeLanguage
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
        $locale = Session::get('locale');
        $langs = LanguagesController::getActiveLangs();
        //if(empty($locale)) $locale = $langs[Auth::user()->langId - 1]->name;
        if(empty($locale)) $locale = 'vi';
        Session::put('locale',$locale);
        App::setLocale($locale);
        return $next($request);
    }
}
