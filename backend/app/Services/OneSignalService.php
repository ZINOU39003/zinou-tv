<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class OneSignalService
{
    protected $appId;
    protected $restApiKey;

    public function __construct()
    {
        // Default to the provided App ID if not in env
        $this->appId = env('ONESIGNAL_APP_ID', 'caca1acd-7cc9-4d14-af31-4ce9bcf4e52b');
        $this->restApiKey = env('ONESIGNAL_REST_API_KEY', '');
    }

    /**
     * Send a notification to all users.
     *
     * @param string $title The notification title
     * @param string $message The notification content
     * @param array $data Optional extra data (e.g., match_id)
     * @return bool
     */
    public function sendToAll($title, $message, $data = [])
    {
        if (empty($this->restApiKey)) {
            Log::warning('OneSignal API Key is empty. Cannot send notification: ' . $title);
            return false;
        }

        $payload = [
            'app_id' => $this->appId,
            'included_segments' => ['All'],
            'headings' => ['en' => $title, 'ar' => $title],
            'contents' => ['en' => $message, 'ar' => $message],
            'data' => $data,
        ];

        try {
            $response = Http::withHeaders([
                'Authorization' => 'Basic ' . $this->restApiKey,
                'Content-Type' => 'application/json; charset=utf-8',
            ])->post('https://onesignal.com/api/v1/notifications', $payload);

            if ($response->successful()) {
                Log::info('OneSignal notification sent successfully: ' . $title);
                return true;
            } else {
                Log::error('OneSignal notification failed: ' . $response->body());
                return false;
            }
        } catch (\Exception $e) {
            Log::error('OneSignal Exception: ' . $e->getMessage());
            return false;
        }
    }
}
