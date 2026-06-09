@extends('admin.layouts.app')

@section('title', 'Create Category')
@section('header_title', 'Create Category')
@section('header_subtitle', 'Add a new group classification for IPTV channels')

@section('content')

    <div class="card" style="max-width: 600px; margin: 0 auto;">
        <form action="{{ route('admin.categories.store') }}" method="POST" enctype="multipart/form-data">
            @csrf

            <div class="form-group">
                <label for="name">Category Name (English)</label>
                <input type="text" id="name" name="name" class="form-control" placeholder="Sports" required value="{{ old('name') }}" oninput="document.getElementById('slug').value = this.value.toLowerCase().replace(/[^a-z0-9 -]/g, '').replace(/\s+/g, '-').replace(/-+/g, '-')">
            </div>

            <div class="form-group">
                <label for="name_ar">Category Name (Arabic)</label>
                <input type="text" id="name_ar" name="name_ar" class="form-control" placeholder="رياضة" required value="{{ old('name_ar') }}">
            </div>

            <div class="form-group">
                <label for="slug">URL Slug</label>
                <input type="text" id="slug" name="slug" class="form-control" placeholder="sports" required value="{{ old('slug') }}">
            </div>

            <div class="form-group">
                <label for="type">Classification Type</label>
                <select id="type" name="type" class="form-control" required>
                    <option value="content_type" {{ old('type') == 'content_type' ? 'selected' : '' }}>Content Type (نوع المحتوى)</option>
                    <option value="country" {{ old('type') == 'country' ? 'selected' : '' }}>Country (الدولة)</option>
                    <option value="language" {{ old('type') == 'language' ? 'selected' : '' }}>Language (اللغة)</option>
                    <option value="continent" {{ old('type') == 'continent' ? 'selected' : '' }}>Continent (القارة)</option>
                    <option value="network" {{ old('type') == 'network' ? 'selected' : '' }}>Network / Brand (الشبكات والشركات الباثة)</option>
                </select>
            </div>

            <div class="form-group">
                <label for="icon">Icon Name (Material Design icon tag or image URL)</label>
                <input type="text" id="icon" name="icon" class="form-control" placeholder="sports_soccer" value="{{ old('icon') }}">
            </div>

            <div class="form-group">
                <label for="image_file">Upload Image File (Optional, overrides Icon text)</label>
                <input type="file" id="image_file" name="image_file" class="form-control" accept="image/*">
            </div>

            <div class="form-group">
                <label for="sort_order">Display Sort Order</label>
                <input type="number" id="sort_order" name="sort_order" class="form-control" required value="{{ old('sort_order', 0) }}">
            </div>

            <div class="form-group" style="display:flex; align-items:center; gap:10px; margin-top: 24px;">
                <input type="checkbox" id="is_active" name="is_active" value="1" checked style="width:20px; height:20px; accent-color:var(--accent-primary);">
                <label for="is_active" style="margin-bottom:0; cursor:pointer;">Active and available on client applications</label>
            </div>

            <div style="display:flex; justify-content:flex-end; gap:12px; margin-top:32px; border-top:1px solid var(--border-glass); padding-top:20px;">
                <a href="{{ route('admin.categories.index') }}" class="btn btn-secondary">Cancel</a>
                <button type="submit" class="btn btn-primary">Create Category</button>
            </div>
        </form>
    </div>

@endsection
