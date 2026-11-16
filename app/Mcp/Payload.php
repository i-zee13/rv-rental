<?php

namespace App\Mcp;

class Payload
{
    public static function make(string $entity, array $data): array
    {
        return [
            'source' => 'mv_miami_rental',
            'entity' => $entity,
            'timezone' => (string) config('app.timezone', 'UTC'),
            'retrieved_at' => now()->utc()->format('Y-m-d\TH:i:s\Z'),
        ] + $data;
    }

    public static function money(mixed $amount, ?string $currency = null): array
    {
        return [
            'amount' => round((float) $amount, 2),
            'currency' => strtoupper($currency ?: (string) config('booking.currency', 'USD')),
        ];
    }
}
