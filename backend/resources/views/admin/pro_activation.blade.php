@extends('admin.layouts.app')

@section('title', 'تفعيل حسابات PRO')
@section('header_title', 'تفعيل حسابات PRO')
@section('header_subtitle', 'تفعيل الباقات مباشرة لمستخدمين أو أجهزة دون الحاجة لتوليد أكواد وتوزيعها')

@section('content')

<div class="grid-3" style="grid-template-columns: 350px 1fr; gap: 28px; align-items: flex-start;">
    
    <!-- Direct Activation Form -->
    <div class="card" style="margin-bottom:0;">
        <div class="section-header" style="margin-bottom:20px;">
            <h2>
                <span class="icon">⚡</span>
                تفعيل حساب جديد
            </h2>
        </div>

        <form action="{{ route('admin.pro-activation.activate') }}" method="POST">
            @csrf

            <div class="form-group">
                <label for="email">البريد الإلكتروني أو كود تفعيل سابق</label>
                <input type="text" id="email" name="email" class="form-control" placeholder="user@gmail.com أو اسم المستخدم" required value="{{ old('email') }}">
                <p style="font-size: 11px; color: var(--text-muted); margin-top: 4px;">إذا كان البريد غير موجود بالنظام، فسيتم إنشاء حساب جديد له فوراً، وكلمة المرور الافتراضية ستكون كود التفعيل المتولد.</p>
            </div>

            <div class="form-group">
                <label for="duration">مدة الاشتراك</label>
                <select id="duration" name="duration" class="form-control" required>
                    <option value="">اختر مدة الباقة...</option>
                    @foreach($durations as $duration)
                        <option value="{{ $duration->value }}">{{ $duration->getLabel() }} ({{ $duration->getDaysCount() }} يوم)</option>
                    @endforeach
                </select>
            </div>

            <div class="form-group">
                <label for="notes">ملاحظات التفعيل (اختياري)</label>
                <textarea id="notes" name="notes" class="form-control" rows="3" placeholder="تفعيل مباشر، هدية، إلخ..."></textarea>
            </div>

            <button type="submit" class="btn btn-primary w-full" style="margin-top:16px; padding:12px;">⚡ تفعيل الحساب الفوري</button>
        </form>
    </div>

    <!-- Recent Direct Activations list -->
    <div class="card" style="margin-bottom:0;">
        <div class="section-header" style="margin-bottom:20px;">
            <h2>
                <span class="icon">📜</span>
                آخر الحسابات المفعّلة مؤخراً
            </h2>
        </div>

        <div class="table-container">
            <table>
                <thead>
                    <tr>
                        <th>البريد الإلكتروني للعميل</th>
                        <th>كود الدخول المتولد</th>
                        <th>الباقة</th>
                        <th>الجهاز المرتبط</th>
                        <th>تاريخ البدء</th>
                        <th>تاريخ الانتهاء</th>
                        <th>الحالة</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($recentActivations as $activation)
                        <tr>
                            <td>
                                @if($activation->user)
                                    <a href="{{ route('admin.users.show', $activation->user->id) }}" style="color:var(--accent-secondary); text-decoration:none; font-weight:700;">
                                        {{ $activation->user->email }}
                                    </a>
                                @else
                                    <span class="text-muted">مستخدم محذوف</span>
                                @endif
                            </td>
                            <td>
                                <code style="font-family:monospace; background:rgba(255,255,255,0.06); padding:4px 8px; border-radius:4px; font-weight:700; color:#fff;">
                                    {{ $activation->code }}
                                </code>
                            </td>
                            <td>{{ $activation->duration->getLabel() }}</td>
                            <td>
                                @if($activation->device)
                                    <span class="badge badge-warning" title="{{ $activation->device->device_id }}">
                                        {{ $activation->device->device_name }}
                                    </span>
                                @else
                                    <span class="text-muted" style="font-size:12px;">لم يرتبط بجهاز بعد</span>
                                @endif
                            </td>
                            <td><span style="font-size:12px; font-family:monospace;">{{ $activation->activated_at ? $activation->activated_at->format('Y-m-d') : '—' }}</span></td>
                            <td><span style="font-size:12px; font-family:monospace;">{{ $activation->expires_at ? $activation->expires_at->format('Y-m-d') : '—' }}</span></td>
                            <td>
                                <span class="badge {{ $activation->isValid() ? 'badge-success' : 'badge-danger' }}">
                                    {{ $activation->isValid() ? 'نشط PRO' : 'منتهي' }}
                                </span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center text-muted" style="padding:40px 0;">لا توجد حسابات مفعّلة مؤخراً بشكل مباشر.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>

@endsection
