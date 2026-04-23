<?php

namespace App\Mail;

use App\Models\Abonnement;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class PaiementConfirmeMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public readonly User $user,
        public readonly Abonnement $abonnement,
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Confirmation de paiement — eCompta360',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.paiement-confirme',
        );
    }
}
