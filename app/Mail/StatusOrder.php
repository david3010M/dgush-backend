<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Address;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class StatusOrder extends Mailable
{
    use Queueable, SerializesModels;

    public $order;
    public $user;
    public $orderItems;
    public $total;
    public $messageToSend;
    public $subject;
    public $status;

    public function __construct($order, $user, $orderItems, $total, $messageToSend, $subject, $status)
    {
        $this->order = $order;
        $this->user = $user;
        $this->orderItems = $orderItems;
        $this->total = $total;
        $this->messageToSend = $messageToSend;
        $this->subject = $subject;
        $this->status = $status;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            from: new Address('dgush@gmail.com', 'DGush'),
            subject: $this->subject,
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'mail.statusOrder',
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
