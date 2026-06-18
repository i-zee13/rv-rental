<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\AdminContentAiService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AdminAiController extends Controller
{
    public function descriptions(Request $request, AdminContentAiService $ai): JsonResponse
    {
        $data = $request->validate([
            'type' => 'required|in:vehicle,property,blog',
            'context' => 'required|array',
        ]);

        try {
            $result = $ai->generateDescriptions($data['type'], $data['context']);

            return response()->json(['ok' => true, 'data' => $result]);
        } catch (\Throwable $e) {
            return response()->json(['ok' => false, 'message' => $e->getMessage()], 422);
        }
    }

    public function seo(Request $request, AdminContentAiService $ai): JsonResponse
    {
        $data = $request->validate([
            'type' => 'required|in:vehicle,property,blog',
            'context' => 'required|array',
        ]);

        try {
            $result = $ai->generateSeo($data['type'], $data['context']);

            return response()->json(['ok' => true, 'data' => $result]);
        } catch (\Throwable $e) {
            return response()->json(['ok' => false, 'message' => $e->getMessage()], 422);
        }
    }
}
