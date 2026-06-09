<?php

namespace App\Http\Middleware;

use App\Models\ApiKey;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ValidateApiKey
{
    public function handle(Request $request, Closure $next): Response
    {
        $apiKey = $request->header('X-API-Key');

        if (!$apiKey) {
            return response()->json([
                'success' => false,
                'message' => 'API Key is missing.'
            ], 401);
        }

        // Check if static key matches .env
        $staticKey = env('X_API_KEY', 'SportIptvDefaultApiKeySecret2026');
        if ($apiKey === $staticKey) {
            return $next($request);
        }

        // Otherwise check in database api_keys table
        $hashedKey = hash('sha256', $apiKey);
        $keyRecord = ApiKey::where('key_hash', $hashedKey)->first();

        if (!$keyRecord || !$keyRecord->isValid()) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid API Key or expired.'
            ], 401);
        }

        // Log last used time
        $keyRecord->update(['last_used_at' => now()]);

        return $next($request);
    }
}
