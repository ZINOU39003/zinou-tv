<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\ActivateCodeRequest;
use App\Services\LicenseService;
use App\Traits\HasApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class LicenseController extends Controller
{
    use HasApiResponse;

    protected LicenseService $licenseService;

    public function __construct(LicenseService $licenseService)
    {
        $this->licenseService = $licenseService;
    }

    public function activate(ActivateCodeRequest $request): JsonResponse
    {
        $code = $request->input('code');
        $deviceData = [
            'device_id' => $request->input('device_id'),
            'device_name' => $request->input('device_name'),
            'device_model' => $request->input('device_model'),
            'android_version' => $request->input('android_version'),
            'app_version' => $request->input('app_version'),
            'ip_address' => $request->ip(),
        ];

        $result = $this->licenseService->activateCode($code, $deviceData);

        if (!$result['success']) {
            return $this->error($result['message'], 400);
        }

        // Return credentials so Android client can auto-login
        // Note: Default password is the code itself
        $credentials = [
            'email' => strtolower($code) . '@sportiptv.com',
            'password' => $code,
        ];

        // Generate JWT token for the user immediately to login after activation
        if (!$token = auth('api')->attempt($credentials)) {
            return $this->success([
                'subscription' => $result['subscription'],
                'user' => $result['user'],
                'token' => null
            ], 'License activated, please log in manually.');
        }

        return $this->success([
            'subscription' => $result['subscription'],
            'user' => $result['user'],
            'token' => $token,
            'token_type' => 'bearer',
            'expires_in' => auth('api')->factory()->getTTL() * 60,
        ], 'License activated and authenticated.');
    }

    public function validateLicense(Request $request): JsonResponse
    {
        $user = $request->user();
        $deviceId = $request->header('X-Device-ID');

        if (!$deviceId) {
            return $this->error('X-Device-ID header is missing.', 400);
        }

        $result = $this->licenseService->validateLicense($user, $deviceId);

        if (!$result['is_valid']) {
            return $this->error($result['message'], 403);
        }

        return $this->success($result['subscription'], 'License is valid.');
    }
}
