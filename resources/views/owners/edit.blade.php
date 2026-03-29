@extends('layouts.app')
@section('title', 'تعديل بيانات')
@section('page-title', 'تعديل بيانات صاحب الزيتون')

@section('content')
<div class="row justify-content-center">
<div class="col-lg-7">
<div class="card">
    <div class="card-header"><i class="fas fa-edit me-2"></i>تعديل: {{ $owner->name }}</div>
    <div class="card-body">
        <form action="{{ route('owners.update', $owner) }}" method="POST">
            @csrf @method('PUT')
            <div class="row g-3">
                <div class="col-12">
                    <label class="form-label">الاسم الكامل <span class="text-danger">*</span></label>
                    <input type="text" name="name" class="form-control @error('name') is-invalid @enderror"
                           value="{{ old('name', $owner->name) }}" required>
                    @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-6">
                    <label class="form-label">المدينة <span class="text-danger">*</span></label>
                    <input type="text" name="city" class="form-control" value="{{ old('city', $owner->city) }}" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label">الشارع <span class="text-danger">*</span></label>
                    <input type="text" name="street" class="form-control" value="{{ old('street', $owner->street) }}" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label">رقم الهاتف <span class="text-danger">*</span></label>
                    <input type="text" name="phone" class="form-control" value="{{ old('phone', $owner->phone) }}" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label">رقم الدور</label>
                    <div class="input-group">
                        <input type="text" class="form-control fw-bold text-success fs-5 text-center"
                               value="{{ $owner->floor_number }}" readonly>
                        <span class="input-group-text"><i class="fas fa-hashtag"></i></span>
                    </div>
                    <div class="form-text text-muted">رقم الدور ثابت ولا يمكن تغييره</div>
                </div>
                <div class="col-12 d-flex gap-2 justify-content-end">
                    <a href="{{ route('owners.show', $owner) }}" class="btn btn-light">إلغاء</a>
                    <button type="submit" class="btn btn-olive px-4">
                        <i class="fas fa-save me-2"></i>حفظ التعديلات
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>
</div>
</div>
@endsection
