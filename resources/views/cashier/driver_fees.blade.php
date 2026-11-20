@extends('layouts.app')
@section('title', 'أجور ناقلي الزيتون')
@section('page-title', 'أجور ناقلي الزيتون')

@section('styles')
<style>
    .driver-header {
        background: linear-gradient(135deg, #78350f, #b45309);
        color: #fff; border-radius: 14px; padding: 1.2rem 1.5rem;
        margin-bottom: 1.2rem; text-align: center;
    }
    .driver-header h4 { font-weight: 900; color: #fde68a; margin-bottom: .25rem; }
    .driver-header p  { opacity: .85; font-size: .88rem; }

    .summary-box {
        background: #fff; border-radius: 12px; border: 2px solid #fcd34d;
        padding: 1rem 1.5rem; margin-bottom: 1.2rem;
        display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: .75rem;
        box-shadow: 0 2px 10px rgba(0,0,0,.06);
    }
    .summary-box .total-amount { font-size: 2rem; font-weight: 900; color: #b45309; }
    .summary-box .total-label  { font-size: .85rem; color: #78350f; }

    .driver-table th { background: #fef3c7; color: #78350f; font-weight: 800; font-size: .85rem; }
    .driver-table td { vertical-align: middle; font-size: .88rem; }
    .driver-table .no-fee { color: #ccc; font-style: italic; }

    .filter-bar { background: #fff; border-radius: 10px; border: 1px solid #e5e7eb; padding: .75rem 1rem; margin-bottom: 1rem; }

    .paid-badge   { background:#d1fae5; color:#065f46; font-weight:700; padding:2px 10px; border-radius:20px; font-size:.8rem; }
    .unpaid-badge { background:#fee2e2; color:#991b1b; font-weight:700; padding:2px 10px; border-radius:20px; font-size:.8rem; }
</style>
@endsection

@section('content')

<div class="driver-header">
    <div style="font-size:2rem">🚚</div>
    <h4>أجور ناقلي الزيتون</h4>
    <p>سجل داخلي للمعصرة — لا يظهر في الفواتير أو إيصالات العملاء</p>
</div>

{{-- ── فلتر التاريخ ── --}}
<div class="filter-bar">
    <form method="GET" action="{{ route('cashier.driver-fees') }}" class="row g-2 align-items-end">
        <div class="col-md-4">
            <label class="form-label" style="font-size:.82rem">تاريخ</label>
            <input type="date" name="date" class="form-control form-control-sm" value="{{ request('date') }}">
        </div>
        <div class="col-md-4">
            <label class="form-label" style="font-size:.82rem">اسم الناقل</label>
            <input type="text" name="carrier" class="form-control form-control-sm" placeholder="بحث باسم الناقل..." value="{{ request('carrier') }}">
        </div>
        <div class="col-md-4 d-flex gap-2">
            <button type="submit" class="btn btn-sm btn-warning flex-grow-1">
                <i class="fas fa-search me-1"></i>بحث
            </button>
            <a href="{{ route('cashier.driver-fees') }}" class="btn btn-sm btn-light">مسح</a>
        </div>
    </form>
</div>

{{-- ── ملخص ── --}}
<div class="summary-box">
    <div>
        <div class="total-label">إجمالي كمية الزيتون المنقول</div>
        <div class="total-amount">{{ number_format($totalOliveKg, 1) }} <small style="font-size:1rem">كيلو</small></div>
    </div>
    <div style="text-align:center">
        <div style="font-size:1.5rem; font-weight:900; color:#374151">{{ $totalCount }}</div>
        <div class="total-label">طلبية</div>
    </div>
</div>

{{-- ── الجدول ── --}}
<div class="card">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover driver-table mb-0">
                <thead>
                    <tr>
                        <th>رقم الإيصال</th>
                        <th>التاريخ</th>
                        <th>صاحب الزيتون</th>
                        <th>اسم الناقل</th>
                        <th>كمية الزيتون</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($receipts as $receipt)
                    <tr>
                        <td><strong>{{ $receipt->receipt_number }}</strong></td>
                        <td>{{ $receipt->created_at->format('Y/m/d') }}</td>
                        <td>{{ $receipt->owner->name }}</td>
                        <td>{{ $receipt->carrier_name }}</td>
                        <td><strong>{{ number_format($receipt->olive_weight_kg, 1) }}</strong> كيلو</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="text-center text-muted py-4">
                            لا توجد بيانات مطابقة
                        </td>
                    </tr>
                    @endforelse
                </tbody>
                @if($receipts->count() > 0)
                <tfoot>
                    <tr style="background:#fef3c7">
                        <td colspan="3" class="fw-bold text-end" style="color:#78350f">الإجمالي:</td>
                        <td><strong style="color:#b45309;font-size:1rem">{{ number_format($totalOliveKg, 1) }} كيلو</strong></td>
                        <td></td>
                    </tr>
                </tfoot>
                @endif
            </table>
        </div>
    </div>
</div>

{{-- pagination --}}
<div class="mt-3">{{ $receipts->links() }}</div>

@endsection
