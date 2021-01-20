<?php

namespace App\Mail;

use App\EmailTemplate;
use App\Product;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class NewProductsMail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     *
     * @return void
     */

    private $new_products;
    private $template;
    private $sender = 'info@wefullfill.com';

    public function __construct($new_products)
    {
        $this->new_products = $new_products;
        $this->template = EmailTemplate::find(14);
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->from($this->sender,'Wefullfill')->subject('Our New Products')->view('emails.new_products')->with([
            'template' => $this->template,
            'new_products' => $this->new_products,
        ]);
    }
}
