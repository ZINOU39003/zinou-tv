<?php

namespace App\Http\Middleware;

use App\Models\ActivationCode;
use App\Enums\ActivationCodeStatus;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckSubscription
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized access.'
            ], 401);
        }

        // Admins can bypass subscription check
        if ($user->isAdmin()) {
            return $next($request);
        }

        $code = ActivationCode::where('user_id', $user->id)->first();

        if (!$code) {
            return response()->json([
                'success' => false,
                'message' => 'No subscription found. Please activate an activation code.'
            ], 403);
        }

        if ($code->status === ActivationCodeStatus::REVOKED) {
            return response()->json([
                'success' => false,
                'message' => 'Your subscription has been suspended/revoked.'
            ], 403);
        }

        if ($code->expires_at && $code->expires_at->isPast()) {
            $code->update(['status' => ActivationCodeStatus::EXPIRED]);
            return response()->json([
                'success' => false,
                'message' => 'Your subscription has expired.'
            ], 403);
        }

        if ($code->status !== ActivationCodeStatus::ACTIVE) {
            return response()->json([
                'success' => false,
                'message' => 'Your subscription is inactive.'
            ], 403);
        }

        return $next($request);
    }
}
