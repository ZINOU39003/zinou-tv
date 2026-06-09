<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\LoginRequest;
use App\Http\Resources\UserResource;
use App\Models\Device;
use App\Models\User;
use App\Services\DeviceService;
use App\Traits\HasApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    use HasApiResponse;

    protected DeviceService $deviceService;

    public function __construct(DeviceService $deviceService)
    {
        $this->deviceService = $deviceService;
    }

    public function login(LoginRequest $request): JsonResponse
    {
        $credentials = $request->only('email', 'password');
        $deviceId = $request->input('device_id');

        // Check if device is blocked
        if ($this->deviceService->isDeviceBlocked($deviceId)) {
            return $this->error('This device has been blocked.', 403);
        }

        if (!$token = Auth::guard('api')->attempt($credentials)) {
            return $this->error('Invalid email or password.', 401);
        }

        $user = Auth::guard('api')->user();

        if (!$user->is_active) {
            Auth::guard('api')->logout();
            return $this->error('Your account is deactivated.', 403);
        }

        // Verify device binding (except for admin users)
        if (!$user->isAdmin()) {
            $code = $user->activeActivationCode;
            if ($code && $code->device && $code->device->device_id !== $deviceId) {
                Auth::guard('api')->logout();
                return $this->error('This account is registered to another device.', 403);
            }
        }

        // Register or update device connection
        $this->deviceService->registerOrUpdateDevice($user, [
            'device_id' => $deviceId,
            'device_name' => $request->input('device_name', $request->header('User-Agent')),
            'device_model' => $request->input('device_model'),
            'android_version' => $request->input('android_version'),
            'app_version' => $request->input('app_version'),
            'ip_address' => $request->ip(),
        ]);

        return $this->respondWithToken($token, $user);
    }

    public function me(): JsonResponse
    {
        $user = Auth::guard('api')->user();
        return $this->success(new UserResource($user));
    }

    public function logout(): JsonResponse
    {
        Auth::guard('api')->logout();
        return $this->success(null, 'Successfully logged out');
    }

    public function refresh(): JsonResponse
    {
        try {
            $newToken = Auth::guard('api')->refresh();
            return $this->respondWithToken($newToken, Auth::guard('api')->user());
        } catch (\Exception $e) {
            return $this->error('Token cannot be refreshed.', 401);
        }
    }

    protected function respondWithToken($token, User $user): JsonResponse
    {
        return $this->success([
            'token' => $token,
            'token_type' => 'bearer',
            'expires_in' => Auth::guard('api')->factory()->getTTL() * 60,
            'user' => new UserResource($user),
        ], 'Authentication successful');
    }
}
