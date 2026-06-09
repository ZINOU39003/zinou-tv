<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivationCode;
use App\Models\User;
use App\Enums\CodeDuration;
use App\Enums\ActivationCodeStatus;
use App\Enums\UserRole;
use App\Services\LicenseService;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ProActivationController extends Controller
{
    protected LicenseService $licenseService;

    public function __construct(LicenseService $licenseService)
    {
        $this->licenseService = $licenseService;
    }

    /**
     * Display the PRO activation page.
     */
    public function index(Request $request): View
    {
        $durations = CodeDuration::cases();
        
        // Fetch active/expired activation codes with users
        $recentActivations = ActivationCode::whereIn('status', [ActivationCodeStatus::ACTIVE, ActivationCodeStatus::EXPIRED])
            ->with(['user', 'device'])
            ->latest()
            ->limit(10)
            ->get();

        return view('admin.pro_activation', compact('durations', 'recentActivations'));
    }

    /**
     * Directly activate a PRO account.
     */
    public function activate(Request $request): RedirectResponse
    {
        $request->validate([
            'email' => 'required|string', // can be an email or code-style email
            'duration' => 'required|string',
            'notes' => 'nullable|string',
        ]);

        $email = strtolower(trim($request->input('email')));
        $durationEnum = CodeDuration::from($request->input('duration'));
        $notes = $request->input('notes');

        // Check if input is just a username/string without @, if so append default domain
        if (!str_contains($email, '@')) {
            $email = $email . '@sportiptv.com';
        }

        try {
            DB::transaction(function () use ($email, $durationEnum, $notes) {
                // Find or create user
                $user = User::where('email', $email)->first();
                
                // Generate a code string
                $segments = [];
                for ($i = 0; $i < 4; $i++) {
                    $segments[] = strtoupper(Str::random(4));
                }
                $codeString = implode('-', $segments);

                if (!$user) {
                    $user = User::create([
                        'name' => 'IPTV PRO User',
                        'email' => $email,
                        'password' => Hash::make($codeString), // default password is the code itself
                        'role' => UserRole::USER,
                        'is_active' => true,
                    ]);
                }

                // If user already has an active code, expire or revoke it first to bind the new one
                ActivationCode::where('user_id', $user->id)
                    ->where('status', ActivationCodeStatus::ACTIVE)
                    ->update(['status' => ActivationCodeStatus::EXPIRED]);

                // Calculate times
                $activatedAt = Carbon::now();
                $expiresAt = $activatedAt->copy()->addDays($durationEnum->getDaysCount());

                // Create the active activation code
                ActivationCode::create([
                    'code' => $codeString,
                    'duration' => $durationEnum,
                    'status' => ActivationCodeStatus::ACTIVE,
                    'user_id' => $user->id,
                    'device_id' => null, // Will bind automatically upon first device login
                    'activated_at' => $activatedAt,
                    'expires_at' => $expiresAt,
                    'created_by' => auth()->id() ?: 1,
                    'notes' => $notes ?: 'Direct activation from admin panel',
                ]);
            });

            return redirect()->back()->with('success', 'تم تفعيل الحساب بنجاح! يمكن للمستخدم الآن تسجيل الدخول مباشرة باستخدام البريد الإلكتروني المدخل.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'حدث خطأ أثناء التفعيل: ' . $e->getMessage());
        }
    }
}
