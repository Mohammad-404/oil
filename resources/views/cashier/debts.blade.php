@extends('layouts.app')
@section('title', 'قائمة الديون')
@section('page-title', 'الديون — الإيصالات المعلّقة')

@section('content')

{{-- ملخص --}}
<div class="row g-3 mb-3">
    <div class="col-6 col-md-3">
        <div class="card text-center border-danger">
            <div class="card-body py-3">
                <div style="font-size:1.6rem;font-weight:900;color:#b91c1c">{{ number_format($totalDebt,3) }} <small style="font-size:.7rem">د.أ</small></div>
                @if($totalDebtOilKg > 0)
                <div style="font-size:1rem;font-weight:800;color:#d97706">+ {{ number_format($totalDebtOilKg,2) }} <small style="font-size:.7rem">كيلو زيت</small></div>
                @endif
                <div class="small text-muted mt-1">إجمالي الديون النشطة</div>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="card text-center border-warning">
            <div class="card-body py-3">
                <div style="font-size:1.8rem;font-weight:900;color:#d97706">{{ $totalCount }}</div>
                <div class="small text-muted">إيصال معلّق</div>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="card text-center border-success">
            <div class="card-body py-3">
                <div style="font-size:1.6rem;font-weight:900;color:#16a34a">{{ number_format($paidTotal,3) }} <small style="font-size:.7rem">د.أ</small></div>
                @if($paidOilKg > 0)
                <div style="font-size:1rem;font-weight:800;color:#0891b2">+ {{ number_format($paidOilKg,2) }} <small style="font-size:.7rem">كيلو زيت</small></div>
                @endif
                <div class="small text-muted mt-1">إجمالي الديون المسدَّدة</div>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="card text-center" style="border-color:#86efac">
            <div class="card-body py-3">
                <div style="font-size:1.8rem;font-weight:900;color:#15803d">{{ $paidCount }}</div>
                <div class="small text-muted">دين مسدَّد</div>
            </div>
        </div>
    </div>
</div>

{{-- ════════════════════════════════════════ --}}
{{-- جدول الديون النشطة                       --}}
{{-- ════════════════════════════════════════ --}}

{{-- بحث الديون النشطة --}}
<form method="GET" action="{{ route('cashier.debts') }}" class="mb-3">
    {{-- نحافظ على فلاتر الديون المسدّدة --}}
    @if($paidSearch)   <input type="hidden" name="paid_search" value="{{ $paidSearch }}"> @endif
    @if($paidFromDate) <input type="hidden" name="paid_from"   value="{{ $paidFromDate }}"> @endif
    @if($paidToDate)   <input type="hidden" name="paid_to"     value="{{ $paidToDate }}"> @endif

    <div class="input-group">
        <span class="input-group-text bg-danger text-white border-danger">
            <i class="fas fa-exclamation-triangle"></i>
        </span>
        <input type="text" name="search" class="form-control"
               placeholder="بحث في الديون النشطة — اسم أو رقم إيصال..."
               value="{{ request('search') }}">
        <button type="submit" class="btn btn-danger"><i class="fas fa-search"></i></button>
        @if(request('search'))
        <a href="{{ route('cashier.debts') }}" class="btn btn-outline-secondary">✕</a>
        @endif
    </div>
</form>

<div class="card mb-5">
    <div class="card-header d-flex align-items-center justify-content-between"
         style="background:#fee2e2;color:#7f1d1d;font-weight:800">
        <span><i class="fas fa-exclamation-triangle me-2"></i>الإيصالات المعلّقة — ديون غير مسدَّدة</span>
        <span class="badge bg-danger">{{ $totalCount }}</span>
    </div>
    <div class="card-body p-0">
        <table class="table table-hover mb-0" style="font-size:.9rem">
            <thead style="background:#fef2f2">
                <tr>
                    <th style="color:#7f1d1d">رقم الإيصال</th>
                    <th style="color:#7f1d1d">صاحب الزيتون</th>
                    <th style="color:#7f1d1d">الناقل</th>
                    <th style="color:#7f1d1d">المبلغ المدين</th>
                    <th style="color:#7f1d1d">التاريخ</th>
                    <th style="color:#7f1d1d">إجراء</th>
                </tr>
            </thead>
            <tbody>
                @forelse($receipts as $receipt)
                <tr>
                    <td class="fw-bold" style="color:#7f1d1d">{{ $receipt->receipt_number }}</td>
                    <td>
                        {{ $receipt->owner->name ?? '—' }}
                        <small class="text-muted d-block">دور: {{ $receipt->owner->floor_number ?? '—' }}</small>
                    </td>
                    <td>{{ $receipt->carrier_name }}</td>
                    <td>
                        @if($receipt->payment_method === 'oil_return')
                            <span style="font-weight:900;color:#d97706;font-size:1rem">
                                {{ number_format($receipt->oil_return_kg, 2) }} كيلو زيت
                            </span>
                            <small class="text-muted d-block">رديات زيت</small>
                        @else
                            <span style="font-weight:900;color:#b91c1c;font-size:1rem">
                                {{ number_format($receipt->total_fee, 3) }} د.أ
                            </span>
                            @if($receipt->can_total > 0)
                            <small class="text-muted d-block">
                                عصر {{ number_format($receipt->press_fee,3) }} + تنك {{ number_format($receipt->can_total,3) }}
                            </small>
                            @endif
                            @if($receipt->discount_amount > 0)
                            <small class="text-danger d-block">خصم: −{{ number_format($receipt->discount_amount,3) }} د.أ</small>
                            @endif
                        @endif
                    </td>
                    <td class="text-muted small">{{ $receipt->created_at->format('Y/m/d') }}</td>
                    <td>
                        <div class="btn-group btn-group-sm">
                            <a href="{{ route('cashier.debt', $receipt) }}"
                               class="btn btn-outline-danger fw-bold" title="عرض تفاصيل الدين">
                                <i class="fas fa-eye me-1"></i>عرض
                            </a>
                            <a href="{{ route('cashier.debt-print', $receipt) }}" target="_blank"
                               class="btn btn-outline-secondary" title="طباعة">
                                <i class="fas fa-print"></i>
                            </a>
                            <button type="button" class="btn btn-success fw-bold"
                                    onclick="confirmPaid({{ $receipt->id }}, '{{ $receipt->receipt_number }}', {{ number_format($receipt->total_fee, 3) }})">
                                <i class="fas fa-check-circle me-1"></i>تم السداد
                            </button>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="text-center text-muted py-5">
                        <i class="fas fa-check-circle fa-3x mb-2 d-block text-success"></i>
                        لا توجد ديون — جميع الإيصالات مسدَّدة ✓
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

@if($receipts->hasPages())
<div class="mb-4 d-flex justify-content-center">
    {{ $receipts->links() }}
</div>
@endif

{{-- نموذج تسديد مخفي --}}
<form id="paidForm" method="POST" action="">
    @csrf
</form>

{{-- ════════════════════════════════════════ --}}
{{-- جدول الديون المسدَّدة                    --}}
{{-- ════════════════════════════════════════ --}}

<div class="d-flex align-items-center mb-3 gap-2">
    <span class="fw-bold" style="color:#15803d;font-size:1.05rem">
        <i class="fas fa-check-double me-1"></i>الديون المسدَّدة
    </span>
    <span class="badge bg-success">{{ $paidCount }}</span>
</div>

{{-- فلتر الديون المسدَّدة --}}
<form method="GET" action="{{ route('cashier.debts') }}" class="card card-body mb-3 py-3" id="paidFilterForm">
    {{-- نحافظ على بحث الديون النشطة --}}
    @if(request('search')) <input type="hidden" name="search" value="{{ request('search') }}"> @endif

    <div class="row g-2 align-items-end">
        <div class="col-md-4">
            <label class="form-label small fw-bold mb-1">بحث</label>
            <input type="text" name="paid_search" class="form-control form-control-sm"
                   placeholder="اسم أو رقم إيصال..." value="{{ $paidSearch }}">
        </div>
        <div class="col-md-3">
            <label class="form-label small fw-bold mb-1">من تاريخ السداد</label>
            <input type="date" name="paid_from" class="form-control form-control-sm" value="{{ $paidFromDate }}">
        </div>
        <div class="col-md-3">
            <label class="form-label small fw-bold mb-1">إلى تاريخ السداد</label>
            <input type="date" name="paid_to" class="form-control form-control-sm" value="{{ $paidToDate }}">
        </div>
        <div class="col-md-2 d-flex gap-1">
            <button type="submit" class="btn btn-success btn-sm flex-fill">
                <i class="fas fa-search me-1"></i>بحث
            </button>
            @if($paidSearch || $paidFromDate || $paidToDate)
            <a href="{{ route('cashier.debts') }}" class="btn btn-outline-secondary btn-sm">✕</a>
            @endif
        </div>
    </div>
</form>

<div class="card">
    <div class="card-header d-flex align-items-center justify-content-between"
         style="background:#f0fdf4;color:#15803d;font-weight:800;border-bottom:1px solid #86efac">
        <span><i class="fas fa-check-double me-2"></i>الديون التي تم سدادها</span>
        @if($paidSearch || $paidFromDate || $paidToDate)
        <span class="badge bg-success">{{ $paidReceipts->total() }} نتيجة</span>
        @endif
    </div>
    <div class="card-body p-0">
        <table class="table table-hover mb-0" style="font-size:.9rem">
            <thead style="background:#f0fdf4">
                <tr>
                    <th style="color:#15803d">رقم الإيصال</th>
                    <th style="color:#15803d">صاحب الزيتون</th>
                    <th style="color:#15803d">الناقل</th>
                    <th style="color:#15803d">المبلغ المسدَّد</th>
                    <th style="color:#15803d">تاريخ الدين</th>
                    <th style="color:#15803d">تاريخ السداد</th>
                    <th style="color:#15803d">الملاحظات</th>
                    <th style="color:#15803d">إجراء</th>
                </tr>
            </thead>
            <tbody>
                @forelse($paidReceipts as $receipt)
                <tr>
                    <td class="fw-bold" style="color:#15803d">{{ $receipt->receipt_number }}</td>
                    <td>
                        {{ $receipt->owner->name ?? '—' }}
                        <small class="text-muted d-block">دور: {{ $receipt->owner->floor_number ?? '—' }}</small>
                    </td>
                    <td>{{ $receipt->carrier_name }}</td>
                    <td>
                        @if($receipt->payment_method === 'oil_return')
                            <span style="font-weight:900;color:#0891b2;font-size:1rem">
                                {{ number_format($receipt->oil_return_kg, 2) }} كيلو زيت
                            </span>
                        @else
                            <span style="font-weight:900;color:#16a34a;font-size:1rem">
                                {{ number_format($receipt->total_fee, 3) }} د.أ
                            </span>
                        @endif
                    </td>
                    <td class="text-muted small">{{ $receipt->created_at->format('Y/m/d') }}</td>
                    <td>
                        <span class="badge bg-success" style="font-size:.82rem">
                            {{ \Carbon\Carbon::parse($receipt->debt_paid_at)->format('Y/m/d H:i') }}
                        </span>
                    </td>
                    <td class="text-muted small" style="max-width:180px">
                        {{ $receipt->notes ?? '—' }}
                    </td>
                    <td>
                        <div class="btn-group btn-group-sm">
                            <a href="{{ route('receipts.print-row', $receipt) }}" target="_blank"
                               class="btn btn-outline-success" title="طباعة">
                                <i class="fas fa-print"></i>
                            </a>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="8" class="text-center text-muted py-4">
                        <i class="fas fa-inbox fa-2x mb-2 d-block"></i>
                        لا توجد ديون مسدَّدة بعد
                    </td>
                </tr>
                @endforelse
            </tbody>
            @if($paidReceipts->count() > 0)
            <tfoot style="background:#dcfce7;font-weight:700">
                <tr>
                    <td colspan="3" class="text-end text-muted small">الإجمالي:</td>
                    <td style="color:#15803d">{{ number_format($paidTotal, 3) }} د.أ</td>
                    <td colspan="4"></td>
                </tr>
            </tfoot>
            @endif
        </table>
    </div>
</div>

@if($paidReceipts->hasPages())
<div class="mt-3 d-flex justify-content-center">
    {{ $paidReceipts->links() }}
</div>
@endif

@section('scripts')
<script>
function confirmPaid(id, num, amount) {
    if (!confirm('تأكيد تسديد الدين؟\n\nإيصال رقم: ' + num + '\nالمبلغ: ' + amount + ' د.أ\n\nسيُضاف للأجور النقدية وتُسجَّل ملاحظة السداد تلقائياً.')) return;
    const form = document.getElementById('paidForm');
    form.action = '{{ route("cashier.mark-paid", ":id") }}'.replace(':id', id);
    form.submit();
}
</script>
@endsection

@endsection
