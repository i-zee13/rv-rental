<?php

namespace App\Services;

use App\Models\Lead;
use Carbon\Carbon;

/**
 * Builds personalized, context-aware email copy for lead follow-ups.
 * Mimics AI-style responses using lead data (no external API required).
 */
class LeadEmailComposer
{
    public function customerSubject(Lead $lead): string
    {
        if ($lead->property_name) {
            return "Your rental inquiry — {$lead->property_name} | {$lead->reference}";
        }

        $vehicle = $lead->vehicle_name ?: 'your rental';

        return "Your Miami rental request — {$vehicle} | {$lead->reference}";
    }

    public function customerBody(Lead $lead): array
    {
        $name = $lead->first_name;
        $vehicle = $lead->vehicle_name ?: 'the vehicle you selected';
        $greeting = $this->timeBasedGreeting();

        $intro = "{$greeting} {$name},";

        $paragraphs = [
            "Thank you for reaching out to " . config('app.name') . ". We've received your reservation inquiry and our team is already reviewing the details.",
        ];

        if ($lead->property_name) {
            $paragraphs[] = "You inquired about <strong>{$lead->property_name}</strong>. Our rentals team will share availability, pricing details, and next steps.";
        } elseif ($lead->vehicle_name) {
            $paragraphs[] = "You expressed interest in the <strong>{$lead->vehicle_name}</strong>. Great choice — it's one of our most popular options in Miami.";
        }

        if ($lead->pickup_date && $lead->dropoff_date) {
            $from = $lead->pickup_date->format('M j, Y');
            $to = $lead->dropoff_date->format('M j, Y');
            $days = max(1, $lead->pickup_date->diffInDays($lead->dropoff_date));
            $paragraphs[] = "Your requested dates are <strong>{$from}</strong> to <strong>{$to}</strong> ({$days} day" . ($days > 1 ? 's' : '') . "). We'll confirm availability shortly.";
        }

        if ($lead->pickup_location) {
            $drop = $lead->dropoff_location && $lead->dropoff_location !== $lead->pickup_location
                ? " with drop-off at <strong>{$lead->dropoff_location}</strong>"
                : '';
            $paragraphs[] = "Pick-up location noted: <strong>{$lead->pickup_location}</strong>{$drop}.";
        }

        $paragraphs[] = "A specialist will contact you within <strong>1 business hour</strong> to finalize your booking. If you need immediate assistance, call us at <strong>+1 (786) 978-5809</strong> or reply to this email.";

        $paragraphs[] = "Reference number: <strong>{$lead->reference}</strong> — please keep this for your records.";

        return [
            'greeting' => $intro,
            'paragraphs' => $paragraphs,
            'cta_text' => $lead->property_name ? 'Browse Rentals' : 'Browse Our Fleet',
            'cta_url' => $lead->property_name ? route('properties.search') : route('search'),
        ];
    }

    public function adminSubject(Lead $lead): string
    {
        return "[New Lead] {$lead->full_name} — {$lead->reference}";
    }

    public function adminSummary(Lead $lead): array
    {
        return [
            'reference' => $lead->reference,
            'name' => $lead->full_name,
            'email' => $lead->email,
            'phone' => $lead->phone ?: '—',
            'vehicle' => $lead->vehicle_name ?: '—',
            'property' => $lead->property_name ?: '—',
            'dates' => $lead->pickup_date
                ? $lead->pickup_date->format('Y-m-d') . ' → ' . ($lead->dropoff_date?->format('Y-m-d') ?? '—')
                : '—',
            'pickup' => $lead->pickup_location ?: '—',
            'dropoff' => $lead->dropoff_location ?: '—',
            'message' => $lead->message ?: '—',
            'source' => ucfirst($lead->source),
            'portal_url' => route('admin.leads.show', $lead->id),
        ];
    }

    protected function timeBasedGreeting(): string
    {
        $hour = (int) Carbon::now('America/New_York')->format('G');

        if ($hour < 12) {
            return 'Good morning';
        }
        if ($hour < 17) {
            return 'Good afternoon';
        }

        return 'Good evening';
    }
}
