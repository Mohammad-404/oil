@extends('layouts.app')
@section('title', 'جدول حساب الأجرة')

@section('content')
<!-- Header -->
<div class="card mb-4" style="background: linear-gradient(135deg, #8a6420, var(--gold)); color:#fff; border-radius:14px; padding:20px;">
    <div class="d-flex align-items-center gap-3">
        <span style="font-size:3rem;">💰</span>
        <div>
            <h4 class="mb-0" style="font-family:'Cairo',sans-serif; font-weight:800;">
                جدول حساب أجرة صاحب/ي الزيتون
            </h4>
            <small style="opacity:.85;">الزيت يتم الحساب مباشرة — مع إمكانية الحساب</small>
        </div>
    </div>
</div>

<!-- Filter -->
<div class="card mb-4">
    <div class="card-header"><i class="fas fa-filter"></i> تصفية الحسابات</div>
    <div class="card-body">
        <form method="GET" class="row g-3 align-items-end">
            <div class="col-md-3">
                <label class="form-label">من تاريخ</label>
                <input type="date" name="from" class="form-control" value="{{ request('from', date('Y-m-01')) }}">
            </div>
            <div class="col-md-3">
                <label class="form-label">إلى تاريخ</label>
                <input type="date" name="to" class="form-control" value="{{ request('to', date('Y-m-d')) }}">
            </div>
            <div class="col-md-3">
                <label class="form-label">صاحب الزيتون</label>
                <select name="owner_id" class="form-select">
                    <option value="">الجميع</option>
                    @foreach($owners as $owner)
                        <option value="{{ $owner->id }}" {{ request('owner_id') == $owner->id ? 'selected' : '' }}>
                            {{ $owner->name }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3">
                <button type="submit" class="btn btn-gold w-100">
                    <i class="fas fa-calculator me-1"></i> حساب
                </button>
            </div>
        </form>
    </div>
</div>

<!-- جدول الأجور - حدول الأجرة فقط (Section 1 from image) -->
<div class="card mb-4">
    <div class="card-header" style="background: linear-gradient(135deg, var(--olive-dark), var(--olive-mid));">
        <i class="fas fa-table"></i>
        ① جدول الأجرة فقط
        <span class="badge bg-light text-dark ms-2">{{ $sessions->count() }} جلسة</span>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead>
                    <tr>
                        <th>الكمية (كغم)</th>
                        <th>السعر (دينار/كغم)</th>
                        <th>المجموع (دينار)</th>
                        <th>صاحب الزيتون</th>
                        <th>رقم الدور</th>
                        <th>التاريخ</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($sessions as $session)
                    <tr>
                        <td>
                            <strong>{{ number_format($session->olives_weight_kg, 1) }}</strong>
                            @if($session->olives_weight_gram)
                                <small class="text-muted"> + {{ $session->olives_weight_gram }}غ</small>
                            @endif
                        </td>
                        <td>{{ number_format($session->fee_per_kg, 3) }}</td>
                        <td class="fw-bold text-success">{{ number_format($session->total_fee, 3) }}</td>
                        <td>{{ $session->owner->name }}</td>
                        <td><span class="badge badge-olive">{{ $session->queue_number }}</span></td>
                        <td>{{ $session->session_date->format('Y/m/d') }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="text-center py-4 text-muted">لا توجد بيانات في الفترة المحددة</td>
                    </tr>
                    @endforelse
                </tbody>
                <tfoot>
                    <tr style="background:#f0f7e8; font-weight:800;">
                        <td>{{ number_format($sessions->sum('olives_weight_kg'), 1) }} كغم</td>
                        <td>—</td>
                        <td class="text-success fs-5">{{ number_format($sessions->sum('total_fee'), 3) }} د</td>
                        <td colspan="3"><strong>المجموع الكلي</strong></td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
</div>

<!-- الأجرة بالكيلو والغرام (Section 2 from image) -->
<div class="row g-4">
    <div class="col-lg-6">
        <div class="card">
            <div class="card-header" style="background: linear-gradient(135deg, #8a6420, var(--gold));">
                <i class="fas fa-weight-hanging"></i>
                ② الأجرة (بالكيلو والغرام)
            </div>
            <div class="card-body p-0">
                <table class="table mb-0">
                    <thead>
                        <tr>
                            <th>الكمية (كغم)</th>
                            <th>الكيلو</th>
                            <th>الغرام</th>
                            <th>الإجمالي</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($sessions as $session)
                        @php
                            $fee = $session->total_fee;
                            $feeKg = floor($fee);
                            $feeGram = round(($fee - $feeKg) * 1000);
                        @endphp
                        <tr>
                            <td>{{ number_format($session->olives_weight_kg, 1) }}</td>
                            <td class="fw-bold">{{ $feeKg }}</td>
                            <td class="text-muted">{{ $feeGram }}</td>
                            <td>{{ number_format($fee, 3) }} د</td>
                        </tr>
                        @endforeach
                    </tbody>
                    <tfoot>
                        @php
                            $totalFee = $sessions->sum('total_fee');
                            $totalKg = floor($totalFee);
                            $totalGram = round(($totalFee - $totalKg) * 1000);
                        @endphp
                        <tr style="background:#fff3cd; font-weight:800;">
                            <td>المجموع</td>
                            <td class="text-success">{{ $totalKg }}</td>
                            <td class="text-warning">{{ $totalGram }}</td>
                            <td class="text-success fs-5">{{ number_format($totalFee, 3) }} د</td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>

    <!-- Summary Box -->
    <div class="col-lg-6">
        <div class="card h-100" style="border: 2px solid var(--gold);">
            <div class="card-header" style="background: linear-gradient(135deg, var(--olive-dark), var(--olive-mid));">
                <i class="fas fa-chart-pie"></i> ملخص الحسابات
            </div>
            <div class="card-body">
                <div class="d-flex justify-content-between mb-3 pb-3 border-bottom">
                    <span class="text-muted">عدد الجلسات</span>
                    <strong class="fs-5">{{ $sessions->count() }}</strong>
                </div>
                <div class="d-flex justify-content-between mb-3 pb-3 border-bottom">
                    <span class="text-muted">إجمالي الزيتون</span>
                    <strong class="fs-5">{{ number_format($sessions->sum('olives_weight_kg'), 1) }} كغم</strong>
                </div>
                <div class="d-flex justify-content-between mb-3 pb-3 border-bottom">
                    <span class="text-muted">إجمالي الزيت المستخرج</span>
                    <strong class="fs-5">{{ number_format($sessions->sum('extracted_oil_kg'), 1) }} كغم</strong>
                </div>
                <div class="d-flex justify-content-between mb-4 pb-3 border-bottom">
                    <span class="text-muted">إجمالي الزيت المصفى</span>
                    <strong class="fs-5">{{ number_format($sessions->sum('filtered_oil_kg'), 1) }} كغم</strong>
                </div>

                <div class="p-3 rounded-3 text-center" style="background: linear-gradient(135deg, var(--olive-dark), var(--olive-mid)); color:#fff;">
                    <small style="opacity:.8;">إجمالي الأجرة المستحقة</small>
                    <div style="font-size: 2.2rem; font-weight: 900; font-family:'Cairo',sans-serif;">
                        {{ number_format($sessions->sum('total_fee'), 3) }}
                        <small style="font-size:1rem;">دينار</small>
                    </div>
                    <small>مدفوع: {{ number_format($sessions->where('payment_status','paid')->sum('total_fee'), 3) }} د</small>
                    |
                    <small>متبقي: {{ number_format($sessions->whereIn('payment_status',['pending','partial'])->sum('total_fee'), 3) }} د</small>
                </div>

                <div class="d-grid gap-2 mt-3 no-print">
                    <button onclick="window.print()" class="btn btn-olive">
                        <i class="fas fa-print me-2"></i> طباعة التقرير
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
@media print {
    .no-print { display: none !important; }
    #sidebar, #topbar { display: none !important; }
    #main { margin: 0 !important; }
    .page-content { padding: 10px !important; }
}
</style>
@endpush
