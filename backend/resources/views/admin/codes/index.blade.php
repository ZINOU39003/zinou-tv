@extends('admin.layouts.app')

@section('title', 'Activation Codes')
@section('header_title', 'Activation Codes')
@section('header_subtitle', 'Manage licensing codes and device registrations')

@section('actions')
    <div style="display:flex; gap:12px; align-items:center;">
        <form action="{{ route('admin.codes.index') }}" method="GET" style="display:flex; gap:10px;">
            <select name="status" class="form-control" style="width:140px;" onchange="this.form.submit()">
                <option value="">All Statuses</option>
                <option value="unused" {{ $status == 'unused' ? 'selected' : '' }}>Unused</option>
                <option value="active" {{ $status == 'active' ? 'selected' : '' }}>Active</option>
                <option value="expired" {{ $status == 'expired' ? 'selected' : '' }}>Expired</option>
                <option value="revoked" {{ $status == 'revoked' ? 'selected' : '' }}>Revoked</option>
            </select>
            
            <input type="text" name="search" class="form-control" placeholder="Search code or email..." value="{{ $search }}" style="width:200px;">
            <button type="submit" class="btn btn-secondary">Search</button>
        </form>

        <a href="{{ route('admin.codes.create') }}" class="btn btn-primary">Generate Codes</a>
    </div>
@endsection

@section('content')

    <div class="card">
        <div class="table-container">
            <table>
                <thead>
                    <tr>
                        <th>Activation Code</th>
                        <th>Duration</th>
                        <th>Status</th>
                        <th>Bound User</th>
                        <th>Device Bound</th>
                        <th>Expires At</th>
                        <th class="text-right">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($codes as $code)
                        <tr>
                            <td>
                                <code style="font-family:monospace; background:rgba(255,255,255,0.06); padding:6px 10px; border-radius:4px; font-weight:700; font-size:14px; letter-spacing:0.5px; color:#fff;">
                                    {{ $code->code }}
                                </code>
                            </td>
                            <td>{{ $code->duration->getLabel() }}</td>
                            <td>
                                <span class="badge {{ $code->status->value == 'unused' ? 'badge-info' : ($code->isValid() ? 'badge-success' : 'badge-danger') }}">
                                    {{ $code->status->value }}
                                </span>
                            </td>
                            <td>
                                @if($code->user)
                                    <a href="{{ route('admin.users.show', $code->user->id) }}" style="color:var(--accent-secondary); text-decoration:none; font-weight:500;">
                                        {{ $code->user->name }}
                                    </a>
                                @else
                                    <span class="text-muted">—</span>
                                @endif
                            </td>
                            <td>
                                @if($code->device)
                                    <span class="badge badge-warning" title="{{ $code->device->device_id }}">
                                        {{ $code->device->device_name }}
                                    </span>
                                @else
                                    <span class="text-muted">—</span>
                                @endif
                            </td>
                            <td>
                                @if($code->expires_at)
                                    <span style="font-family:monospace;">{{ $code->expires_at->format('Y-m-d H:i') }}</span>
                                @else
                                    <span class="text-muted">—</span>
                                @endif
                            </td>
                            <td class="text-right">
                                <div style="display:flex; justify-content:flex-end; gap:8px;">
                                    @if($code->device_id)
                                        <form action="{{ route('admin.codes.reset-device', $code->id) }}" method="POST" onsubmit="return confirm('Resetting this code will allow it to be used on a different device. The current device will be detached. Proceed?')">
                                            @csrf
                                            <button type="submit" class="btn btn-secondary btn-sm" title="Reset Device Lock">Reset Device</button>
                                        </form>
                                    @endif

                                    @if($code->status->value == 'active')
                                        <!-- Quick Extend Form -->
                                        <form action="{{ route('admin.codes.extend', $code->id) }}" method="POST" style="display:inline-flex; gap:4px;">
                                            @csrf
                                            <select name="duration" class="form-control" style="padding:4px 8px; font-size:11px; width:110px; height: 30px; border-radius: 4px;" required>
                                                <option value="1_month">+1 Month</option>
                                                <option value="3_months">+3 Months</option>
                                                <option value="6_months">+6 Months</option>
                                                <option value="1_year">+1 Year</option>
                                            </select>
                                            <button type="submit" class="btn btn-primary btn-sm" style="height: 30px; padding: 4px 10px;">Extend</button>
                                        </form>
                                    @endif

                                    @if($code->status->value !== 'revoked')
                                        <form action="{{ route('admin.codes.revoke', $code->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to revoke this license? It will block access immediately.')">
                                            @csrf
                                            <button type="submit" class="btn btn-danger btn-sm">Revoke</button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center text-muted" style="padding:40px 0;">No activation codes generated. Click Generate Codes to create some.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-4">
            {{ $codes->appends(['search' => $search, 'status' => $status])->links() }}
        </div>
    </div>

@endsection
