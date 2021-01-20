<?php

namespace App\Mail;

use App\EmailTemplate;
use App\Product;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ProductDeleteMail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     *
     * @return void
     */

    private $product;
    private $template;
    private $sender = 'info@wefullfill.com';

    public function __construct(Product $product)
    {
        $this->product = $product;
        $this->template = EmailTemplate::find(15);
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->from($this->sender,'Wefullfill')->subject('Product Deleted on Wefullfill')->view('emails.product_delete')->with([
            'product' => $this->product,
            'template' => $this->template
        ]);
    }
}
