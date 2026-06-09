<?php

namespace App\Services;

use App\Models\ActivationCode;
use App\Models\Device;
use App\Models\User;
use App\Enums\ActivationCodeStatus;
use App\Enums\CodeDuration;
use App\Enums\UserRole;
use Carbon\Carbon;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class LicenseService
{
    protected DeviceService $deviceService;

    public function __construct(DeviceService $deviceService)
    {
        $this->deviceService = $deviceService;
    }

    /**
     * Generate a single activation code.
     */
    public function generateCode(CodeDuration $duration, int $createdByUserId, ?string $notes = null): ActivationCode
    {
        $codeString = $this->generateUniqueCodeString();

        return ActivationCode::create([
            'code' => $codeString,
            'duration' => $duration,
            'status' => ActivationCodeStatus::UNUSED,
            'created_by' => $createdByUserId,
            'notes' => $notes,
        ]);
    }

    /**
     * Generate multiple activation codes in batch.
     */
    public function generateBatch(CodeDuration $duration, int $count, int $createdByUserId, ?string $notes = null): array
    {
        $codes = [];
        for ($i = 0; $i < $count; $i++) {
            $codes[] = $this->generateCode($duration, $createdByUserId, $notes);
        }
        return $codes;
    }

    /**
     * Activate a code for a device.
     */
    public function activateCode(string $codeString, array $deviceData): array
    {
        return DB::transaction(function () use ($codeString, $deviceData) {
            $code = ActivationCode::where('code', $codeString)->first();

            if (!$code) {
                return ['success' => false, 'message' => 'Invalid activation code'];
            }

            if ($code->status === ActivationCodeStatus::REVOKED) {
                return ['success' => false, 'message' => 'This code has been revoked'];
            }

            if ($code->status === ActivationCodeStatus::EXPIRED || ($code->expires_at && $code->expires_at->isPast())) {
                $code->update(['status' => ActivationCodeStatus::EXPIRED]);
                return ['success' => false, 'message' => 'This code has expired'];
            }

            $deviceId = $deviceData['device_id'];

            // Check if device is blocked
            if ($this->deviceService->isDeviceBlocked($deviceId)) {
                return ['success' => false, 'message' => 'This device is blocked'];
            }

            // Case 1: Code is active but on a different device
            if ($code->status === ActivationCodeStatus::ACTIVE) {
                if ($code->device && $code->device->device_id !== $deviceId) {
                    return ['success' => false, 'message' => 'This activation code is already linked to another device'];
                }
                
                // Already active on this device
                return [
                    'success' => true,
                    'message' => 'License already active on this device',
                    'user' => $code->user,
                    'subscription' => $this->getSubscriptionDetails($code)
                ];
            }

            // Case 2: Code is unused. We will activate it and link/create a user and bind device.
            // Create a shadow user for this license
            $email = strtolower($codeString) . '@sportiptv.com';
            $user = User::create([
                'name' => 'IPTV User ' . substr($codeString, 0, 4),
                'email' => $email,
                'password' => Hash::make($codeString), // default password is the code itself
                'role' => UserRole::USER,
                'is_active' => true,
            ]);

            // Register device
            $device = $this->deviceService->registerOrUpdateDevice($user, $deviceData);

            // Set times
            $activatedAt = Carbon::now();
            $expiresAt = $activatedAt->copy()->addDays($code->duration->getDaysCount());

            $code->update([
                'status' => ActivationCodeStatus::ACTIVE,
                'user_id' => $user->id,
                'device_id' => $device->id,
                'activated_at' => $activatedAt,
                'expires_at' => $expiresAt,
            ]);

            // Return success with details
            return [
                'success' => true,
                'message' => 'License activated successfully',
                'user' => $user,
                'subscription' => $this->getSubscriptionDetails($code)
            ];
        });
    }

    /**
     * Validate a license for a given user/device.
     */
    public function validateLicense(User $user, string $deviceId): array
    {
        $code = ActivationCode::where('user_id', $user->id)->first();

        if (!$code) {
            return ['is_valid' => false, 'message' => 'No license found for this user'];
        }

        if ($code->status === ActivationCodeStatus::REVOKED) {
            return ['is_valid' => false, 'message' => 'License has been revoked'];
        }

        if ($code->expires_at && $code->expires_at->isPast()) {
            $code->update(['status' => ActivationCodeStatus::EXPIRED]);
            return ['is_valid' => false, 'message' => 'License has expired'];
        }

        if ($code->status !== ActivationCodeStatus::ACTIVE) {
            return ['is_valid' => false, 'message' => 'License is not active'];
        }

        // Validate device binding
        if (!$code->device || $code->device->device_id !== $deviceId) {
            return ['is_valid' => false, 'message' => 'License is bound to another device'];
        }

        if ($code->device->is_blocked) {
            return ['is_valid' => false, 'message' => 'Your device has been blocked'];
        }

        return [
            'is_valid' => true,
            'subscription' => $this->getSubscriptionDetails($code)
        ];
    }

    /**
     * Reset device binding for an activation code.
     */
    public function resetDevice(int $codeId): bool
    {
        $code = ActivationCode::find($codeId);
        if ($code) {
            // Keep the code active but detach the device
            return $code->update([
                'device_id' => null
                // Note: Keep user_id so user account remains, but allow new device activation
            ]);
        }
        return false;
    }

    /**
     * Revoke activation code.
     */
    public function revokeCode(int $codeId): bool
    {
        $code = ActivationCode::find($codeId);
        if ($code) {
            return $code->update([
                'status' => ActivationCodeStatus::REVOKED
            ]);
        }
        return false;
    }

    /**
     * Extend activation code subscription duration.
     */
    public function extendSubscription(int $codeId, CodeDuration $duration): bool
    {
        $code = ActivationCode::find($codeId);
        if ($code) {
            $days = $duration->getDaysCount();
            
            // If expired or unused, extend from now, else extend from current expires_at
            if ($code->status === ActivationCodeStatus::ACTIVE && $code->expires_at && $code->expires_at->isFuture()) {
                $newExpiresAt = $code->expires_at->copy()->addDays($days);
            } else {
                $newExpiresAt = Carbon::now()->addDays($days);
            }

            return $code->update([
                'expires_at' => $newExpiresAt,
                'status' => ActivationCodeStatus::ACTIVE, // Reactivate if it was expired
            ]);
        }
        return false;
    }

    /**
     * Format subscription details.
     */
    protected function getSubscriptionDetails(ActivationCode $code): array
    {
        $expiresAt = $code->expires_at;
        $daysRemaining = $expiresAt ? (int) max(0, round(Carbon::now()->diffInDays($expiresAt, false))) : 0;

        // Build device info if bound
        $deviceData = null;
        if ($code->device) {
            $deviceData = [
                'device_id' => $code->device->device_id,
                'device_name' => $code->device->device_name,
                'device_model' => $code->device->device_model,
                'last_active_at' => $code->device->updated_at?->toIso8601String(),
            ];
        }

        return [
            'code' => substr($code->code, 0, 4) . '-****-****-' . substr($code->code, -4),
            'status' => $code->status->value,
            'duration' => $code->duration->value,
            'activated_at' => $code->activated_at ? $code->activated_at->toIso8601String() : null,
            'expires_at' => $expiresAt ? $expiresAt->toIso8601String() : null,
            'days_remaining' => $daysRemaining,
            'device' => $deviceData,
        ];
    }

    /**
     * Generate unique activation code string: XXXX-XXXX-XXXX-XXXX
     */
    protected function generateUniqueCodeString(): string
    {
        do {
            $segments = [];
            for ($i = 0; $i < 4; $i++) {
                $segments[] = strtoupper(Str::random(4));
            }
            $code = implode('-', $segments);
        } while (ActivationCode::where('code', $code)->exists());

        return $code;
    }
}
