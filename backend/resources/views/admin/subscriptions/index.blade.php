@extends('admin.layouts.app')

@section('title', 'Subscriptions Management')
@section('header_title', 'Subscriptions')
@section('header_subtitle', 'Overview of active plans and customer expiry details')

@section('actions')
    <form action="{{ route('admin.subscriptions.index') }}" method="GET" style="display:flex; gap:10px;">
        <input type="text" name="search" class="form-control" placeholder="Search by email or code..." value="{{ $search }}" style="width:240px;">
        <button type="submit" class="btn btn-secondary">Search</button>
    </form>
@endsection

@section('content')

    <div class="card">
        <div class="table-container">
            <table>
                <thead>
                    <tr>
                        <th>Activation Code</th>
                        <th>User Email</th>
                        <th>Registered Device</th>
                        <th>Activated At</th>
                        <th>Expires At</th>
                        <th>Days Left</th>
                        <th>Status</th>
                        <th class="text-right">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($subscriptions as $sub)
                        @php
                            $daysLeft = $sub->expires_at ? max(0, now()->diffInDays($sub->expires_at, false)) : 0;
                        @endphp
                        <tr>
                            <td>
                                <code style="font-family:monospace; background:rgba(255,255,255,0.05); padding:4px 8px; border-radius:4px; font-weight:700;">
                                    {{ $sub->code }}
                                </code>
                            </td>
                            <td>
                                @if($sub->user)
                                    <a href="{{ route('admin.users.show', $sub->user->id) }}" style="color:var(--accent-secondary); text-decoration:none; font-weight:500;">
                                        {{ $sub->user->email }}
                                    </a>
                                @else
                                    <span class="text-muted">Deleted User</span>
                                @endif
                            </td>
                            <td>
                                @if($sub->device)
                                    <span class="badge badge-warning" title="{{ $sub->device->device_id }}">
                                        {{ $sub->device->device_name }} ({{ $sub->device->device_model }})
                                    </span>
                                @else
                                    <span class="badge badge-danger">Not Bound</span>
                                @endif
                            </td>
                            <td>{{ $sub->activated_at ? $sub->activated_at->format('Y-m-d H:i') : '—' }}</td>
                            <td>{{ $sub->expires_at ? $sub->expires_at->format('Y-m-d H:i') : '—' }}</td>
                            <td>
                                <span style="font-weight:700; color: {{ $daysLeft < 7 ? 'var(--danger)' : 'var(--success)' }}">
                                    {{ $daysLeft }} days
                                </span>
                            </td>
                            <td>
                                <span class="badge {{ $sub->isValid() ? 'badge-success' : 'badge-danger' }}">
                                    {{ $sub->status->value }}
                                </span>
                            </td>
                            <td class="text-right">
                                <div style="display:flex; justify-content:flex-end; gap:8px;">
                                    
                                    <!-- Subscription extension -->
                                    <form action="{{ route('admin.subscriptions.extend', $sub->id) }}" method="POST" style="display:inline-flex; gap:4px;">
                                        @csrf
                                        <select name="duration" class="form-control" style="padding:4px 8px; font-size:11px; width:110px; height: 30px; border-radius: 4px;" required>
                                            @foreach($durations as $duration)
                                                <option value="{{ $duration->value }}">+ {{ $duration->getLabel() }}</option>
                                            @endforeach
                                        </select>
                                        <button type="submit" class="btn btn-primary btn-sm" style="height: 30px; padding: 4px 10px;">Extend</button>
                                    </form>

                                    @if($sub->isValid())
                                        <form action="{{ route('admin.subscriptions.cancel', $sub->id) }}" method="POST" onsubmit="return confirm('Expire this subscription immediately? The user will lose access.')">
                                            @csrf
                                            <button type="submit" class="btn btn-danger btn-sm" style="height: 30px;">Cancel Plan</button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center text-muted" style="padding:40px 0;">No active or expired subscriptions found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-4">
            {{ $subscriptions->appends(['search' => $search])->links() }}
        </div>
    </div>

@endsection
