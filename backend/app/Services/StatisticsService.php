<?php

namespace App\Services;

use App\Enums\ActivationCodeStatus;
use App\Enums\UserRole;
use App\Models\ActivationCode;
use App\Models\Channel;
use App\Models\Device;
use App\Models\StreamSession;
use App\Models\User;
use Illuminate\Support\Carbon;

class StatisticsService
{
    public function getDashboardStats(): array
    {
        $liveSince = Carbon::now()->subMinutes(2);

        $liveViewers = StreamSession::where('last_seen_at', '>=', $liveSince)->count();
        $totalInstalls = Device::count();
        $activeDevicesToday = Device::where('last_active_at', '>=', Carbon::today())->count();

        $channelViewers = StreamSession::with('channel:id,name,name_ar')
            ->where('last_seen_at', '>=', $liveSince)
            ->orderByDesc('last_seen_at')
            ->get()
            ->map(fn (StreamSession $session) => [
                'channel_id' => $session->channel_id,
                'channel_name' => $session->channel?->name_ar ?: $session->channel?->name,
                'device_name' => $session->device_name,
                'last_seen' => $session->last_seen_at?->diffForHumans(),
            ]);

        return [
            'total_users' => User::where('role', UserRole::USER)->count(),
            'active_subscriptions' => ActivationCode::where('status', ActivationCodeStatus::ACTIVE)->count(),
            'expired_subscriptions' => ActivationCode::where('status', ActivationCodeStatus::EXPIRED)->count(),
            'total_channels' => Channel::where('is_active', true)->count(),
            'unused_codes' => ActivationCode::where('status', ActivationCodeStatus::UNUSED)->count(),
            'live_viewers' => $liveViewers,
            'total_installs' => $totalInstalls,
            'active_devices_today' => $activeDevicesToday,
            'channel_viewers' => $channelViewers,
            'viewers_by_channel' => StreamSession::query()
                ->selectRaw('channel_id, COUNT(*) as viewers')
                ->where('last_seen_at', '>=', $liveSince)
                ->groupBy('channel_id')
                ->with('channel:id,name,name_ar')
                ->orderByDesc('viewers')
                ->get()
                ->map(fn ($row) => [
                    'channel_id' => $row->channel_id,
                    'channel_name' => $row->channel?->name_ar ?: $row->channel?->name,
                    'viewers' => (int) $row->viewers,
                ]),
        ];
    }
}
