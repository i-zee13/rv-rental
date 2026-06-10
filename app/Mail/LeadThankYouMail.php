<?php

namespace App\Mail;

use App\Models\Lead;
use App\Services\LeadEmailComposer;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class LeadThankYouMail extends Mailable
{
    use Queueable, SerializesModels;

    public array $emailContent;

    public function __construct(public Lead $lead)
    {
        $this->emailContent = (new LeadEmailComposer())->customerBody($lead);
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: (new LeadEmailComposer())->customerSubject($this->lead),
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.leads.customer-reply',
            with: [
                'lead' => $this->lead,
                'content' => $this->emailContent,
            ],
        );
    }
}
