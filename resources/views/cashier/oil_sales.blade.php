@extends('layouts.app')
@section('title', 'رديات الزيتون')
@section('page-title', 'رديات الزيتون')

@section('content')

{{-- ملخص --}}
<div class="row g-3 mb-3">
    <div class="col-6 col-md-3">
        <div class="card text-center border-warning">
            <div class="card-body py-3">
                <div style="font-size:1.8rem;font-weight:900;color:#d97706">{{ number_format($totalOilRetKg, 3) }}</div>
                <div class="small text-muted">إجمالي الرديات (كيلو)</div>
            </div>
        </div>
    </div>
    @if($totalBuyKg > 0)
    <div class="col-6 col-md-3">
        <div class="card text-center" style="border-color:#86efac">
            <div class="card-body py-3">
                <div style="font-size:1.8rem;font-weight:900;color:#16a34a">{{ number_format($totalBuyKg, 3) }}</div>
                <div class="small text-muted">+ مشتريات زيت (كيلو)</div>
            </div>
        </div>
    </div>
    @endif
    @if($totalSoldKg > 0)
    <div class="col-6 col-md-3">
        <div class="card text-center" style="border-color:#fca5a5">
            <div class="card-body py-3">
                <div style="font-size:1.8rem;font-weight:900;color:#b91c1c">{{ number_format($totalSoldKg, 3) }}</div>
                <div class="small text-muted">− مبيعات زيت (كيلو)</div>
            </div>
        </div>
    </div>
    @endif
    <div class="col-6 col-md-3">
        <div class="card text-center" style="border-color:{{ $netOilKg >= 0 ? '#6ee7b7' : '#fca5a5' }};background:{{ $netOilKg >= 0 ? '#f0fdf4' : '#fff5f5' }}">
            <div class="card-body py-3">
                <div style="font-size:1.8rem;font-weight:900;color:{{ $netOilKg >= 0 ? '#15803d' : '#b91c1c' }}">
                    {{ number_format($netOilKg, 3) }}
                </div>
                <div class="small text-muted">🫙 صافي المخزون (كيلو)</div>
                @if($totalSoldKg > 0 || $totalBuyKg > 0)
                <div class="small mt-1" style="font-size:.7rem;color:#888">
                    {{ number_format($totalOilRetKg, 2) }} رديات
                    @if($totalBuyKg > 0) + {{ number_format($totalBuyKg, 2) }} شراء @endif
                    @if($totalSoldKg > 0) − {{ number_format($totalSoldKg, 2) }} مبيعات @endif
                </div>
                @endif
            </div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="card text-center border-olive">
            <div class="card-body py-3">
                <div style="font-size:1.8rem;font-weight:900;color:var(--olive-dark)">{{ $totalCount }}</div>
                <div class="small text-muted">إيصال برديات زيت</div>
            </div>
        </div>
    </div>
</div>

{{-- بحث --}}
<form method="GET" action="{{ route('cashier.oil-sales') }}" class="mb-3">
    <div class="input-group">
        <input type="text" name="search" class="form-control"
               placeholder="ابحث باسم صاحب الزيتون أو رقم الإيصال..."
               value="{{ request('search') }}">
        <button type="submit" class="btn btn-warning text-dark fw-bold">
            <i class="fas fa-search"></i>
        </button>
        @if(request('search'))
        <a href="{{ route('cashier.oil-sales') }}" class="btn btn-outline-secondary">✕</a>
        @endif
    </div>
</form>

<div class="card">
    <div class="card-header" style="background:#fef3c7;color:#78350f;font-weight:800">
        <i class="fas fa-bottle-droplet me-2"></i>إيصالات رديات الزيت
    </div>
    <div class="card-body p-0">
        <table class="table table-hover mb-0" style="font-size:.9rem">
            <thead style="background:#fffbeb">
                <tr>
                    <th>رقم الإيصال</th>
                    <th>صاحب الزيتون</th>
                    <th>الناقل</th>
                    <th>الزيت المعصور</th>
                    <th style="color:#d97706">🫙 كيلو الرديات</th>
                    <th>التاريخ</th>
                    <th>إجراء</th>
                </tr>
            </thead>
            <tbody>
                @forelse($receipts as $receipt)
                @php $isDebtReturn = $receipt->status === 'debt'; @endphp
                <tr @if($isDebtReturn) style="background:#fff5f5" @endif>
                    <td class="fw-bold text-success">
                        {{ $receipt->receipt_number }}
                        @if($isDebtReturn)
                        <span class="badge" style="background:#dc2626;color:#fff;font-size:.7rem;margin-right:4px">(دَين)</span>
                        @endif
                    </td>
                    <td>
                        {{ $receipt->owner->name ?? '—' }}
                        <small class="text-muted d-block">دور: {{ $receipt->owner->floor_number ?? '—' }}</small>
                    </td>
                    <td>{{ $receipt->carrier_name }}</td>
                    <td style="color:var(--olive-dark)">
                        <strong>{{ $receipt->oil_total_label }}</strong>
                    </td>
                    <td>
                        <span style="font-weight:900;color:{{ $isDebtReturn ? '#b91c1c' : '#d97706' }};font-size:1rem">
                            {{ number_format($receipt->oil_return_kg, 3) }} كيلو
                            @if($isDebtReturn)<small style="font-size:.75em">(دَين)</small>@endif
                        </span>
                        <small class="text-muted d-block">
                            أجرة بعد الخصم: {{ number_format($receipt->discounted_press_fee, 3) }}
                            @if($receipt->cans_in_return && $receipt->can_total > 0)
                                + تنك بعد الخصم {{ number_format($receipt->discounted_can_total,3) }}
                            @endif
                            د.أ
                        </small>
                    </td>
                    <td class="text-muted small">{{ $receipt->created_at->format('Y/m/d') }}</td>
                    <td></td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="text-center text-muted py-5">
                        <i class="fas fa-bottle-droplet fa-3x mb-2 d-block" style="color:#d97706;opacity:.4"></i>
                        لا توجد إيصالات برديات زيت
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

@if($receipts->hasPages())
<div class="mt-3 d-flex justify-content-center">
    {{ $receipts->links() }}
</div>
@endif

@endsection
