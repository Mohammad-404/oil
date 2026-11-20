@extends('layouts.app')
@section('title', 'تعديل الإيصال')
@section('page-title', 'تعديل الإيصال')

@section('content')
<div class="row justify-content-center">
<div class="col-lg-8">
<div class="card">
    <div class="card-header">
        <i class="fas fa-edit me-2"></i>تعديل إيصال: {{ $receipt->receipt_number }}
    </div>
    <div class="card-body">
        <form action="{{ route('receipts.update', $receipt) }}" method="POST">
            @csrf @method('PUT')
            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label">صاحب الزيتون</label>
                    <select name="owner_id" id="ownerSelect" class="form-select" required>
                        @foreach($owners as $owner)
                            <option value="{{ $owner->id }}"
                                    data-floor="{{ $owner->floor_number }}"
                                    {{ $receipt->owner_id == $owner->id ? 'selected' : '' }}>
                                {{ $owner->name }} - {{ $owner->city }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-6">
                    <label class="form-label">حامل الزيتون</label>
                    <input type="text" name="carrier_name" class="form-control" value="{{ $receipt->carrier_name }}" required>
                </div>
                <div class="col-md-3">
                    <label class="form-label">رقم الدور</label>
                    <input type="number" name="floor_number" id="floorInput" class="form-control" value="{{ $receipt->floor_number }}" readonly>
                </div>
                <div class="col-md-3">
                    <label class="form-label">الزيتون (كيلو)</label>
                    <input type="number" name="olive_weight_kg" step="0.1" class="form-control" value="{{ $receipt->olive_weight_kg }}" required>
                </div>
                @if(auth()->user()->isCashier())
                <div class="col-md-3">
                    <label class="form-label">كمية الزيت (كيلو)</label>
                    <input type="number" name="oil_weight_kg" id="oilWeightKg" step="0.1" class="form-control" value="{{ $receipt->oil_weight_kg }}">
                </div>
                <div class="col-md-3">
                    <label class="form-label">الزيت (غرام)</label>
                    <input type="number" name="oil_weight_gram" id="oilWeightGm" step="1" class="form-control" value="{{ $receipt->oil_weight_gram }}">
                </div>
                <div class="col-md-4">
                    <label class="form-label">الأجرة / كيلو</label>
                    <input type="number" name="fee_per_kg" id="feePerKg" step="0.01" class="form-control" value="{{ $receipt->fee_per_kg }}" required>
                </div>
                <div class="col-md-4">
                    <label class="form-label">المجموع</label>
                    <div class="input-group">
                        <input type="text" id="totalFee" class="form-control fw-bold text-success" readonly value="{{ $receipt->total_fee }}">
                        <span class="input-group-text">د.أ</span>
                    </div>
                </div>
                <div class="col-md-4">
                    <label class="form-label">الحالة</label>
                    <select name="status" class="form-select">
                        <option value="pending"    {{ $receipt->status=='pending'    ? 'selected':'' }}>في الانتظار</option>
                        <option value="processing" {{ $receipt->status=='processing' ? 'selected':'' }}>قيد المعالجة</option>
                        <option value="completed"  {{ $receipt->status=='completed'  ? 'selected':'' }}>مكتمل</option>
                        <option value="debt"       {{ $receipt->status=='debt'       ? 'selected':'' }}>دَين</option>
                    </select>
                </div>
                @else
                {{-- اليوزر العادي: يعدل الحالة فقط بين الانتظار والمعالجة --}}
                <input type="hidden" name="oil_weight_kg"   value="{{ $receipt->oil_weight_kg }}">
                <input type="hidden" name="oil_weight_gram" value="{{ $receipt->oil_weight_gram }}">
                <input type="hidden" name="fee_per_kg"      value="{{ $receipt->fee_per_kg }}">
                <div class="col-md-4">
                    <label class="form-label">الحالة</label>
                    @if(in_array($receipt->status, ['pending', 'processing']))
                        <select name="status" class="form-select">
                            <option value="pending"    {{ $receipt->status=='pending'    ? 'selected':'' }}>في الانتظار</option>
                            <option value="processing" {{ $receipt->status=='processing' ? 'selected':'' }}>قيد المعالجة</option>
                        </select>
                    @else
                        <select name="status" class="form-select">
                            <option value="{{ $receipt->status }}" selected>{{ $receipt->status_label }} (الحالي)</option>
                            <option value="pending">في الانتظار</option>
                            <option value="processing">قيد المعالجة</option>
                        </select>
                        <small class="text-muted">يمكن إعادة الحالة إلى: في الانتظار أو قيد المعالجة فقط</small>
                    @endif
                </div>
                @endif
                <div class="col-12">
                    <label class="form-label">ملاحظات</label>
                    <textarea name="notes" class="form-control" rows="2">{{ $receipt->notes }}</textarea>
                </div>
                <div class="col-12 d-flex gap-2 justify-content-end">
                    <a href="{{ route('receipts.show', $receipt) }}" class="btn btn-light">إلغاء</a>
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
@section('scripts')
<script>
    // رقم الدور يتغير تلقائياً مع صاحب الزيتون
    document.getElementById('ownerSelect').addEventListener('change', function() {
        const opt = this.options[this.selectedIndex];
        document.getElementById('floorInput').value = opt.dataset.floor || '';
    });

    // حساب المجموع
    ['oilWeightKg','oilWeightGm','feePerKg'].forEach(function(id) {
        document.getElementById(id)?.addEventListener('input', calc);
    });
    function calc() {
        const kg  = parseFloat(document.getElementById('oilWeightKg').value) || 0;
        const gm  = parseFloat(document.getElementById('oilWeightGm').value) || 0;
        const fee = parseFloat(document.getElementById('feePerKg').value)    || 0;
        document.getElementById('totalFee').value = ((kg + gm / 1000) * fee).toFixed(3);
    }
</script>
@endsection
