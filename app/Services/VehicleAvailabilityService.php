<?php

namespace App\Services;

use App\Models\Booking;
use App\Models\Vehicle;
use Carbon\Carbon;

class VehicleAvailabilityService
{
    public function isEnabled(): bool
    {
        return (bool) config('booking.check_availability', false);
    }

    public function isVehicleBookable(int $vehicleId, string $startDate, string $endDate): bool
    {
        if (!$this->isEnabled()) {
            return Vehicle::where('id', $vehicleId)->where('status', 'available')->exists();
        }

        $vehicle = Vehicle::find($vehicleId);
        if (!$vehicle || $vehicle->status !== 'available') {
            return false;
        }

        return !$this->hasOverlap($vehicleId, $startDate, $endDate);
    }

    public function hasOverlap(int $vehicleId, string $startDate, string $endDate): bool
    {
        $start = Carbon::parse($startDate)->toDateString();
        $end = Carbon::parse($endDate)->toDateString();
        $statuses = config('booking.blocking_statuses', ['pending', 'confirmed']);

        return Booking::where('vehicle_id', $vehicleId)
            ->whereIn('status', $statuses)
            ->whereNotNull('start_date')
            ->whereNotNull('end_date')
            ->where(function ($q) use ($start, $end) {
                $q->whereBetween('start_date', [$start, $end])
                    ->orWhereBetween('end_date', [$start, $end])
                    ->orWhere(function ($q2) use ($start, $end) {
                        $q2->where('start_date', '<=', $start)
                            ->where('end_date', '>=', $end);
                    });
            })
            ->exists();
    }
}
