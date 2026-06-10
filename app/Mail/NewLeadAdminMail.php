<?php

namespace App\Mail;

use App\Models\Lead;
use App\Services\LeadEmailComposer;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class NewLeadAdminMail extends Mailable
{
    use Queueable, SerializesModels;

    public array $summary;

    public function __construct(public Lead $lead)
    {
        $this->summary = (new LeadEmailComposer())->adminSummary($lead);
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: (new LeadEmailComposer())->adminSubject($this->lead),
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.leads.admin-notification',
            with: [
                'lead' => $this->lead,
                'summary' => $this->summary,
            ],
        );
    }
}
