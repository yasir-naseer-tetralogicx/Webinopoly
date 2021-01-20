<?php

namespace App\Mail;

use App\EmailTemplate;
use App\Ticket;
use App\User;
use App\Wallet;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class TicketReplyMail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    private $user;
    private $ticket;
    private $template;

    private $sender = 'info@wefullfill.com';
    public function __construct(User $user, Ticket $ticket)
    {
        $this->user = $user;
        $this->ticket = $ticket;
        $this->template = EmailTemplate::find(11);
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->from($this->sender,'Wefullfill')->subject('Ticket Replied')->view('emails.ticket_reply')->with([
            'template' => $this->template,
            'ticket' => $this->ticket,
        ]);
    }
}
