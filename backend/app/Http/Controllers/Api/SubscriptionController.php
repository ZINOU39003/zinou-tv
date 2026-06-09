<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\SubscriptionResource;
use App\Models\ActivationCode;
use App\Traits\HasApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SubscriptionController extends Controller
{
    use HasApiResponse;

    public function show(Request $request): JsonResponse
    {
        $user = $request->user();

        $code = ActivationCode::where('user_id', $user->id)
            ->with('device')
            ->first();

        if (!$code) {
            return $this->error('No active subscription found.', 404);
        }

        return $this->success(new SubscriptionResource($code), 'Subscription details retrieved.');
    }
}
