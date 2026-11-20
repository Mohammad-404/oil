<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <title>تفاصيل الحساب — {{ $owner->name }}</title>
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;600;700;800;900&display=swap" rel="stylesheet">
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body { font-family: 'Cairo', sans-serif; background: #fff; direction: rtl; color: #1a1a1a; font-size: 13px; }
        .page { max-width: 720px; margin: 0 auto; padding: 20px; }

        .doc-header {
            display: flex; justify-content: space-between; align-items: flex-start;
            border-bottom: 3px solid #2d5016; padding-bottom: 12px; margin-bottom: 14px;
        }
        .press-name { font-size: 1.25rem; font-weight: 900; color: #2d5016; }
        .press-sub  { font-size: .78rem; color: #666; margin-top: 2px; }
        .doc-meta   { text-align: left; font-size: .8rem; color: #555; }
        .doc-meta strong { color: #2d5016; }

        .owner-box {
            background: #e8f5d4; border: 1.5px solid #7ab648; border-radius: 8px;
            padding: 10px 14px; margin-bottom: 10px;
            display: flex; gap: 20px; align-items: center;
        }
        .owner-box .turn {
            background: #2d5016; color: #f0d060; width: 52px; height: 52px;
            border-radius: 50%; display: flex; align-items: center; justify-content: center;
            font-size: 1.4rem; font-weight: 900; flex-shrink: 0;
        }
        .owner-box .name { font-size: 1.1rem; font-weight: 900; color: #2d5016; }
        .owner-box .meta { font-size: .8rem; color: #555; margin-top: 3px; }

        /* ── طريقة الدفع بارزة ── */
        .pay-method-banner {
            display: flex; gap: 8px; margin-bottom: 14px; flex-wrap: wrap;
        }
        .pay-badge {
            display: flex; align-items: center; gap: 6px;
            padding: 7px 16px; border-radius: 8px; font-weight: 800; font-size: .9rem;
            border: 2px solid; flex: 1; justify-content: center;
        }
        .pay-badge.active-cash       { background: #f0fdf4; border-color: #22c55e; color: #15803d; }
        .pay-badge.active-oil_return { background: #fffbeb; border-color: #f59e0b; color: #92400e; }
        .pay-badge.active-can        { background: #eff6ff; border-color: #3b82f6; color: #1d4ed8; }
        .pay-badge.inactive          { background: #f9f9f9; border-color: #e5e7eb; color: #d1d5db; }
        .check-icon { font-size: 1.1rem; }

        .summary-grid { display: grid; grid-template-columns: repeat(4,1fr); gap: 8px; margin-bottom: 14px; }
        .summary-cell { border: 1.5px solid #ddd; border-radius: 7px; padding: 7px 8px; text-align: center; }
        .summary-cell.olive { border-color: #7ab648; }
        .summary-cell.oil   { border-color: #c9a227; }
        .summary-cell.press { border-color: #3b82f6; }
        .summary-cell.total { border-color: #ef4444; background: #fff5f5; }
        .summary-cell .sv   { font-size: 1.2rem; font-weight: 900; }
        .summary-cell .sl   { font-size: .7rem; color: #666; margin-top: 2px; }
        .summary-cell.olive .sv { color: #2d5016; }
        .summary-cell.oil   .sv { color: #b45309; }
        .summary-cell.press .sv { color: #1d4ed8; }
        .summary-cell.total .sv { color: #dc2626; }

        .section-title {
            background: #2d5016; color: #fff; font-weight: 800; font-size: .88rem;
            padding: 6px 12px; border-radius: 5px; margin-bottom: 8px;
        }
        table { width: 100%; border-collapse: collapse; margin-bottom: 14px; font-size: .82rem; }
        thead th {
            background: #e8f5d4; color: #2d5016; font-weight: 800;
            padding: 6px 8px; border: 1px solid #c5e0a0; text-align: center;
        }
        tbody td { padding: 6px 8px; border: 1px solid #e0e0e0; vertical-align: middle; }
        tbody tr:nth-child(even) { background: #fafff5; }
        tfoot td {
            background: #2d5016; color: #fff; font-weight: 900;
            padding: 7px 8px; border: 1px solid #2d5016;
        }

        .pay-cash       { color: #15803d; font-weight: 700; }
        .pay-oil_return { color: #b45309; font-weight: 700; }
        .pay-can        { color: #1d4ed8; font-weight: 700; }

        .can-tag {
            background: #dbeafe; color: #1e40af; border-radius: 4px;
            padding: 1px 5px; font-weight: 700; font-size: .72rem;
            display: inline-block; margin: 1px;
        }
        .can-price-row {
            font-size: .72rem; color: #64748b; margin-top: 2px;
        }

        .payment-summary { border: 2px solid #2d5016; border-radius: 8px; padding: 10px 14px; margin-bottom: 14px; }
        .payment-summary h4 { font-size: .9rem; font-weight: 900; color: #2d5016; margin-bottom: 8px; }
        .pay-row { display: flex; justify-content: space-between; padding: 5px 0; border-bottom: 1px dashed #ddd; font-size: .83rem; }
        .pay-row:last-child { border: none; }
        .pay-row .label { color: #555; }
        .pay-row .val   { font-weight: 800; }

        .grand-total-box {
            background: #2d5016; color: #fff; border-radius: 10px;
            padding: 14px; text-align: center; margin-bottom: 14px;
        }
        .grand-total-box .gt-label  { font-size: .88rem; opacity: .85; }
        .grand-total-box .gt-amount { font-size: 2.4rem; font-weight: 900; color: #f0d060; line-height: 1.1; }
        .grand-total-box .gt-unit   { font-size: .85rem; opacity: .8; }

        .signatures { display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-top: 24px; }
        .sig-box { border-top: 2px solid #2d5016; padding-top: 6px; font-size: .78rem; color: #555; text-align: center; }

        .footer-note { text-align: center; font-size: .72rem; color: #999; margin-top: 16px; }

        .no-print {
            text-align: center; padding: 12px; background: #f0f0f0;
            border-bottom: 1px solid #ddd; position: sticky; top: 0; z-index: 10;
            display: flex; gap: 10px; justify-content: center; align-items: center;
        }
        .btn-print { font-family:'Cairo',sans-serif; font-size:.95rem; padding:8px 24px; border-radius:7px; cursor:pointer; border:none; font-weight:700; background:#2d5016; color:#fff; }
        .btn-close  { font-family:'Cairo',sans-serif; font-size:.95rem; padding:8px 24px; border-radius:7px; cursor:pointer; border:none; font-weight:700; background:#6b7280; color:#fff; }

        @media print {
            .no-print { display: none !important; }
            body { margin: 0; font-size: 12px; }
            .page { padding: 10px; max-width: 100%; }
        }
    </style>
</head>
<body>

<div class="no-print">
    <button class="btn-print" onclick="window.print()">🖨️ طباعة</button>
    <button class="btn-close" onclick="window.close()">✕ إغلاق</button>
</div>

<div class="page">

    {{-- رأس المستند --}}
    <div class="doc-header">
        <div>
            <div style="font-size:1.5rem">🫒</div>
            <div class="press-name">{{ $pressName }}</div>
            @if(!empty($pressPhone))
            <div class="press-sub" style="color:#2d5016;font-weight:700">📞 {{ $pressPhone }}</div>
            @endif
            <div class="press-sub">تفاصيل حساب العميل</div>
        </div>
        <div class="doc-meta">
            <div>التاريخ: <strong>{{ now()->format('Y/m/d') }}</strong></div>
            <div>الوقت: <strong>{{ now()->format('H:i') }}</strong></div>
        </div>
    </div>

    {{-- بيانات المالك --}}
    <div class="owner-box">
        <div class="turn">{{ $owner->floor_number }}</div>
        <div>
            <div class="name">{{ $owner->name }}</div>
            <div class="meta">
                📍 {{ $owner->city }} — {{ $owner->street }}
                &nbsp;|&nbsp; 📞 {{ $owner->phone }}
                &nbsp;|&nbsp; رقم الدور: <strong style="color:#2d5016">{{ $owner->floor_number }}</strong>
            </div>
        </div>
    </div>

    @php
        $receipts     = $owner->receipts;
        $totalOlive   = $receipts->sum('olive_weight_kg');
        $totalOilKg   = $receipts->sum('oil_weight_kg');
        $totalOilGm   = (int)$receipts->sum('oil_weight_gram');
        $totalPress   = $receipts->sum('press_fee');
        $grandTotal   = $receipts->sum('total_fee');
        $extractRate  = $totalOlive > 0 ? round(($totalOilKg / $totalOlive) * 100, 1) : 0;

        $totalOilReturn   = $receipts->where('payment_method','oil_return')->sum('oil_return_kg');
        $totalOilReturnGm = (int)$receipts->where('payment_method','oil_return')->sum('oil_return_gram');

        $totalCan16 = (int)$receipts->sum('can_16_count');
        $totalCan8  = (int)$receipts->sum('can_8_count');
        $totalCan4  = (int)$receipts->sum('can_4_count');
        $totalCanVal= $receipts->sum('can_total');

        $cashTotal      = $receipts->where('payment_method','cash')->sum('total_fee');
        $oilReturnTotal = $receipts->where('payment_method','oil_return')->sum('total_fee');
        $canTotal       = $receipts->where('payment_method','can')->sum('total_fee');
        // نقدي فعلي: نقدي + تنك رديات زيت غير مُسوَّاة
        $netCashTotal   = $cashTotal
                        + $receipts->where('payment_method','oil_return')->where('cans_in_return', false)->sum('discounted_can_total')
                        + $canTotal;
        // رديات زيت بالكيلو
        $netOilRetKg    = $totalOilReturn;

        $normOilKg = $totalOilKg + (int)floor($totalOilGm / 1000);
        $normOilGm = $totalOilGm % 1000;

        // طريقة الدفع السائدة
        $dominantMethod = $receipts->groupBy('payment_method')->sortByDesc(fn($g) => $g->count())->keys()->first() ?? 'cash';
    @endphp

    {{-- ── طريقة الدفع بارزة ── --}}
    <div class="pay-method-banner">
        <div class="pay-badge {{ $dominantMethod === 'cash'       ? 'active-cash'       : 'inactive' }}">
            @if($dominantMethod === 'cash') <span class="check-icon">✅</span> @else <span class="check-icon">⬜</span> @endif
            نقدي
        </div>
        <div class="pay-badge {{ $dominantMethod === 'oil_return' ? 'active-oil_return' : 'inactive' }}">
            @if($dominantMethod === 'oil_return') <span class="check-icon">✅</span> @else <span class="check-icon">⬜</span> @endif
            رديات زيت
            @if($dominantMethod === 'oil_return' && $totalOilReturn > 0)
                <small>({{ number_format($totalOilReturn,1) }} كيلو)</small>
            @endif
        </div>
        <div class="pay-badge {{ $dominantMethod === 'can'        ? 'active-can'        : 'inactive' }}">
            @if($dominantMethod === 'can') <span class="check-icon">✅</span> @else <span class="check-icon">⬜</span> @endif
            بدل تنكة
            @if($dominantMethod === 'can' && $totalCanVal > 0)
                <small>({{ number_format($totalCanVal,3) }} د.أ)</small>
            @endif
        </div>
    </div>

    {{-- ملخص إجمالي --}}
    <div class="summary-grid">
        <div class="summary-cell olive">
            <div class="sv">{{ number_format($totalOlive, 1) }}</div>
            <div class="sl">كيلو زيتون</div>
        </div>
        <div class="summary-cell oil">
            <div class="sv">
                {{ number_format($normOilKg, 0) }}
                @if($normOilGm > 0)<span style="font-size:.7rem">+{{ $normOilGm }}غم</span>@endif
            </div>
            <div class="sl">كيلو زيت معصور</div>
        </div>
        <div class="summary-cell press">
            <div class="sv">{{ number_format($totalPress, 3) }}</div>
            <div class="sl">أجرة العصر (د.أ)</div>
        </div>
        <div class="summary-cell total">
            <div class="sv">{{ number_format($netCashTotal, 3) }}</div>
            <div class="sl">💵 نقدي (د.أ)</div>
        </div>
        @if($netOilRetKg > 0)
        <div class="summary-cell" style="border-color:#d97706;background:#fffbeb">
            <div class="sv" style="color:#b45309">{{ number_format($netOilRetKg, 3) }}</div>
            <div class="sl">🫙 رديات الزيت (كيلو)</div>
        </div>
        @endif
    </div>
    @if($extractRate > 0)
    <div style="text-align:center;font-size:.78rem;color:#555;margin-top:-8px;margin-bottom:12px">
        نسبة الاستخلاص: <strong style="color:#2d5016">{{ $extractRate }}%</strong>
    </div>
    @endif

    {{-- جدول الإيصالات --}}
    <div class="section-title">📋 تفاصيل الإيصالات</div>
    <table>
        <thead>
            <tr>
                <th>رقم الإيصال</th>
                <th>الناقل</th>
                <th>زيتون (كيلو)</th>
                <th>زيت (كيلو)</th>
                <th>أجرة العصر</th>
                <th>طريقة الدفع</th>
                <th>المستحق</th>
            </tr>
        </thead>
        <tbody>
            @foreach($receipts as $r)
            <tr>
                <td style="font-weight:700;text-align:center">{{ $r->receipt_number }}</td>
                <td>{{ $r->carrier_name }}</td>
                <td style="text-align:center">{{ number_format($r->olive_weight_kg, 1) }}</td>
                <td style="text-align:center">
                    @if($r->oil_weight_kg > 0)
                        {{ number_format($r->oil_weight_kg, 0) }}
                        @if((int)$r->oil_weight_gram > 0)
                            +{{ (int)$r->oil_weight_gram }}غم
                        @endif
                    @else
                        <span style="color:#ccc">—</span>
                    @endif
                </td>
                <td style="text-align:center">
                    {{ number_format($r->press_fee, 3) }} د.أ
                    <div style="font-size:.68rem;color:#888">
                        {{ number_format($r->olive_weight_kg,1) }} × {{ number_format($r->fee_per_kg,3) }}
                    </div>
                </td>
                <td style="text-align:center">
                    @if($r->payment_method === 'cash')
                        <span class="pay-cash">💵 نقدي</span>
                        @if($r->can_total > 0)
                            <div style="margin-top:4px;border-top:1px dashed #bfdbfe;padding-top:3px">
                                @if($r->can_16_count > 0)
                                    <div><span class="can-tag">{{ $r->can_16_count }} × تنكة 16</span>
                                    <span class="can-price-row">= {{ number_format($r->can_16_count * $canPrices['can_16'], 3) }} د.أ</span></div>
                                @endif
                                @if($r->can_8_count > 0)
                                    <div><span class="can-tag">{{ $r->can_8_count }} × تنكة 8</span>
                                    <span class="can-price-row">= {{ number_format($r->can_8_count * $canPrices['can_8'], 3) }} د.أ</span></div>
                                @endif
                                @if($r->can_4_count > 0)
                                    <div><span class="can-tag">{{ $r->can_4_count }} × تنكة 4</span>
                                    <span class="can-price-row">= {{ number_format($r->can_4_count * $canPrices['can_4'], 3) }} د.أ</span></div>
                                @endif
                                <div style="font-size:.75rem;color:#1d4ed8;font-weight:700;margin-top:2px">
                                    📦 تنك: {{ number_format($r->can_total,3) }} د.أ
                                </div>
                            </div>
                        @endif
                    @elseif($r->payment_method === 'oil_return')
                        @php
                            $rN1 = (float)($r->oil_return_divisor ?? 7);
                            $rN2 = (float)($r->can_return_divisor  ?? 7);
                            if ($r->cans_in_return && $r->can_total > 0) {
                                $rReturnKg = $rN2 > 0 ? (float)$r->total_fee / $rN2 : 0;
                                $rFormula  = number_format($r->total_fee,3) . ' ÷ ' . number_format($rN2,3);
                            } else {
                                $rReturnKg = $rN1 > 0 ? (float)$r->discounted_press_fee / $rN1 : 0;
                                $rFormula  = number_format($r->discounted_press_fee,3) . ' ÷ ' . number_format($rN1,3);
                            }
                        @endphp
                        <span class="pay-oil_return">🫙 رديات زيت</span>
                        @if($r->press_fee > 0)
                            <div style="font-size:.72rem;color:#92400e;margin-top:2px">
                                {{ $rFormula }}
                                = <strong>{{ number_format($rReturnKg,3) }} كيلو زيت</strong>
                            </div>
                        @endif
                        @if($r->can_total > 0)
                            @if($r->cans_in_return)
                                <div style="margin-top:4px;background:#fef3c7;border-radius:4px;padding:3px 6px;font-size:.71rem;color:#92400e">
                                    📦 تنك مُسوَّاة بالرديات ✓
                                    @if($r->can_16_count > 0)({{ $r->can_16_count }}×16)@endif
                                    @if($r->can_8_count  > 0)({{ $r->can_8_count  }}×8)@endif
                                    @if($r->can_4_count  > 0)({{ $r->can_4_count  }}×4)@endif
                                </div>
                            @else
                                <div style="margin-top:4px;background:#fee2e2;border-radius:4px;padding:3px 5px;font-size:.72rem;color:#991b1b;font-weight:700">
                                    📦 تنك تُدفع نقداً:
                                    @if($r->can_16_count > 0) {{ $r->can_16_count }}×16 @endif
                                    @if($r->can_8_count  > 0) {{ $r->can_8_count  }}×8  @endif
                                    @if($r->can_4_count  > 0) {{ $r->can_4_count  }}×4  @endif
                                    = {{ number_format($r->discounted_can_total,3) }} د.أ بعد الخصم
                                </div>
                            @endif
                        @endif
                    @else
                        <span style="color:#999">—</span>
                    @endif
                </td>
                <td style="text-align:center;font-weight:800;color:#dc2626">
                    {{ number_format($r->total_fee, 3) }} د.أ
                    @if($r->discount_amount > 0)
                        <div style="font-size:.68rem;color:#b45309;font-weight:600">خصم −{{ number_format($r->discount_amount,3) }} د.أ</div>
                    @endif
                    @if($r->can_total > 0)
                        <div style="font-size:.68rem;color:#64748b;font-weight:400">
                            {{ number_format($r->press_fee,3) }} + {{ number_format($r->can_total,3) }}
                        </div>
                    @endif
                </td>
            </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr>
                <td colspan="4" style="text-align:right">الإجمالي الكلي</td>
                <td style="text-align:center">{{ number_format($totalPress, 3) }} د.أ</td>
                <td style="text-align:center;font-size:.8rem">
                    @if($totalCanVal > 0) تنك: {{ number_format($totalCanVal,3) }} د.أ @endif
                </td>
                <td style="text-align:center;font-size:.95rem">
                    <div style="color:#f0d060">💵 {{ number_format($netCashTotal, 3) }} د.أ</div>
                    @if($netOilRetKg > 0)
                    <div style="color:#fcd34d;font-size:.85rem;margin-top:2px">🫙 {{ number_format($netOilRetKg, 3) }} كيلو زيت</div>
                    @endif
                </td>
            </tr>
        </tfoot>
    </table>

    {{-- ملخص طريقة الدفع --}}
    @php
        $cashReceipts      = $receipts->where('payment_method','cash');
        $oilReturnReceipts = $receipts->where('payment_method','oil_return');
        $cashTotalFee      = $cashReceipts->sum('total_fee');
        $oilReturnTotalFee = $oilReturnReceipts->sum(fn($r) => $r->cans_in_return ? (float)$r->total_fee : $r->discounted_press_fee);
    @endphp
    @if($cashTotalFee > 0 || $oilReturnTotalFee > 0)
    <div class="payment-summary">
        <h4>💳 ملخص طريقة الدفع</h4>

        @if($cashTotalFee > 0)
        <div class="pay-row">
            <span class="label">
                💵 نقدي
                @php $cashCans = $cashReceipts->sum('discounted_can_total'); @endphp
                @if($cashCans > 0)
                    <span style="font-size:.75rem;color:#1e40af">
                        (أجرة عصر + تنك {{ number_format($cashCans,3) }} د.أ)
                    </span>
                @endif
            </span>
            <span class="val pay-cash">{{ number_format($cashTotalFee, 3) }} د.أ</span>
        </div>
        @endif

        @if($oilReturnTotalFee > 0)
        @php
            $oilCansInReturn = $oilReturnReceipts->where('cans_in_return', true)->sum('discounted_can_total');
            $oilCansCash     = $oilReturnReceipts->where('cans_in_return', false)->sum('discounted_can_total');
            // إجمالي الرديات بالكيلو (المحسوب المحفوظ)
            $totalReturnKg   = $oilReturnReceipts->sum('oil_return_kg');
        @endphp
        <div class="pay-row">
            <span class="label">
                🫙 رديات زيت
                <span style="font-size:.74rem;color:#92400e;display:block;margin-top:2px">
                    إجمالي الرديات: <strong>{{ number_format($totalReturnKg, 2) }} كيلو</strong>
                    @if($oilCansInReturn > 0)
                        <span style="color:#b45309">(شامل تنك مُسوَّاة {{ number_format($oilCansInReturn,3) }} د.أ)</span>
                    @endif
                </span>
            </span>
            <span class="val pay-oil_return">{{ number_format($oilReturnTotalFee, 3) }} د.أ</span>
        </div>
        @if($oilCansCash > 0)
        <div class="pay-row" style="background:#fff5f5">
            <span class="label" style="color:#991b1b">📦 تنك تُدفع نقداً (رديات زيت)</span>
            <span class="val" style="color:#dc2626">{{ number_format($oilCansCash, 3) }} د.أ</span>
        </div>
        @endif
        @endif

        @if($totalCanVal > 0)
        <div class="pay-row" style="border-top:1px solid #dbeafe;margin-top:4px;padding-top:6px">
            <span class="label" style="color:#1e40af;font-weight:700">
                📦 إجمالي التنك
                <span style="font-size:.75rem;font-weight:400">
                    @if($totalCan16 > 0) &nbsp;{{ $totalCan16 }}×16كيلو@endif
                    @if($totalCan8  > 0) &nbsp;{{ $totalCan8  }}×8كيلو@endif
                    @if($totalCan4  > 0) &nbsp;{{ $totalCan4  }}×4كيلو@endif
                </span>
            </span>
            <span class="val pay-can">{{ number_format($totalCanVal, 3) }} د.أ</span>
        </div>
        @endif

    </div>
    @endif

    {{-- الإجمالي الكبير --}}
    <div class="grand-total-box">
        <div class="gt-label">إجمالي المستحق</div>
        <div style="display:flex;justify-content:center;align-items:center;gap:24px;flex-wrap:wrap;margin:8px 0">
            <div style="text-align:center">
                <div class="gt-amount">{{ number_format($netCashTotal, 3) }}</div>
                <div class="gt-unit">💵 نقدي (د.أ)</div>
            </div>
            @if($netOilRetKg > 0)
            <div style="width:1px;background:rgba(255,255,255,.3);align-self:stretch;min-height:40px"></div>
            <div style="text-align:center">
                <div class="gt-amount" style="color:#fde68a">{{ number_format($netOilRetKg, 3) }}</div>
                <div class="gt-unit">🫙 رديات الزيت (كيلو)</div>
            </div>
            @endif
        </div>
        <div style="margin-top:6px;font-size:.78rem;opacity:.75;border-top:1px solid rgba(255,255,255,.25);padding-top:6px">
            {{ $receipts->count() }} إيصال &nbsp;|&nbsp; {{ number_format($totalOlive,1) }} كيلو زيتون
        </div>
    </div>

    {{-- توقيع --}}
    <div class="signatures">
        <div class="sig-box">
            <div style="height:40px"></div>
            توقيع المالك
        </div>
        <div class="sig-box">
            <div style="height:40px"></div>
            توقيع الكاشير
        </div>
    </div>

    <div class="footer-note">
        {{ $pressName }} — {{ now()->format('Y/m/d H:i') }} — شكراً لثقتكم 🫒
    </div>

</div>
</body>
</html>
