<?php

namespace App\Http\Controllers;

use App\Services\OneSignalService;
use Illuminate\Http\Request;

class AdminNotificationController extends Controller
{
    protected $oneSignalService;

    public function __construct(OneSignalService $oneSignalService)
    {
        $this->oneSignalService = $oneSignalService;
    }

    public function send(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'message' => 'required|string',
            'image_url' => 'nullable|url',
        ]);

        $title = $request->input('title');
        $message = $request->input('message');
        $imageUrl = $request->input('image_url');

        $success = $this->oneSignalService->sendCustomNotification($title, $message, $imageUrl);

        if ($success) {
            return response()->json(['message' => 'Notification sent successfully'], 200);
        } else {
            return response()->json(['error' => 'Failed to send notification. Check logs for details.'], 500);
        }
    }
}
