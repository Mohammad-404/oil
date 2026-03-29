@extends('layouts.app')
@section('title', 'الإعدادات')
@section('page-title', 'إعدادات المعصرة')

@section('content')
<div class="row justify-content-center">
<div class="col-lg-7">
<div class="card">
    <div class="card-header"><i class="fas fa-cog me-2"></i>إعدادات النظام</div>
    <div class="card-body">
        <form action="{{ route('settings.update') }}" method="POST">
            @csrf
            <div class="row g-3">
                <div class="col-12">
                    <label class="form-label">🫒 اسم المعصرة</label>
                    <input type="text" name="press_name" class="form-control" value="{{ $settings['press_name'] ?? 'معصرة الزيتون' }}" placeholder="اسم المعصرة">
                </div>
                <div class="col-md-6">
                    <label class="form-label">💰 الأجرة الافتراضية / كيلو (د.أ)</label>
                    <input type="number" name="fee_per_kg" class="form-control" step="0.01" value="{{ $settings['fee_per_kg'] ?? '2.00' }}" placeholder="2.00">
                    <small class="text-muted">هذه القيمة ستظهر تلقائياً عند إنشاء إيصال جديد</small>
                </div>
                <div class="col-md-6">
                    <label class="form-label">📞 هاتف المعصرة</label>
                    <input type="text" name="press_phone" class="form-control" value="{{ $settings['press_phone'] ?? '' }}" placeholder="07xxxxxxxx">
                </div>
                <div class="col-12">
                    <label class="form-label">📍 عنوان المعصرة</label>
                    <input type="text" name="press_address" class="form-control" value="{{ $settings['press_address'] ?? '' }}" placeholder="عنوان المعصرة">
                </div>
                <div class="col-12 d-flex gap-2 justify-content-end mt-2">
                    <button type="submit" class="btn btn-olive px-4">
                        <i class="fas fa-save me-2"></i>حفظ الإعدادات
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

<div class="card mt-3">
    <div class="card-header"><i class="fas fa-info-circle me-2"></i>معلومات النظام</div>
    <div class="card-body small text-muted">
        <div class="row">
            <div class="col-6"><strong>الإصدار:</strong> 1.0.0</div>
            <div class="col-6"><strong>Laravel:</strong> 10.x</div>
            <div class="col-12 mt-2">نظام إدارة معصرة الزيتون — جميع الحقوق محفوظة</div>
        </div>
    </div>
</div>
</div>
</div>
@endsection
