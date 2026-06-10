<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Lead extends Model
{
    use SoftDeletes;

    public const STATUSES = [
        'new'       => 'New',
        'lead'      => 'Lead',
        'contacted' => 'Contacted',
        'qualified' => 'Qualified',
        'converted' => 'Converted',
        'spam'      => 'Spam',
        'closed'    => 'Closed',
    ];

    protected $fillable = [
        'reference', 'status', 'source', 'vehicle_id', 'vehicle_name',
        'first_name', 'last_name', 'email', 'phone',
        'pickup_location', 'dropoff_location',
        'pickup_date', 'dropoff_date', 'pickup_time', 'dropoff_time',
        'message', 'ip_address', 'user_agent', 'locale',
        'customer_email_sent', 'admin_email_sent', 'admin_notes', 'contacted_at',
    ];

    protected $casts = [
        'pickup_date' => 'date',
        'dropoff_date' => 'date',
        'customer_email_sent' => 'boolean',
        'admin_email_sent' => 'boolean',
        'contacted_at' => 'datetime',
    ];

    public function vehicle(): BelongsTo
    {
        return $this->belongsTo(Vehicle::class);
    }

    public function getFullNameAttribute(): string
    {
        return trim($this->first_name . ' ' . ($this->last_name ?? ''));
    }

    public function getStatusLabelAttribute(): string
    {
        return self::STATUSES[$this->status] ?? ucfirst($this->status);
    }

    public static function generateReference(): string
    {
        do {
            $ref = 'LD-' . strtoupper(substr(uniqid(), -8));
        } while (self::where('reference', $ref)->exists());

        return $ref;
    }
}
