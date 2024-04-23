<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class RecoverMail extends Mailable
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
        $this->subject('Mật khẩu đăng nhập mới !');
        $this->data = $data;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->from('no-reply@stds.vn', 'STD&S Warehouse')->markdown('User.Recover',[
            'url' => url('/login'),
            'email' => $this->data['email'],
            'user' => $this->data['fullname'],
            'password' => $this->data['password'],
        ]);
    }
}
