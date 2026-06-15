<?php

namespace App\Services;

use App\Models\Addon;
use App\Models\AiLog;
use App\Models\Booking;
use App\Models\Lead;
use App\Models\Property;
use App\Models\Vehicle;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class BookingAssistantService
{
    public const SESSION_KEY = 'booking_chat';

    public function __construct(
        protected OpenAiClient $openAi,
        protected VehicleAvailabilityService $availability,
        protected BookingCreatorService $bookingCreator,
        protected LeadNotificationService $leadNotifier,
    ) {}

    public function start(): array
    {
        $state = $this->freshState();
        $state['step'] = 'choose_type';
        $this->saveState($state);

        return $this->respond($state, [
            $this->assistantMessage(__('chat.welcome')),
        ], $this->typeOptions());
    }

    public function reset(): array
    {
        session()->forget(self::SESSION_KEY);

        return $this->start();
    }

    public function handleMessage(string $message): array
    {
        $state = $this->getState();
        $message = trim($message);

        if ($message === '') {
            return $this->respond($state, [$this->assistantMessage(__('chat.empty_message'))]);
        }

        $state['history'][] = ['role' => 'user', 'text' => $message];

        if ($this->matchesAny($message, ['reset', 'start over', 'empezar de nuevo', 'reiniciar'])) {
            return $this->reset();
        }

        if ($state['step'] === 'done') {
            $ref = $state['data']['booking_reference'] ?? $state['data']['lead_reference'] ?? '';

            return $this->respond($state, [$this->assistantMessage(__('chat.already_booked', ['ref' => $ref]))]);
        }

        if ($state['step'] === 'confirm') {
            if ($this->matchesAny($message, ['yes', 'confirm', 'ok', 'book', 'si', 'sí', 'confirmar'])) {
                return $this->finalizeBooking($state);
            }
            if ($this->matchesAny($message, ['no', 'change', 'edit', 'cambiar'])) {
                if (($state['data']['booking_type'] ?? '') === 'property') {
                    $state['step'] = 'property';
                    $this->saveState($state);

                    return $this->respond($state, [
                        $this->assistantMessage(__('chat.restart_pick_property')),
                    ], $this->propertyOptions());
                }

                $state['step'] = 'vehicle';
                $this->saveState($state);

                return $this->respond($state, [
                    $this->assistantMessage(__('chat.restart_pick_vehicle')),
                ], $this->vehicleOptions());
            }
        }

        $aiParsed = $this->tryAiExtraction($state, $message);
        if ($aiParsed) {
            $state = $this->mergeExtractedData($state, $aiParsed['extracted'] ?? []);
            if (!empty($aiParsed['reply'])) {
                $state = $this->advanceSteps($state);

                return $this->respondAfterAdvance($state, [$this->assistantMessage($aiParsed['reply'])]);
            }
        }

        return $this->processStep($state, $message);
    }

    public function handleAction(string $action, array $payload = []): array
    {
        $state = $this->getState();

        return match ($action) {
            'choose_vehicle' => $this->beginVehicleFlow($state),
            'choose_property' => $this->beginPropertyFlow($state),
            'select_vehicle' => $this->selectVehicle($state, (int) ($payload['vehicle_id'] ?? 0)),
            'select_property' => $this->selectProperty($state, (int) ($payload['property_id'] ?? 0)),
            'toggle_addon' => $this->toggleAddon($state, (int) ($payload['addon_id'] ?? 0)),
            'skip_addons' => $this->skipAddons($state),
            'skip_phone' => $this->skipPhone($state),
            'confirm' => $this->finalizeBooking($state),
            'cancel_confirm' => $this->cancelConfirm($state),
            default => $this->respond($state, [$this->assistantMessage(__('chat.unknown_action'))]),
        };
    }

    protected function processStep(array $state, string $message): array
    {
        return match ($state['step']) {
            'choose_type' => $this->stepChooseType($state, $message),
            'property' => $this->stepProperty($state, $message),
            'vehicle' => $this->stepVehicle($state, $message),
            'start_date' => $this->stepStartDate($state, $message),
            'end_date' => $this->stepEndDate($state, $message),
            'pickup_location' => $this->stepPickupLocation($state, $message),
            'dropoff_location' => $this->stepDropoffLocation($state, $message),
            'addons' => $this->stepAddons($state, $message),
            'first_name' => $this->stepFirstName($state, $message),
            'last_name' => $this->stepLastName($state, $message),
            'email' => $this->stepEmail($state, $message),
            'phone' => $this->stepPhone($state, $message),
            'confirm' => $this->stepConfirm($state, $message),
            default => $this->start(),
        };
    }

    protected function stepChooseType(array $state, string $message): array
    {
        if ($this->matchesPropertyIntent($message)) {
            return $this->beginPropertyFlow($state);
        }

        if ($vehicleId = $this->parseVehicleId($message)) {
            $state['data']['booking_type'] = 'vehicle';
            $this->saveState($state);

            return $this->selectVehicle($state, $vehicleId);
        }

        if ($this->matchesVehicleIntent($message)) {
            return $this->beginVehicleFlow($state);
        }

        return $this->respond($state, [
            $this->assistantMessage(__('chat.choose_type_prompt')),
        ], $this->typeOptions());
    }

    protected function beginVehicleFlow(array $state): array
    {
        $state['data']['booking_type'] = 'vehicle';
        $state['step'] = 'vehicle';
        $this->saveState($state);

        return $this->respond($state, [
            $this->assistantMessage(__('chat.pick_vehicle')),
        ], $this->vehicleOptions());
    }

    protected function beginPropertyFlow(array $state): array
    {
        $state['data']['booking_type'] = 'property';
        $state['step'] = 'property';
        $this->saveState($state);

        return $this->respond($state, [
            $this->assistantMessage(__('chat.pick_property')),
        ], $this->propertyOptions());
    }

    protected function stepProperty(array $state, string $message): array
    {
        $propertyId = $this->parsePropertyId($message);
        if (!$propertyId) {
            return $this->respond($state, [
                $this->assistantMessage(__('chat.property_not_found')),
            ], $this->propertyOptions());
        }

        return $this->selectProperty($state, $propertyId);
    }

    protected function selectProperty(array $state, int $propertyId): array
    {
        $property = $this->availableProperties()->firstWhere('id', $propertyId);
        if (!$property) {
            return $this->respond($state, [
                $this->assistantMessage(__('chat.property_not_found')),
            ], $this->propertyOptions());
        }

        $state['data']['booking_type'] = 'property';
        $state['data']['property_id'] = $property->id;
        $state['step'] = 'first_name';
        $this->saveState($state);

        return $this->respond($state, [
            $this->assistantMessage(__('chat.property_selected', [
                'name' => $this->propertyLabel($property),
                'price' => $property->displayPrice(),
            ])),
            $this->assistantMessage(__('chat.ask_first_name')),
        ]);
    }

    protected function stepVehicle(array $state, string $message): array
    {
        $vehicleId = $this->parseVehicleId($message);
        if (!$vehicleId) {
            return $this->respond($state, [
                $this->assistantMessage(__('chat.vehicle_not_found')),
            ], $this->vehicleOptions());
        }

        return $this->selectVehicle($state, $vehicleId);
    }

    protected function selectVehicle(array $state, int $vehicleId): array
    {
        $vehicle = $this->availableVehicles()->firstWhere('id', $vehicleId);
        if (!$vehicle) {
            return $this->respond($state, [
                $this->assistantMessage(__('chat.vehicle_not_found')),
            ], $this->vehicleOptions());
        }

        $state['data']['vehicle_id'] = $vehicle->id;
        $state['data']['booking_type'] = 'vehicle';
        $state['step'] = 'start_date';
        $this->saveState($state);

        return $this->respond($state, [
            $this->assistantMessage(__('chat.vehicle_selected', [
                'name' => $this->vehicleLabel($vehicle),
                'price' => number_format((float) $vehicle->price_per_day, 2),
            ])),
            $this->assistantMessage(__('chat.ask_start_date')),
        ]);
    }

    protected function stepStartDate(array $state, string $message): array
    {
        $date = $this->parseDate($message);
        if (!$date || $date->lt(Carbon::today())) {
            return $this->respond($state, [$this->assistantMessage(__('chat.invalid_start_date'))]);
        }

        $state['data']['start_date'] = $date->toDateString();
        $state['step'] = 'end_date';
        $this->saveState($state);

        return $this->respond($state, [
            $this->assistantMessage(__('chat.ask_end_date')),
        ]);
    }

    protected function stepEndDate(array $state, string $message): array
    {
        $date = $this->parseDate($message);
        $start = Carbon::parse($state['data']['start_date']);

        if (!$date || $date->lt($start)) {
            return $this->respond($state, [$this->assistantMessage(__('chat.invalid_end_date'))]);
        }

        $state['data']['end_date'] = $date->toDateString();

        if ($failed = $this->validateDatesOrFail($state)) {
            return $failed;
        }

        $state['step'] = 'pickup_location';
        $this->saveState($state);

        $messages = [];
        if ($this->availability->isEnabled()) {
            $messages[] = $this->assistantMessage(__('chat.dates_available'));
        }
        $messages[] = $this->assistantMessage(__('chat.ask_pickup_location'));

        return $this->respond($state, $messages);
    }

    protected function stepPickupLocation(array $state, string $message): array
    {
        $state['data']['pickup_location'] = $message;
        $state['step'] = 'dropoff_location';
        $this->saveState($state);

        return $this->respond($state, [
            $this->assistantMessage(__('chat.ask_dropoff_location')),
        ], [
            ['id' => 'same_as_pickup', 'label' => __('chat.same_as_pickup'), 'action' => 'message', 'value' => __('chat.same_as_pickup')],
        ]);
    }

    protected function stepDropoffLocation(array $state, string $message): array
    {
        if ($this->matchesAny($message, ['same', 'same as pickup', 'igual', 'mismo lugar'])) {
            $message = $state['data']['pickup_location'];
        }

        $state['data']['dropoff_location'] = $message;
        $state['step'] = 'addons';
        $this->saveState($state);

        return $this->respond($state, [
            $this->assistantMessage(__('chat.ask_addons')),
        ], $this->addonOptions($state), [
            ['id' => 'skip_addons', 'label' => __('chat.skip_addons'), 'action' => 'skip_addons'],
        ]);
    }

    protected function stepAddons(array $state, string $message): array
    {
        if ($this->matchesAny($message, ['skip', 'none', 'no', 'ninguno', 'saltar'])) {
            return $this->skipAddons($state);
        }

        $addonId = $this->parseAddonId($message);
        if ($addonId) {
            return $this->toggleAddon($state, $addonId);
        }

        return $this->respond($state, [
            $this->assistantMessage(__('chat.addon_help')),
        ], $this->addonOptions($state), [
            ['id' => 'skip_addons', 'label' => __('chat.skip_addons'), 'action' => 'skip_addons'],
            ['id' => 'continue_addons', 'label' => __('chat.continue'), 'action' => 'skip_addons'],
        ]);
    }

    protected function skipAddons(array $state): array
    {
        $state['data']['_addons_done'] = true;
        $state['step'] = 'first_name';
        $this->saveState($state);

        return $this->respond($state, [
            $this->assistantMessage(__('chat.ask_first_name')),
        ]);
    }

    protected function toggleAddon(array $state, int $addonId): array
    {
        $addon = Addon::where('is_active', true)->find($addonId);
        if (!$addon) {
            return $this->respond($state, [
                $this->assistantMessage(__('chat.addon_not_found')),
            ], $this->addonOptions($state));
        }

        $ids = $state['data']['addon_ids'] ?? [];
        if (in_array($addonId, $ids, true)) {
            $ids = array_values(array_diff($ids, [$addonId]));
        } else {
            $ids[] = $addonId;
        }

        $state['data']['addon_ids'] = $ids;
        $this->saveState($state);

        $selected = empty($ids)
            ? __('chat.no_addons_selected')
            : __('chat.addons_selected', ['count' => count($ids)]);

        return $this->respond($state, [
            $this->assistantMessage($selected),
        ], $this->addonOptions($state), [
            ['id' => 'skip_addons', 'label' => __('chat.continue'), 'action' => 'skip_addons'],
        ]);
    }

    protected function stepFirstName(array $state, string $message): array
    {
        $state['data']['first_name'] = $message;
        $state['step'] = 'last_name';
        $this->saveState($state);

        return $this->respond($state, [$this->assistantMessage(__('chat.ask_last_name'))]);
    }

    protected function stepLastName(array $state, string $message): array
    {
        $state['data']['last_name'] = $message;
        $state['step'] = 'email';
        $this->saveState($state);

        return $this->respond($state, [$this->assistantMessage(__('chat.ask_email'))]);
    }

    protected function stepEmail(array $state, string $message): array
    {
        if (!filter_var($message, FILTER_VALIDATE_EMAIL)) {
            return $this->respond($state, [$this->assistantMessage(__('chat.invalid_email'))]);
        }

        $state['data']['email'] = $message;
        $state['step'] = 'phone';
        $this->saveState($state);

        return $this->respond($state, [
            $this->assistantMessage(__('chat.ask_phone')),
        ], [], [
            ['id' => 'skip_phone', 'label' => __('chat.skip_phone'), 'action' => 'skip_phone'],
        ]);
    }

    protected function stepPhone(array $state, string $message): array
    {
        $state['data']['phone'] = $message;
        $state['data']['_addons_done'] = $state['data']['_addons_done'] ?? true;
        $state['step'] = 'confirm';
        $this->saveState($state);

        return $this->showConfirmation($state);
    }

    protected function skipPhone(array $state): array
    {
        $state['data']['phone'] = null;
        $state['data']['_addons_done'] = $state['data']['_addons_done'] ?? true;
        $state['step'] = 'confirm';
        $this->saveState($state);

        return $this->showConfirmation($state);
    }

    protected function stepConfirm(array $state, string $message): array
    {
        if ($this->matchesAny($message, ['yes', 'confirm', 'ok', 'book', 'si', 'sí', 'confirmar'])) {
            return $this->finalizeBooking($state);
        }

        return $this->showConfirmation($state);
    }

    protected function cancelConfirm(array $state): array
    {
        if (($state['data']['booking_type'] ?? '') === 'property') {
            $state['step'] = 'property';
            $this->saveState($state);

            return $this->respond($state, [
                $this->assistantMessage(__('chat.restart_pick_property')),
            ], $this->propertyOptions());
        }

        $state['step'] = 'vehicle';
        $this->saveState($state);

        return $this->respond($state, [
            $this->assistantMessage(__('chat.restart_pick_vehicle')),
        ], $this->vehicleOptions());
    }

    protected function showConfirmation(array $state): array
    {
        if (($state['data']['booking_type'] ?? '') === 'property') {
            return $this->showPropertyConfirmation($state);
        }

        if ($failed = $this->validateDatesOrFail($state)) {
            return $failed;
        }

        $quote = $this->buildQuote($state['data']);

        $summary = __('chat.confirm_summary', [
            'vehicle' => $quote['vehicle_title'],
            'start' => $quote['start']->format('M j, Y'),
            'end' => $quote['end']->format('M j, Y'),
            'days' => $quote['days'],
            'pickup' => $state['data']['pickup_location'],
            'dropoff' => $state['data']['dropoff_location'],
            'name' => trim($state['data']['first_name'] . ' ' . $state['data']['last_name']),
            'email' => $state['data']['email'],
            'phone' => $state['data']['phone'] ?? '—',
            'total' => number_format($quote['total'], 2),
            'currency' => env('CURRENCY', 'USD'),
        ]);

        return $this->respond($state, [
            $this->assistantMessage($summary),
            $this->assistantMessage(__('chat.confirm_prompt')),
        ], [], [
            ['id' => 'confirm', 'label' => __('chat.confirm_booking'), 'action' => 'confirm'],
            ['id' => 'cancel_confirm', 'label' => __('chat.change_details'), 'action' => 'cancel_confirm'],
        ]);
    }

    protected function showPropertyConfirmation(array $state): array
    {
        $property = Property::with('translations')->find($state['data']['property_id'] ?? 0);
        $name = $property ? $this->propertyLabel($property) : __('chat.property_fallback');

        $summary = __('chat.property_confirm_summary', [
            'property' => $name,
            'price' => $property?->displayPrice() ?? '—',
            'name' => trim(($state['data']['first_name'] ?? '') . ' ' . ($state['data']['last_name'] ?? '')),
            'email' => $state['data']['email'] ?? '',
            'phone' => $state['data']['phone'] ?? '—',
        ]);

        return $this->respond($state, [
            $this->assistantMessage($summary),
            $this->assistantMessage(__('chat.confirm_prompt')),
        ], [], [
            ['id' => 'confirm', 'label' => __('chat.confirm_inquiry'), 'action' => 'confirm'],
            ['id' => 'cancel_confirm', 'label' => __('chat.change_details'), 'action' => 'cancel_confirm'],
        ]);
    }

    protected function finalizeBooking(array $state): array
    {
        if (($state['data']['booking_type'] ?? 'vehicle') === 'property') {
            return $this->finalizePropertyInquiry($state);
        }

        try {
            $data = $state['data'];
            $required = ['vehicle_id', 'start_date', 'end_date', 'pickup_location', 'dropoff_location', 'first_name', 'last_name', 'email'];
            foreach ($required as $field) {
                if (empty($data[$field])) {
                    return $this->respond($state, [$this->assistantMessage(__('chat.missing_fields'))]);
                }
            }

            if ($failed = $this->validateDatesOrFail($state)) {
                return $failed;
            }

            $booking = $this->bookingCreator->create($data, 'Booked via AI chat assistant');

            $state['step'] = 'done';
            $state['data']['booking_reference'] = $booking->reference;
            $state['data']['booking_id'] = $booking->id;
            $this->saveState($state);

            try {
                AiLog::create([
                    'action' => 'booking_chat_complete',
                    'prompt' => Str::limit(json_encode($state['history'] ?? []), 5000),
                    'response' => $booking->reference,
                    'meta' => ['booking_id' => $booking->id],
                ]);
            } catch (\Throwable $logError) {
                Log::warning('AI booking log failed', ['error' => $logError->getMessage()]);
            }

            return $this->respond($state, [
                $this->assistantMessage(__('chat.booking_success', [
                    'ref' => $booking->reference,
                    'total' => number_format((float) $booking->total, 2),
                    'currency' => $booking->currency ?? env('CURRENCY', 'USD'),
                ])),
            ], [], [], true, $booking->reference);
        } catch (\Throwable $e) {
            Log::error('AI booking chat failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'data' => $state['data'] ?? [],
            ]);

            $message = config('app.debug')
                ? __('chat.booking_failed_debug', ['error' => $e->getMessage()])
                : __('chat.booking_failed');

            return $this->respond($state, [
                $this->assistantMessage($message),
            ]);
        }
    }

    protected function finalizePropertyInquiry(array $state): array
    {
        try {
            $data = $state['data'];
            $required = ['property_id', 'first_name', 'last_name', 'email'];
            foreach ($required as $field) {
                if (empty($data[$field])) {
                    return $this->respond($state, [$this->assistantMessage(__('chat.missing_fields'))]);
                }
            }

            $property = Property::with('translations')->find($data['property_id']);
            if (!$property) {
                return $this->respond($state, [
                    $this->assistantMessage(__('chat.property_not_found')),
                ], $this->propertyOptions());
            }

            $lead = Lead::create([
                'reference' => Lead::generateReference(),
                'status' => 'new',
                'source' => 'ai_chat',
                'property_id' => $property->id,
                'property_name' => $this->propertyLabel($property),
                'first_name' => $data['first_name'],
                'last_name' => $data['last_name'],
                'email' => $data['email'],
                'phone' => $data['phone'] ?? null,
                'message' => 'Property rental inquiry submitted via AI chat assistant.',
                'locale' => app()->getLocale(),
            ]);

            $this->leadNotifier->sendEmails($lead);

            $state['step'] = 'done';
            $state['data']['lead_reference'] = $lead->reference;
            $state['data']['lead_id'] = $lead->id;
            $this->saveState($state);

            return $this->respond($state, [
                $this->assistantMessage(__('chat.property_inquiry_success', [
                    'ref' => $lead->reference,
                    'property' => $this->propertyLabel($property),
                ])),
            ], [], [], true, $lead->reference);
        } catch (\Throwable $e) {
            Log::error('AI property inquiry failed', [
                'error' => $e->getMessage(),
                'data' => $state['data'] ?? [],
            ]);

            return $this->respond($state, [
                $this->assistantMessage(__('chat.booking_failed')),
            ]);
        }
    }

    protected function validateDatesOrFail(array &$state): ?array
    {
        $vehicleId = (int) ($state['data']['vehicle_id'] ?? 0);
        $start = $state['data']['start_date'] ?? null;
        $end = $state['data']['end_date'] ?? null;

        if (!$vehicleId || !$start || !$end) {
            return null;
        }

        $validationKey = $vehicleId . '|' . $start . '|' . $end;
        if (($state['data']['_dates_validated'] ?? '') === $validationKey) {
            return null;
        }

        if ($this->availability->isVehicleBookable($vehicleId, $start, $end)) {
            $state['data']['_dates_validated'] = $validationKey;
            $this->saveState($state);

            return null;
        }

        $vehicle = Vehicle::find($vehicleId);
        $label = $vehicle ? $this->vehicleLabel($vehicle) : __('chat.vehicle_fallback');

        $state['data']['start_date'] = null;
        $state['data']['end_date'] = null;
        $state['data']['_dates_validated'] = null;
        $state['step'] = 'start_date';
        $this->saveState($state);

        return $this->respond($state, [
            $this->assistantMessage(__('chat.dates_unavailable_early', [
                'vehicle' => $label,
                'start' => Carbon::parse($start)->format('M j, Y'),
                'end' => Carbon::parse($end)->format('M j, Y'),
            ])),
            $this->assistantMessage(__('chat.ask_start_date')),
        ], $this->vehicleOptions());
    }

    protected function tryAiExtraction(array $state, string $message): ?array
    {
        if (!$this->openAi->isConfigured()) {
            return null;
        }

        $fleet = $this->availableVehicles()->map(fn (Vehicle $v) => [
            'id' => $v->id,
            'name' => $this->vehicleLabel($v),
            'price_per_day' => (float) $v->price_per_day,
        ])->values()->all();

        $addons = Addon::where('is_active', true)->with('translations')->get()->map(fn (Addon $a) => [
            'id' => $a->id,
            'name' => $a->name,
            'price' => (float) $a->price,
        ])->values()->all();

        $locale = app()->getLocale();
        $bookingType = $state['data']['booking_type'] ?? 'unknown';
        $properties = $this->availableProperties()->map(fn (Property $p) => [
            'id' => $p->id,
            'name' => $this->propertyLabel($p),
            'price' => $p->displayPrice(),
        ])->values()->all();

        $system = <<<PROMPT
You are a friendly booking assistant for a Miami vehicle and property rental website.
Current step: {$state['step']}
Booking type: {$bookingType}
Collected data JSON: {$this->json($state['data'])}
Available vehicles: {$this->json($fleet)}
Available properties: {$this->json($properties)}
Available add-ons: {$this->json($addons)}
Locale: {$locale}

If the user asks about homes, apartments, houses, or condos, guide them toward property_id and booking_type "property".
If the user asks about cars or vehicles, use booking_type "vehicle" and vehicle_id.

Return JSON only:
{
  "reply": "short helpful reply to the user",
  "extracted": {
    "booking_type": null,
    "vehicle_id": null,
    "property_id": null,
    "start_date": "YYYY-MM-DD or null",
    "end_date": "YYYY-MM-DD or null",
    "pickup_location": null,
    "dropoff_location": null,
    "addon_ids": [],
    "first_name": null,
    "last_name": null,
    "email": null,
    "phone": null
  }
}
Only include extracted fields you are confident about from the user message.
PROMPT;

        $result = $this->openAi->chatJson([
            ['role' => 'system', 'content' => $system],
            ['role' => 'user', 'content' => $message],
        ]);

        if (!$result) {
            return null;
        }

        AiLog::create([
            'action' => 'booking_chat_ai',
            'prompt' => $message,
            'response' => json_encode($result),
            'meta' => ['step' => $state['step']],
        ]);

        return $result;
    }

    protected function mergeExtractedData(array $state, array $extracted): array
    {
        foreach ($extracted as $key => $value) {
            if ($value === null || $value === '' || $value === []) {
                continue;
            }
            if ($key === 'addon_ids' && is_array($value)) {
                $state['data']['addon_ids'] = array_values(array_unique(array_map('intval', $value)));

                continue;
            }
            if ($key === 'booking_type' && in_array($value, ['vehicle', 'property'], true)) {
                $state['data']['booking_type'] = $value;

                continue;
            }
            if ($key === 'property_id' && (int) $value > 0) {
                $state['data']['property_id'] = (int) $value;
                $state['data']['booking_type'] = 'property';

                continue;
            }
            $state['data'][$key] = $value;
        }

        $this->saveState($state);

        return $state;
    }

    protected function advanceSteps(array $state): array
    {
        $order = ['vehicle', 'start_date', 'end_date', 'pickup_location', 'dropoff_location', 'addons', 'first_name', 'last_name', 'email', 'phone', 'confirm'];
        $data = $state['data'];

        foreach ($order as $step) {
            $filled = match ($step) {
                'vehicle' => !empty($data['vehicle_id']),
                'start_date' => !empty($data['start_date']),
                'end_date' => !empty($data['end_date']),
                'pickup_location' => !empty($data['pickup_location']),
                'dropoff_location' => !empty($data['dropoff_location']),
                'addons' => !empty($data['_addons_done']) || !empty($data['first_name']),
                'first_name' => !empty($data['first_name']),
                'last_name' => !empty($data['last_name']),
                'email' => !empty($data['email']),
                'phone' => array_key_exists('phone', $data),
                'confirm' => true,
                default => false,
            };

            if (!$filled) {
                $state['step'] = $step;
                if ($step === 'addons' && !empty($data['addon_ids'])) {
                    $state['step'] = $this->nextAfterAddons($data);
                }
                break;
            }

            if ($step === 'addons') {
                $state['step'] = $this->nextAfterAddons($data);
                if ($state['step'] !== 'addons') {
                    continue;
                }
            }
        }

        if (!empty($data['first_name']) && !empty($data['last_name']) && !empty($data['email'])
            && array_key_exists('phone', $data)
            && (!empty($data['_addons_done']) || !empty($data['first_name']))
            && !empty($data['vehicle_id'])
            && !empty($data['start_date']) && !empty($data['end_date'])
            && !empty($data['pickup_location']) && !empty($data['dropoff_location'])) {
            $state['step'] = 'confirm';
        }

        $this->saveState($state);

        return $state;
    }

    protected function nextAfterAddons(array $data): string
    {
        if (empty($data['first_name'])) {
            return 'first_name';
        }
        if (empty($data['last_name'])) {
            return 'last_name';
        }
        if (empty($data['email'])) {
            return 'email';
        }
        if (!array_key_exists('phone', $data)) {
            return 'phone';
        }

        return 'confirm';
    }

    protected function respondAfterAdvance(array $state, array $messages): array
    {
        if (!empty($state['data']['start_date']) && !empty($state['data']['end_date'])) {
            if ($failed = $this->validateDatesOrFail($state)) {
                return $failed;
            }
        }

        if ($state['step'] === 'confirm') {
            return $this->showConfirmation($state);
        }

        $prompt = match ($state['step']) {
            'choose_type' => __('chat.choose_type_prompt'),
            'property' => __('chat.pick_property'),
            'vehicle' => __('chat.pick_vehicle'),
            'start_date' => __('chat.ask_start_date'),
            'end_date' => __('chat.ask_end_date'),
            'pickup_location' => __('chat.ask_pickup_location'),
            'dropoff_location' => __('chat.ask_dropoff_location'),
            'addons' => __('chat.ask_addons'),
            'first_name' => __('chat.ask_first_name'),
            'last_name' => __('chat.ask_last_name'),
            'email' => __('chat.ask_email'),
            'phone' => __('chat.ask_phone'),
            default => null,
        };

        if ($prompt) {
            $messages[] = $this->assistantMessage($prompt);
        }

        $options = match ($state['step']) {
            'choose_type' => $this->typeOptions(),
            'property' => $this->propertyOptions(),
            'vehicle' => $this->vehicleOptions(),
            'addons' => $this->addonOptions($state),
            default => [],
        };

        $actions = match ($state['step']) {
            'addons' => [
                ['id' => 'skip_addons', 'label' => __('chat.skip_addons'), 'action' => 'skip_addons'],
            ],
            'phone' => [
                ['id' => 'skip_phone', 'label' => __('chat.skip_phone'), 'action' => 'skip_phone'],
            ],
            default => [],
        };

        return $this->respond($state, $messages, $options, $actions);
    }

    protected function buildQuote(array $data): array
    {
        return $this->bookingCreator->buildQuote($data);
    }

    protected function typeOptions(): array
    {
        return [
            [
                'id' => 'type_vehicle',
                'label' => __('chat.option_vehicle'),
                'action' => 'choose_vehicle',
            ],
            [
                'id' => 'type_property',
                'label' => __('chat.option_property'),
                'action' => 'choose_property',
            ],
        ];
    }

    protected function propertyOptions(): array
    {
        return $this->availableProperties()->map(function (Property $property) {
            return [
                'id' => 'property_' . $property->id,
                'label' => $this->propertyLabel($property),
                'meta' => $property->displayPrice(),
                'action' => 'select_property',
                'property_id' => $property->id,
            ];
        })->values()->all();
    }

    protected function availableProperties()
    {
        return Property::where('status', 'available')
            ->with(['translations', 'type.translations'])
            ->orderByDesc('featured')
            ->limit(8)
            ->get();
    }

    protected function propertyLabel(Property $property): string
    {
        return $property->title();
    }

    protected function parsePropertyId(string $message): ?int
    {
        if (preg_match('/\b(\d+)\b/', $message, $m)) {
            $id = (int) $m[1];
            if ($this->availableProperties()->contains('id', $id)) {
                return $id;
            }
        }

        $needle = Str::lower($message);
        foreach ($this->availableProperties() as $property) {
            $label = Str::lower($this->propertyLabel($property));
            if (Str::contains($label, $needle) || Str::contains($needle, Str::lower($property->neighborhood ?? ''))) {
                return $property->id;
            }
        }

        return null;
    }

    protected function matchesPropertyIntent(string $message): bool
    {
        return $this->matchesAny($message, [
            'house', 'home', 'apartment', 'condo', 'townhouse', 'villa', 'property', 'rental',
            'casa', 'apartamento', 'condominio', 'alquiler', 'vivienda', 'hogar',
        ]);
    }

    protected function matchesVehicleIntent(string $message): bool
    {
        return $this->matchesAny($message, [
            'vehicle', 'car', 'rv', 'fleet', 'drive', 'vehiculo', 'vehículo', 'auto', 'coche',
        ]);
    }

    protected function vehicleOptions(): array
    {
        return $this->availableVehicles()->map(function (Vehicle $vehicle) {
            return [
                'id' => 'vehicle_' . $vehicle->id,
                'label' => $this->vehicleLabel($vehicle),
                'meta' => '$' . number_format((float) $vehicle->price_per_day, 0) . '/day',
                'action' => 'select_vehicle',
                'vehicle_id' => $vehicle->id,
            ];
        })->values()->all();
    }

    protected function addonOptions(array $state): array
    {
        $selected = $state['data']['addon_ids'] ?? [];

        return Addon::where('is_active', true)->with('translations')->orderBy('code')->get()->map(function (Addon $addon) use ($selected) {
            $picked = in_array($addon->id, $selected, true);

            return [
                'id' => 'addon_' . $addon->id,
                'label' => $addon->name . ($picked ? ' ✓' : ''),
                'meta' => '$' . number_format((float) $addon->price, 2),
                'action' => 'toggle_addon',
                'addon_id' => $addon->id,
            ];
        })->values()->all();
    }

    protected function availableVehicles()
    {
        return Vehicle::where('status', 'available')->with('translations', 'images')->orderBy('featured', 'desc')->get();
    }

    protected function vehicleLabel(Vehicle $vehicle): string
    {
        $translation = $vehicle->translations->firstWhere('locale', app()->getLocale())
            ?? $vehicle->translations->first();

        return $translation->title ?? trim($vehicle->make . ' ' . $vehicle->model . ' ' . $vehicle->year);
    }

    protected function parseVehicleId(string $message): ?int
    {
        if (preg_match('/\b(\d+)\b/', $message, $m)) {
            $id = (int) $m[1];
            if ($this->availableVehicles()->contains('id', $id)) {
                return $id;
            }
        }

        $needle = Str::lower($message);
        foreach ($this->availableVehicles() as $vehicle) {
            $label = Str::lower($this->vehicleLabel($vehicle));
            if (Str::contains($label, $needle) || Str::contains($needle, Str::lower($vehicle->make ?? ''))) {
                return $vehicle->id;
            }
        }

        return null;
    }

    protected function parseAddonId(string $message): ?int
    {
        if (preg_match('/\b(\d+)\b/', $message, $m)) {
            return (int) $m[1];
        }

        return null;
    }

    protected function parseDate(string $message): ?Carbon
    {
        try {
            return Carbon::parse($message)->startOfDay();
        } catch (\Throwable) {
            return null;
        }
    }

    protected function matchesAny(string $message, array $needles): bool
    {
        $hay = Str::lower(trim($message));
        foreach ($needles as $needle) {
            if ($hay === Str::lower($needle) || Str::contains($hay, Str::lower($needle))) {
                return true;
            }
        }

        return false;
    }

    protected function assistantMessage(string $text): array
    {
        return ['role' => 'assistant', 'text' => $text];
    }

    protected function respond(
        array $state,
        array $messages,
        array $options = [],
        array $actions = [],
        bool $completed = false,
        ?string $bookingReference = null
    ): array {
        foreach ($messages as $message) {
            if (($message['role'] ?? '') === 'assistant') {
                $state['history'][] = $message;
            }
        }
        $this->saveState($state);

        return [
            'step' => $state['step'],
            'messages' => $messages,
            'options' => $options,
            'actions' => $actions,
            'completed' => $completed || $state['step'] === 'done',
            'booking_reference' => $bookingReference ?? ($state['data']['booking_reference'] ?? null),
        ];
    }

    protected function freshState(): array
    {
        return [
            'step' => 'welcome',
            'data' => [
                'booking_type' => null,
                'vehicle_id' => null,
                'property_id' => null,
                'start_date' => null,
                'end_date' => null,
                'pickup_location' => null,
                'dropoff_location' => null,
                'addon_ids' => [],
                'first_name' => null,
                'last_name' => null,
                'email' => null,
                'phone' => null,
            ],
            'history' => [],
        ];
    }

    protected function getState(): array
    {
        return session(self::SESSION_KEY, $this->freshState());
    }

    protected function saveState(array $state): void
    {
        session([self::SESSION_KEY => $state]);
    }

    protected function json(mixed $data): string
    {
        return json_encode($data, JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR);
    }
}
