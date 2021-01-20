<?php

namespace App\Mail;

use App\EmailTemplate;
use App\User;
use App\Wallet;
use App\Wishlist;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class WalletApproveMail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    private $user;
    private $wallet;
    private $template;

    private $sender = 'info@wefullfill.com';
    public function __construct(User $user, Wallet $wallet)
    {
        $this->user = $user;
        $this->wallet = $wallet;
        $this->template = EmailTemplate::find(10);
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->from($this->sender,'Wefullfill')->subject('Wallet Approved')->view('emails.wallet_approve')->with([
            'template' => $this->template,
            'wallet' => $this->wallet,
        ]);
    }
}
