@extends('layouts.app')
@section('title', 'تسجيل جلسة عصر جديدة')

@section('content')
<div class="row justify-content-center">
    <div class="col-xl-9">

        <!-- Header card -->
        <div class="card mb-4" style="background: linear-gradient(135deg, var(--olive-dark), var(--olive-mid)); color:#fff; border-radius:14px; padding:20px;">
            <div class="d-flex align-items-center gap-3">
                <span style="font-size:3rem;">🫒</span>
                <div>
                    <h4 class="mb-0" style="font-family:'Cairo',sans-serif; font-weight:800;">مشاشة تسجيل جلسة العصر</h4>
                    <small style="opacity:.8;">أدخل بيانات صاحب الزيتون وحامل الزيتون</small>
                </div>
            </div>
        </div>

        <form action="{{ route('sessions.store') }}" method="POST">
            @csrf

            <!-- Section 1: Owner info (مشاشة تسجيل من قبل صاحب الزيتون) -->
            <div class="card mb-4">
                <div class="card-header">
                    <i class="fas fa-user-tie"></i>
                    مشاشة تسجيل من قِبَل صاحب الزيتون
                </div>
                <div class="card-body">
                    <div class="row g-3">

                        <!-- Queue Number -->
                        <div class="col-md-3">
                            <label class="form-label"><i class="fas fa-hashtag text-success me-1"></i> رقم الدور *</label>
                            <input type="number" name="queue_number" class="form-control @error('queue_number') is-invalid @enderror"
                                   value="{{ $nextQueue ?? old('queue_number') }}" required min="1"
                                   style="font-size:1.4rem; font-weight:800; text-align:center; color:var(--olive-dark);">
                            @error('queue_number')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <!-- Owner Name -->
                        <div class="col-md-9">
                            <label class="form-label"><i class="fas fa-user text-success me-1"></i> اسم صاحب الزيتون *</label>
                            <div class="input-group">
                                <input type="text" name="owner_name_search" id="ownerSearch"
                                       class="form-control" placeholder="ابحث عن صاحب أو أضف جديداً..."
                                       value="{{ old('owner_name_search') }}" autocomplete="off">
                                <button type="button" class="btn btn-olive" data-bs-toggle="modal" data-bs-target="#newOwnerModal">
                                    <i class="fas fa-user-plus"></i> جديد
                                </button>
                            </div>
                            <input type="hidden" name="owner_id" id="ownerId" value="{{ old('owner_id') }}">
                            <div id="ownerSuggestions" class="list-group mt-1 shadow" style="position:absolute; z-index:999; min-width:300px;"></div>
                        </div>

                        <!-- City -->
                        <div class="col-md-4">
                            <label class="form-label"><i class="fas fa-city text-success me-1"></i> المدينة</label>
                            <input type="text" name="city" id="ownerCity" class="form-control" value="{{ old('city') }}" readonly style="background:#f8f9fa;">
                        </div>

                        <!-- Shares -->
                        <div class="col-md-4">
                            <label class="form-label"><i class="fas fa-share-alt text-success me-1"></i> الأسهم / الحصص</label>
                            <input type="text" name="shares" id="ownerShares" class="form-control" value="{{ old('shares') }}" placeholder="مثال: 1/2 أو 1">
                        </div>

                        <!-- Phone -->
                        <div class="col-md-4">
                            <label class="form-label"><i class="fas fa-phone text-success me-1"></i> رقم الهاتف</label>
                            <input type="text" name="phone" id="ownerPhone" class="form-control" value="{{ old('phone') }}" placeholder="07xxxxxxxx">
                        </div>

                    </div>
                </div>
            </div>

            <!-- Section 2: Olive carrier info -->
            <div class="card mb-4">
                <div class="card-header">
                    <i class="fas fa-truck"></i>
                    بيانات حامل الزيتون
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label"><i class="fas fa-user text-success me-1"></i> اسم حامل الزيتون</label>
                            <input type="text" name="carrier_name" class="form-control @error('carrier_name') is-invalid @enderror"
                                   value="{{ old('carrier_name') }}" placeholder="اسم من حمل الزيتون">
                            @error('carrier_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label"><i class="fas fa-weight text-success me-1"></i> كمية الزيتون الواصلة (كغم) *</label>
                            <div class="input-group">
                                <input type="number" name="olives_weight_kg" id="olivesWeightKg"
                                       class="form-control @error('olives_weight_kg') is-invalid @enderror"
                                       value="{{ old('olives_weight_kg') }}" step="0.5" min="0" required
                                       placeholder="0.0">
                                <span class="input-group-text fw-bold">كغم</span>
                            </div>
                            @error('olives_weight_kg')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label"><i class="fas fa-weight-hanging text-success me-1"></i> الكمية بالغرام (إضافية)</label>
                            <div class="input-group">
                                <input type="number" name="olives_weight_gram" id="olivesWeightGram"
                                       class="form-control" value="{{ old('olives_weight_gram', 0) }}" min="0" max="999">
                                <span class="input-group-text fw-bold">غرام</span>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label"><i class="fas fa-calendar text-success me-1"></i> تاريخ الاستلام</label>
                            <input type="date" name="session_date" class="form-control" value="{{ old('session_date', date('Y-m-d')) }}">
                        </div>
                        <div class="col-12">
                            <label class="form-label"><i class="fas fa-sticky-note text-success me-1"></i> ملاحظات</label>
                            <textarea name="notes" class="form-control" rows="2" placeholder="أي ملاحظات إضافية...">{{ old('notes') }}</textarea>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Section 3: Cashier section (مشاشة التي تظهر عند الكاشير) -->
            <div class="card mb-4" style="border: 2px solid var(--gold);">
                <div class="card-header" style="background: linear-gradient(135deg, #8a6420, var(--gold));">
                    <i class="fas fa-cash-register"></i>
                    مشاشة الكاشير — بيانات الاستلام والأجرة
                </div>
                <div class="card-body">
                    <div class="row g-3">

                        <div class="col-md-6">
                            <label class="form-label"><i class="fas fa-tint text-warning me-1"></i> كمية الزيت المستخرج (كغم)</label>
                            <div class="input-group">
                                <input type="number" name="extracted_oil_kg" id="extractedOilKg"
                                       class="form-control" value="{{ old('extracted_oil_kg') }}" step="0.1" min="0"
                                       placeholder="0.0" oninput="calcFee()">
                                <span class="input-group-text fw-bold">كغم</span>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label"><i class="fas fa-tint text-warning me-1"></i> الزيت المصفى (كغم)</label>
                            <div class="input-group">
                                <input type="number" name="filtered_oil_kg" class="form-control"
                                       value="{{ old('filtered_oil_kg') }}" step="0.1" min="0" placeholder="0.0">
                                <span class="input-group-text fw-bold">كغم</span>
                            </div>
                        </div>

                        <!-- Fee calculation -->
                        <div class="col-12">
                            <div class="p-3 rounded-3" style="background:rgba(201,168,76,.1); border:1.5px solid var(--gold-light);">
                                <h6 class="fw-bold mb-3" style="color:var(--olive-dark);">
                                    <i class="fas fa-calculator me-2"></i> حساب الأجرة
                                </h6>
                                <div class="row g-3 align-items-end">
                                    <div class="col-md-4">
                                        <label class="form-label">سعر الأجرة لكل كغم زيتون</label>
                                        <div class="input-group">
                                            <input type="number" name="fee_per_kg" id="feePerKg"
                                                   class="form-control" value="{{ old('fee_per_kg', $settings->fee_per_kg ?? 0.10) }}"
                                                   step="0.01" min="0" oninput="calcFee()">
                                            <span class="input-group-text">دينار/كغم</span>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label">أجرة إضافية</label>
                                        <div class="input-group">
                                            <input type="number" name="extra_fee" id="extraFee"
                                                   class="form-control" value="{{ old('extra_fee', 0) }}" step="0.01" min="0" oninput="calcFee()">
                                            <span class="input-group-text">دينار</span>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label fw-bold text-success">إجمالي الأجرة</label>
                                        <div class="input-group">
                                            <input type="number" name="total_fee" id="totalFee"
                                                   class="form-control fw-bold" value="{{ old('total_fee', 0) }}"
                                                   step="0.01" min="0"
                                                   style="background:rgba(74,124,47,.1); font-size:1.2rem; font-weight:800; color:var(--olive-dark);">
                                            <span class="input-group-text fw-bold">دينار</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">حالة الدفع</label>
                            <select name="payment_status" class="form-select">
                                <option value="pending" {{ old('payment_status') == 'pending' ? 'selected' : '' }}>⏳ في الانتظار</option>
                                <option value="paid" {{ old('payment_status') == 'paid' ? 'selected' : '' }}>✅ مدفوع</option>
                                <option value="partial" {{ old('payment_status') == 'partial' ? 'selected' : '' }}>🔶 جزئي</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">الحالة</label>
                            <select name="status" class="form-select">
                                <option value="received" {{ old('status') == 'received' ? 'selected' : '' }}>📥 مستلم</option>
                                <option value="processing" {{ old('status') == 'processing' ? 'selected' : '' }}>⚙️ جارٍ العصر</option>
                                <option value="done" {{ old('status') == 'done' ? 'selected' : '' }}>✅ منتهي</option>
                            </select>
                        </div>

                    </div>
                </div>
            </div>

            <!-- Actions -->
            <div class="d-flex gap-3 justify-content-end mb-4">
                <a href="{{ route('sessions.index') }}" class="btn btn-outline-secondary btn-lg px-4">
                    <i class="fas fa-arrow-right me-2"></i> إلغاء
                </a>
                <button type="submit" name="action" value="save" class="btn btn-olive btn-lg px-4">
                    <i class="fas fa-save me-2"></i> حفظ الجلسة
                </button>
                <button type="submit" name="action" value="save_print" class="btn btn-gold btn-lg px-4">
                    <i class="fas fa-print me-2"></i> حفظ وطباعة
                </button>
            </div>
        </form>
    </div>
</div>

<!-- New Owner Modal -->
<div class="modal fade" id="newOwnerModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header" style="background:linear-gradient(135deg, var(--olive-dark), var(--olive-mid)); color:#fff;">
                <h5 class="modal-title"><i class="fas fa-user-plus me-2"></i> إضافة صاحب زيتون جديد</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label class="form-label">الاسم الكامل *</label>
                    <input type="text" id="newOwnerName" class="form-control" placeholder="اسم صاحب الزيتون">
                </div>
                <div class="mb-3">
                    <label class="form-label">المدينة</label>
                    <input type="text" id="newOwnerCity" class="form-control" placeholder="المدينة / القرية">
                </div>
                <div class="mb-3">
                    <label class="form-label">رقم الهاتف</label>
                    <input type="text" id="newOwnerPhoneModal" class="form-control" placeholder="07xxxxxxxx">
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">إلغاء</button>
                <button type="button" class="btn btn-olive" onclick="saveNewOwner()">
                    <i class="fas fa-save me-1"></i> حفظ وتحديد
                </button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
// Fee auto-calculation
function calcFee() {
    const kg   = parseFloat(document.getElementById('olivesWeightKg').value) || 0;
    const rate = parseFloat(document.getElementById('feePerKg').value) || 0;
    const extra= parseFloat(document.getElementById('extraFee').value) || 0;
    document.getElementById('totalFee').value = (kg * rate + extra).toFixed(3);
}

// Owner search autocomplete
let searchTimeout;
document.getElementById('ownerSearch').addEventListener('input', function() {
    clearTimeout(searchTimeout);
    const q = this.value;
    if (q.length < 2) { document.getElementById('ownerSuggestions').innerHTML = ''; return; }
    searchTimeout = setTimeout(() => {
        fetch(`/api/owners/search?q=${encodeURIComponent(q)}`)
            .then(r => r.json())
            .then(data => {
                const box = document.getElementById('ownerSuggestions');
                box.innerHTML = '';
                data.forEach(o => {
                    const item = document.createElement('button');
                    item.type = 'button';
                    item.className = 'list-group-item list-group-item-action';
                    item.innerHTML = `<strong>${o.name}</strong> <small class="text-muted">— ${o.city || ''}</small>`;
                    item.onclick = () => selectOwner(o);
                    box.appendChild(item);
                });
            });
    }, 300);
});

function selectOwner(o) {
    document.getElementById('ownerSearch').value   = o.name;
    document.getElementById('ownerId').value        = o.id;
    document.getElementById('ownerCity').value      = o.city  || '';
    document.getElementById('ownerPhone').value     = o.phone || '';
    document.getElementById('ownerSuggestions').innerHTML = '';
}

function saveNewOwner() {
    const name  = document.getElementById('newOwnerName').value;
    const city  = document.getElementById('newOwnerCity').value;
    const phone = document.getElementById('newOwnerPhoneModal').value;
    if (!name) { alert('الاسم مطلوب'); return; }

    fetch('/api/owners', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content },
        body: JSON.stringify({ name, city, phone })
    }).then(r => r.json()).then(o => {
        selectOwner(o);
        bootstrap.Modal.getInstance(document.getElementById('newOwnerModal')).hide();
    });
}

// Close suggestions on outside click
document.addEventListener('click', e => {
    if (!e.target.closest('#ownerSearch')) document.getElementById('ownerSuggestions').innerHTML = '';
});
</script>
@endpush
