@extends('admin.layouts.app')

@section('title', 'Create Tournament')
@section('header_title', 'Create Tournament')
@section('header_subtitle', 'Add a new tournament or competition')

@section('content')

    <div class="card" style="max-width: 700px; margin: 0 auto;">
        <form action="{{ route('admin.tournaments.store') }}" method="POST">
            @csrf

            <div style="display:grid; grid-template-columns:1fr 1fr; gap:20px;">
                <div class="form-group">
                    <label for="name">Tournament Name (English)</label>
                    <input type="text" id="name" name="name" class="form-control" placeholder="World Cup 2026" required value="{{ old('name') }}">
                </div>

                <div class="form-group">
                    <label for="name_ar">Tournament Name (Arabic)</label>
                    <input type="text" id="name_ar" name="name_ar" class="form-control" placeholder="كأس العالم 2026" value="{{ old('name_ar') }}">
                </div>
            </div>

            <div class="form-group">
                <label for="logo_url">Logo URL</label>
                <input type="url" id="logo_url" name="logo_url" class="form-control" placeholder="https://cdn.example.com/tournament-logo.png" value="{{ old('logo_url') }}">
            </div>

            <div class="form-group">
                <label for="sort_order">Display Sort Order</label>
                <input type="number" id="sort_order" name="sort_order" class="form-control" required value="{{ old('sort_order', 0) }}">
            </div>

            <div class="form-group" style="display:flex; align-items:center; gap:10px; margin-top: 24px;">
                <input type="checkbox" id="is_active" name="is_active" value="1" checked style="width:20px; height:20px; accent-color:var(--accent-primary);">
                <label for="is_active" style="margin-bottom:0; cursor:pointer;">Active and visible in the application</label>
            </div>

            <div style="display:flex; justify-content:flex-end; gap:12px; margin-top:32px; border-top:1px solid var(--border-glass); padding-top:20px;">
                <a href="{{ route('admin.tournaments.index') }}" class="btn btn-secondary">Cancel</a>
                <button type="submit" class="btn btn-primary">Create Tournament</button>
            </div>
        </form>
    </div>

@endsection
