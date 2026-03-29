@extends('layouts.app')
@section('title', 'الكاشير')
@section('page-title', 'الكاشير')

@section('styles')
<style>
    /* ── صندوق البحث ── */
    .cashier-search-box {
        background: linear-gradient(135deg, var(--olive-dark), var(--olive-main));
        border-radius: 16px;
        padding: 2rem 1.5rem;
        color: #fff;
        text-align: center;
        margin-bottom: 1.5rem;
    }
    .cashier-search-box h4 { font-weight: 900; color: var(--gold-light); margin-bottom: .5rem; }
    .cashier-search-box .input-group { max-width: 380px; margin: 0 auto; }
    .cashier-search-box input {
        font-size: 1.6rem; font-weight: 900; text-align: center;
        border: none; border-radius: 10px 0 0 10px !important; letter-spacing: 2px;
    }
    .cashier-search-box .btn { border-radius: 0 10px 10px 0 !important; font-weight: 700; padding: 0 1.5rem; }

    /* ── رأس المالك ── */
    .owner-header {
        background: linear-gradient(135deg, #1a3a0a, #2d5a14);
        color: #fff;
        border-radius: 14px;
        padding: 1.2rem 1.5rem;
        margin-bottom: 1rem;
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

    /* ── بطاقة الحساب الإجمالي ── */
    .grand-calc {
        background: #fff;
        border-radius: 14px;
        border: 2px solid var(--olive-pale);
        padding: 1.2rem 1.5rem;
        margin-bottom: 1.2rem;
        box-shadow: 0 2px 12px rgba(0,0,0,.07);
    }
    .grand-calc h6 {
        font-weight: 800; color: var(--olive-dark);
        border-bottom: 2px solid var(--olive-pale); padding-bottom: .5rem; margin-bottom: 1rem;
    }
    .calc-grid {
        display: grid; grid-template-columns: repeat(5, 1fr); gap: .6rem;
    }
    .calc-item {
        background: #f8faf4; border-radius: 10px; padding: .8rem .6rem; text-align: center;
        border-top: 3px solid;
    }
    .calc-item.olive  { border-color: var(--olive-main); }
    .calc-item.oil    { border-color: var(--gold); }
    .calc-item.press  { border-color: #3b82f6; }
    .calc-item.driver { border-color: #f59e0b; }
    .calc-item.total  { border-color: #ef4444; background: #fff5f5; }
    .calc-item .cv    { font-size: 1.4rem; font-weight: 900; line-height: 1.1; }
    .calc-item .cl    { font-size: .72rem; color: #666; margin-top: 3px; }
    .calc-item.olive  .cv { color: var(--olive-dark); }
    .calc-item.oil    .cv { color: var(--gold); }
    .calc-item.press  .cv { color: #1d4ed8; }
    .calc-item.driver .cv { color: #b45309; }
    .calc-item.total  .cv { color: #dc2626; }

    .formula-box {
        background: #f0f7e8; border: 1px solid #c5e0a0; border-radius: 10px;
        padding: .6rem 1rem; font-size: .82rem; color: var(--olive-dark); margin-top: .75rem;
        direction: rtl; text-align: right;
    }
    .formula-box span { font-weight: 800; }

    /* ── بطاقة الإيصال ── */
    .receipt-card {
        background: #fff; border-radius: 12px;
        border: 1.5px solid #e0ecd5; margin-bottom: 1rem;
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

    /* ── نتيجة الحساب للإيصال ── */
    .receipt-calc {
        display: grid; grid-template-columns: repeat(3, 1fr); gap: .5rem;
        background: #fffbeb; border: 1px solid #fcd34d; border-radius: 10px;
        padding: .75rem; margin-bottom: .75rem;
    }
    .rc-item { text-align: center; }
    .rc-item .rc-l { font-size: .7rem; color: #78350f; }
    .rc-item .rc-v { font-weight: 800; font-size: 1rem; color: #92400e; }
    .rc-item.total-item .rc-v { color: #dc2626; font-size: 1.15rem; }

    .oil-form {
        background: #f0f9ff; border: 1.5px solid #bae6fd; border-radius: 10px; padding: 1rem;
    }
    .oil-form-title { font-size: .84rem; color: #0c4a6e; font-weight: 800; margin-bottom: .6rem; }

    .no-result-box { text-align: center; padding: 3rem 1rem; color: #888; }
    .no-result-box .big-icon { font-size: 4rem; margin-bottom: 1rem; }

    @media (max-width: 700px) {
        .calc-grid { grid-template-columns: repeat(3, 1fr); }
        .ri-grid   { grid-template-columns: repeat(2, 1fr); }
        .receipt-calc { grid-template-columns: repeat(2, 1fr); }
    }
</style>
@endsection

@section('content')

{{-- ── صندوق البحث ── --}}
<div class="cashier-search-box">
    <h4><i class="fas fa-cash-register me-2"></i>كاشير المعصرة</h4>
    <p class="mb-3" style="opacity:.8; font-size:.9rem">ابحث بـ <strong>رقم الدور</strong> لعرض بيانات صاحب الزيتون وحسابه الكامل</p>
    <form method="GET" action="{{ route('cashier.index') }}">
        <div class="input-group">
            <input type="number" name="floor_number" class="form-control"
                   placeholder="رقم الدور..."
                   value="{{ request('floor_number') }}"
                   min="1" autofocus>
            <button type="submit" class="btn btn-warning">
                <i class="fas fa-search me-1"></i>بحث
            </button>
        </div>
    </form>
</div>

@if(request()->filled('floor_number'))

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
            <a href="{{ route('owners.show', $owner) }}" class="btn btn-sm btn-outline-light">
                <i class="fas fa-user me-1"></i>ملف المالك
            </a>
        </div>

        @php
            $totalOlive   = $receipts->sum('olive_weight_kg');
            $totalOilKg   = $receipts->sum('oil_weight_kg');
            $totalOilGm   = $receipts->sum('oil_weight_gram');
            $totalPressF  = $receipts->sum('press_fee');
            $totalDriverF = $receipts->sum('driver_fee');
            $grandTotal   = $receipts->sum('total_fee');
            $extractRate  = $totalOlive > 0 ? ($totalOilKg / $totalOlive) * 100 : 0;

            // normalize grams
            $totalOilKg  += floor($totalOilGm / 1000);
            $totalOilGm   = $totalOilGm % 1000;
        @endphp

        {{-- ── بطاقة الحساب الإجمالي ── --}}
        <div class="grand-calc">
            <h6><i class="fas fa-calculator me-2"></i>ملخص الحساب الإجمالي — {{ $receipts->count() }} إيصال</h6>

            <div class="calc-grid">
                <div class="calc-item olive">
                    <div class="cv">{{ number_format($totalOlive, 1) }}</div>
                    <div class="cl">كيلو زيتون</div>
                </div>
                <div class="calc-item oil">
                    <div class="cv">
                        {{ number_format($totalOilKg, 0) }}
                        @if($totalOilGm > 0)<small style="font-size:.7rem">+{{ (int)$totalOilGm }}غم</small>@endif
                    </div>
                    <div class="cl">كيلو زيت معصور</div>
                </div>
                <div class="calc-item press">
                    <div class="cv">{{ number_format($totalPressF, 3) }}</div>
                    <div class="cl">أجرة العصر (د.أ)</div>
                </div>
                <div class="calc-item driver">
                    <div class="cv">{{ number_format($totalDriverF, 2) }}</div>
                    <div class="cl">أجرة السائق (د.أ)</div>
                </div>
                <div class="calc-item total">
                    <div class="cv">{{ number_format($grandTotal, 3) }}</div>
                    <div class="cl">الإجمالي المستحق (د.أ)</div>
                </div>
            </div>

            <div class="formula-box">
                <i class="fas fa-info-circle me-1"></i>
                <span>المعادلة:</span>
                أجرة العصر = كمية الزيتون × سعر العصر/كيلو &nbsp;|&nbsp;
                الإجمالي = أجرة العصر + أجرة السائق &nbsp;|&nbsp;
                نسبة الاستخلاص = <span>{{ number_format($extractRate, 1) }}%</span>
            </div>
        </div>

        {{-- ── الإيصالات ── --}}
        @forelse($receipts as $receipt)
        <div class="receipt-card">
            <div class="receipt-card-header">
                <span class="rnum"><i class="fas fa-file-alt me-1"></i>{{ $receipt->receipt_number }}</span>
                <div class="d-flex gap-2 align-items-center">
                    <span class="badge badge-{{ $receipt->status }} rounded-pill px-3">{{ $receipt->status_label }}</span>
                    <span class="text-muted small">{{ $receipt->created_at->format('Y/m/d') }}</span>
                </div>
            </div>
            <div class="receipt-card-body">

                {{-- معلومات ── --}}
                <div class="ri-grid">
                    <div class="ri-item">
                        <div class="ri-l">حامل الزيتون</div>
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
                    <div class="ri-item">
                        <div class="ri-l">نسبة الاستخلاص</div>
                        <div class="ri-v" style="color:#0d9488">
                            {{ $receipt->extraction_rate > 0 ? $receipt->extraction_rate . '%' : '—' }}
                        </div>
                    </div>
                </div>

                {{-- ── نتيجة الحساب ── --}}
                <div class="receipt-calc">
                    <div class="rc-item">
                        <div class="rc-l">سعر العصر / كيلو</div>
                        <div class="rc-v">{{ number_format($receipt->fee_per_kg, 3) }} د.أ</div>
                    </div>
                    <div class="rc-item">
                        <div class="rc-l">أجرة العصر</div>
                        <div class="rc-v">
                            {{ number_format($receipt->olive_weight_kg, 1) }} ×
                            {{ number_format($receipt->fee_per_kg, 3) }} =
                            <strong>{{ number_format($receipt->press_fee, 3) }} د.أ</strong>
                        </div>
                    </div>
                    <div class="rc-item total-item">
                        <div class="rc-l">الإجمالي (عصر + سائق)</div>
                        <div class="rc-v">
                            {{ number_format($receipt->press_fee, 3) }} +
                            {{ number_format($receipt->driver_fee, 2) }} =
                            <strong>{{ number_format($receipt->total_fee, 3) }} د.أ</strong>
                        </div>
                    </div>
                </div>

                {{-- ── نموذج التحديث ── --}}
                <div class="oil-form">
                    <div class="oil-form-title"><i class="fas fa-edit me-1"></i>تحديث بيانات العصر والأجرة</div>
                    <form action="{{ route('cashier.update-oil', $receipt) }}" method="POST">
                        @csrf @method('PATCH')
                        <div class="row g-2 align-items-end">
                            <div class="col-6 col-md-2">
                                <label class="form-label" style="font-size:.78rem">زيت (كيلو)</label>
                                <input type="number" name="oil_weight_kg" class="form-control form-control-sm"
                                       step="1" min="0" value="{{ $receipt->oil_weight_kg }}" placeholder="0">
                            </div>
                            <div class="col-6 col-md-2">
                                <label class="form-label" style="font-size:.78rem">كسور (غم)</label>
                                <input type="number" name="oil_weight_gram" class="form-control form-control-sm"
                                       step="1" min="0" max="999" value="{{ $receipt->oil_weight_gram }}" placeholder="0">
                            </div>
                            <div class="col-6 col-md-2">
                                <label class="form-label" style="font-size:.78rem">سعر العصر/كيلو</label>
                                <input type="number" name="fee_per_kg" class="form-control form-control-sm"
                                       step="0.001" min="0" value="{{ $receipt->fee_per_kg }}"
                                       id="feeKg_{{ $receipt->id }}" placeholder="0.000">
                            </div>
                            <div class="col-6 col-md-2">
                                <label class="form-label" style="font-size:.78rem">أجرة السائق (د.أ)</label>
                                <input type="number" name="driver_fee" class="form-control form-control-sm"
                                       step="0.01" min="0" value="{{ $receipt->driver_fee }}"
                                       id="driverFee_{{ $receipt->id }}" placeholder="0.00">
                            </div>
                            <div class="col-6 col-md-2">
                                <label class="form-label" style="font-size:.78rem">الإجمالي المحسوب</label>
                                <div class="input-group input-group-sm">
                                    <input type="text" class="form-control fw-bold text-danger" readonly
                                           id="calcTotal_{{ $receipt->id }}"
                                           value="{{ number_format($receipt->total_fee, 3) }}">
                                    <span class="input-group-text" style="font-size:.72rem">د.أ</span>
                                </div>
                            </div>
                            <div class="col-12 col-md-1">
                                <label class="form-label" style="font-size:.78rem">الحالة</label>
                                <select name="status" class="form-select form-select-sm">
                                    <option value="pending"    {{ $receipt->status=='pending'    ? 'selected':'' }}>انتظار</option>
                                    <option value="processing" {{ $receipt->status=='processing' ? 'selected':'' }}>معالجة</option>
                                    <option value="completed"  {{ $receipt->status=='completed'  ? 'selected':'' }}>مكتمل</option>
                                </select>
                            </div>
                            <div class="col-12 d-flex gap-2 justify-content-end mt-1">
                                <a href="{{ route('receipts.print', $receipt) }}" target="_blank"
                                   class="btn btn-sm btn-outline-secondary">
                                    <i class="fas fa-print me-1"></i>طباعة
                                </a>
                                <button type="submit" class="btn btn-olive btn-sm px-3">
                                    <i class="fas fa-save me-1"></i>حفظ التحديث
                                </button>
                            </div>
                            <input type="hidden" name="olive_weight_kg" value="{{ $receipt->olive_weight_kg }}">
                        </div>
                    </form>
                </div>

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
@endsection

@section('scripts')
<script>
// حساب الإجمالي تلقائياً عند تغيير سعر العصر أو أجرة السائق
document.querySelectorAll('[id^="feeKg_"]').forEach(input => {
    const id       = input.id.replace('feeKg_', '');
    const form     = input.closest('form');
    const oliveKg  = parseFloat(form.querySelector('[name="olive_weight_kg"]').value) || 0;
    const driverEl = document.getElementById('driverFee_' + id);
    const totalEl  = document.getElementById('calcTotal_' + id);

    function recalc() {
        const fee    = parseFloat(input.value)   || 0;
        const driver = parseFloat(driverEl.value) || 0;
        const press  = oliveKg * fee;
        totalEl.value = (press + driver).toFixed(3);
    }

    input.addEventListener('input', recalc);
    driverEl.addEventListener('input', recalc);
});
</script>
@endsection
