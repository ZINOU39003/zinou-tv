<?php

namespace App\Http\Middleware;

use App\Models\ActivationCode;
use App\Models\Device;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckDeviceBinding
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

        // Admins bypass device check
        if ($user->isAdmin()) {
            return $next($request);
        }

        $deviceId = $request->header('X-Device-ID');

        if (!$deviceId) {
            return response()->json([
                'success' => false,
                'message' => 'Device ID header is missing.'
            ], 400);
        }

        // Check if device is blocked
        $device = Device::where('device_id', $deviceId)->first();
        if ($device && $device->is_blocked) {
            return response()->json([
                'success' => false,
                'message' => 'This device has been blocked.'
            ], 403);
        }

        $code = ActivationCode::where('user_id', $user->id)->first();

        if (!$code) {
            return response()->json([
                'success' => false,
                'message' => 'No active activation code found.'
            ], 403);
        }

        // Validate bound device - if the bound device had a placeholder ID (e.g. "unknown_device"),
        // auto-update to the real device ID from the current request
        if ($code->device && $code->device->device_id !== $deviceId) {
            if ($code->device->device_id === 'unknown_device') {
                // Auto-fix: update the device record with the real device ID
                $code->device->update(['device_id' => $deviceId]);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'This subscription is linked to another device.'
                ], 403);
            }
        }

        return $next($request);
    }
}
