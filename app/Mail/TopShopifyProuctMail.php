<?php

namespace App\Mail;

use App\EmailTemplate;
use App\Product;
use App\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class TopShopifyProuctMail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    private $user;
    private $top_products_stores;
    private $template;

    private $sender = 'info@wefullfill.com';
    public function __construct(User $user)
    {
        $this->user = $user;
        $this->top_products_stores = Product::all();
        $this->template = EmailTemplate::find(13);
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->from($this->sender,'Wefullfill')->subject('Our Top Products')->view('emails.top_products_new')->with([
            'user' => $this->user,
            'template' => $this->template,
            'top_products_stores' => $this->top_products_stores,
        ]);
    }
}
