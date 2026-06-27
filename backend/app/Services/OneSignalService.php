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
    public function sendToAll($title, $message, $data = [], $options = [])
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
        ];

        if (!empty($data)) {
            $payload['data'] = $data;
        }

        // Add rich notification options
        if (!empty($options['big_picture'])) {
            $payload['big_picture'] = $options['big_picture'];
            $payload['ios_attachments'] = ['id1' => $options['big_picture']];
        }
        if (!empty($options['large_icon'])) {
            $payload['large_icon'] = $options['large_icon'];
        }
        if (!empty($options['buttons'])) {
            $payload['buttons'] = $options['buttons'];
        }

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

    /**
     * Send a custom notification with optional image.
     */
    public function sendCustomNotification($title, $message, $imageUrl = null, $data = [])
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
        ];

        if (!empty($data)) {
            $payload['data'] = $data;
        }

        if (!empty($imageUrl)) {
            $payload['big_picture'] = $imageUrl;
            $payload['ios_attachments'] = ['id1' => $imageUrl];
        }

        try {
            $response = Http::withHeaders([
                'Authorization' => 'Basic ' . $this->restApiKey,
                'Content-Type' => 'application/json; charset=utf-8',
            ])->post('https://onesignal.com/api/v1/notifications', $payload);

            if ($response->successful()) {
                Log::info('Custom OneSignal notification sent successfully: ' . $title);
                return true;
            } else {
                Log::error('Custom OneSignal notification failed: ' . $response->body());
                return false;
            }
        } catch (\Exception $e) {
            Log::error('OneSignal Exception: ' . $e->getMessage());
            return false;
        }
    }
}
