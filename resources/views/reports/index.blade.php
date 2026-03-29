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
    <div class="col-6 col-md-3">
        <div class="stat-card bg-olive text-center">
            <div class="stat-icon">📋</div>
            <div class="stat-value">{{ number_format($totalCount) }}</div>
            <div class="stat-label">إجمالي الإيصالات</div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="stat-card bg-teal text-center">
            <div class="stat-icon">🫒</div>
            <div class="stat-value">{{ number_format($totalOlive, 0) }}</div>
            <div class="stat-label">كيلو زيتون</div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="stat-card bg-gold text-center">
            <div class="stat-icon">🫙</div>
            <div class="stat-value">{{ number_format($totalOil, 0) }}</div>
            <div class="stat-label">كيلو زيت</div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="stat-card bg-slate text-center">
            <div class="stat-icon">💰</div>
            <div class="stat-value">{{ number_format($totalFees, 0) }}</div>
            <div class="stat-label">د.أ إجمالي الأجور</div>
        </div>
    </div>
</div>

<!-- جدول التقرير -->
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
                        <th>رقم الإيصال</th>
                        <th>صاحب الزيتون</th>
                        <th>المدينة</th>
                        <th>الزيتون (كيلو)</th>
                        <th>الزيت (كيلو)</th>
                        <th>الأجرة</th>
                        <th>الحالة</th>
                        <th>التاريخ</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($receipts as $receipt)
                    <tr>
                        <td><a href="{{ route('receipts.show', $receipt) }}" class="text-success fw-bold text-decoration-none">{{ $receipt->receipt_number }}</a></td>
                        <td>{{ $receipt->owner->name }}</td>
                        <td>{{ $receipt->owner->city }}</td>
                        <td>{{ number_format($receipt->olive_weight_kg, 1) }}</td>
                        <td>{{ $receipt->oil_weight_kg ? number_format($receipt->oil_weight_kg, 1) : '—' }}</td>
                        <td>{{ number_format($receipt->total_fee, 2) }} د.أ</td>
                        <td><span class="badge badge-{{ $receipt->status }} rounded-pill px-2">{{ $receipt->status_label }}</span></td>
                        <td class="small text-muted">{{ $receipt->created_at->format('Y/m/d') }}</td>
                    </tr>
                    @empty
                    <tr><td colspan="8" class="text-center py-4 text-muted">لا توجد بيانات للفترة المحددة</td></tr>
                    @endforelse
                </tbody>
                @if($receipts->count() > 0)
                <tfoot>
                    <tr style="background:var(--olive-pale); font-weight:700">
                        <td colspan="3" class="text-end">المجموع:</td>
                        <td>{{ number_format($totalOlive, 1) }}</td>
                        <td>{{ number_format($totalOil, 1) }}</td>
                        <td>{{ number_format($totalFees, 2) }} د.أ</td>
                        <td colspan="2"></td>
                    </tr>
                </tfoot>
                @endif
            </table>
        </div>
    </div>
</div>
@endsection
