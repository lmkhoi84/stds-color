<?php
namespace App\Http\Controllers;
use Illuminate\Support\Facades\Mail;
use App\Mail\RegisterMail;
use App\Mail\RecoverMail;
class SendMailController extends Controller
{
	public static function RegisterMail($data){
		Mail::to($data['email'])->send(new RegisterMail($data));
	}

	public static function RecoverMail($data){
		Mail::to($data['email'])->send(new RecoverMail($data));
	}
}
