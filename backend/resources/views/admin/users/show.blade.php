@extends('admin.layouts.app')

@section('title', 'User Details - ' . $user->name)
@section('header_title', $user->name)
@section('header_subtitle', 'User profile and hardware binding information')

@section('content')

    <div style="display: grid; grid-template-columns: 1fr 2fr; gap: 24px;">
        
        <!-- Sidebar Info -->
        <div>
            <!-- User Status Card -->
            <div class="card">
                <h2 class="mb-4" style="font-size:18px; font-weight:700;">Account Profile</h2>
                
                <div style="display:flex; flex-direction:column; gap:16px;">
                    <div>
                        <span class="text-muted" style="font-size:12px; text-transform:uppercase; font-weight:600;">Status</span>
                        <div class="mt-4">
                            <span class="badge {{ $user->is_active ? 'badge-success' : 'badge-danger' }}">
                                {{ $user->is_active ? 'Account Active' : 'Deactivated' }}
                            </span>
                        </div>
                    </div>

                    <div>
                        <span class="text-muted" style="font-size:12px; text-transform:uppercase; font-weight:600;">Email</span>
                        <p style="font-weight:600; font-size:15px; margin-top:4px;">{{ $user->email }}</p>
                    </div>

                    <div>
                        <span class="text-muted" style="font-size:12px; text-transform:uppercase; font-weight:600;">Registered Date</span>
                        <p style="font-weight:600; font-size:15px; margin-top:4px;">{{ $user->created_at->format('M d, Y H:i') }}</p>
                    </div>
                </div>

                <div class="mt-4" style="border-top:1px solid var(--border-glass); padding-top:20px; display:flex; flex-direction:column; gap:10px;">
                    <form action="{{ route('admin.users.toggle-status', $user->id) }}" method="POST">
                        @csrf
                        <button type="submit" class="btn btn-secondary w-full" style="justify-content:center;">
                            {{ $user->is_active ? 'Deactivate Account' : 'Activate Account' }}
                        </button>
                    </form>
                    
                    <form action="{{ route('admin.users.destroy', $user->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this user? This will delete all devices and logs associated with them.')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger w-full" style="justify-content:center;">Delete Account</button>
                    </form>
                </div>
            </div>
        </div>

        <!-- Details Area -->
        <div style="display:flex; flex-direction:column; gap:24px;">
            
            <!-- Device Locks -->
            <div class="card">
                <h2 class="mb-4" style="font-size:18px; font-weight:700;">Bound Devices</h2>
                
                <div class="table-container">
                    <table>
                        <thead>
                            <tr>
                                <th>Device Name</th>
                                <th>Model</th>
                                <th>OS Version</th>
                                <th>IP Address</th>
                                <th>Last Active</th>
                                <th class="text-right">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($user->devices as $device)
                                <tr>
                                    <td>
                                        <span style="font-weight:600;">{{ $device->device_name }}</span>
                                        <span style="display:block; font-size:10px; font-family:monospace; color:var(--text-muted);">{{ $device->device_id }}</span>
                                    </td>
                                    <td>{{ $device->device_model }}</td>
                                    <td>Android {{ $device->android_version }}</td>
                                    <td style="font-family:monospace;">{{ $device->ip_address }}</td>
                                    <td>{{ $device->last_active_at ? $device->last_active_at->diffForHumans() : 'Never' }}</td>
                                    <td class="text-right">
                                        <form action="{{ route('admin.devices.toggle-block', $device->id) }}" method="POST">
                                            @csrf
                                            <button type="submit" class="btn {{ $device->is_blocked ? 'btn-primary' : 'btn-danger' }} btn-sm">
                                                {{ $device->is_blocked ? 'Unblock Device' : 'Block Device' }}
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center text-muted" style="padding: 30px 0;">No hardware devices bound yet.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Activation Codes -->
            <div class="card">
                <h2 class="mb-4" style="font-size:18px; font-weight:700;">Subscription Licenses</h2>

                <div class="table-container">
                    <table>
                        <thead>
                            <tr>
                                <th>Activation Code</th>
                                <th>Duration</th>
                                <th>Status</th>
                                <th>Activated At</th>
                                <th>Expires At</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($user->activationCodes as $code)
                                <tr>
                                    <td>
                                        <code style="font-family:monospace; background:rgba(255,255,255,0.05); padding:4px 8px; border-radius:4px; font-weight:700;">
                                            {{ $code->code }}
                                        </code>
                                    </td>
                                    <td>{{ $code->duration->getLabel() }}</td>
                                    <td>
                                        <span class="badge {{ $code->isValid() ? 'badge-success' : 'badge-danger' }}">
                                            {{ $code->status->value }}
                                        </span>
                                    </td>
                                    <td>{{ $code->activated_at ? $code->activated_at->format('Y-m-d H:i') : '—' }}</td>
                                    <td>{{ $code->expires_at ? $code->expires_at->format('Y-m-d H:i') : '—' }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center text-muted" style="padding: 30px 0;">No licenses active.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Activity Logs -->
            <div class="card">
                <h2 class="mb-4" style="font-size:18px; font-weight:700;">Activity Logs</h2>

                <div class="table-container" style="max-height: 300px; overflow-y: auto;">
                    <table>
                        <thead>
                            <tr>
                                <th>Action</th>
                                <th>Details</th>
                                <th>IP Address</th>
                                <th>Timestamp</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($user->activityLogs as $log)
                                <tr>
                                    <td><span class="badge badge-info">{{ $log->action }}</span></td>
                                    <td>{{ $log->details }}</td>
                                    <td style="font-family:monospace;">{{ $log->ip_address }}</td>
                                    <td>{{ $log->created_at->diffForHumans() }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="text-center text-muted" style="padding: 30px 0;">No logs found for this user.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

        </div>
    </div>

@endsection
