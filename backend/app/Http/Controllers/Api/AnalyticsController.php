<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Device;
use App\Models\StreamSession;
use App\Traits\HasApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AnalyticsController extends Controller
{
    use HasApiResponse;

    public function heartbeat(Request $request): JsonResponse
    {
        $data = $request->validate([
            'device_id' => 'required|string|max:191',
            'channel_id' => 'required|integer|exists:channels,id',
            'device_name' => 'nullable|string|max:191',
            'app_version' => 'nullable|string|max:32',
        ]);

        StreamSession::updateOrCreate(
            ['device_id' => $data['device_id']],
            [
                'channel_id' => $data['channel_id'],
                'device_name' => $data['device_name'] ?? null,
                'app_version' => $data['app_version'] ?? null,
                'last_seen_at' => now(),
            ]
        );

        Device::where('device_id', $data['device_id'])->update(['last_active_at' => now()]);

        return $this->success(null, 'Heartbeat recorded.');
    }
}
