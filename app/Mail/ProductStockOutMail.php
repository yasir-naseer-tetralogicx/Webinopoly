<?php

namespace App\Mail;

use App\EmailTemplate;
use App\Product;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ProductStockOutMail extends Mailable
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
        $this->template = EmailTemplate::find(16);
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->from($this->sender,'Wefullfill')->subject('Product is Out of Stock on Wefullfill')->view('emails.product_stock')->with([
            'product' => $this->product,
            'template' => $this->template,
        ]);
    }
}
