<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Device;
use App\Enums\UserRole;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Contracts\View\View;

class UserController extends Controller
{
    public function index(Request $request): View
    {
        $search = $request->input('search');
        
        $users = User::where('role', UserRole::USER)
            ->when($search, function ($query) use ($search) {
                $query->where('name', 'like', "%{$search}%")
                      ->orWhere('email', 'like', "%{$search}%");
            })
            ->with(['devices', 'activationCodes'])
            ->latest()
            ->paginate(15);

        return view('admin.users.index', compact('users', 'search'));
    }

    public function show(User $user): View
    {
        $user->load(['devices', 'activationCodes', 'activityLogs']);
        return view('admin.users.show', compact('user'));
    }

    public function toggleStatus(User $user): RedirectResponse
    {
        $user->update([
            'is_active' => !$user->is_active
        ]);

        $status = $user->is_active ? 'activated' : 'deactivated';
        return redirect()->back()->with('success', "User account {$status} successfully.");
    }

    public function toggleBlockDevice(int $deviceId): RedirectResponse
    {
        $device = Device::findOrFail($deviceId);
        $device->update([
            'is_blocked' => !$device->is_blocked
        ]);

        $status = $device->is_blocked ? 'blocked' : 'unblocked';
        return redirect()->back()->with('success', "Device {$status} successfully.");
    }

    public function destroy(User $user): RedirectResponse
    {
        // Don't delete admin accounts
        if ($user->isAdmin()) {
            return redirect()->back()->with('error', 'Cannot delete administrator account.');
        }

        $user->delete();
        return redirect()->route('admin.users.index')->with('success', 'User account deleted successfully.');
    }
}
