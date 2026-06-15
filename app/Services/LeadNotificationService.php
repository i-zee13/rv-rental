<?php

namespace App\Services;

use App\Mail\LeadThankYouMail;
use App\Mail\NewLeadAdminMail;
use App\Models\Lead;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class LeadNotificationService
{
    public function sendEmails(Lead $lead): void
    {
        try {
            Mail::to($lead->email)->send(new LeadThankYouMail($lead));
            $lead->update(['customer_email_sent' => true]);
        } catch (\Throwable $e) {
            Log::error('Lead customer email failed', ['lead_id' => $lead->id, 'error' => $e->getMessage()]);
        }

        try {
            $adminEmail = config('leads.admin_email');
            if ($adminEmail) {
                Mail::to($adminEmail)->send(new NewLeadAdminMail($lead));
                $lead->update(['admin_email_sent' => true]);
            }
        } catch (\Throwable $e) {
            Log::error('Lead admin email failed', ['lead_id' => $lead->id, 'error' => $e->getMessage()]);
        }
    }
}
