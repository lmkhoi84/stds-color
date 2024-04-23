<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class RegisterMail extends Mailable
{
    use Queueable, SerializesModels;
    public $data;
    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($data)
    {
        $this->subject('Đăng ký người dùng mới !');
        $this->data = $data;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->from('no-reply@stds.vn', 'STD&S Warehouse')->bcc('khoi.leminh@stdsvn.com')->markdown('User.Register',[
            'url' => url('/login'),
            'email' => $this->data['email'],
            'user' => $this->data['fullname'],
            'password' => $this->data['password'],
        ]);
    }
}
