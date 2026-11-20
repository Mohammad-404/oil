@extends('layouts.app')
@section('title', 'إيصال جديد')
@section('page-title', 'إضافة إيصال جديد')

@section('content')
<div class="row justify-content-center">
<div class="col-lg-7">
<div class="card">
    <div class="card-header">
        <i class="fas fa-plus-circle me-2"></i>استمارة استلام الزيتون — حجز الدور
    </div>
    <div class="card-body">
        <div class="alert" style="background:#fff8e1; border:1px solid #f0c040; border-radius:10px; font-size:.88rem; color:#7a5c00">
            <i class="fas fa-info-circle me-1"></i>
            هذا الإيصال <strong>حجز دور فقط</strong>. كمية الزيت والأجرة تُسجَّل لاحقاً عند الكاشير.
        </div>

        <form action="{{ route('receipts.store') }}" method="POST">
            @csrf

            <div class="row g-3">

                <!-- صاحب الزيتون -->
                <div class="col-12">
                    <label class="form-label">🧑 اسم صاحب الزيتون <span class="text-danger">*</span></label>
                    <select name="owner_id" class="form-select @error('owner_id') is-invalid @enderror" required id="ownerSelect">
                        <option value="">-- اختر صاحب الزيتون --</option>
                        @foreach($owners as $owner)
                            <option value="{{ $owner->id }}"
                                data-city="{{ $owner->city }}"
                                data-street="{{ $owner->street }}"
                                data-phone="{{ $owner->phone }}"
                                data-floor="{{ $owner->floor_number }}"
                                {{ (old('owner_id', $selectedOwner?->id) == $owner->id) ? 'selected' : '' }}>
                                {{ $owner->name }} - {{ $owner->city }}
                            </option>
                        @endforeach
                    </select>
                    @error('owner_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    <div class="mt-1">
                        <a href="{{ route('owners.create') }}" class="text-success small">
                            <i class="fas fa-plus"></i> إضافة صاحب جديد
                        </a>
                    </div>
                </div>

                <!-- معلومات الشخص المختار -->
                <div class="col-12" id="ownerInfo" style="display:none">
                    <div class="alert" style="background:#e8f5d4; border:1px solid #7ab648; border-radius:10px;">
                        <div class="row g-2 small">
                            <div class="col-6"><i class="fas fa-city me-1" style="color:var(--olive-main)"></i><strong>المدينة:</strong> <span id="infoCity"></span></div>
                            <div class="col-6"><i class="fas fa-road me-1" style="color:var(--olive-main)"></i><strong>الشارع:</strong> <span id="infoStreet"></span></div>
                            <div class="col-6"><i class="fas fa-phone me-1" style="color:var(--olive-main)"></i><strong>الهاتف:</strong> <span id="infoPhone"></span></div>
                            <div class="col-6"><i class="fas fa-building me-1" style="color:var(--olive-main)"></i><strong>رقم الدور:</strong> <span id="infoFloor"></span></div>
                        </div>
                    </div>
                </div>

                <!-- حامل الزيتون + رقم الدور -->
                <div class="col-md-8">
                    <label class="form-label">🚚 اسم حامل الزيتون (الناقل) <span class="text-danger">*</span></label>
                    <input type="text" name="carrier_name" class="form-control @error('carrier_name') is-invalid @enderror"
                           value="{{ old('carrier_name') }}" placeholder="اسم الشخص الذي أحضر الزيتون" required>
                    @error('carrier_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="col-md-4">
                    <label class="form-label">🏢 رقم الدور</label>
                    <input type="number" name="floor_number" class="form-control" value="{{ old('floor_number') }}" id="floorInput" readonly>
                </div>

                <!-- كمية الزيتون -->
                <div class="col-12"><hr class="my-1">
                    <h6 class="fw-bold mb-0" style="color:var(--olive-dark)">🫒 كمية الزيتون الواردة</h6>
                </div>

                <div class="col-md-6">
                    <label class="form-label">كمية الزيتون (كيلو) <span class="text-danger">*</span></label>
                    <div class="input-group">
                        <input type="number" name="olive_weight_kg" step="0.1" min="0.1"
                               class="form-control @error('olive_weight_kg') is-invalid @enderror"
                               value="{{ old('olive_weight_kg') }}" placeholder="0.0" required>
                        <span class="input-group-text">كيلو</span>
                    </div>
                    @error('olive_weight_kg')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
                </div>

                <input type="hidden" name="fee_per_kg" value="{{ $fee_per_kg }}">

                <!-- ملاحظات -->
                <div class="col-12">
                    <label class="form-label">📝 ملاحظات</label>
                    <textarea name="notes" class="form-control" rows="2" placeholder="أي ملاحظات إضافية...">{{ old('notes') }}</textarea>
                </div>

                <!-- أزرار -->
                <div class="col-12 d-flex gap-2 justify-content-end mt-2">
                    <a href="{{ route('receipts.index') }}" class="btn btn-light">إلغاء</a>
                    <button type="submit" class="btn btn-olive px-4">
                        <i class="fas fa-save me-2"></i>حفظ الإيصال
                    </button>
                </div>

            </div>
        </form>
    </div>
</div>
</div>
</div>
@endsection

@section('scripts')
<script>
    const ownerSelect = document.getElementById('ownerSelect');
    const ownerInfo   = document.getElementById('ownerInfo');

    function showOwnerInfo() {
        const opt = ownerSelect.options[ownerSelect.selectedIndex];
        if (opt.value) {
            document.getElementById('infoCity').textContent   = opt.dataset.city   || '—';
            document.getElementById('infoStreet').textContent = opt.dataset.street || '—';
            document.getElementById('infoPhone').textContent  = opt.dataset.phone  || '—';
            document.getElementById('infoFloor').textContent  = opt.dataset.floor  || '—';
            document.getElementById('floorInput').value       = opt.dataset.floor  || '';
            ownerInfo.style.display = 'block';
        } else {
            ownerInfo.style.display = 'none';
        }
    }

    ownerSelect.addEventListener('change', showOwnerInfo);
    showOwnerInfo();
</script>
@endsection
