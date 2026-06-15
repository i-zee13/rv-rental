<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class OpenAiClient
{
    public function isConfigured(): bool
    {
        return (bool) config('ai.openai.api_key');
    }

    /**
     * @param  array<int, array{role: string, content: string}>  $messages
     */
    public function chatJson(array $messages): ?array
    {
        $apiKey = config('ai.openai.api_key');
        if (!$apiKey) {
            return null;
        }

        try {
            $response = Http::withToken($apiKey)
                ->timeout((int) config('ai.openai.timeout', 30))
                ->post('https://api.openai.com/v1/chat/completions', [
                    'model' => config('ai.openai.model', 'gpt-4o-mini'),
                    'temperature' => 0.3,
                    'response_format' => ['type' => 'json_object'],
                    'messages' => $messages,
                ]);

            if (!$response->successful()) {
                Log::warning('OpenAI request failed', ['body' => $response->body()]);

                return null;
            }

            $content = $response->json('choices.0.message.content');
            if (!is_string($content)) {
                return null;
            }

            $decoded = json_decode($content, true);

            return is_array($decoded) ? $decoded : null;
        } catch (\Throwable $e) {
            Log::warning('OpenAI exception', ['message' => $e->getMessage()]);

            return null;
        }
    }
}
