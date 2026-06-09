@extends('admin.layouts.app')

@section('title', 'إضافة باقة')
@section('header_title', 'إضافة باقة')
@section('header_subtitle', 'إنشاء باقة جديدة تابعة لشبكة بث')

@section('content')

    <div class="card" style="max-width: 600px; margin: 0 auto;">
        <form action="{{ route('admin.packages.store') }}" method="POST" enctype="multipart/form-data">
            @csrf

            <div class="form-group">
                <label for="category_id">الشبكة (Network)</label>
                <select id="category_id" name="category_id" class="form-control" required>
                    <option value="">— اختر الشبكة —</option>
                    @foreach($categories as $cat)
                        <option value="{{ $cat->id }}" {{ old('category_id') == $cat->id ? 'selected' : '' }}>{{ $cat->name_ar ?: $cat->name }}</option>
                    @endforeach
                </select>
            </div>

            <div class="form-group">
                <label for="name">اسم الباقة (بالإنجليزية)</label>
                <input type="text" id="name" name="name" class="form-control" placeholder="BEIN SPORT HD" required value="{{ old('name') }}" oninput="document.getElementById('slug').value = this.value.toLowerCase().replace(/[^a-z0-9 -]/g, '').replace(/\s+/g, '-').replace(/-+/g, '-')">
            </div>

            <div class="form-group">
                <label for="name_ar">اسم الباقة (بالعربية)</label>
                <input type="text" id="name_ar" name="name_ar" class="form-control" placeholder="بين سبورت HD" required value="{{ old('name_ar') }}">
            </div>

            <div class="form-group">
                <label for="slug">المعرّف (Slug)</label>
                <input type="text" id="slug" name="slug" class="form-control" placeholder="bein-sport-hd" required value="{{ old('slug') }}">
            </div>

            <div class="form-group">
                <label for="logo_url">رابط الشعار (URL)</label>
                <input type="text" id="logo_url" name="logo_url" class="form-control" placeholder="https://example.com/logo.png" value="{{ old('logo_url') }}">
            </div>

            <div class="form-group">
                <label for="image_file">أو رفع صورة الشعار</label>
                <input type="file" id="image_file" name="image_file" class="form-control" accept="image/*">
            </div>

            <div class="form-group">
                <label for="sort_order">ترتيب العرض</label>
                <input type="number" id="sort_order" name="sort_order" class="form-control" required value="{{ old('sort_order', 0) }}">
            </div>

            <div class="form-group" style="display:flex; align-items:center; gap:10px; margin-top: 24px;">
                <input type="checkbox" id="is_active" name="is_active" value="1" checked style="width:20px; height:20px; accent-color:var(--accent-primary);">
                <label for="is_active" style="margin-bottom:0; cursor:pointer;">نشطة ومتاحة في التطبيقات</label>
            </div>

            <div style="display:flex; justify-content:flex-end; gap:12px; margin-top:32px; border-top:1px solid var(--border-glass); padding-top:20px;">
                <a href="{{ route('admin.packages.index') }}" class="btn btn-secondary">إلغاء</a>
                <button type="submit" class="btn btn-primary">إنشاء الباقة</button>
            </div>
        </form>
    </div>

@endsection
