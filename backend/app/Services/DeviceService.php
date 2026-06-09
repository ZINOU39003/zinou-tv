<?php

namespace App\Services;

use App\Models\Device;
use App\Models\User;
use Carbon\Carbon;

class DeviceService
{
    public function registerOrUpdateDevice(User $user, array $deviceData): Device
    {
        $device = Device::where('device_id', $deviceData['device_id'])->first();

        if ($device) {
            // Update device details
            $device->update([
                'user_id' => $user->id,
                'device_name' => $deviceData['device_name'] ?? $device->device_name,
                'device_model' => $deviceData['device_model'] ?? $device->device_model,
                'android_version' => $deviceData['android_version'] ?? $device->android_version,
                'app_version' => $deviceData['app_version'] ?? $device->app_version,
                'ip_address' => $deviceData['ip_address'] ?? null,
                'last_active_at' => Carbon::now(),
            ]);
        } else {
            // Create new device
            $device = Device::create([
                'user_id' => $user->id,
                'device_id' => $deviceData['device_id'],
                'device_name' => $deviceData['device_name'] ?? 'Unknown Device',
                'device_model' => $deviceData['device_model'] ?? 'Unknown Model',
                'android_version' => $deviceData['android_version'] ?? 'Unknown OS',
                'app_version' => $deviceData['app_version'] ?? '1.0.0',
                'ip_address' => $deviceData['ip_address'] ?? null,
                'last_active_at' => Carbon::now(),
            ]);
        }

        return $device;
    }

    public function isDeviceBlocked(string $deviceId): bool
    {
        $device = Device::where('device_id', $deviceId)->first();
        return $device ? (bool) $device->is_blocked : false;
    }

    public function blockDevice(string $deviceId, bool $block = true): bool
    {
        $device = Device::where('device_id', $deviceId)->first();
        if ($device) {
            return $device->update(['is_blocked' => $block]);
        }
        return false;
    }
}
