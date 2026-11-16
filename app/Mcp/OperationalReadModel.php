<?php

namespace App\Mcp;

use App\Models\Booking;
use App\Models\BlogPost;
use App\Models\Faq;
use App\Models\Lead;
use App\Models\Page;
use App\Models\Payment;
use App\Models\Property;
use App\Models\Vehicle;
use App\Models\VehicleCategory;
use App\Services\StripeCheckoutService;
use App\Services\VehicleAvailabilityService;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;

class OperationalReadModel
{
    public function __construct(
        protected VehicleAvailabilityService $availability,
    ) {}

    public function businessOverview(array $args): array
    {
        [$from, $to] = $this->period($args);

        $paid = Payment::query()->where('status', 'paid')->whereBetween('updated_at', [$from, $to]);

        return Payload::make('business_overview', [
            'period' => $this->periodPayload($from, $to),
            'vehicles_total' => Vehicle::count(),
            'vehicles_with_status_available' => Vehicle::where('status', 'available')->count(),
            'bookings_created' => Booking::whereBetween('created_at', [$from, $to])->count(),
            'bookings_pending' => Booking::whereBetween('created_at', [$from, $to])->whereIn('status', ['pending', 'pending_payment'])->count(),
            'collected_revenue' => Payload::money((clone $paid)->sum('amount')),
            'paid_payment_count' => (clone $paid)->count(),
            'new_leads' => Lead::where('status', 'new')->whereBetween('created_at', [$from, $to])->count(),
            'property_inquiries' => Lead::whereNotNull('property_id')->whereBetween('created_at', [$from, $to])->count(),
            'vehicle_inquiries' => Lead::whereNotNull('vehicle_id')->whereNull('property_id')->whereBetween('created_at', [$from, $to])->count(),
            'properties_total' => Property::count(),
            'limitations' => BusinessRules::limitations(),
        ]);
    }

    public function listVehicles(array $args): array
    {
        $query = Vehicle::query()->with(['translations', 'category.translations'])->orderByDesc('id');
        $this->filterVehicles($query, $args);
        [$rows, $pagination] = $this->slice($query, $args);

        return Payload::make('vehicle_list', [
            'vehicles' => $rows->map(fn (Vehicle $vehicle) => $this->vehicleSummary($vehicle))->values()->all(),
            'pagination' => $pagination,
        ]);
    }

    public function getVehicle(array $args): array
    {
        $vehicle = $this->findVehicle($args);
        $vehicle->load(['translations', 'category.translations']);

        $summary = $this->vehicleSummary($vehicle);
        $translation = $this->englishTranslation($vehicle->translations);

        return Payload::make('vehicle', $summary + [
            'doors' => (int) $vehicle->doors,
            'bags' => (int) $vehicle->bags,
            'security_deposit' => Payload::money($vehicle->security_deposit),
            'cleaning_fee' => Payload::money($vehicle->cleaning_fee),
            'delivery_fee' => Payload::money($vehicle->delivery_fee),
            'delivery_available' => (bool) $vehicle->delivery_available,
            'instant_book' => (bool) $vehicle->instant_book,
            'pet_friendly' => (bool) $vehicle->pet_friendly,
            'smoking_allowed' => (bool) $vehicle->smoking_allowed,
            'description' => $translation?->description,
            'fee_note' => 'Security deposit, cleaning fee, and delivery fee are listing fields. BookingCreatorService does not add them to bookings.subtotal, taxes, or total.',
            'rate_note' => 'Confirmed booking totals use price_per_day times inclusive dates. price_per_week and price_per_month are display rates and are not used by BookingCreatorService.',
        ]);
    }

    public function checkVehicleAvailability(array $args): array
    {
        $vehicle = Vehicle::query()->find($args['vehicle_id']);

        if (! $vehicle) {
            throw new ToolInputException('Vehicle not found.');
        }

        $start = $args['start'];
        $end = $args['end'];

        return Payload::make('vehicle_availability', [
            'vehicle_id' => $vehicle->id,
            'vehicle_status' => $vehicle->status,
            'requested_period' => ['start' => $start, 'end' => $end],
            'available' => $this->availability->isVehicleBookable($vehicle->id, $start, $end),
            'calendar_conflict' => $this->availability->hasOverlap($vehicle->id, $start, $end),
            'date_overlap_enforced' => $this->availability->isEnabled(),
            'check_mode' => BusinessRules::availabilityMode(),
            'blocking_statuses' => array_values(config('booking.blocking_statuses', [])),
            'note' => $this->availability->isEnabled()
                ? 'available matches VehicleAvailabilityService: status must be available and calendar_conflict must be false. Cancelled bookings are not in blocking_statuses.'
                : 'BOOKING_CHECK_AVAILABILITY is off. available matches the website and only checks status=available. calendar_conflict is informational and does not block a booking while overlap checks are disabled.',
        ]);
    }

    public function fleetSummary(array $args): array
    {
        $rows = Vehicle::query()
            ->leftJoin('vehicle_categories', 'vehicle_categories.id', '=', 'vehicles.category_id')
            ->selectRaw('vehicle_categories.slug as category_slug, vehicles.status as status, COUNT(*) as aggregate')
            ->groupBy('vehicle_categories.slug', 'vehicles.status')
            ->get();

        $names = VehicleCategory::query()->with('translations')->get()
            ->mapWithKeys(fn (VehicleCategory $category) => [$category->slug => $category->translatedName('en')]);

        $byCategory = [];
        $byStatus = [];

        foreach ($rows as $row) {
            $slug = $row->category_slug;
            $key = $slug ?: 'uncategorized';
            $byCategory[$key] ??= [
                'slug' => $slug,
                'name' => $slug ? ($names[$slug] ?? $slug) : 'Uncategorized',
                'total' => 0,
                'by_status' => [],
            ];
            $byCategory[$key]['total'] += (int) $row->aggregate;
            $byCategory[$key]['by_status'][$row->status] = (int) $row->aggregate;
            $byStatus[$row->status] = ($byStatus[$row->status] ?? 0) + (int) $row->aggregate;
        }

        return Payload::make('fleet_summary', [
            'total' => Vehicle::count(),
            'by_status' => $byStatus,
            'by_category' => array_values($byCategory),
            'status_values' => ['available', 'unavailable', 'maintenance'],
            'note' => 'Counts use the vehicle status column. They are not date-based availability.',
        ]);
    }

    public function listBookings(array $args): array
    {
        $query = Booking::query()->with(['payments', 'vehicle.translations'])->orderByDesc('created_at');
        $this->filterBookings($query, $args);
        [$rows, $pagination] = $this->slice($query, $args);

        return Payload::make('booking_list', [
            'bookings' => $rows->map(fn (Booking $booking) => $this->bookingSummary($booking))->values()->all(),
            'pagination' => $pagination,
        ]);
    }

    public function getBooking(array $args): array
    {
        $booking = $this->findBooking($args);
        $booking->load([
            'payments',
            'addons.addon.translations',
            'vehicle' => fn ($query) => $query->withTrashed()->with('translations'),
        ]);

        $summary = $this->bookingSummary($booking);

        return Payload::make('booking', $summary + [
            'customer' => [
                'customer_id' => $booking->customer_id,
                'first_name' => $booking->first_name,
                'last_name' => $booking->last_name,
                'email' => $booking->email,
                'phone' => $booking->phone,
            ],
            'pickup_location' => $booking->pickup_location,
            'dropoff_location' => $booking->dropoff_location,
            'notes' => $booking->notes,
            'rental_days' => $this->rentalDays($booking->start_date, $booking->end_date),
            'addons' => $booking->addons->map(function ($row) use ($booking) {
                return [
                    'addon_id' => $row->addon_id,
                    'code' => $row->addon?->code,
                    'name' => $row->addon?->name,
                    'quantity' => (int) $row->quantity,
                    'price' => Payload::money($row->price, $booking->currency),
                ];
            })->values()->all(),
            'payments' => $booking->payments->map(fn (Payment $payment) => [
                'id' => $payment->id,
                'provider' => $payment->provider,
                'status' => $payment->status,
                'amount' => Payload::money($payment->amount, $payment->currency),
                'created_at' => $this->iso($payment->created_at),
                'updated_at' => $this->iso($payment->updated_at),
            ])->values()->all(),
            'pricing_note' => 'subtotal, taxes, and total are the amounts stored when the booking was created. They are not collected revenue unless a payment status is paid.',
        ]);
    }

    public function upcomingBookings(array $args): array
    {
        $query = Booking::query()
            ->with(['payments', 'vehicle.translations'])
            ->whereDate('start_date', '>=', now()->toDateString())
            ->whereIn('status', ['pending', 'pending_payment', 'confirmed', 'active'])
            ->orderBy('start_date');

        if (! empty($args['within_days'])) {
            $query->whereDate('start_date', '<=', now()->addDays((int) $args['within_days'])->toDateString());
        }

        [$rows, $pagination] = $this->slice($query, $args);

        return Payload::make('upcoming_bookings', [
            'definition' => 'Bookings whose start_date is today or later and whose status is pending, pending_payment, confirmed, or active. The application does not auto-advance status to active.',
            'bookings' => $rows->map(fn (Booking $booking) => $this->bookingSummary($booking))->values()->all(),
            'pagination' => $pagination,
        ]);
    }

    public function activeRentals(array $args): array
    {
        $today = now()->toDateString();
        $query = Booking::query()
            ->with(['payments', 'vehicle.translations'])
            ->whereIn('status', ['confirmed', 'active'])
            ->whereDate('start_date', '<=', $today)
            ->whereDate('end_date', '>=', $today)
            ->orderBy('start_date');

        [$rows, $pagination] = $this->slice($query, $args);

        return Payload::make('active_rentals', [
            'definition' => 'Bookings with status confirmed or active whose start_date is today or earlier and end_date is today or later. Pending and pending_payment bookings are not treated as active rentals. The admin UI does not automatically set status to active.',
            'as_of' => $today,
            'bookings' => $rows->map(fn (Booking $booking) => $this->bookingSummary($booking))->values()->all(),
            'pagination' => $pagination,
        ]);
    }

    public function bookingStatistics(array $args): array
    {
        [$from, $to] = $this->period($args);
        $query = Booking::query()->whereBetween('created_at', [$from, $to]);
        $byStatus = (clone $query)
            ->selectRaw('status, COUNT(*) as aggregate, SUM(total) as total_amount, SUM(taxes) as tax_amount')
            ->groupBy('status')
            ->get()
            ->map(fn ($row) => [
                'status' => $row->status,
                'count' => (int) $row->aggregate,
                'booking_totals' => Payload::money($row->total_amount),
                'taxes' => Payload::money($row->tax_amount),
            ])->values()->all();

        return Payload::make('booking_statistics', [
            'period' => $this->periodPayload($from, $to),
            'bookings_created' => (clone $query)->count(),
            'booking_totals' => Payload::money((clone $query)->sum('total')),
            'taxes_on_created_bookings' => Payload::money((clone $query)->sum('taxes')),
            'by_status' => $byStatus,
            'note' => 'booking_totals are stored booking amounts, not collected cash. Use mv_get_revenue_summary for paid payments.',
        ]);
    }

    public function listProperties(array $args): array
    {
        $query = Property::query()->with(['translations', 'type.translations'])->orderByDesc('id');
        $this->filterProperties($query, $args);
        [$rows, $pagination] = $this->slice($query, $args);

        return Payload::make('property_list', [
            'reservations_supported' => false,
            'properties' => $rows->map(fn (Property $property) => $this->propertySummary($property))->values()->all(),
            'pagination' => $pagination,
        ]);
    }

    public function getProperty(array $args): array
    {
        $property = $this->findProperty($args);
        $property->load(['translations', 'type.translations']);
        $translation = $property->translations->firstWhere('locale', 'en') ?? $property->translations->first();

        return Payload::make('property', $this->propertySummary($property) + [
            'reference' => $property->reference,
            'address' => $property->fullAddress(),
            'latitude' => $property->latitude,
            'longitude' => $property->longitude,
            'security_deposit' => Payload::money($property->security_deposit),
            'cleaning_fee' => Payload::money($property->cleaning_fee),
            'instant_book' => (bool) $property->instant_book,
            'amenities' => $property->amenityLabels(),
            'description' => $translation?->description,
            'highlights' => $translation?->highlights,
            'reservations_supported' => false,
            'note' => 'instant_book and status are listing fields. There is no property reservation calendar. Customers submit a lead.',
        ]);
    }

    public function propertyInquiries(array $args): array
    {
        $query = Lead::query()->whereNotNull('property_id')->orderByDesc('created_at');

        if (! empty($args['property_id'])) {
            $query->where('property_id', $args['property_id']);
        }
        if (! empty($args['status'])) {
            $query->where('status', $args['status']);
        }
        $this->applyCreatedRange($query, $args);

        [$rows, $pagination] = $this->slice($query, $args);

        return Payload::make('property_inquiries', [
            'definition' => 'Leads with a property_id. These are inquiries, not reservations.',
            'inquiries' => $rows->map(fn (Lead $lead) => $this->leadSummary($lead))->values()->all(),
            'pagination' => $pagination,
        ]);
    }

    public function listLeads(array $args): array
    {
        $query = Lead::query()->orderByDesc('created_at');
        $this->filterLeads($query, $args);
        [$rows, $pagination] = $this->slice($query, $args);

        return Payload::make('lead_list', [
            'leads' => $rows->map(fn (Lead $lead) => $this->leadSummary($lead))->values()->all(),
            'pagination' => $pagination,
        ]);
    }

    public function getLead(array $args): array
    {
        $lead = $this->findLead($args);

        return Payload::make('lead', $this->leadSummary($lead) + [
            'customer' => [
                'first_name' => $lead->first_name,
                'last_name' => $lead->last_name,
                'email' => $lead->email,
                'phone' => $lead->phone,
            ],
            'pickup_location' => $lead->pickup_location,
            'dropoff_location' => $lead->dropoff_location,
            'pickup_time' => $lead->pickup_time,
            'dropoff_time' => $lead->dropoff_time,
            'message' => $lead->message,
            'admin_notes' => $lead->admin_notes,
            'locale' => $lead->locale,
            'customer_email_sent' => (bool) $lead->customer_email_sent,
            'admin_email_sent' => (bool) $lead->admin_email_sent,
        ]);
    }

    public function leadStatistics(array $args): array
    {
        [$from, $to] = $this->period($args);
        $query = Lead::query()->whereBetween('created_at', [$from, $to]);

        return Payload::make('lead_statistics', [
            'period' => $this->periodPayload($from, $to),
            'total' => (clone $query)->count(),
            'by_status' => $this->countsBy($query, 'status'),
            'by_source' => $this->countsBy($query, 'source'),
            'by_type' => [
                'property_inquiry' => (clone $query)->whereNotNull('property_id')->count(),
                'vehicle_inquiry' => (clone $query)->whereNotNull('vehicle_id')->whereNull('property_id')->count(),
                'general' => (clone $query)->whereNull('property_id')->whereNull('vehicle_id')->count(),
            ],
            'booking_conversion_rate' => null,
            'booking_conversion_rate_available' => false,
            'note' => 'Status converted is set by an admin. It is not linked to a booking, so no conversion rate is calculated.',
        ]);
    }

    public function uncontactedLeads(array $args): array
    {
        $query = Lead::query()
            ->where('status', 'new')
            ->whereNull('contacted_at')
            ->orderBy('created_at');
        $this->applyCreatedRange($query, $args);
        [$rows, $pagination] = $this->slice($query, $args);

        return Payload::make('uncontacted_leads', [
            'definition' => 'Leads whose status is new and contacted_at is null. Status lead is a later pipeline stage and is not included.',
            'leads' => $rows->map(fn (Lead $lead) => $this->leadSummary($lead))->values()->all(),
            'pagination' => $pagination,
        ]);
    }

    public function paymentSummary(array $args): array
    {
        $query = Payment::query();
        $period = null;

        if (! empty($args['from']) || ! empty($args['to'])) {
            [$from, $to] = $this->period($args);
            $query->whereBetween('updated_at', [$from, $to]);
            $period = $this->periodPayload($from, $to);
        }

        $rows = (clone $query)
            ->selectRaw('status, COUNT(*) as aggregate, SUM(amount) as total_amount')
            ->groupBy('status')
            ->get()
            ->map(fn ($row) => [
                'status' => $row->status,
                'count' => (int) $row->aggregate,
                'amount' => Payload::money($row->total_amount),
            ])->values()->all();

        return Payload::make('payment_summary', [
            'scope' => $period ? 'updated_in_period' : 'all_payment_records',
            'period' => $period,
            'by_status' => $rows,
            'recognized_on' => 'payments.updated_at',
            'note' => 'Statuses are stored strings. The application writes pending and paid. It does not create refund records.',
        ]);
    }

    public function revenueSummary(array $args): array
    {
        [$from, $to] = $this->period($args);
        $paid = Payment::query()->where('status', 'paid')->whereBetween('updated_at', [$from, $to]);
        $created = Booking::query()->whereBetween('created_at', [$from, $to]);

        return Payload::make('revenue_summary', [
            'period' => $this->periodPayload($from, $to),
            'collected_revenue' => Payload::money((clone $paid)->sum('amount')),
            'paid_payment_count' => (clone $paid)->count(),
            'booking_totals_created_in_period' => Payload::money((clone $created)->sum('total')),
            'bookings_created' => (clone $created)->count(),
            'recognized_on' => 'payments.updated_at where status is paid',
            'note' => 'collected_revenue is cash recorded on payment rows. booking_totals_created_in_period includes unpaid pay-at-pickup and unfinished checkout totals.',
        ]);
    }

    public function pendingPayments(array $args): array
    {
        $query = Booking::query()
            ->with(['payments', 'vehicle.translations'])
            ->whereIn('status', ['pending', 'pending_payment', 'confirmed', 'active'])
            ->whereDoesntHave('payments', fn ($payment) => $payment->where('status', 'paid'))
            ->orderByDesc('created_at');

        [$rows, $pagination] = $this->slice($query, $args);

        return Payload::make('pending_payments', [
            'definition' => 'Bookings in pending, pending_payment, confirmed, or active with no payment in status paid. pending with no payment row is pay-at-pickup. pending_payment is an unfinished Stripe checkout. confirmed without a paid payment was confirmed without collected Stripe cash.',
            'bookings' => $rows->map(fn (Booking $booking) => $this->bookingSummary($booking))->values()->all(),
            'pagination' => $pagination,
        ]);
    }

    public function searchFaqs(array $args): array
    {
        $term = '%'.str_replace(['%', '_'], '', $args['query']).'%';
        $faqs = Faq::query()
            ->with('translations')
            ->where('is_active', true)
            ->whereHas('translations', function ($query) use ($term) {
                $query->where('question', 'like', $term)->orWhere('answer', 'like', $term);
            })
            ->orderBy('sort_order')
            ->limit(20)
            ->get();

        return Payload::make('faqs', [
            'query' => $args['query'],
            'truncated' => false,
            'faqs' => $faqs->map(function (Faq $faq) {
                $translations = [];
                foreach ($faq->translations as $translation) {
                    $translations[$translation->locale] = [
                        'question' => $translation->question,
                        'answer' => $translation->answer,
                    ];
                }

                return [
                    'id' => $faq->id,
                    'scope' => $faq->scope,
                    'page_keys' => $faq->page_keys,
                    'translations' => $translations,
                ];
            })->values()->all(),
        ]);
    }

    public function siteInformation(array $args): array
    {
        $pages = Page::query()->with('translations')->where('is_published', true)->orderBy('slug')->get()
            ->map(function (Page $page) {
                $translation = $page->translations->firstWhere('locale', 'en') ?? $page->translations->first();

                return [
                    'slug' => $page->slug,
                    'title' => $translation?->title ?: $page->slug,
                ];
            })->values()->all();

        return Payload::make('site_information', [
            'brand' => 'MV Miami Rental',
            'location' => 'Miami, FL 33122',
            'email' => 'info@mvmiamirental.com',
            'locales' => ['en', 'es'],
            'currency' => strtoupper((string) config('booking.currency', 'USD')),
            'tax_rate' => (float) config('booking.tax_rate', 0),
            'availability_mode' => BusinessRules::availabilityMode(),
            'stripe_checkout_configured' => app(StripeCheckoutService::class)->isConfigured(),
            'property_reservations_supported' => false,
            'published_pages' => $pages,
            'published_blog_posts' => BlogPost::where('status', 'published')->count(),
            'limitations' => BusinessRules::limitations(),
        ]);
    }

    protected function filterVehicles(Builder $query, array $args): void
    {
        if (! empty($args['category'])) {
            $query->whereHas('category', fn ($category) => $category->where('slug', $args['category']));
        }
        if (! empty($args['status'])) {
            $query->where('status', $args['status']);
        }
        if (isset($args['min_price'])) {
            $query->where('price_per_day', '>=', $args['min_price']);
        }
        if (isset($args['max_price'])) {
            $query->where('price_per_day', '<=', $args['max_price']);
        }
        if (! empty($args['search'])) {
            $term = '%'.$this->like($args['search']).'%';
            $query->where(function ($inner) use ($term) {
                $inner->where('make', 'like', $term)
                    ->orWhere('model', 'like', $term)
                    ->orWhere('slug', 'like', $term)
                    ->orWhereHas('translations', fn ($translation) => $translation->where('title', 'like', $term));
            });
        }
    }

    protected function filterBookings(Builder $query, array $args): void
    {
        if (! empty($args['status'])) {
            $query->where('status', $args['status']);
        }
        if (! empty($args['vehicle_id'])) {
            $query->where('vehicle_id', $args['vehicle_id']);
        }
        if (! empty($args['email'])) {
            $query->where('email', $args['email']);
        }
        if (! empty($args['payment_state'])) {
            $this->applyPaymentState($query, $args['payment_state']);
        }
        if (! empty($args['created_from'])) {
            $query->where('created_at', '>=', Carbon::parse($args['created_from'])->startOfDay());
        }
        if (! empty($args['created_to'])) {
            $query->where('created_at', '<=', Carbon::parse($args['created_to'])->endOfDay());
        }
        if (! empty($args['rental_from']) || ! empty($args['rental_to'])) {
            $start = $args['rental_from'] ?? '1970-01-01';
            $end = $args['rental_to'] ?? '2999-12-31';
            $query->whereNotNull('start_date')->whereNotNull('end_date')
                ->where('start_date', '<=', $end)
                ->where('end_date', '>=', $start);
        }
    }

    protected function applyPaymentState(Builder $query, string $state): void
    {
        if ($state === 'paid') {
            $query->whereHas('payments', fn ($payment) => $payment->where('status', 'paid'));

            return;
        }

        $query->whereDoesntHave('payments', fn ($payment) => $payment->where('status', 'paid'));

        if ($state === 'pay_at_pickup') {
            $query->where('status', 'pending')->whereDoesntHave('payments');
        } elseif ($state === 'pending') {
            $query->where(function ($inner) {
                $inner->where('status', 'pending_payment')
                    ->orWhereHas('payments', fn ($payment) => $payment->where('status', 'pending'));
            });
        } elseif ($state === 'unpaid') {
            $query->where('status', '!=', 'pending');
        }
    }

    protected function filterProperties(Builder $query, array $args): void
    {
        if (! empty($args['type'])) {
            $query->whereHas('type', fn ($type) => $type->where('slug', $args['type']));
        }
        if (! empty($args['status'])) {
            $query->where('status', $args['status']);
        }
        if (isset($args['bedrooms_min'])) {
            $query->where('bedrooms', '>=', $args['bedrooms_min']);
        }
        if (isset($args['bathrooms_min'])) {
            $query->where('bathrooms', '>=', $args['bathrooms_min']);
        }
        if (array_key_exists('pets_allowed', $args)) {
            $query->where('pets_allowed', (bool) $args['pets_allowed']);
        }
        if (array_key_exists('furnished', $args)) {
            $query->where('furnished', (bool) $args['furnished']);
        }
        if (isset($args['min_nights_max'])) {
            $query->where('min_nights', '<=', $args['min_nights_max']);
        }
        if (isset($args['min_price'])) {
            $query->where('price_per_month', '>=', $args['min_price']);
        }
        if (isset($args['max_price'])) {
            $query->where('price_per_month', '<=', $args['max_price']);
        }
        if (! empty($args['search'])) {
            $term = '%'.$this->like($args['search']).'%';
            $query->where(function ($inner) use ($term) {
                $inner->where('city', 'like', $term)
                    ->orWhere('neighborhood', 'like', $term)
                    ->orWhere('slug', 'like', $term)
                    ->orWhereHas('translations', fn ($translation) => $translation->where('title', 'like', $term));
            });
        }
    }

    protected function filterLeads(Builder $query, array $args): void
    {
        if (! empty($args['status'])) {
            $query->where('status', $args['status']);
        }
        if (! empty($args['source'])) {
            $query->where('source', $args['source']);
        }
        if (! empty($args['vehicle_id'])) {
            $query->where('vehicle_id', $args['vehicle_id']);
        }
        if (! empty($args['property_id'])) {
            $query->where('property_id', $args['property_id']);
        }
        if (! empty($args['lead_type'])) {
            if ($args['lead_type'] === 'property_inquiry') {
                $query->whereNotNull('property_id');
            } elseif ($args['lead_type'] === 'vehicle_inquiry') {
                $query->whereNotNull('vehicle_id')->whereNull('property_id');
            } elseif ($args['lead_type'] === 'general') {
                $query->whereNull('property_id')->whereNull('vehicle_id');
            }
        }
        if (array_key_exists('contacted', $args)) {
            $args['contacted'] ? $query->whereNotNull('contacted_at') : $query->whereNull('contacted_at');
        }
        $this->applyCreatedRange($query, $args);
    }

    protected function applyCreatedRange(Builder $query, array $args): void
    {
        if (! empty($args['created_from'])) {
            $query->where('created_at', '>=', Carbon::parse($args['created_from'])->startOfDay());
        }
        if (! empty($args['created_to'])) {
            $query->where('created_at', '<=', Carbon::parse($args['created_to'])->endOfDay());
        }
    }

    protected function findVehicle(array $args): Vehicle
    {
        $query = Vehicle::query();
        $vehicle = ! empty($args['id'])
            ? $query->find($args['id'])
            : $query->where('slug', $args['slug'])->first();

        if (! $vehicle) {
            throw new ToolInputException('Vehicle not found.');
        }

        return $vehicle;
    }

    protected function findProperty(array $args): Property
    {
        $query = Property::query();
        $property = ! empty($args['id'])
            ? $query->find($args['id'])
            : $query->where('slug', $args['slug'])->first();

        if (! $property) {
            throw new ToolInputException('Property not found.');
        }

        return $property;
    }

    protected function findBooking(array $args): Booking
    {
        $booking = ! empty($args['id'])
            ? Booking::query()->find($args['id'])
            : Booking::query()->where('reference', $args['reference'])->first();

        if (! $booking) {
            throw new ToolInputException('Booking not found.');
        }

        return $booking;
    }

    protected function findLead(array $args): Lead
    {
        $lead = ! empty($args['id'])
            ? Lead::query()->find($args['id'])
            : Lead::query()->where('reference', $args['reference'])->first();

        if (! $lead) {
            throw new ToolInputException('Lead not found.');
        }

        return $lead;
    }

    protected function vehicleSummary(Vehicle $vehicle): array
    {
        $translation = $this->englishTranslation($vehicle->translations);
        $category = $vehicle->category;

        return [
            'id' => $vehicle->id,
            'slug' => $vehicle->slug,
            'title' => $translation?->title ?: trim(($vehicle->make ?? '').' '.($vehicle->model ?? '')),
            'make' => $vehicle->make,
            'model' => $vehicle->model,
            'year' => $vehicle->year,
            'category' => $category ? [
                'id' => $category->id,
                'slug' => $category->slug,
                'name' => $category->translatedName('en'),
            ] : null,
            'status' => $vehicle->status,
            'seats' => (int) $vehicle->seats,
            'transmission' => $vehicle->transmission,
            'fuel_type' => $vehicle->fuel_type,
            'price_per_day' => Payload::money($vehicle->price_per_day),
            'price_per_week' => $vehicle->price_per_week === null ? null : Payload::money($vehicle->price_per_week),
            'price_per_month' => $vehicle->price_per_month === null ? null : Payload::money($vehicle->price_per_month),
            'featured' => (bool) $vehicle->featured,
        ];
    }

    protected function propertySummary(Property $property): array
    {
        $translation = $property->translations->firstWhere('locale', 'en') ?? $property->translations->first();
        $type = $property->type;

        return [
            'id' => $property->id,
            'slug' => $property->slug,
            'title' => $translation?->title ?: trim(($property->neighborhood ?? '').' '.$property->city),
            'type' => $type ? [
                'id' => $type->id,
                'slug' => $type->slug,
                'name' => $type->translatedName('en'),
            ] : null,
            'city' => $property->city,
            'neighborhood' => $property->neighborhood,
            'bedrooms' => (int) $property->bedrooms,
            'bathrooms' => (float) $property->bathrooms,
            'sqft' => $property->sqft === null ? null : (int) $property->sqft,
            'max_guests' => $property->max_guests === null ? null : (int) $property->max_guests,
            'min_nights' => (int) $property->min_nights,
            'price_per_night' => $property->price_per_night === null ? null : Payload::money($property->price_per_night),
            'price_per_week' => $property->price_per_week === null ? null : Payload::money($property->price_per_week),
            'price_per_month' => Payload::money($property->price_per_month),
            'pets_allowed' => (bool) $property->pets_allowed,
            'furnished' => (bool) $property->furnished,
            'status' => $property->status,
            'available_from' => $this->dateOnly($property->available_from),
        ];
    }

    protected function bookingSummary(Booking $booking): array
    {
        $vehicle = $booking->relationLoaded('vehicle') ? $booking->vehicle : null;
        $title = null;
        if ($vehicle) {
            $translation = $this->englishTranslation($vehicle->translations);
            $title = $translation?->title ?: trim(($vehicle->make ?? '').' '.($vehicle->model ?? ''));
        }

        return [
            'id' => $booking->id,
            'reference' => $booking->reference,
            'status' => $booking->status,
            'vehicle_id' => $booking->vehicle_id,
            'vehicle_title' => $title,
            'customer_id' => $booking->customer_id,
            'start_date' => $this->dateOnly($booking->start_date),
            'end_date' => $this->dateOnly($booking->end_date),
            'subtotal' => Payload::money($booking->subtotal, $booking->currency),
            'taxes' => Payload::money($booking->taxes, $booking->currency),
            'total' => Payload::money($booking->total, $booking->currency),
            'payment_state' => $this->paymentState($booking),
            'created_at' => $this->iso($booking->created_at),
        ];
    }

    protected function leadSummary(Lead $lead): array
    {
        return [
            'id' => $lead->id,
            'reference' => $lead->reference,
            'lead_type' => $this->leadType($lead),
            'status' => $lead->status,
            'source' => $lead->source,
            'vehicle_id' => $lead->vehicle_id,
            'vehicle_name' => $lead->vehicle_name,
            'property_id' => $lead->property_id,
            'property_name' => $lead->property_name,
            'pickup_date' => $this->dateOnly($lead->pickup_date),
            'dropoff_date' => $this->dateOnly($lead->dropoff_date),
            'contacted_at' => $this->iso($lead->contacted_at),
            'has_contact' => filled($lead->email) || filled($lead->phone),
            'created_at' => $this->iso($lead->created_at),
        ];
    }

    protected function leadType(Lead $lead): string
    {
        if ($lead->property_id) {
            return 'property_inquiry';
        }
        if ($lead->vehicle_id) {
            return 'vehicle_inquiry';
        }

        return 'general';
    }

    protected function paymentState(Booking $booking): string
    {
        $payments = $booking->relationLoaded('payments') ? $booking->payments : $booking->payments()->get();

        if ($payments->contains(fn (Payment $payment) => $payment->status === 'paid')) {
            return 'paid';
        }
        if ($payments->contains(fn (Payment $payment) => $payment->status === 'refunded')) {
            return 'refunded';
        }
        if ($payments->contains(fn (Payment $payment) => $payment->status === 'failed')) {
            return 'failed';
        }
        if ($booking->status === 'pending_payment' || $payments->contains(fn (Payment $payment) => $payment->status === 'pending')) {
            return 'pending';
        }
        if ($booking->status === 'pending') {
            return 'pay_at_pickup';
        }

        return 'unpaid';
    }

    protected function countsBy(Builder $query, string $column): array
    {
        return (clone $query)
            ->selectRaw($column.' as bucket, COUNT(*) as aggregate')
            ->groupBy($column)
            ->pluck('aggregate', 'bucket')
            ->map(fn ($count) => (int) $count)
            ->all();
    }

    protected function period(array $args): array
    {
        $to = ! empty($args['to']) ? Carbon::parse($args['to']) : now();
        $from = ! empty($args['from']) ? Carbon::parse($args['from']) : $to->copy()->startOfMonth();
        $from = $from->copy()->startOfDay();
        $to = $to->copy()->endOfDay();

        if ($to->lt($from)) {
            throw new ToolInputException('The to date must be on or after the from date.');
        }

        return [$from, $to];
    }

    protected function periodPayload(Carbon $from, Carbon $to): array
    {
        return [
            'from' => $from->toDateString(),
            'to' => $to->toDateString(),
            'inclusive' => true,
        ];
    }

    protected function slice(Builder $query, array $args): array
    {
        $page = max(1, (int) ($args['page'] ?? 1));
        $perPage = min(50, max(1, (int) ($args['per_page'] ?? 25)));
        $total = (clone $query)->count();
        $items = $query->forPage($page, $perPage)->get();

        return [$items, [
            'page' => $page,
            'per_page' => $perPage,
            'total' => $total,
            'has_more' => ($page * $perPage) < $total,
        ]];
    }

    protected function rentalDays(mixed $start, mixed $end): ?int
    {
        if (! $start || ! $end) {
            return null;
        }

        $from = Carbon::parse($start)->startOfDay();
        $to = Carbon::parse($end)->startOfDay();

        if ($to->lt($from)) {
            return null;
        }

        return (int) $from->diffInDays($to) + 1;
    }

    protected function englishTranslation($translations): mixed
    {
        return $translations?->firstWhere('locale', 'en') ?? $translations?->first();
    }

    protected function like(string $value): string
    {
        return str_replace(['%', '_'], '', $value);
    }

    protected function dateOnly(mixed $value): ?string
    {
        if ($value === null || $value === '') {
            return null;
        }

        return Carbon::parse($value)->toDateString();
    }

    protected function iso(mixed $value): ?string
    {
        if ($value === null || $value === '') {
            return null;
        }

        return Carbon::parse($value)->utc()->format('Y-m-d\TH:i:s\Z');
    }
}
