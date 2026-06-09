<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\GenerateCodesRequest;
use App\Models\ActivationCode;
use App\Enums\CodeDuration;
use App\Services\LicenseService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;

class ActivationCodeController extends Controller
{
    protected LicenseService $licenseService;

    public function __construct(LicenseService $licenseService)
    {
        $this->licenseService = $licenseService;
    }

    public function index(Request $request): View
    {
        $search = $request->input('search');
        $status = $request->input('status');

        $codes = ActivationCode::when($search, function ($query) use ($search) {
                $query->where('code', 'like', "%{$search}%")
                      ->orWhereHas('user', function ($q) use ($search) {
                          $q->where('email', 'like', "%{$search}%");
                      });
            })
            ->when($status, function ($query) use ($status) {
                $query->where('status', $status);
            })
            ->with(['user', 'device'])
            ->latest()
            ->paginate(15);

        return view('admin.codes.index', compact('codes', 'search', 'status'));
    }

    public function create(): View
    {
        $durations = CodeDuration::cases();
        return view('admin.codes.generate', compact('durations'));
    }

    public function store(GenerateCodesRequest $request): RedirectResponse
    {
        $duration = CodeDuration::from($request->input('duration'));
        $count = (int) $request->input('count');
        $notes = $request->input('notes');
        $creatorId = Auth::id();

        $this->licenseService->generateBatch($duration, $count, $creatorId, $notes);

        return redirect()->route('admin.codes.index')
            ->with('success', "Successfully generated {$count} activation codes.");
    }

    public function resetDevice(int $id): RedirectResponse
    {
        $result = $this->licenseService->resetDevice($id);

        if ($result) {
            return redirect()->back()->with('success', 'Device binding reset successfully. A new device can now register with this code.');
        }

        return redirect()->back()->with('error', 'Failed to reset device binding.');
    }

    public function revoke(int $id): RedirectResponse
    {
        $result = $this->licenseService->revokeCode($id);

        if ($result) {
            return redirect()->back()->with('success', 'Activation code revoked successfully.');
        }

        return redirect()->back()->with('error', 'Failed to revoke activation code.');
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
}
