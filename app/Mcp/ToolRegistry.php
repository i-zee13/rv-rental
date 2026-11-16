<?php

namespace App\Mcp;

use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class ToolRegistry
{
    public function __construct(protected OperationalReadModel $readModel) {}

    public function list(): array
    {
        return array_map(fn (array $tool) => [
            'name' => $tool['name'],
            'description' => $tool['description'],
            'inputSchema' => $tool['inputSchema'],
            'annotations' => [
                'title' => $tool['title'],
                'readOnlyHint' => true,
                'destructiveHint' => false,
                'idempotentHint' => true,
                'openWorldHint' => false,
            ],
        ], $this->definitions());
    }

    public function names(): array
    {
        return array_column($this->definitions(), 'name');
    }

    public function call(string $name, array $arguments): array
    {
        $tool = collect($this->definitions())->firstWhere('name', $name);

        if (! $tool) {
            throw new UnknownMcpToolException('Unknown tool: '.$name);
        }

        $validator = Validator::make($arguments, $tool['rules']);

        if ($validator->fails()) {
            throw new ToolInputException($validator->errors()->first() ?: 'Invalid tool arguments.');
        }

        try {
            $validated = $validator->validated();
        } catch (ValidationException $exception) {
            throw new ToolInputException($exception->getMessage());
        }

        return $this->readModel->{$tool['handler']}($validated);
    }

    protected function definitions(): array
    {
        $page = [
            'page' => ['type' => 'integer', 'minimum' => 1],
            'per_page' => ['type' => 'integer', 'minimum' => 1, 'maximum' => 50],
        ];
        $pageRules = ['page' => 'sometimes|integer|min:1', 'per_page' => 'sometimes|integer|min:1|max:50'];
        $period = [
            'from' => ['type' => 'string', 'description' => 'Inclusive start date YYYY-MM-DD. Defaults to the first day of the to-date month.'],
            'to' => ['type' => 'string', 'description' => 'Inclusive end date YYYY-MM-DD. Defaults to today in the application timezone.'],
        ];
        $periodRules = ['from' => 'sometimes|date_format:Y-m-d', 'to' => 'sometimes|date_format:Y-m-d'];

        return [
            $this->tool('mv_get_business_overview', 'Business overview', 'Counts and collected revenue for a reporting period. Vehicle availability in this tool is status-only.', 'businessOverview', $period, $periodRules),
            $this->tool('mv_list_vehicles', 'List vehicles', 'List vehicles. Status is not date availability.', 'listVehicles', $page + [
                'category' => ['type' => 'string'],
                'status' => ['type' => 'string', 'enum' => ['available', 'unavailable', 'maintenance']],
                'min_price' => ['type' => 'number'],
                'max_price' => ['type' => 'number'],
                'search' => ['type' => 'string'],
            ], $pageRules + [
                'category' => 'sometimes|string|max:100',
                'status' => 'sometimes|in:available,unavailable,maintenance',
                'min_price' => 'sometimes|numeric|min:0',
                'max_price' => 'sometimes|numeric|min:0',
                'search' => 'sometimes|string|max:100',
            ]),
            $this->tool('mv_get_vehicle', 'Get vehicle', 'Vehicle listing detail. Does not include VIN.', 'getVehicle', [
                'id' => ['type' => 'integer'],
                'slug' => ['type' => 'string'],
            ], ['id' => 'nullable|integer|required_without:slug', 'slug' => 'nullable|string|required_without:id']),
            $this->tool('mv_check_vehicle_availability', 'Check vehicle availability', 'Uses VehicleAvailabilityService. When overlap checks are disabled, available only means status is available.', 'checkVehicleAvailability', [
                'vehicle_id' => ['type' => 'integer'],
                'start' => ['type' => 'string'],
                'end' => ['type' => 'string'],
            ], [
                'vehicle_id' => 'required|integer',
                'start' => 'required|date_format:Y-m-d',
                'end' => 'required|date_format:Y-m-d|after_or_equal:start',
            ], ['vehicle_id', 'start', 'end']),
            $this->tool('mv_list_bookings', 'List bookings', 'Booking list without customer contact. Use payment_state for unpaid/pay-at-pickup/pending Stripe. Use rental_from/rental_to with status confirmed for active/overlapping rentals.', 'listBookings', $page + [
                'status' => ['type' => 'string'],
                'payment_state' => ['type' => 'string', 'enum' => ['paid', 'pending', 'pay_at_pickup', 'unpaid']],
                'vehicle_id' => ['type' => 'integer'],
                'email' => ['type' => 'string'],
                'created_from' => ['type' => 'string'],
                'created_to' => ['type' => 'string'],
                'rental_from' => ['type' => 'string'],
                'rental_to' => ['type' => 'string'],
            ], $pageRules + [
                'status' => 'sometimes|string|max:40',
                'payment_state' => 'sometimes|in:paid,pending,pay_at_pickup,unpaid',
                'vehicle_id' => 'sometimes|integer',
                'email' => 'sometimes|email|max:200',
                'created_from' => 'sometimes|date_format:Y-m-d',
                'created_to' => 'sometimes|date_format:Y-m-d',
                'rental_from' => 'sometimes|date_format:Y-m-d',
                'rental_to' => 'sometimes|date_format:Y-m-d',
            ]),
            $this->tool('mv_get_booking', 'Get booking', 'Booking detail including customer contact and payment status. Omits Stripe identifiers.', 'getBooking', [
                'id' => ['type' => 'integer'],
                'reference' => ['type' => 'string'],
            ], ['id' => 'nullable|integer|required_without:reference', 'reference' => 'nullable|string|required_without:id']),
            $this->tool('mv_list_properties', 'List properties', 'Housing listings. Reservations are not supported. Price filters use price_per_month.', 'listProperties', $page + [
                'type' => ['type' => 'string'],
                'status' => ['type' => 'string'],
                'bedrooms_min' => ['type' => 'integer'],
                'bathrooms_min' => ['type' => 'number'],
                'pets_allowed' => ['type' => 'boolean'],
                'furnished' => ['type' => 'boolean'],
                'min_nights_max' => ['type' => 'integer'],
                'min_price' => ['type' => 'number'],
                'max_price' => ['type' => 'number'],
                'search' => ['type' => 'string'],
            ], $pageRules + [
                'type' => 'sometimes|string|max:100',
                'status' => 'sometimes|string|max:40',
                'bedrooms_min' => 'sometimes|integer|min:0',
                'bathrooms_min' => 'sometimes|numeric|min:0',
                'pets_allowed' => 'sometimes|boolean',
                'furnished' => 'sometimes|boolean',
                'min_nights_max' => 'sometimes|integer|min:1',
                'min_price' => 'sometimes|numeric|min:0',
                'max_price' => 'sometimes|numeric|min:0',
                'search' => 'sometimes|string|max:100',
            ]),
            $this->tool('mv_get_property', 'Get property', 'Property listing detail, including address. Not a reservation.', 'getProperty', [
                'id' => ['type' => 'integer'],
                'slug' => ['type' => 'string'],
            ], ['id' => 'nullable|integer|required_without:slug', 'slug' => 'nullable|string|required_without:id']),
            $this->tool('mv_list_leads', 'List leads', 'Lead list without contact details. Use lead_type=property_inquiry for housing inquiries. Use status=new and contacted=false for uncontacted leads.', 'listLeads', $page + [
                'status' => ['type' => 'string'],
                'source' => ['type' => 'string'],
                'lead_type' => ['type' => 'string', 'enum' => ['property_inquiry', 'vehicle_inquiry', 'general']],
                'vehicle_id' => ['type' => 'integer'],
                'property_id' => ['type' => 'integer'],
                'contacted' => ['type' => 'boolean'],
                'created_from' => ['type' => 'string'],
                'created_to' => ['type' => 'string'],
            ], $pageRules + [
                'status' => 'sometimes|string|max:40',
                'source' => 'sometimes|string|max:80',
                'lead_type' => 'sometimes|in:property_inquiry,vehicle_inquiry,general',
                'vehicle_id' => 'sometimes|integer',
                'property_id' => 'sometimes|integer',
                'contacted' => 'sometimes|boolean',
                'created_from' => 'sometimes|date_format:Y-m-d',
                'created_to' => 'sometimes|date_format:Y-m-d',
            ]),
            $this->tool('mv_get_lead', 'Get lead', 'Lead detail including customer contact. Omits IP address and user agent.', 'getLead', [
                'id' => ['type' => 'integer'],
                'reference' => ['type' => 'string'],
            ], ['id' => 'nullable|integer|required_without:reference', 'reference' => 'nullable|string|required_without:id']),
            $this->tool('mv_get_revenue_summary', 'Revenue summary', 'Collected revenue from payments with status paid, separate from booking totals.', 'revenueSummary', $period, $periodRules),
        ];
    }

    protected function tool(string $name, string $title, string $description, string $handler, array $properties, array $rules, array $required = []): array
    {
        return [
            'name' => $name,
            'title' => $title,
            'description' => $description,
            'handler' => $handler,
            'rules' => $rules,
            'inputSchema' => [
                'type' => 'object',
                'properties' => $properties === [] ? new \stdClass() : $properties,
                'required' => $required,
                'additionalProperties' => false,
            ],
        ];
    }
}
