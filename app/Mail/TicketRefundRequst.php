<?php

namespace App\Mail;

use App\EmailTemplate;
use App\Ticket;
use App\Wishlist;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class TicketRefundRequst extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    private $user = "order@wefullfill.com";

    private $sender;
    private $ticket;
    private $template;

    public function __construct($sender,Ticket $ticket)
    {
        $this->sender = $sender;
        $this->ticket = $ticket;
        $this->template = EmailTemplate::find(7);
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->from($this->user,'Wefullfill')->subject('There is a ticket request')->view('emails.refund_request')->with([
            'ticket' => $this->ticket,
            'template' => $this->template
        ]);
    }
}
