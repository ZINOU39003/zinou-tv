@extends('admin.layouts.app')

@section('title', 'Generate Activation Codes')
@section('header_title', 'Generate Codes')
@section('header_subtitle', 'Generate single or batch activation codes for users')

@section('content')

    <div class="card" style="max-width: 600px; margin: 0 auto;">
        <form action="{{ route('admin.codes.store') }}" method="POST">
            @csrf

            <div class="form-group">
                <label for="duration">Plan Duration</label>
                <select id="duration" name="duration" class="form-control" required>
                    <option value="">Select Plan Duration</option>
                    @foreach($durations as $duration)
                        <option value="{{ $duration->value }}">{{ $duration->getLabel() }} ({{ $duration->getDaysCount() }} Days)</option>
                    @endforeach
                </select>
            </div>

            <div class="form-group">
                <label for="count">Quantity to Generate (1 to 100)</label>
                <input type="number" id="count" name="count" class="form-control" min="1" max="100" required value="{{ old('count', 1) }}">
            </div>

            <div class="form-group">
                <label for="notes">Notes / Batch Description (Optional)</label>
                <textarea id="notes" name="notes" class="form-control" rows="4" placeholder="For reseller Ahmed, or batch generated on May 31st..."></textarea>
            </div>

            <div style="display:flex; justify-content:flex-end; gap:12px; margin-top:32px; border-top:1px solid var(--border-glass); padding-top:20px;">
                <a href="{{ route('admin.codes.index') }}" class="btn btn-secondary">Cancel</a>
                <button type="submit" class="btn btn-primary">Generate Batch</button>
            </div>
        </form>
    </div>

@endsection
