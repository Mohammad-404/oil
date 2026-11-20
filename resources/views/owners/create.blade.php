@extends('layouts.app')
@section('title', 'إضافة صاحب زيتون')
@section('page-title', 'إضافة صاحب زيتون جديد')

@section('content')
<div class="row justify-content-center">
<div class="col-lg-7">
<div class="card">
    <div class="card-header"><i class="fas fa-user-plus me-2"></i>بيانات صاحب الزيتون</div>
    <div class="card-body">
        <form action="{{ route('owners.store') }}" method="POST">
            @csrf
            <div class="row g-3">
                <div class="col-12">
                    <label class="form-label">الاسم الكامل <span class="text-danger">*</span></label>
                    <input type="text" name="name" class="form-control @error('name') is-invalid @enderror"
                           value="{{ old('name') }}" placeholder="اسم صاحب الزيتون" required>
                    @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-6">
                    <label class="form-label">المدينة <span class="text-danger">*</span></label>
                    <input type="text" name="city" class="form-control @error('city') is-invalid @enderror"
                           value="{{ old('city') }}" placeholder="المدينة" required>
                    @error('city')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <input type="hidden" name="street" value="">
                <div class="col-md-6">
                    <label class="form-label">رقم الهاتف <span class="text-danger">*</span></label>
                    <input type="text" name="phone" class="form-control @error('phone') is-invalid @enderror"
                           value="{{ old('phone') }}" placeholder="07xxxxxxxx" required>
                    @error('phone')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-6">
                    <label class="form-label">رقم الدور</label>
                    <div class="input-group">
                        <input type="text" class="form-control fw-bold text-success fs-5 text-center"
                               value="{{ (\App\Models\Owner::max('floor_number') ?? 0) + 1 }}" readonly>
                        <span class="input-group-text"><i class="fas fa-magic"></i></span>
                    </div>
                    <div class="form-text text-muted">يُعيَّن تلقائياً عند الحفظ</div>
                </div>
                <div class="col-12 d-flex gap-2 justify-content-end">
                    <a href="{{ route('owners.index') }}" class="btn btn-light">إلغاء</a>
                    <button type="submit" class="btn btn-olive px-4">
                        <i class="fas fa-save me-2"></i>حفظ
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>
</div>
</div>
@endsection
