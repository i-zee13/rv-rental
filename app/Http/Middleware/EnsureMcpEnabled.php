<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureMcpEnabled
{
    public function handle(Request $request, Closure $next): Response
    {
        if (! config('mcp.enabled')) {
            return response()->json(['message' => 'Not found'], 404);
        }

        return $next($request);
    }
}
