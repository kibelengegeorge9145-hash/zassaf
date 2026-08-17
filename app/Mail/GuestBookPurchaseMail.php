<?php

namespace App\Mail;

use App\Models\Payment;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class GuestBookPurchaseMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public Payment $payment,
        public string $downloadUrl,
    ) {
    }

    public function envelope(): Envelope
    {
        return new Envelope(subject: __('books.emails.guest_subject'));
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.guest-book-purchase',
            with: [
                'book' => $this->payment->book,
                'amount' => $this->payment->formatted_amount,
                'reference' => $this->payment->transaction_reference,
                'downloadUrl' => $this->downloadUrl,
            ],
        );
    }
}
