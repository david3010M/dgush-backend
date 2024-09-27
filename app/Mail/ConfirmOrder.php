<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Address;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ConfirmOrder extends Mailable
{
    use Queueable, SerializesModels;

    public $order;
    public $user;
    public $orderItems;
    public $total;

    /**
     * Create a new message instance.
     */
    public function __construct($order, $user, $orderItems, $total)
    {
        $this->order = $order;
        $this->user = $user;
        $this->orderItems = $orderItems;
        $this->total = $total;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            from: new Address('dgush@gmail.com', 'DGush'),
            subject: 'Orden confirmada',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'mail.confirmOrder',
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        return [];
    }
}
