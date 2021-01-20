<?php

namespace App\Mail;

use App\EmailTemplate;
use App\RetailerOrder;
use App\Wishlist;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class WishlistReqeustMail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    private $user = "info@wefullfill.com";

    private $sender;
    private $wishlist;
    private $template;

    public function __construct($sender,Wishlist $wishlist)
    {
        $this->sender = $sender;
        $this->wishlist = $wishlist;
        $this->template = EmailTemplate::find(5);
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->from($this->user,'Wefullfill')->subject('There is a wishlist request')->view('emails.wishlist_request')->with([
            'template' => $this->template,
            'wishlist' => $this->wishlist,
        ]);
    }
}
