@extends('layouts.app')
@section('title', 'الإيصالات')
@section('page-title', 'الإيصالات — طباعة الدور')

@section('content')

{{-- بحث --}}
<form method="GET" action="{{ route('user.receipts') }}" class="mb-3">
    <div class="input-group">
        <input type="text" name="search" class="form-control"
               placeholder="ابحث باسم صاحب الزيتون أو رقم الدور..."
               value="{{ request('search') }}">
        <button type="submit" class="btn btn-olive">
            <i class="fas fa-search"></i>
        </button>
        @if(request('search'))
        <a href="{{ route('user.receipts') }}" class="btn btn-outline-secondary">✕</a>
        @endif
    </div>
</form>

<div class="card">
    <div class="card-body p-0">
        <table class="table table-hover mb-0" style="font-size:.9rem">
            <thead style="background:var(--olive-pale)">
                <tr>
                    <th style="color:var(--olive-dark)">رقم الإيصال</th>
                    <th style="color:var(--olive-dark)">صاحب الزيتون</th>
                    <th style="color:var(--olive-dark)">الناقل</th>
                    <th style="color:var(--olive-dark)">كمية الزيتون</th>
                    <th style="color:var(--olive-dark)">رقم الدور</th>
                    <th style="color:var(--olive-dark)">التاريخ</th>
                    <th style="color:var(--olive-dark)">طباعة</th>
                </tr>
            </thead>
            <tbody>
                @forelse($receipts as $receipt)
                <tr>
                    <td class="fw-bold" style="color:var(--olive-dark)">{{ $receipt->receipt_number }}</td>
                    <td>{{ $receipt->owner->name ?? '—' }}</td>
                    <td>{{ $receipt->carrier_name }}</td>
                    <td>{{ number_format($receipt->olive_weight_kg, 1) }} كيلو</td>
                    <td>
                        <span class="badge" style="background:var(--olive-main);color:#fff;font-size:.85rem">
                            {{ $receipt->floor_number ?? '—' }}
                        </span>
                    </td>
                    <td class="text-muted small">{{ $receipt->created_at->format('Y/m/d') }}</td>
                    <td>
                        <a href="{{ route('receipts.print', $receipt) }}"
                           class="btn btn-sm btn-outline-success fw-bold">
                            <i class="fas fa-print"></i> طباعة
                        </a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="text-center text-muted py-4">
                        <i class="fas fa-inbox fa-2x mb-2 d-block"></i>
                        لا توجد إيصالات
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
