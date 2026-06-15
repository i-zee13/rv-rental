<?php

namespace App\Http\Controllers;

use App\Services\BookingAssistantService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class BookingChatController extends Controller
{
    public function __construct(
        protected BookingAssistantService $assistant
    ) {}

    public function start(): JsonResponse
    {
        if (!config('ai.enabled')) {
            return response()->json(['error' => 'Chat unavailable'], 503);
        }

        return response()->json($this->assistant->start());
    }

    public function message(Request $request): JsonResponse
    {
        if (!config('ai.enabled')) {
            return response()->json(['error' => 'Chat unavailable'], 503);
        }

        $data = $request->validate([
            'message' => 'required|string|max:2000',
        ]);

        return response()->json($this->assistant->handleMessage($data['message']));
    }

    public function action(Request $request): JsonResponse
    {
        if (!config('ai.enabled')) {
            return response()->json(['error' => 'Chat unavailable'], 503);
        }

        $data = $request->validate([
            'action' => 'required|string|max:50',
            'payload' => 'nullable|array',
        ]);

        return response()->json(
            $this->assistant->handleAction($data['action'], $data['payload'] ?? [])
        );
    }

    public function reset(): JsonResponse
    {
        if (!config('ai.enabled')) {
            return response()->json(['error' => 'Chat unavailable'], 503);
        }

        return response()->json($this->assistant->reset());
    }
}
