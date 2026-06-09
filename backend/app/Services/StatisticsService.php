<?php

namespace App\Services;

use App\Models\User;
use App\Models\Channel;
use App\Models\ActivationCode;
use App\Enums\ActivationCodeStatus;
use App\Enums\UserRole;

class StatisticsService
{
    public function getDashboardStats(): array
    {
        $totalUsers = User::where('role', UserRole::USER)->count();
        
        $activeSubscriptions = ActivationCode::where('status', ActivationCodeStatus::ACTIVE)->count();
        
        $expiredSubscriptions = ActivationCode::where('status', ActivationCodeStatus::EXPIRED)->count();
        
        $totalChannels = Channel::count();
        
        $unusedCodes = ActivationCode::where('status', ActivationCodeStatus::UNUSED)->count();

        return [
            'total_users' => $totalUsers,
            'active_subscriptions' => $activeSubscriptions,
            'expired_subscriptions' => $expiredSubscriptions,
            'total_channels' => $totalChannels,
            'unused_codes' => $unusedCodes,
        ];
    }
}
