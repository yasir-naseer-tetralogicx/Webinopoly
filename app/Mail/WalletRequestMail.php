<?php

namespace App\Mail;

use App\EmailTemplate;
use App\Wallet;
use App\Wishlist;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use PharIo\Manifest\Email;

class WalletRequestMail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    private $user = "info@wefullfill.com";

    private $sender;
    private $wallet;
    private $template;

    public function __construct($sender,Wallet $wallet)
    {
        $this->sender = $sender;
        $this->wallet = $wallet;
        $this->template = EmailTemplate::find(6);
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->from($this->user,'Wefullfill')->subject('There is a wallet request')->view('emails.wallet_reqeust')->with([
            'template' => $this->template,
            'wallet' => $this->wallet,
        ]);
    }
}
