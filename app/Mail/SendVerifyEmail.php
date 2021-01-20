<?php

namespace App\Mail;

use App\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class SendVerifyEmail extends Mailable
{
    use Queueable, SerializesModels;

    private $user;
    private $sender = 'info@wefullfill.com';

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(User $user)
    {
        $this->user = $user;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->from($this->sender,'Wefullfill')->subject('Verify Email')->view('emails.reset')->with([
            'token' => $this->token,
            'user' => $this->user
        ]);
    }
}
