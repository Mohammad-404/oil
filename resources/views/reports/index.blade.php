@extends('layouts.app')
@section('title', 'التقارير')
@section('page-title', 'التقارير والإحصائيات')

@section('content')
<!-- فلاتر -->
<div class="card mb-3">
    <div class="card-header"><i class="fas fa-filter me-2"></i>تصفية التقارير</div>
    <div class="card-body">
        <form method="GET" class="row g-3 align-items-end">
            <div class="col-md-3">
                <label class="form-label">من تاريخ</label>
                <input type="date" name="from_date" class="form-control" value="{{ request('from_date') }}">
            </div>
            <div class="col-md-3">
                <label class="form-label">إلى تاريخ</label>
                <input type="date" name="to_date" class="form-control" value="{{ request('to_date') }}">
            </div>
            <div class="col-md-3">
                <label class="form-label">الحالة</label>
                <select name="status" class="form-select">
                    <option value="">الكل</option>
                    <option value="pending"    {{ request('status')=='pending'    ? 'selected':'' }}>في الانتظار</option>
                    <option value="processing" {{ request('status')=='processing' ? 'selected':'' }}>قيد المعالجة</option>
                    <option value="completed"  {{ request('status')=='completed'  ? 'selected':'' }}>مكتمل</option>
                    <option value="debt"       {{ request('status')=='debt'       ? 'selected':'' }}>دَين</option>
                </select>
            </div>
            <div class="col-md-3 d-grid">
                <button type="submit" class="btn btn-olive">
                    <i class="fas fa-search me-1"></i>عرض التقرير
                </button>
            </div>
        </form>
    </div>
</div>

<!-- ملخص -->
<div class="row g-3 mb-3">
    <div class="col-6 col-md-2">
        <div class="stat-card bg-olive text-center">
            <div class="stat-icon">📋</div>
            <div class="stat-value">{{ number_format($totalCount) }}</div>
            <div class="stat-label">إجمالي الإيصالات</div>
        </div>
    </div>
    <div class="col-6 col-md-2">
        <div class="stat-card bg-teal text-center">
            <div class="stat-icon">🫒</div>
            <div class="stat-value">{{ number_format($totalOlive, 0) }}</div>
            <div class="stat-label">كيلو زيتون</div>
        </div>
    </div>
    <div class="col-6 col-md-2">
        <div class="stat-card bg-gold text-center">
            <div class="stat-icon">🫙</div>
            <div class="stat-value">{{ number_format($totalOil, 0) }}</div>
            <div class="stat-label">كيلو زيت</div>
        </div>
    </div>
    <div class="col-6 col-md-2">
        <div class="stat-card text-center" style="background:#fffbeb;border:1px solid #fcd34d;color:#78350f">
            <div class="stat-icon" style="color:#d97706">🫙</div>
            <div class="stat-value" style="color:#d97706">{{ number_format($oilReturnKg, 2) }}</div>
            <div class="stat-label" style="color:#92400e">رديات الزيت (كيلو)</div>
        </div>
    </div>
    <div class="col-6 col-md-2">
        <div class="stat-card text-center" style="background:#fee2e2;border:1px solid #fca5a5">
            <div class="stat-icon" style="color:#b91c1c">⚠️</div>
            @if($debtTotal > 0)
            <div class="stat-value" style="color:#b91c1c;font-size:1.1rem">{{ number_format($debtTotal, 2) }} <small style="font-size:.5em">د.أ</small></div>
            @endif
            @if($debtOilKg > 0)
            <div style="font-weight:900;color:#d97706;font-size:1rem">{{ number_format($debtOilKg, 2) }} <small style="font-size:.6em">كيلو زيت</small></div>
            @endif
            @if($debtTotal == 0 && $debtOilKg == 0)
            <div class="stat-value" style="color:#b91c1c">0</div>
            @endif
            <div class="stat-label" style="color:#7f1d1d">الديون</div>
        </div>
    </div>
    <div class="col-6 col-md-2">
        <div class="stat-card text-center" style="background:{{ $netOilKg >= 0 ? '#f0fdf4' : '#fff5f5' }};border:1px solid {{ $netOilKg >= 0 ? '#86efac' : '#fca5a5' }}">
            <div class="stat-icon" style="color:{{ $netOilKg >= 0 ? '#15803d' : '#b91c1c' }}">🫙</div>
            <div class="stat-value" style="color:{{ $netOilKg >= 0 ? '#15803d' : '#b91c1c' }};font-size:1.2rem">{{ number_format($netOilKg, 2) }}</div>
            <div class="stat-label" style="color:{{ $netOilKg >= 0 ? '#166534' : '#7f1d1d' }}">صافي مخزون الزيت (كيلو)</div>
            <div style="font-size:.62rem;color:#888;margin-top:2px">
                {{ number_format($oilReturnKg, 2) }} رديات
                @if($oilBuyKg > 0) + {{ number_format($oilBuyKg, 2) }} شراء @endif
                @if($oilSellKg > 0) − {{ number_format($oilSellKg, 2) }} مبيعات @endif
            </div>
        </div>
    </div>
    <div class="col-6 col-md-2">
        <div class="stat-card bg-slate text-center">
            <div class="stat-icon">💰</div>
            <div class="stat-value">{{ number_format($totalNetFees, 0) }}</div>
            <div class="stat-label">د.أ صافي الأجور</div>
            <div style="font-size:.62rem;opacity:.85;margin-top:2px">
                أجرة نقدية: {{ number_format($totalFees,2) }}
                @if($oilBuyTotal > 0) <br>− شراء زيت: {{ number_format($oilBuyTotal,2) }} @endif
                @if($oilSellTotal > 0) <br>+ بيع زيت: {{ number_format($oilSellTotal,2) }} @endif
            </div>
        </div>
    </div>
</div>

<!-- جدول التقرير -->
<div class="row g-3 mb-3">
    <div class="col-md-6">
        <div class="stat-card text-center" style="background:#fef2f2;border:1px solid #fca5a5">
            <div class="stat-value text-danger">{{ number_format($oilBuyDebt, 3) }}</div>
            <div class="stat-label">إجمالي ديون شراء الزيت علينا (د.أ) — جميع العمليات</div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="stat-card text-center" style="background:#fffbeb;border:1px solid #fcd34d">
            <div class="stat-value" style="color:#92400e">{{ number_format($oilSellDebt, 3) }}</div>
            <div class="stat-label">إجمالي ديون بيع الزيت إلنا (د.أ) — جميع العمليات</div>
        </div>
    </div>
</div>
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <span><i class="fas fa-table me-2"></i>تفاصيل الإيصالات ({{ $totalCount }})</span>
        <button onclick="window.print()" class="btn btn-sm btn-light no-print">
            <i class="fas fa-print me-1"></i>طباعة التقرير
        </button>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>رقم الإيصال</th>
                        <th>صاحب الزيتون</th>
                        <th>حامل الزيتون</th>
                        <th>الزيتون (كيلو)</th>
                        <th>كمية الزيت</th>
                        <th>الأجرة</th>
                        <th>الحالة</th>
                        <th>التاريخ</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($receipts as $receipt)
                    <tr>
                        <td class="text-muted small">{{ $loop->iteration }}</td>
                        <td><a href="{{ route('receipts.show', $receipt) }}" class="text-success fw-bold text-decoration-none">{{ $receipt->receipt_number }}</a></td>
                        <td>
                            {{ $receipt->owner->name }}
                            <div class="text-muted small">{{ $receipt->owner->city }}</div>
                        </td>
                        <td>{{ $receipt->carrier_name }}</td>
                        <td><strong>{{ number_format($receipt->olive_weight_kg, 1) }}</strong></td>
                        <td>
                            @if($receipt->oil_weight_kg)
                                {{ number_format($receipt->oil_weight_kg, 1) }} كيلو
                                @if($receipt->oil_weight_gram)
                                    + {{ $receipt->oil_weight_gram }} غم
                                @endif
                            @else
                                <span class="text-muted">—</span>
                            @endif
                        </td>
                        <td><strong>{{ number_format($receipt->total_fee, 2) }}</strong> د.أ</td>
                        <td>
                            <span class="badge badge-{{ $receipt->status }} rounded-pill px-2"
                                  @if($receipt->status === 'debt') style="background:#dc2626;color:#fff" @endif>
                                {{ $receipt->status_label }}
                            </span>
                        </td>
                        <td class="small text-muted">{{ $receipt->created_at->format('Y/m/d') }}</td>
                    </tr>
                    @empty
                    <tr><td colspan="9" class="text-center py-4 text-muted">لا توجد بيانات للفترة المحددة</td></tr>
                    @endforelse
                </tbody>
                @if($receipts->count() > 0)
                <tfoot>
                    <tr style="background:var(--olive-pale); font-weight:700">
                        <td colspan="4" class="text-end">المجموع:</td>
                        <td>{{ number_format($totalOlive, 1) }}</td>
                        <td>{{ number_format($totalOil, 1) }}</td>
                        <td>
                            {{ number_format($totalFees, 2) }} د.أ
                            <div style="font-size:.72rem;color:#555;font-weight:400;margin-top:2px">
                                صافي: {{ number_format($totalNetFees, 2) }} د.أ
                                @if($oilBuyTotal > 0) <br>− شراء زيت: {{ number_format($oilBuyTotal,2) }} @endif
                                @if($oilSellTotal > 0) <br>+ بيع زيت: {{ number_format($oilSellTotal,2) }} @endif
                            </div>
                            @if($oilReturnKg > 0)
                            <div style="font-size:.75rem;color:#d97706;margin-top:3px">🫙 رديات: {{ number_format($oilReturnKg,2) }} كيلو</div>
                            @endif
                            @if($debtTotal > 0 || $debtOilKg > 0)
                            <div style="font-size:.75rem;color:#b91c1c;margin-top:2px">
                                ⚠️ ديون:
                                @if($debtTotal > 0) {{ number_format($debtTotal,2) }} د.أ @endif
                                @if($debtOilKg > 0) &nbsp;+&nbsp; {{ number_format($debtOilKg,2) }} كيلو زيت @endif
                            </div>
                            @endif
                        </td>
                        <td colspan="2"></td>
                    </tr>
                </tfoot>
                @endif
            </table>
        </div>
    </div>
</div>
@endsection
