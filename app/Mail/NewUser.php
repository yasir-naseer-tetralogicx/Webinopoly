<?php

namespace App\Mail;

use App\EmailTemplate;
use App\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class NewUser extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    private $user;
    private $template;

    private $sender = 'info@wefullfill.com';
    public function __construct(User $user)
    {
        $this->user = $user;
        $this->template = EmailTemplate::find(1);
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->from($this->sender,'Wefullfill')->subject('Welcome to Wefullfill')->view('emails.new_user')->with([
            'user' => $this->user,
            'template' => $this->template,
        ]);
    }
}
