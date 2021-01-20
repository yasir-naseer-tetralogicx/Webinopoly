<?php

namespace App\Mail;

use App\EmailTemplate;
use App\User;
use App\Wishlist;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class WishlistComplateMail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    private $user;
    private $wishlist;
    private $template;

    private $sender = 'info@wefullfill.com';
    public function __construct(User $user, Wishlist $wishlist)
    {
        $this->user = $user;
        $this->wishlist = $wishlist;
        $this->template = EmailTemplate::find(12);
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->from($this->sender,'Wefullfill')->subject('Wishlist Completed')->view('emails.wishlist_complete')->with([
            'template' => $this->template,
            'wishlist' => $this->wishlist,
        ]);
    }
}
