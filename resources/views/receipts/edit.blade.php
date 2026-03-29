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
                    <select name="owner_id" class="form-select" required>
                        @foreach($owners as $owner)
                            <option value="{{ $owner->id }}" {{ $receipt->owner_id == $owner->id ? 'selected' : '' }}>
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
                    <input type="number" name="floor_number" class="form-control" value="{{ $receipt->floor_number }}">
                </div>
                <div class="col-md-3">
                    <label class="form-label">الزيتون (كيلو)</label>
                    <input type="number" name="olive_weight_kg" id="oliveWeight" step="0.1" class="form-control" value="{{ $receipt->olive_weight_kg }}" required>
                </div>
                <div class="col-md-3">
                    <label class="form-label">كمية الزيت (كيلو)</label>
                    <input type="number" name="oil_weight_kg" step="0.1" class="form-control" value="{{ $receipt->oil_weight_kg }}">
                </div>
                <div class="col-md-3">
                    <label class="form-label">الزيت (غرام)</label>
                    <input type="number" name="oil_weight_gram" step="1" class="form-control" value="{{ $receipt->oil_weight_gram }}">
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
                    </select>
                </div>
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
    document.getElementById('oliveWeight').addEventListener('input', calc);
    document.getElementById('feePerKg').addEventListener('input', calc);
    function calc() {
        const w = parseFloat(document.getElementById('oliveWeight').value)||0;
        const f = parseFloat(document.getElementById('feePerKg').value)||0;
        document.getElementById('totalFee').value = (w*f).toFixed(2);
    }
</script>
@endsection
