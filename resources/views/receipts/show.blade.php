@extends('layouts.app')
@section('title', 'إيصال ' . $receipt->receipt_number)
@section('page-title', 'تفاصيل الإيصال')

@section('content')
<div class="row g-3">
    <!-- تفاصيل الإيصال -->
    <div class="col-lg-8">
        <div class="card mb-3">
            <div class="card-header d-flex justify-content-between align-items-center">
                <span><i class="fas fa-file-alt me-2"></i>إيصال رقم: <strong class="text-warning">{{ $receipt->receipt_number }}</strong></span>
                <span class="badge badge-{{ $receipt->status }} rounded-pill px-3 py-2 fs-6">{{ $receipt->status_label }}</span>
            </div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-md-6">
                        <div class="p-3 rounded-3" style="background:#f0f7e8; border:1px solid #c8e6a0">
                            <h6 class="fw-bold text-success mb-3"><i class="fas fa-user me-2"></i>بيانات صاحب الزيتون</h6>
                            <table class="table table-sm table-borderless mb-0">
                                <tr><td class="text-muted">الاسم</td><td><strong>{{ $receipt->owner->name }}</strong></td></tr>
                                <tr><td class="text-muted">المدينة</td><td>{{ $receipt->owner->city }}</td></tr>
                                <tr><td class="text-muted">الشارع</td><td>{{ $receipt->owner->street }}</td></tr>
                                <tr><td class="text-muted">الهاتف</td><td>{{ $receipt->owner->phone }}</td></tr>
                                <tr><td class="text-muted">رقم الدور</td><td>{{ $receipt->floor_number ?? '—' }}</td></tr>
                            </table>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="p-3 rounded-3" style="background:#fff8e7; border:1px solid #fcd34d">
                            <h6 class="fw-bold mb-3" style="color:#92400e"><i class="fas fa-weight me-2"></i>بيانات الزيتون</h6>
                            <table class="table table-sm table-borderless mb-0">
                                <tr><td class="text-muted">حامل الزيتون</td><td><strong>{{ $receipt->carrier_name }}</strong></td></tr>
                                <tr><td class="text-muted">كمية الزيتون</td><td><strong class="text-success fs-5">{{ number_format($receipt->olive_weight_kg, 1) }} كيلو</strong></td></tr>
                                @if(auth()->user()->isCashier())
                                <tr><td class="text-muted">الأجرة/كيلو</td><td>{{ number_format($receipt->fee_per_kg, 2) }} د.أ</td></tr>
                                @if($receipt->discount_amount > 0)
                                <tr><td class="text-muted">قبل الخصم</td><td>{{ number_format((float)$receipt->press_fee + (float)$receipt->can_total, 3) }} د.أ</td></tr>
                                <tr><td class="text-muted">الخصم</td><td>−{{ number_format($receipt->discount_amount, 3) }} د.أ</td></tr>
                                @endif
                                <tr><td class="text-muted fw-bold">إجمالي الأجرة</td><td><strong class="text-danger fs-5">{{ number_format($receipt->total_fee, 2) }} د.أ</strong></td></tr>
                                @endif
                            </table>
                        </div>
                    </div>

                    @if($receipt->oil_weight_kg && auth()->user()->isCashier())
                    <div class="col-12">
                        <div class="p-3 rounded-3" style="background:#e0f2fe; border:1px solid #7dd3fc">
                            <h6 class="fw-bold mb-2" style="color:#0c4a6e"><i class="fas fa-oil-can me-2"></i>نتيجة العصر</h6>
                            <div class="row">
                                <div class="col-6">
                                    <span class="text-muted small">كمية الزيت:</span>
                                    <div class="fs-4 fw-bold text-primary">
                                        {{ number_format($receipt->oil_weight_kg, 1) }} كيلو
                                        @if($receipt->oil_weight_gram)
                                            + {{ $receipt->oil_weight_gram }} غم
                                        @endif
                                    </div>
                                </div>
                                <div class="col-6">
                                    <span class="text-muted small">نسبة الاستخلاص:</span>
                                    <div class="fs-4 fw-bold text-info">
                                        {{ number_format(($receipt->oil_weight_kg / $receipt->olive_weight_kg) * 100, 1) }}%
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endif

                    @if($receipt->notes)
                    <div class="col-12">
                        <label class="form-label">ملاحظات</label>
                        <div class="p-3 bg-light rounded">{{ $receipt->notes }}</div>
                    </div>
                    @endif
                </div>
            </div>
        </div>

        @if($receipt->status === 'pending' && auth()->user()->isCashier())
        <div class="alert" style="background:#fff8e1;border:1px solid #f0c040;border-radius:10px;font-size:.88rem;color:#7a5c00">
            <i class="fas fa-info-circle me-1"></i>
            هذا الإيصال في الانتظار — كمية الزيت والأجرة تُدخل عند <a href="{{ route('cashier.index', ['floor_number' => $receipt->floor_number]) }}" class="fw-bold" style="color:#7a5c00">الكاشير</a>.
        </div>
        @endif
    </div>

    <!-- أزرار الإجراءات -->
    <div class="col-lg-4">
        <div class="card">
            <div class="card-header"><i class="fas fa-tools me-2"></i>إجراءات</div>
            <div class="card-body d-grid gap-2">
                <a href="{{ route('receipts.print', $receipt) }}" class="btn btn-info text-white" target="_blank">
                    <i class="fas fa-print me-2"></i>طباعة الإيصال
                </a>
                @if(auth()->user()->isCashier())
                <a href="{{ route('receipts.pdf', $receipt) }}" class="btn btn-danger">
                    <i class="fas fa-file-pdf me-2"></i>تحميل PDF
                </a>
                @endif
                <a href="{{ route('receipts.edit', $receipt) }}" class="btn btn-warning text-white">
                    <i class="fas fa-edit me-2"></i>تعديل الإيصال
                </a>
                <hr>
                <a href="{{ route('owners.show', $receipt->owner) }}" class="btn btn-outline-success">
                    <i class="fas fa-user me-2"></i>ملف صاحب الزيتون
                </a>
                <a href="{{ route('receipts.index') }}" class="btn btn-light">
                    <i class="fas fa-arrow-right me-2"></i>العودة للقائمة
                </a>
                @if(auth()->user()->isCashier())
                <hr>
                @if($receipt->status === 'pending')
                <form action="{{ route('receipts.destroy', $receipt) }}" method="POST"
                      onsubmit="return confirm('هل أنت متأكد من حذف الإيصال رقم {{ $receipt->receipt_number }}؟')">
                    @csrf @method('DELETE')
                    <button type="submit" class="btn btn-outline-danger w-100">
                        <i class="fas fa-trash me-2"></i>حذف الإيصال
                    </button>
                </form>
                @else
                <div class="alert alert-warning py-2 text-center" style="font-size:.83rem">
                    <i class="fas fa-lock me-1"></i>
                    لا يمكن حذف هذا الإيصال<br>
                    <small>الحذف مسموح فقط للإيصالات قيد الانتظار</small>
                </div>
                @endif
                @endif
            </div>
        </div>

        <div class="card mt-3">
            <div class="card-header"><i class="fas fa-info-circle me-2"></i>معلومات الإيصال</div>
            <div class="card-body small text-muted">
                <div class="mb-1"><strong>تاريخ الاستلام:</strong><br>{{ $receipt->created_at->format('Y/m/d H:i') }}</div>
                <div class="mb-1"><strong>آخر تحديث:</strong><br>{{ $receipt->updated_at->format('Y/m/d H:i') }}</div>
                <div><strong>رقم الإيصال:</strong><br><code>{{ $receipt->receipt_number }}</code></div>
            </div>
        </div>
    </div>
</div>
@endsection
