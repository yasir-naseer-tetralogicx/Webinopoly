<?php

namespace App\Mail;

use App\EmailTemplate;
use App\RetailerOrder;
use App\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class OrderStatusMail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    private $user;
    private $order;
    private $template;

    private $sender = 'info@wefullfill.com';
    public function __construct(User $user, RetailerOrder $order)
    {
        $this->user = $user;
        $this->order = $order;
        $this->template = EmailTemplate::find(4);
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->from($this->sender,'Wefullfill')->subject('Order Status Updated')->view('emails.order_status')->with([
            'template' => $this->template,
            'order' => $this->order,
        ]);
    }
}
