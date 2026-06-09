<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivationCode;
use App\Enums\ActivationCodeStatus;
use App\Enums\CodeDuration;
use App\Services\LicenseService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Contracts\View\View;

class SubscriptionController extends Controller
{
    protected LicenseService $licenseService;

    public function __construct(LicenseService $licenseService)
    {
        $this->licenseService = $licenseService;
    }

    public function index(Request $request): View
    {
        $search = $request->input('search');

        $subscriptions = ActivationCode::whereIn('status', [ActivationCodeStatus::ACTIVE, ActivationCodeStatus::EXPIRED])
            ->when($search, function ($query) use ($search) {
                $query->where('code', 'like', "%{$search}%")
                      ->orWhereHas('user', function ($q) use ($search) {
                          $q->where('email', 'like', "%{$search}%");
                      });
            })
            ->with(['user', 'device'])
            ->latest('activated_at')
            ->paginate(15);

        $durations = CodeDuration::cases();

        return view('admin.subscriptions.index', compact('subscriptions', 'search', 'durations'));
    }

    public function extend(Request $request, int $id): RedirectResponse
    {
        $request->validate([
            'duration' => 'required|string'
        ]);

        $duration = CodeDuration::from($request->input('duration'));
        $result = $this->licenseService->extendSubscription($id, $duration);

        if ($result) {
            return redirect()->back()->with('success', 'Subscription extended successfully.');
        }

        return redirect()->back()->with('error', 'Failed to extend subscription.');
    }

    public function cancel(int $id): RedirectResponse
    {
        // Expire immediately
        $code = ActivationCode::findOrFail($id);
        $result = $code->update([
            'status' => ActivationCodeStatus::EXPIRED,
            'expires_at' => now(),
        ]);

        if ($result) {
            return redirect()->back()->with('success', 'Subscription cancelled/expired successfully.');
        }

        return redirect()->back()->with('error', 'Failed to cancel subscription.');
    }
}
