@extends('layouts.app')
@section('title', 'الكاشير')
@section('page-title', 'الكاشير')

@section('styles')
<style>
    .cashier-search-box {
        background: linear-gradient(135deg, var(--olive-dark), var(--olive-main));
        border-radius: 16px; padding: 2rem 1.5rem;
        color: #fff; text-align: center; margin-bottom: 1.5rem;
    }
    .cashier-search-box h4 { font-weight: 900; color: var(--gold-light); margin-bottom: .5rem; }
    .cashier-search-box .input-group { max-width: 380px; margin: 0 auto; }
    .cashier-search-box input {
        font-size: 1.6rem; font-weight: 900; text-align: center;
        border: none; border-radius: 10px 0 0 10px !important; letter-spacing: 2px;
    }
    .cashier-search-box .btn { border-radius: 0 10px 10px 0 !important; font-weight: 700; padding: 0 1.5rem; }

    .owner-header {
        background: linear-gradient(135deg, #1a3a0a, #2d5a14);
        color: #fff; border-radius: 14px;
        padding: 1.2rem 1.5rem; margin-bottom: 1rem;
        display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: .75rem;
    }
    .turn-badge {
        background: var(--gold); color: #fff;
        font-size: 1.8rem; font-weight: 900;
        width: 62px; height: 62px; border-radius: 50%;
        display: flex; align-items: center; justify-content: center; flex-shrink: 0;
        box-shadow: 0 3px 10px rgba(0,0,0,.3);
    }
    .owner-name { font-size: 1.35rem; font-weight: 800; color: var(--gold-light); }
    .owner-meta { font-size: .85rem; color: rgba(255,255,255,.75); margin-top: 3px; }

    .grand-calc {
        background: #fff; border-radius: 14px;
        border: 2px solid var(--olive-pale);
        padding: 1.2rem 1.5rem; margin-bottom: 1.2rem;
        box-shadow: 0 2px 12px rgba(0,0,0,.07);
    }
    .grand-calc h6 {
        font-weight: 800; color: var(--olive-dark);
        border-bottom: 2px solid var(--olive-pale); padding-bottom: .5rem; margin-bottom: 1rem;
    }
    .calc-grid { display: grid; grid-template-columns: repeat(4, 1fr); gap: .6rem; }
    .calc-item { background: #f8faf4; border-radius: 10px; padding: .8rem .6rem; text-align: center; border-top: 3px solid; }
    .calc-item.olive { border-color: var(--olive-main); }
    .calc-item.oil   { border-color: var(--gold); }
    .calc-item.press { border-color: #3b82f6; }
    .calc-item.total { border-color: #ef4444; background: #fff5f5; }
    .calc-item .cv   { font-size: 1.35rem; font-weight: 900; line-height: 1.1; }
    .calc-item .cl   { font-size: .72rem; color: #666; margin-top: 3px; }
    .calc-item.olive .cv { color: var(--olive-dark); }
    .calc-item.oil   .cv { color: var(--gold); }
    .calc-item.press .cv { color: #1d4ed8; }
    .calc-item.total .cv { color: #dc2626; }

    .receipt-card {
        background: #fff; border-radius: 12px;
        border: 1.5px solid #e0ecd5; margin-bottom: 1.2rem;
        overflow: hidden; box-shadow: 0 2px 8px rgba(0,0,0,.05);
    }
    .receipt-card-header {
        background: var(--olive-pale); padding: .65rem 1.2rem;
        display: flex; align-items: center; justify-content: space-between;
        border-bottom: 1px solid #d0e8b8;
    }
    .receipt-card-header .rnum { font-weight: 800; color: var(--olive-dark); }
    .receipt-card-body { padding: 1rem 1.2rem; }

    .ri-grid { display: grid; grid-template-columns: repeat(4, 1fr); gap: .5rem 1rem; margin-bottom: .75rem; }
    .ri-item .ri-l { font-size: .72rem; color: #888; }
    .ri-item .ri-v { font-weight: 700; font-size: .92rem; }

    /* ── النموذج الموحّد ── */
    .cashier-form-box {
        background: #f0f9ff; border: 1.5px solid #bae6fd;
        border-radius: 12px; padding: 1.1rem;
    }
    .form-section-title {
        font-size: .84rem; font-weight: 800; margin-bottom: .65rem;
        display: flex; align-items: center; gap: .4rem; color: #0c4a6e;
    }
    .section-divider { border: none; border-top: 1px dashed #bae6fd; margin: .9rem 0; }

    /* ── طريقة الدفع — مربعات صغيرة ── */
    .pay-choice { display: flex; gap: .5rem; margin-bottom: .75rem; }
    .pay-choice-btn {
        display: flex; align-items: center; gap: .4rem;
        padding: .45rem .9rem; border: 2px solid #ddd; border-radius: 8px;
        cursor: pointer; font-size: .84rem; font-weight: 700;
        background: #f9f9f9; color: #666; transition: all .15s; user-select: none;
    }
    .pay-choice-btn .pc-dot {
        width: 16px; height: 16px; border-radius: 50%;
        border: 2px solid #ccc; flex-shrink: 0; transition: all .15s;
        display: flex; align-items: center; justify-content: center;
    }
    .pay-choice-btn.active-cash {
        border-color: #22c55e; background: #f0fdf4; color: #15803d;
    }
    .pay-choice-btn.active-cash .pc-dot {
        border-color: #22c55e; background: #22c55e;
    }
    .pay-choice-btn.active-oil_return {
        border-color: #f59e0b; background: #fffbeb; color: #92400e;
    }
    .pay-choice-btn.active-oil_return .pc-dot {
        border-color: #f59e0b; background: #f59e0b;
    }

    /* ── رديات الزيت ── */
    .oil-return-box { background: #fffbeb; border-radius: 8px; padding: .75rem .9rem; }
    .oil-return-formula {
        background: #fef3c7; border: 1px solid #fcd34d; border-radius: 6px;
        padding: .35rem .75rem; font-size: .8rem; color: #78350f; margin-bottom: .55rem;
        display: flex; justify-content: space-between; align-items: center;
        transition: opacity .2s;
    }
    .oil-return-formula.formula-disabled { opacity: .35; pointer-events: none; }
    .oil-return-result {
        background: #f59e0b; color: #fff; border-radius: 6px;
        padding: .3rem .75rem; font-size: .88rem; font-weight: 900;
        text-align: center; margin-top: .5rem;
    }
    .pay-panel { display: none; }
    .pay-panel.visible { display: block; }

    /* ── التنكة — دائماً ظاهرة ── */
    .can-box {
        background: #eff6ff; border: 1px solid #bfdbfe;
        border-radius: 10px; padding: .75rem .9rem; margin-top: .6rem;
    }
    .can-box-title {
        font-size: .8rem; font-weight: 800; color: #1e40af; margin-bottom: .5rem;
        display: flex; align-items: center; gap: .35rem;
    }
    .can-rows { display: flex; gap: .5rem; flex-wrap: wrap; }
    .can-item {
        flex: 1; min-width: 90px; background: #fff; border: 1.5px solid #bfdbfe;
        border-radius: 8px; padding: .5rem .6rem; text-align: center;
    }
    .can-item .ci-label { font-size: .7rem; font-weight: 700; color: #1e40af; }
    .can-item .ci-price { font-size: .68rem; color: #64748b; margin-bottom: .3rem; }
    .can-item input { width: 100%; text-align: center; font-weight: 700; }
    .can-total-display {
        background: #dbeafe; border-radius: 7px; padding: .45rem .75rem;
        font-size: .88rem; font-weight: 800; color: #1d4ed8;
        text-align: center; margin-top: .5rem;
    }

    /* ── أجرة الناقل (ضمن النموذج) ── */
    .driver-inline {
        background: #fffbeb; border: 1.5px solid #f59e0b;
        border-radius: 10px; padding: .75rem 1rem;
    }
    .driver-inline .driver-title {
        font-size: .82rem; font-weight: 800; color: #92400e; margin-bottom: .5rem;
    }

    /* ── إجمالي محسوب ── */
    .press-result {
        background: #f0fdf4; border: 1px solid #86efac;
        border-radius: 8px; padding: .5rem .9rem;
        font-size: .88rem; color: #166534; font-weight: 700;
        display: flex; justify-content: space-between; align-items: center;
    }
    .press-result .big-amt { font-size: 1.2rem; font-weight: 900; color: #15803d; }

    .no-result-box { text-align: center; padding: 3rem 1rem; color: #888; }
    .no-result-box .big-icon { font-size: 4rem; margin-bottom: 1rem; }

    @media (max-width: 700px) {
        .calc-grid { grid-template-columns: repeat(2, 1fr); }
        .ri-grid   { grid-template-columns: repeat(2, 1fr); }
        .pay-method-tabs { flex-direction: column; }
    }
</style>
@endsection

@section('content')

<div class="cashier-search-box">
    <h4><i class="fas fa-cash-register me-2"></i>كاشير المعصرة</h4>

    {{-- تبويبات البحث --}}
    <div class="d-flex justify-content-center gap-2 mb-3">
        <button type="button" id="tab-floor" onclick="switchTab('floor')"
                class="btn btn-sm fw-bold {{ request()->filled('receipt_number') ? 'btn-outline-light' : 'btn-warning text-dark' }}">
            <i class="fas fa-hashtag me-1"></i>رقم الدور
        </button>
        <button type="button" id="tab-receipt" onclick="switchTab('receipt')"
                class="btn btn-sm fw-bold {{ request()->filled('receipt_number') ? 'btn-warning text-dark' : 'btn-outline-light' }}">
            <i class="fas fa-file-alt me-1"></i>رقم الإيصال
        </button>
    </div>

    {{-- بحث برقم الدور --}}
    <form method="GET" action="{{ route('cashier.index') }}" id="form-floor"
          style="{{ request()->filled('receipt_number') ? 'display:none' : '' }}">
        <div class="input-group">
            <input type="number" name="floor_number" class="form-control"
                   placeholder="رقم الدور..."
                   value="{{ request('floor_number') }}"
                   min="1" {{ !request()->filled('receipt_number') ? 'autofocus' : '' }}>
            <input type="hidden" name="status" value="{{ request('status') }}">
            <button type="submit" class="btn btn-warning">
                <i class="fas fa-search me-1"></i>بحث
            </button>
        </div>
        <p class="mt-2 mb-0" style="opacity:.7;font-size:.82rem">ابحث بـ <strong>رقم الدور</strong> لعرض بيانات صاحب الزيتون</p>
    </form>

    {{-- بحث برقم الإيصال --}}
    <form method="GET" action="{{ route('cashier.index') }}" id="form-receipt"
          style="{{ request()->filled('receipt_number') ? '' : 'display:none' }}">
        <div class="input-group">
            <input type="text" name="receipt_number" class="form-control"
                   placeholder="رقم الإيصال..."
                   value="{{ request('receipt_number') }}"
                   {{ request()->filled('receipt_number') ? 'autofocus' : '' }}>
            <input type="hidden" name="status" value="{{ request('status') }}">
            <button type="submit" class="btn btn-warning">
                <i class="fas fa-search me-1"></i>بحث
            </button>
        </div>
        <p class="mt-2 mb-0" style="opacity:.7;font-size:.82rem">ابحث بـ <strong>رقم الإيصال</strong> للوصول مباشرة لبيانات المالك</p>
    </form>

    @if(isset($receiptNotFound) && $receiptNotFound)
    <div class="alert alert-warning mt-3 py-2 mb-0" style="max-width:380px;margin:auto;font-size:.88rem">
        <i class="fas fa-exclamation-triangle me-1"></i>
        رقم الإيصال <strong>{{ request('receipt_number') }}</strong> غير موجود
    </div>
    @endif

    {{-- فلتر الحالة --}}
    @if(request()->filled('floor_number') || request()->filled('receipt_number'))
    <div class="d-flex justify-content-center gap-2 mt-3">
        <a href="{{ request()->fullUrlWithQuery(['status' => '']) }}"
           class="btn btn-sm fw-bold {{ $statusFilter === '' ? 'btn-light' : 'btn-outline-light' }}">
            الكل
        </a>
        <a href="{{ request()->fullUrlWithQuery(['status' => 'pending']) }}"
           class="btn btn-sm fw-bold {{ $statusFilter === 'pending' ? 'btn-warning text-dark' : 'btn-outline-light' }}">
            <i class="fas fa-clock me-1"></i>قيد الانتظار
        </a>
        <a href="{{ request()->fullUrlWithQuery(['status' => 'processing']) }}"
           class="btn btn-sm fw-bold {{ $statusFilter === 'processing' ? 'btn-info text-dark' : 'btn-outline-light' }}">
            <i class="fas fa-spinner me-1"></i>قيد المعالجة
        </a>
        <a href="{{ request()->fullUrlWithQuery(['status' => 'completed']) }}"
           class="btn btn-sm fw-bold {{ $statusFilter === 'completed' ? 'btn-success' : 'btn-outline-light' }}">
            <i class="fas fa-check me-1"></i>مكتمل
        </a>
        <a href="{{ request()->fullUrlWithQuery(['status' => 'debt']) }}"
           class="btn btn-sm fw-bold {{ $statusFilter === 'debt' ? 'btn-danger' : 'btn-outline-light' }}">
            <i class="fas fa-file-invoice-dollar me-1"></i>دَين
        </a>
    </div>
    @endif
</div>

@if(request()->filled('floor_number') || request()->filled('receipt_number'))
@if($owner)

    {{-- ── رأس المالك ── --}}
    <div class="owner-header">
        <div class="d-flex align-items-center gap-3">
            <div class="turn-badge">{{ $owner->floor_number }}</div>
            <div>
                <div class="owner-name">{{ $owner->name }}</div>
                <div class="owner-meta">
                    <i class="fas fa-map-marker-alt me-1"></i>{{ $owner->city }} — {{ $owner->street }}
                    &nbsp;|&nbsp;
                    <i class="fas fa-phone me-1"></i>{{ $owner->phone }}
                </div>
            </div>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('cashier.print', $owner) }}" target="_blank"
               class="btn btn-sm btn-warning fw-bold">
                <i class="fas fa-print me-1"></i>طباعة التفاصيل الكاملة
            </a>
            <a href="{{ route('owners.show', $owner) }}" class="btn btn-sm btn-outline-light">
                <i class="fas fa-user me-1"></i>ملف المالك
            </a>
        </div>
    </div>

    @php
        $totalOlive   = $receipts->sum('olive_weight_kg');
        $totalOilKg   = $receipts->sum('oil_weight_kg');
        $totalOilGm   = $receipts->sum('oil_weight_gram');
        $totalPressF  = $receipts->sum('press_fee');
        // نقدي فقط (غير رديات الزيت)
        $cashTotal    = $receipts->where('payment_method', '!=', 'oil_return')->sum('total_fee')
                      + $receipts->where('payment_method', 'oil_return')->where('cans_in_return', false)->sum('discounted_can_total');
        // رديات الزيت (كيلو)
        $oilRetTotal  = $receipts->where('payment_method', 'oil_return')->sum('oil_return_kg');
        $hasOilRet    = $oilRetTotal > 0;
        $extractRate  = $totalOlive > 0 ? ($totalOilKg / $totalOlive) * 100 : 0;
        $totalOilKg  += floor($totalOilGm / 1000);
        $totalOilGm   = $totalOilGm % 1000;
    @endphp

    {{-- ── ملخص إجمالي ── --}}
    <div class="grand-calc">
        <h6><i class="fas fa-calculator me-2"></i>ملخص الحساب — {{ $receipts->count() }} إيصال</h6>
        <div class="calc-grid">
            <div class="calc-item olive">
                <div class="cv">{{ number_format($totalOlive, 1) }}</div>
                <div class="cl">كيلو زيتون</div>
            </div>
            <div class="calc-item oil">
                <div class="cv">
                    {{ number_format($totalOilKg, 0) }}
                    @if($totalOilGm > 0)<small style="font-size:.68rem">+{{ (int)$totalOilGm }}غم</small>@endif
                </div>
                <div class="cl">كيلو زيت معصور</div>
            </div>
            <div class="calc-item press">
                <div class="cv">{{ number_format($totalPressF, 3) }}</div>
                <div class="cl">أجرة العصر (د.أ)</div>
            </div>
            <div class="calc-item total">
                <div class="cv">{{ number_format($cashTotal, 3) }}</div>
                <div class="cl">💵 نقدي (د.أ)</div>
            </div>
            @if($hasOilRet)
            <div class="calc-item" style="background:linear-gradient(135deg,#78350f,#d97706);color:#fff">
                <div class="cv">{{ number_format($oilRetTotal, 3) }}</div>
                <div class="cl">🫙 رديات الزيت (كيلو)</div>
            </div>
            @endif
        </div>
        @if($extractRate > 0)
        <div style="margin-top:.6rem;font-size:.8rem;color:#555;text-align:center">
            نسبة الاستخلاص: <strong style="color:var(--olive-dark)">{{ number_format($extractRate, 1) }}%</strong>
        </div>
        @endif
    </div>

    {{-- ── الإيصالات ── --}}
    @forelse($receipts as $receipt)
    <div class="receipt-card">
        <div class="receipt-card-header">
            <span class="rnum"><i class="fas fa-file-alt me-1"></i>{{ $receipt->receipt_number }}</span>
            <div class="d-flex gap-2 align-items-center">
                <span class="badge badge-{{ $receipt->status }} rounded-pill px-3">{{ $receipt->status_label }}</span>
                <span class="text-muted small">{{ $receipt->created_at->format('Y/m/d') }}</span>
                <a href="{{ route('receipts.print', $receipt) }}" target="_blank"
                   class="btn btn-sm btn-outline-success fw-bold" title="طباعة هذا الإيصال">
                    <i class="fas fa-print"></i>
                </a>
            </div>
        </div>
        <div class="receipt-card-body">

            {{-- معلومات الشحنة --}}
            <div class="ri-grid">
                <div class="ri-item">
                    <div class="ri-l">الناقل</div>
                    <div class="ri-v">{{ $receipt->carrier_name }}</div>
                </div>
                <div class="ri-item">
                    <div class="ri-l">كمية الزيتون</div>
                    <div class="ri-v" style="color:var(--olive-dark)">{{ number_format($receipt->olive_weight_kg, 1) }} كيلو</div>
                </div>
                <div class="ri-item">
                    <div class="ri-l">الزيت المعصور</div>
                    <div class="ri-v" style="color:var(--gold)">{{ $receipt->oil_total_label }}</div>
                </div>
                @if($receipt->olive_oil_ratio)
                <div class="ri-item">
                    <div class="ri-l">نسبة الزيتون/الزيت</div>
                    <div class="ri-v" style="color:#7c3aed;font-weight:800">
                        {{ number_format($receipt->olive_oil_ratio, 1) }}
                        <small style="font-weight:400;font-size:.75em"> كيلو زيتون / كيلو زيت</small>
                    </div>
                </div>
                @endif
                <div class="ri-item">
                    <div class="ri-l">أجرة العصر</div>
                    <div class="ri-v" style="color:#1d4ed8">
                        {{ $receipt->press_fee > 0 ? number_format($receipt->press_fee, 3).' د.أ' : '—' }}
                    </div>
                </div>
            </div>

            @php
                $formLocked = ($receipt->oil_weight_kg > 0) && !session('cashier_unlocked_' . $receipt->id);
            @endphp

            {{-- ── النموذج الموحّد ── --}}
            <div class="cashier-form-box {{ $formLocked ? 'position-relative' : '' }}">

                {{-- شريط التنبيه + زر الفتح إذا كان مقفلاً --}}
                @if($formLocked)
                <div class="d-flex align-items-center justify-content-between mb-2 px-1 py-2 rounded"
                     style="background:#fef9c3;border:1.5px solid #fde047">
                    <span style="font-size:.82rem;font-weight:700;color:#78350f">
                        <i class="fas fa-lock me-1"></i> البيانات محفوظة — فقط الحالة قابلة للتغيير
                    </span>
                    <button type="button" class="btn btn-sm btn-warning fw-bold"
                            onclick="openUnlockModal({{ $receipt->id }}, '{{ $receipt->receipt_number }}')">
                        <i class="fas fa-unlock me-1"></i>تعديل
                    </button>
                </div>
                @endif

                <form action="{{ route('cashier.update-oil', $receipt) }}" method="POST" id="form_{{ $receipt->id }}">
                    @csrf @method('PATCH')

                    {{-- § 1: كمية الزيت --}}
                    <div class="form-section-title">
                        <i class="fas fa-flask" style="color:#0369a1"></i> إدخال كمية الزيت المعصورة
                    </div>
                    <div class="row g-2 mb-1">
                        <div class="col-6 col-md-3">
                            <label class="form-label" style="font-size:.78rem">زيت (كيلو)</label>
                            <input type="number" name="oil_weight_kg"
                                   class="form-control form-control-sm oil-kg-input"
                                   step="1" min="0"
                                   value="{{ (int)$receipt->oil_weight_kg }}"
                                   placeholder="0"
                                   data-id="{{ $receipt->id }}"
                                   data-olive="{{ (float)$receipt->olive_weight_kg }}"
                                   {{ $formLocked ? 'readonly' : '' }}>
                        </div>
                        <div class="col-6 col-md-3">
                            <label class="form-label" style="font-size:.78rem">كسور (غم)</label>
                            <input type="number" name="oil_weight_gram"
                                   class="form-control form-control-sm oil-gram-input"
                                   step="1" min="0" max="999"
                                   value="{{ (int)$receipt->oil_weight_gram }}"
                                   data-id="{{ $receipt->id }}"
                                   placeholder="0"
                                   {{ $formLocked ? 'readonly' : '' }}>
                        </div>
                        <div class="col-6 col-md-3">
                            <label class="form-label" style="font-size:.78rem">سعر العصر / كيلو</label>
                            <div class="input-group input-group-sm">
                                <input type="number" name="fee_per_kg"
                                       class="form-control fee-kg-input"
                                       step="0.001" min="0"
                                       value="{{ $receipt->fee_per_kg }}"
                                       data-id="{{ $receipt->id }}"
                                       placeholder="0.000"
                                       {{ $formLocked ? 'readonly' : '' }}>
                                <span class="input-group-text" style="font-size:.7rem">د.أ</span>
                            </div>
                        </div>
                        <div class="col-6 col-md-3">
                            <label class="form-label" style="font-size:.78rem">أجرة العصر</label>
                            <div class="input-group input-group-sm">
                                <input type="text" class="form-control fw-bold text-primary" readonly
                                       id="pressResult_{{ $receipt->id }}"
                                       value="{{ number_format($receipt->press_fee, 3, '.', '') }}">
                                <span class="input-group-text" style="font-size:.7rem">د.أ</span>
                            </div>
                        </div>
                    </div>

                    <hr class="section-divider">

                    {{-- § 2: طريقة الدفع --}}
                    <div class="form-section-title">
                        <i class="fas fa-money-bill-wave" style="color:#15803d"></i> طريقة تسديد الأجرة
                    </div>

                    <input type="hidden" name="payment_method"
                           id="pm_{{ $receipt->id }}"
                           value="{{ $receipt->payment_method ?? 'cash' }}">

                    {{-- خيارات الدفع (2 أزرار راديو) --}}
                    <div class="pay-choice {{ $formLocked ? 'pe-none opacity-50' : '' }}" id="payChoice_{{ $receipt->id }}">
                        <label class="pay-choice-btn {{ ($receipt->payment_method ?? 'cash') === 'cash' ? 'active-cash' : '' }}"
                               id="pcCash_{{ $receipt->id }}"
                               onclick="setPayMethod({{ $receipt->id }}, 'cash')">
                            <span class="pc-dot"></span>
                            <i class="fas fa-money-bill me-1"></i>نقدي
                        </label>
                        <label class="pay-choice-btn {{ ($receipt->payment_method ?? 'cash') === 'oil_return' ? 'active-oil_return' : '' }}"
                               id="pcOil_{{ $receipt->id }}"
                               onclick="setPayMethod({{ $receipt->id }}, 'oil_return')">
                            <span class="pc-dot"></span>
                            <i class="fas fa-bottle-droplet me-1"></i>رديات زيت
                        </label>
                    </div>

                    {{-- لوحة رديات زيت (تظهر فقط عند الاختيار) --}}
                    <div class="pay-panel {{ ($receipt->payment_method ?? 'cash') === 'oil_return' ? 'visible' : '' }} {{ $formLocked ? 'pe-none opacity-50' : '' }}"
                         id="panel_oil_return_{{ $receipt->id }}">
                        <div class="oil-return-box">
                            @php
                                // إيصال قديم لم تُحسب له رديات بعد: اعرض المقسوم الجديد بدل القيمة الافتراضية القديمة 7.
                                $savedN1 = (float)($receipt->oil_return_divisor ?? 5.5);
                                $savedN2 = (float)($receipt->can_return_divisor ?? 5.5);
                                $n1 = (float)$receipt->oil_return_kg == 0 && $savedN1 == 7 ? 5.5 : $savedN1;
                                $n2 = (float)$receipt->oil_return_kg == 0 && $savedN2 == 7 ? 5.5 : $savedN2;
                            @endphp

                            {{-- § N₁: بدون تنك --}}
                            <div class="oil-return-formula {{ $receipt->cans_in_return ? 'formula-disabled' : '' }}"
                                 id="n1Row_{{ $receipt->id }}">
                                <span>
                                    <i class="fas fa-calculator me-1"></i>
                                    رديات = أجرة العصر بعد الخصم ÷
                                    <input type="number"
                                           name="oil_return_divisor"
                                           class="form-control form-control-sm d-inline-block oil-return-divisor-input"
                                           step="0.001" min="0.001"
                                           style="width:72px;display:inline-block!important;text-align:center;font-weight:800"
                                           value="{{ number_format($n1, 3, '.', '') }}"
                                           {{ $receipt->cans_in_return ? 'disabled' : '' }}
                                           data-id="{{ $receipt->id }}">
                                    <input type="hidden" name="oil_return_divisor"
                                           value="{{ number_format($n1, 3, '.', '') }}"
                                           {{ $receipt->cans_in_return ? '' : 'disabled' }}>
                                </span>
                                <small style="color:#b45309">= كيلو زيت</small>
                            </div>

                            {{-- § N₂: مع تنك --}}
                            <div class="oil-return-formula {{ $receipt->cans_in_return ? '' : 'formula-disabled' }}"
                                 id="n2Row_{{ $receipt->id }}">
                                <span>
                                    <i class="fas fa-boxes-stacked me-1"></i>
                                    رديات = الإجمالي بعد الخصم ÷
                                    <input type="number"
                                           name="can_return_divisor"
                                           class="form-control form-control-sm d-inline-block can-return-divisor-input"
                                           step="0.001" min="0.001"
                                           style="width:72px;display:inline-block!important;text-align:center;font-weight:800"
                                           value="{{ number_format($n2, 3, '.', '') }}"
                                           {{ $receipt->cans_in_return ? '' : 'disabled' }}
                                           data-id="{{ $receipt->id }}">
                                    <input type="hidden" name="can_return_divisor"
                                           value="{{ number_format($n2, 3, '.', '') }}"
                                           {{ $receipt->cans_in_return ? 'disabled' : '' }}>
                                </span>
                                <small style="color:#b45309">= كيلو زيت</small>
                            </div>

                            {{-- حقل الرديات (كيلو) — مخفي، محسوب تلقائياً --}}
                            <input type="hidden" name="oil_return_kg"
                                   id="oilReturnKg_{{ $receipt->id }}"
                                   value="{{ number_format($receipt->oil_return_kg, 3, '.', '') }}">
                            <input type="hidden" name="oil_return_gram" value="0">

                            {{-- خيار: هل التنك مُسوَّاة بالرديات؟ --}}
                            <div style="margin-top:.6rem;background:#fef3c7;border-radius:7px;padding:.45rem .75rem">
                                <div style="display:flex;align-items:center;gap:.6rem">
                                    <label class="form-check-label" style="font-size:.8rem;font-weight:700;color:#78350f;cursor:pointer"
                                           for="cansInReturn_{{ $receipt->id }}">
                                        📦 التنكات مُسوَّاة بالرديات (الشخص لا يدفع نقداً للتنك)
                                    </label>
                                    <div class="form-check form-switch mb-0 ms-auto">
                                        <input class="form-check-input cans-in-return-toggle"
                                               type="checkbox"
                                               name="cans_in_return"
                                               value="1"
                                               id="cansInReturn_{{ $receipt->id }}"
                                               data-id="{{ $receipt->id }}"
                                               {{ $receipt->cans_in_return ? 'checked' : '' }}>
                                    </div>
                                </div>
                            </div>

                            @php
                                $includesCans = $receipt->cans_in_return && $receipt->can_total > 0;
                                if ($includesCans) {
                                    $returnKg = $n2 > 0 ? (float)$receipt->total_fee / $n2 : 0;
                                } else {
                                    $returnKg = $n1 > 0 ? (float)$receipt->discounted_press_fee / $n1 : 0;
                                }
                                $covered = $receipt->cans_in_return ? $receipt->total_fee : $receipt->discounted_press_fee;
                            @endphp
                            <div class="oil-return-result" id="oilReturnDisplay_{{ $receipt->id }}">
                                🫙 الرديات: {{ number_format($returnKg, 3) }} كيلو زيت
                                — تغطي: {{ number_format($covered, 3) }} د.أ
                            </div>
                        </div>
                    </div>

                    {{-- قسم التنك — دائماً ظاهر بغض النظر عن طريقة الدفع --}}
                    <div class="can-box {{ $formLocked ? 'pe-none opacity-50' : '' }}">
                        <div class="can-box-title">
                            <i class="fas fa-box" style="color:#1e40af"></i>
                            التنك (اختياري — تُضاف للإجمالي دائماً)
                        </div>
                        <div class="can-rows">
                            <div class="can-item">
                                <div class="ci-label">🛢️ تنكة 16 كيلو</div>
                                <div class="ci-price">{{ number_format($canPrices['can_16'], 3) }} د.أ</div>
                                <input type="number" name="can_16_count"
                                       class="form-control form-control-sm can-input"
                                       step="1" min="0"
                                       value="{{ $receipt->can_16_count }}"
                                       data-price="{{ $canPrices['can_16'] }}" data-id="{{ $receipt->id }}"
                                       placeholder="0"
                                       {{ $formLocked ? 'readonly' : '' }}>
                            </div>
                            <div class="can-item">
                                <div class="ci-label">🛢️ تنكة 8 كيلو</div>
                                <div class="ci-price">{{ number_format($canPrices['can_8'], 3) }} د.أ</div>
                                <input type="number" name="can_8_count"
                                       class="form-control form-control-sm can-input"
                                       step="1" min="0"
                                       value="{{ $receipt->can_8_count }}"
                                       data-price="{{ $canPrices['can_8'] }}" data-id="{{ $receipt->id }}"
                                       placeholder="0"
                                       {{ $formLocked ? 'readonly' : '' }}>
                            </div>
                            <div class="can-item">
                                <div class="ci-label">🛢️ تنكة 4 كيلو</div>
                                <div class="ci-price">{{ number_format($canPrices['can_4'], 3) }} د.أ</div>
                                <input type="number" name="can_4_count"
                                       class="form-control form-control-sm can-input"
                                       step="1" min="0"
                                       value="{{ $receipt->can_4_count }}"
                                       data-price="{{ $canPrices['can_4'] }}" data-id="{{ $receipt->id }}"
                                       placeholder="0"
                                       {{ $formLocked ? 'readonly' : '' }}>
                            </div>
                        </div>
                        <div class="can-total-display" id="canTotal_{{ $receipt->id }}">
                            مجموع التنك: {{ number_format($receipt->can_total, 3) }} د.أ
                            &nbsp;|&nbsp;
                            الصافي بعد الخصم: {{ number_format($receipt->total_fee, 3) }} د.أ
                        </div>
                    </div>

                    <hr class="section-divider">

                    {{-- الخصم يُطبَّق على أجرة العصر + التنك --}}
                    <div class="row g-2 align-items-end mb-2">
                        <div class="col-6 col-md-3">
                            <label class="form-label fw-bold" style="font-size:.78rem" for="discount_{{ $receipt->id }}">الخصم (د.أ)</label>
                            <div class="input-group input-group-sm">
                                <input type="number" name="discount_amount" id="discount_{{ $receipt->id }}"
                                       class="form-control discount-amount-input" step="0.001" min="0"
                                       max="{{ number_format((float)$receipt->press_fee + (float)$receipt->can_total, 3, '.', '') }}"
                                       value="{{ number_format($receipt->discount_amount ?? 0, 3, '.', '') }}"
                                       data-id="{{ $receipt->id }}" {{ $formLocked ? 'readonly' : '' }}>
                                <span class="input-group-text">د.أ</span>
                            </div>
                        </div>
                        <div class="col-6 col-md-3">
                            <div class="small text-muted">قبل الخصم</div>
                            <strong id="grossTotal_{{ $receipt->id }}">{{ number_format((float)$receipt->press_fee + (float)$receipt->can_total, 3) }}</strong> د.أ
                        </div>
                        <div class="col-6 col-md-3">
                            <div class="small text-muted">قيمة الخصم</div>
                            <strong class="text-danger" id="discountAmount_{{ $receipt->id }}">{{ number_format($receipt->discount_amount, 3) }}</strong> د.أ
                        </div>
                        <div class="col-6 col-md-3">
                            <div class="small text-muted">الصافي بعد الخصم</div>
                            <strong class="text-success" id="netTotal_{{ $receipt->id }}">{{ number_format($receipt->total_fee, 3) }}</strong> د.أ
                        </div>
                    </div>

                    <hr class="section-divider">

                    {{-- § 4: الحالة + حفظ --}}
                    <div class="row g-2 align-items-center">
                        <div class="col-6 col-md-3">
                            <label class="form-label" style="font-size:.78rem">الحالة</label>
                            <select name="status" class="form-select form-select-sm">
                                <option value="pending"    {{ $receipt->status=='pending'    ? 'selected':'' }}>انتظار</option>
                                <option value="processing" {{ $receipt->status=='processing' ? 'selected':'' }}>معالجة</option>
                                <option value="completed"  {{ $receipt->status=='completed'  ? 'selected':'' }}>مكتمل</option>
                                <option value="debt"       {{ $receipt->status=='debt'       ? 'selected':'' }}>دَين</option>
                            </select>
                        </div>
                        <div class="col-12 col-md-9 d-flex justify-content-end align-items-end">
                            <button type="submit" class="btn btn-olive px-4">
                                <i class="fas fa-save me-2"></i>حفظ جميع البيانات
                            </button>
                        </div>
                    </div>

                </form>
            </div>{{-- end cashier-form-box --}}

        </div>
    </div>
    @empty
    <div class="no-result-box">
        <div class="big-icon">📋</div>
        <p>لا توجد إيصالات لهذا الدور</p>
    </div>
    @endforelse

@else
    <div class="no-result-box">
        <div class="big-icon">🔍</div>
        <h5>لا يوجد دور بهذا الرقم</h5>
        <p class="text-muted">رقم الدور <strong>{{ request('floor_number') }}</strong> غير موجود في النظام</p>
    </div>
@endif
@endif

{{-- مودال فتح التعديل بكلمة مرور --}}
<div class="modal fade" id="unlockModal" tabindex="-1">
    <div class="modal-dialog modal-sm">
        <div class="modal-content">
            <div class="modal-header py-2" style="background:#fef3c7">
                <h6 class="modal-title fw-bold mb-0" style="color:#78350f">
                    <i class="fas fa-unlock me-2"></i>تفعيل التعديل
                </h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="unlockForm" method="POST">
                @csrf
                <div class="modal-body">
                    <p class="text-center text-muted small mb-2" id="unlockReceiptNum"></p>
                    @if(session('unlock_error'))
                    <div class="alert alert-danger py-2 text-center small">كلمة المرور غير صحيحة</div>
                    @endif
                    <input type="password" name="unlock_password"
                           class="form-control text-center fw-bold"
                           placeholder="••••••" required autofocus>
                </div>
                <div class="modal-footer py-2">
                    <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">إلغاء</button>
                    <button type="submit" class="btn btn-warning btn-sm fw-bold">
                        <i class="fas fa-unlock me-1"></i>فتح
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection

@section('scripts')
<script>

// ── تبديل تبويبات البحث ──
function switchTab(tab) {
    const floorForm   = document.getElementById('form-floor');
    const receiptForm = document.getElementById('form-receipt');
    const tabFloor    = document.getElementById('tab-floor');
    const tabReceipt  = document.getElementById('tab-receipt');
    if (tab === 'floor') {
        floorForm.style.display   = '';
        receiptForm.style.display = 'none';
        tabFloor.className   = 'btn btn-sm fw-bold btn-warning text-dark';
        tabReceipt.className = 'btn btn-sm fw-bold btn-outline-light';
        floorForm.querySelector('input[name="floor_number"]').focus();
    } else {
        floorForm.style.display   = 'none';
        receiptForm.style.display = '';
        tabFloor.className   = 'btn btn-sm fw-bold btn-outline-light';
        tabReceipt.className = 'btn btn-sm fw-bold btn-warning text-dark';
        receiptForm.querySelector('input[name="receipt_number"]').focus();
    }
}

// ── حساب أجرة العصر ──
function recalcPress(id) {
    const form  = document.getElementById('form_' + id);
    if (!form) return;
    const oilKg  = parseFloat(form.querySelector('.oil-kg-input')?.value)   || 0;
    const oilGm  = parseFloat(form.querySelector('.oil-gram-input')?.value) || 0;
    const oil    = oilKg + oilGm / 1000;
    const fee    = parseFloat(form.querySelector('.fee-kg-input[data-id="' + id + '"]')?.value) || 0;
    const press  = oil * fee;

    const resultEl = document.getElementById('pressResult_' + id);
    if (resultEl) resultEl.value = press.toFixed(3);

    // تحديث مجموع التنك والإجمالي
    recalcCan(id);
}

// ── حساب رديات الزيت تلقائياً ──
function recalcOilReturn(id) {
    const form = document.getElementById('form_' + id);
    if (!form) return;

    const n1 = parseFloat(form.querySelector('[name="oil_return_divisor"]')?.value) || 5.5;
    const n2 = parseFloat(form.querySelector('[name="can_return_divisor"]')?.value) || 5.5;

    const pressEl  = document.getElementById('pressResult_' + id);
    const press    = parseFloat(pressEl?.value) || 0;
    const toggle   = document.getElementById('cansInReturn_' + id);
    const inclCans = toggle && toggle.checked;

    let canSum = 0;
    form.querySelectorAll('.can-input').forEach(function(inp) {
        canSum += (parseInt(inp.value) || 0) * (parseFloat(inp.dataset.price) || 0);
    });

    const gross = press + canSum;
    const discount = Math.min(gross, Math.max(0, parseFloat(form.querySelector('.discount-amount-input')?.value) || 0));
    const net = Math.max(0, gross - discount);
    const netCan = gross > 0 ? Math.min(net, Math.round(canSum * net / gross * 1000) / 1000) : 0;
    let returnKg;
    if (inclCans && canSum > 0) {
        returnKg = n2 > 0 ? net / n2 : 0;
    } else {
        returnKg = n1 > 0 ? (net - netCan) / n1 : 0;
    }

    // حفظ الكيلو في الحقل
    const returnKgEl = document.getElementById('oilReturnKg_' + id);
    if (returnKgEl) returnKgEl.value = returnKg.toFixed(3);

    // عرض النتيجة
    const displayEl = document.getElementById('oilReturnDisplay_' + id);
    if (displayEl) {
        const covered = inclCans ? net : net - netCan;
        displayEl.textContent = '🫙 الرديات: ' + returnKg.toFixed(3) + ' كيلو زيت — تغطي: ' + covered.toFixed(3) + ' د.أ';
    }
    // تفعيل/تعطيل N₁ و N₂ حسب خيار تسوية التنك، مع حفظ قيمة الحقل غير المستخدم.
    const n1Row = document.getElementById('n1Row_' + id);
    const n2Row = document.getElementById('n2Row_' + id);
    [[n1Row, inclCans], [n2Row, !inclCans]].forEach(function([row, inactive]) {
        if (!row) return;
        row.classList.toggle('formula-disabled', inactive);
        const input = row.querySelector('input[type="number"]');
        const savedInput = row.querySelector('input[type="hidden"]');
        if (input) input.disabled = inactive;
        if (input && savedInput) {
            savedInput.value = input.value;
            savedInput.disabled = !inactive;
        }
    });

    // تحديث السطر السفلي دائماً بعد حساب الرديات
    updateSummaryLine(id);
}

// ── تحديث سطر الإجمالي السفلي ──
function updateSummaryLine(id) {
    const form = document.getElementById('form_' + id);
    if (!form) return;
    let canSum = 0;
    form.querySelectorAll('.can-input').forEach(function(inp) {
        canSum += (parseInt(inp.value) || 0) * (parseFloat(inp.dataset.price) || 0);
    });
    const pressEl = document.getElementById('pressResult_' + id);
    const press   = parseFloat(pressEl?.value) || 0;
    const gross   = press + canSum;
    const discountInput = form.querySelector('.discount-amount-input');
    if (discountInput) discountInput.max = Math.max(0, gross).toFixed(3);
    const discount = Math.min(gross, Math.max(0, parseFloat(discountInput?.value) || 0));
    const total = Math.max(0, gross - discount);
    const pm      = document.getElementById('pm_' + id);

    const el = document.getElementById('canTotal_' + id);
    if (!el) return;

    document.getElementById('grossTotal_' + id).textContent = gross.toFixed(3);
    document.getElementById('discountAmount_' + id).textContent = discount.toFixed(3);
    document.getElementById('netTotal_' + id).textContent = total.toFixed(3);
    let line = 'أجرة العصر: ' + press.toFixed(3) + ' د.أ  |  التنك: ' + canSum.toFixed(3) + ' د.أ  |  خصم: -' + discount.toFixed(3) + ' د.أ  |  الصافي: ' + total.toFixed(3) + ' د.أ';
    if (pm && pm.value === 'oil_return') {
        const returnKg = parseFloat(document.getElementById('oilReturnKg_' + id)?.value) || 0;
        line += '  |  🫙 الرديات: ' + returnKg.toFixed(3) + ' كيلو زيت';
    }
    el.textContent = line;
}

// ── حساب مجموع التنك ──
function recalcCan(id) {
    const pm = document.getElementById('pm_' + id);
    // لو طريقة الدفع رديات زيت — احسب الرديات أولاً (تحدّث oilReturnKg_ والسطر السفلي)
    if (pm && pm.value === 'oil_return') {
        recalcOilReturn(id); // recalcOilReturn ينادي updateSummaryLine بالنهاية
    } else {
        updateSummaryLine(id);
    }
}

// ── ربط الأحداث ──
document.querySelectorAll('.fee-kg-input').forEach(function(input) {
    input.addEventListener('input', function() { recalcPress(this.dataset.id); });
});

document.querySelectorAll('.oil-kg-input, .oil-gram-input').forEach(function(input) {
    input.addEventListener('input', function() {
        const id = this.dataset.id;
        recalcPress(id);
        recalcOilReturn(id);
    });
});

document.querySelectorAll('.can-input').forEach(function(input) {
    input.addEventListener('input', function() { recalcCan(this.dataset.id); });
});

document.querySelectorAll('.discount-amount-input').forEach(function(input) {
    input.addEventListener('input', function() { recalcCan(this.dataset.id); });
});

document.querySelectorAll('.cans-in-return-toggle').forEach(function(toggle) {
    toggle.addEventListener('change', function() { recalcOilReturn(this.dataset.id); });
});

document.querySelectorAll('.oil-return-divisor-input').forEach(function(input) {
    input.addEventListener('input', function() { recalcOilReturn(this.dataset.id); });
});

document.querySelectorAll('.can-return-divisor-input').forEach(function(input) {
    input.addEventListener('input', function() { recalcOilReturn(this.dataset.id); });
});

// ── تبديل طريقة الدفع (نقدي / رديات زيت) ──
function setPayMethod(id, method) {
    document.getElementById('pm_' + id).value = method;

    // تحديث أزرار الاختيار
    var cashBtn = document.getElementById('pcCash_' + id);
    var oilBtn  = document.getElementById('pcOil_' + id);
    if (cashBtn) cashBtn.className = 'pay-choice-btn' + (method === 'cash'       ? ' active-cash'       : '');
    if (oilBtn)  oilBtn.className  = 'pay-choice-btn' + (method === 'oil_return' ? ' active-oil_return' : '');

    // إظهار/إخفاء لوحة رديات الزيت فقط
    var oilPanel = document.getElementById('panel_oil_return_' + id);
    if (oilPanel) {
        if (method === 'oil_return') {
            oilPanel.classList.add('visible');
            recalcOilReturn(id);
        } else {
            oilPanel.classList.remove('visible');
        }
    }
    recalcCan(id);
}

// ── فتح مودال تعديل الكاشير ──
function openUnlockModal(id, num) {
    const url = '{{ route("cashier.unlock-form", ":id") }}'.replace(':id', id);
    document.getElementById('unlockForm').action = url;
    document.getElementById('unlockReceiptNum').textContent = 'إيصال رقم: ' + num;
    new bootstrap.Modal(document.getElementById('unlockModal')).show();
}
@if(session('unlock_error'))
openUnlockModal({{ session('unlock_error') }}, '');
@endif
</script>
@endsection
