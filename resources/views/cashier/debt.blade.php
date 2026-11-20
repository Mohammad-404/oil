@extends('layouts.app')
@section('title', 'الدين — ' . $receipt->receipt_number)
@section('page-title', 'صفحة الدين')

@section('styles')
<style>
    .debt-hero {
        background: linear-gradient(135deg, #7f1d1d, #b91c1c);
        border-radius: 16px; padding: 2rem 1.5rem;
        color: #fff; text-align: center; margin-bottom: 1.5rem;
        box-shadow: 0 4px 20px rgba(185,28,28,.35);
    }
    .debt-hero .dh-icon { font-size: 3.5rem; margin-bottom: .5rem; }
    .debt-hero h3 { font-weight: 900; font-size: 1.5rem; color: #fca5a5; margin-bottom: .25rem; }
    .debt-hero .dh-amount { font-size: 3rem; font-weight: 900; color: #fef2f2; line-height: 1.1; }
    .debt-hero .dh-unit   { font-size: 1rem; opacity: .8; }

    .owner-card {
        background: #fff; border: 2px solid #fca5a5; border-radius: 14px;
        padding: 1.1rem 1.4rem; margin-bottom: 1.2rem;
        display: flex; align-items: center; gap: 1rem; flex-wrap: wrap;
    }
    .owner-turn {
        background: #b91c1c; color: #fff;
        font-size: 1.6rem; font-weight: 900;
        width: 56px; height: 56px; border-radius: 50%;
        display: flex; align-items: center; justify-content: center; flex-shrink: 0;
    }
    .owner-name { font-size: 1.2rem; font-weight: 900; color: #7f1d1d; }
    .owner-meta { font-size: .82rem; color: #666; margin-top: 2px; }

    .receipt-detail-card {
        background: #fff; border-radius: 12px;
        border: 1.5px solid #fecaca; margin-bottom: 1rem;
        overflow: hidden; box-shadow: 0 2px 8px rgba(185,28,28,.1);
    }
    .rdc-header {
        background: #fee2e2; padding: .6rem 1.2rem;
        display: flex; align-items: center; justify-content: space-between;
        border-bottom: 1px solid #fca5a5;
    }
    .rdc-header .rnum { font-weight: 800; color: #7f1d1d; font-size:.95rem; }
    .rdc-body { padding: .9rem 1.2rem; }

    .detail-grid { display: grid; grid-template-columns: repeat(3,1fr); gap: .5rem 1rem; margin-bottom: .7rem; }
    .dg-item .dg-l { font-size:.72rem; color:#888; }
    .dg-item .dg-v { font-weight:700; font-size:.9rem; }

    .debt-row {
        background: #fef2f2; border: 1.5px solid #fca5a5; border-radius: 8px;
        padding: .6rem 1rem; display: flex; justify-content: space-between; align-items: center;
        font-size: .88rem;
    }
    .debt-row .dr-label { color: #7f1d1d; font-weight: 700; }
    .debt-row .dr-amount { font-size: 1.2rem; font-weight: 900; color: #b91c1c; }

    .other-debts-card {
        background: #fff; border-radius: 12px;
        border: 1.5px solid #fecaca; margin-bottom: 1.2rem;
    }
    .odc-header {
        background: #fee2e2; padding: .6rem 1.2rem;
        border-bottom: 1px solid #fca5a5;
        font-weight: 800; color: #7f1d1d; font-size:.88rem;
    }
    .odc-body { padding: .75rem 1.2rem; }
    .other-debt-row {
        display: flex; justify-content: space-between; align-items: center;
        padding: .45rem 0; border-bottom: 1px dashed #fecaca; font-size:.85rem;
    }
    .other-debt-row:last-child { border: none; }

    .action-bar {
        display: flex; gap: .75rem; flex-wrap: wrap; margin-bottom: 1.5rem;
    }
</style>
@endsection

@section('content')

@if(session('info'))
<div class="alert alert-warning alert-dismissible fade show fw-bold" role="alert">
    <i class="fas fa-exclamation-triangle me-2"></i>{{ session('info') }}
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
@endif

{{-- البطل: المبلغ المدين --}}
<div class="debt-hero" @if($receipt->payment_method === 'oil_return') style="background:linear-gradient(135deg,#92400e,#d97706)" @endif>
    <div class="dh-icon">{{ $receipt->payment_method === 'oil_return' ? '🫙' : '💸' }}</div>
    <h3>إيصال مُعلَّق — {{ $receipt->payment_method === 'oil_return' ? 'دين رديات زيت' : 'دين' }}</h3>
    @if($receipt->payment_method === 'oil_return')
        <div class="dh-amount">{{ number_format($receipt->oil_return_kg, 2) }}</div>
        <div class="dh-unit">كيلو زيت — غير مُسدَّد</div>
    @else
        <div class="dh-amount">{{ number_format($receipt->total_fee, 3) }}</div>
        <div class="dh-unit">دينار أردني — غير مسدَّد</div>
    @endif
    <div style="margin-top:.75rem;font-size:.85rem;opacity:.75">
        {{ $receipt->receipt_number }} &nbsp;|&nbsp; {{ $receipt->created_at->format('Y/m/d') }}
    </div>
</div>

{{-- بيانات المالك --}}
<div class="owner-card">
    <div class="owner-turn">{{ $receipt->owner->floor_number }}</div>
    <div>
        <div class="owner-name">{{ $receipt->owner->name }}</div>
        <div class="owner-meta">
            <i class="fas fa-map-marker-alt me-1"></i>{{ $receipt->owner->city }} — {{ $receipt->owner->street }}
            &nbsp;|&nbsp; <i class="fas fa-phone me-1"></i>{{ $receipt->owner->phone }}
        </div>
    </div>
    <div class="ms-auto">
        <a href="{{ route('cashier.index', ['floor_number' => $receipt->owner->floor_number]) }}"
           class="btn btn-sm btn-outline-danger fw-bold">
            <i class="fas fa-cash-register me-1"></i>فتح في الكاشير
        </a>
    </div>
</div>

{{-- تفاصيل الإيصال المعلّق --}}
<div class="receipt-detail-card">
    <div class="rdc-header">
        <span class="rnum"><i class="fas fa-file-alt me-1"></i>{{ $receipt->receipt_number }}</span>
        <span class="badge bg-warning text-dark">انتظار</span>
    </div>
    <div class="rdc-body">
        <div class="detail-grid">
            <div class="dg-item">
                <div class="dg-l">الناقل</div>
                <div class="dg-v">{{ $receipt->carrier_name }}</div>
            </div>
            <div class="dg-item">
                <div class="dg-l">كمية الزيتون</div>
                <div class="dg-v" style="color:var(--olive-dark)">{{ number_format($receipt->olive_weight_kg,1) }} كيلو</div>
            </div>
            <div class="dg-item">
                <div class="dg-l">الزيت المعصور</div>
                <div class="dg-v" style="color:var(--gold)">{{ $receipt->oil_total_label }}</div>
            </div>
            <div class="dg-item">
                <div class="dg-l">أجرة العصر</div>
                <div class="dg-v" style="color:#1d4ed8">{{ number_format($receipt->press_fee,3) }} د.أ</div>
            </div>
            <div class="dg-item">
                <div class="dg-l">قيمة التنك</div>
                <div class="dg-v" style="color:#1e40af">
                    {{ $receipt->can_total > 0 ? number_format($receipt->can_total,3).' د.أ' : '—' }}
                </div>
            </div>
            @if($receipt->discount_amount > 0)
            <div class="dg-item">
                <div class="dg-l">خصم من الإجمالي</div>
                <div class="dg-v" style="color:#b91c1c">−{{ number_format($receipt->discount_amount, 3) }} د.أ</div>
            </div>
            @endif
            <div class="dg-item">
                <div class="dg-l">طريقة الدفع</div>
                <div class="dg-v">{{ $receipt->payment_method_label }}</div>
            </div>
        </div>
        <div class="debt-row" @if($receipt->payment_method === 'oil_return') style="background:#fffbeb;border-color:#fcd34d" @endif>
            <span class="dr-label" @if($receipt->payment_method === 'oil_return') style="color:#92400e" @endif>
                <i class="fas fa-exclamation-circle me-1"></i>
                {{ $receipt->payment_method === 'oil_return' ? 'كمية الزيت المدينة' : 'المبلغ المدين' }}
            </span>
            @if($receipt->payment_method === 'oil_return')
                <span class="dr-amount" style="color:#d97706">{{ number_format($receipt->oil_return_kg, 2) }} كيلو زيت</span>
            @else
                <span class="dr-amount">{{ number_format($receipt->total_fee, 3) }} د.أ</span>
            @endif
        </div>
        @if($receipt->notes)
        <div style="margin-top:.6rem;font-size:.8rem;color:#666">
            <i class="fas fa-sticky-note me-1"></i>{{ $receipt->notes }}
        </div>
        @endif
    </div>
</div>

{{-- ديون أخرى لنفس المالك --}}
@php
    $otherDebts = $owner->receipts->where('id', '!=', $receipt->id);
@endphp
@if($otherDebts->count() > 0)
<div class="other-debts-card">
    <div class="odc-header">
        <i class="fas fa-list me-1"></i>
        إيصالات معلّقة أخرى لـ {{ $owner->name }}
        <span class="badge bg-danger ms-2">{{ $otherDebts->count() }}</span>
    </div>
    <div class="odc-body">
        @foreach($otherDebts as $od)
        <div class="other-debt-row">
            <span>
                <strong style="color:#7f1d1d">{{ $od->receipt_number }}</strong>
                <small class="text-muted ms-2">{{ $od->created_at->format('Y/m/d') }}</small>
            </span>
            @if($od->payment_method === 'oil_return')
                <span style="font-weight:800;color:#d97706">{{ number_format($od->oil_return_kg,2) }} كيلو زيت</span>
            @else
                <span style="font-weight:800;color:#b91c1c">{{ number_format($od->total_fee,3) }} د.أ</span>
            @endif
        </div>
        @endforeach
        <div style="border-top:2px solid #fca5a5;margin-top:.5rem;padding-top:.5rem;display:flex;justify-content:space-between;font-weight:900">
            <span style="color:#7f1d1d">إجمالي ديون هذا المالك</span>
            <span style="color:#b91c1c;font-size:1.05rem">
                {{ number_format($otherDebts->sum('total_fee') + $receipt->total_fee, 3) }} د.أ
            </span>
        </div>
    </div>
</div>
@endif

{{-- أزرار الإجراءات --}}
<div class="action-bar">
    <a href="{{ route('cashier.debt-print', $receipt) }}" target="_blank"
       class="btn btn-danger fw-bold">
        <i class="fas fa-print me-1"></i>طباعة وصل الدين
    </a>
<a href="{{ route('cashier.index', ['floor_number' => $receipt->owner->floor_number]) }}"
       class="btn btn-olive fw-bold">
        <i class="fas fa-cash-register me-1"></i>رجوع للكاشير
    </a>
    <a href="{{ route('cashier.debts') }}" class="btn btn-outline-secondary fw-bold">
        <i class="fas fa-list me-1"></i>كل الديون
    </a>
</div>

@endsection
