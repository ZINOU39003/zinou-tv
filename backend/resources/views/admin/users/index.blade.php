@extends('admin.layouts.app')

@section('title', 'Users Management')
@section('header_title', 'Users & Devices')
@section('header_subtitle', 'Manage client accounts and device hardware locks')

@section('actions')
    <form action="{{ route('admin.users.index') }}" method="GET" style="display:flex; gap:10px;">
        <input type="text" name="search" class="form-control" placeholder="Search by name or email..." value="{{ $search }}" style="width:260px;">
        <button type="submit" class="btn btn-secondary">Search</button>
    </form>
@endsection

@section('content')

    <div class="card">
        <div class="table-container">
            <table>
                <thead>
                    <tr>
                        <th class="checkbox-cell"><input type="checkbox" id="selectAll"></th>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Device Status</th>
                        <th>Activation Code</th>
                        <th>Subscription Status</th>
                        <th>Created</th>
                        <th class="text-right">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($users as $user)
                        <tr>
                            <td class="checkbox-cell">
                                <input type="checkbox" class="select-item" value="{{ $user->id }}">
                            </td>
                            <td>
                                <span style="font-weight: 600;">{{ $user->name }}</span>
                            </td>
                            <td>{{ $user->email }}</td>
                            <td>
                                @if($user->devices->count() > 0)
                                    @php $device = $user->devices->first(); @endphp
                                    <span class="badge {{ $device->is_blocked ? 'badge-danger' : 'badge-success' }}">
                                        {{ $device->is_blocked ? 'Device Blocked' : 'Device Locked' }}
                                    </span>
                                @else
                                    <span class="badge badge-warning">No Device Bound</span>
                                @endif
                            </td>
                            <td>
                                @if($user->activationCodes->count() > 0)
                                    <code style="font-family:monospace; background:rgba(255,255,255,0.05); padding:4px 8px; border-radius:4px; font-weight:700;">
                                        {{ $user->activationCodes->first()->code }}
                                    </code>
                                @else
                                    <span class="text-muted">—</span>
                                @endif
                            </td>
                            <td>
                                @if($user->activationCodes->count() > 0)
                                    @php $code = $user->activationCodes->first(); @endphp
                                    <span class="badge {{ $code->isValid() ? 'badge-success' : 'badge-danger' }}">
                                        {{ $code->status->value }}
                                    </span>
                                @else
                                    <span class="badge badge-danger">No Plan</span>
                                @endif
                            </td>
                            <td>{{ $user->created_at->format('M d, Y') }}</td>
                            <td class="text-right">
                                <div style="display:flex; justify-content:flex-end; gap:8px;">
                                    <a href="{{ route('admin.users.show', $user->id) }}" class="btn btn-secondary btn-sm">View Profile</a>
                                    
                                    <form action="{{ route('admin.users.toggle-status', $user->id) }}" method="POST">
                                        @csrf
                                        <button type="submit" class="btn {{ $user->is_active ? 'btn-danger' : 'btn-primary' }} btn-sm">
                                            {{ $user->is_active ? 'Deactivate' : 'Activate' }}
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center text-muted" style="padding:40px 0;">No users found matching query.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-4">
            {{ $users->appends(['search' => $search])->links() }}
        </div>
    </div>

@endsection
