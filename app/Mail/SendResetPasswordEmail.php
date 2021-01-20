<?php

namespace App\Mail;

use App\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class SendResetPasswordEmail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    private $token;
    private $user;
    private $sender = 'info@wefullfill.com';



    /**
     * SendResetPasswordEmail constructor.
     * @param $token
     * @param $user
     */
    public function __construct($token, User $user)
    {
        $this->token = $token;
        $this->user = $user;
    }


    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->from($this->sender,'Wefullfill')->subject('Reset Password Link')->view('emails.reset')->with([
            'token' => $this->token,
            'user' => $this->user
        ]);
    }


}
