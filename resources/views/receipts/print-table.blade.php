<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <title>تقرير الإيصالات — {{ $pressName }}</title>
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;600;700;800;900&display=swap" rel="stylesheet">
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body { font-family: 'Cairo', sans-serif; background: #f4f4f4; direction: rtl; color: #1a1a1a; font-size: 13px; }

        .no-print {
            text-align: center; padding: 10px; background: #2d5016;
            display: flex; gap: 10px; justify-content: center; align-items: center;
            position: sticky; top: 0; z-index: 100;
        }
        .btn-p { font-family:'Cairo',sans-serif; font-size:.9rem; padding:8px 22px; border-radius:6px; cursor:pointer; border:none; font-weight:700; background:#f0d060; color:#2d5016; }
        .btn-c { font-family:'Cairo',sans-serif; font-size:.9rem; padding:8px 22px; border-radius:6px; cursor:pointer; border:none; font-weight:700; background:rgba(255,255,255,.2); color:#fff; }

        .wrapper { max-width: 960px; margin: 0 auto; padding: 16px; }

        /* ── رأس التقرير ── */
        .report-header {
            background: #fff; border-radius: 10px; padding: 18px;
            text-align: center; margin-bottom: 14px;
            border-bottom: 4px solid #2d5016;
            box-shadow: 0 2px 8px rgba(0,0,0,.07);
        }
        .report-header h2 { color: #2d5016; font-size: 1.5rem; font-weight: 900; }
        .report-header .sub { color: #666; font-size: .82rem; margin-top: 4px; }

        /* ── ملخص ── */
        .summary-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 10px; margin-bottom: 16px;
        }
        .sum-box {
            background: #fff; border: 1.5px solid #ddd;
            border-radius: 8px; padding: 10px 14px; text-align: center;
            box-shadow: 0 1px 4px rgba(0,0,0,.05);
        }
        .sum-box .sv { font-size: 1.15rem; font-weight: 900; line-height: 1.2; }
        .sum-box .sl { font-size: .72rem; color: #666; margin-top: 3px; }
        .sum-box .sd { font-size: .68rem; color: #888; margin-top: 2px; }

        /* ── بطاقة الإيصال ── */
        .receipt-card {
            background: #fff; border: 1.5px solid #d1d5db;
            border-radius: 10px; padding: 0;
            margin-bottom: 12px; overflow: hidden;
            box-shadow: 0 2px 8px rgba(0,0,0,.06);
            page-break-inside: avoid;
        }

        /* رأس البطاقة */
        .card-head {
            background: #2d5016; color: #fff;
            padding: 8px 14px;
            display: flex; justify-content: space-between; align-items: center;
        }
        .card-head .rnum { font-size: .95rem; font-weight: 900; letter-spacing: .5px; }
        .card-head .rdate { font-size: .78rem; opacity: .85; }
        .card-head .rstatus { font-size: .75rem; padding: 2px 9px; border-radius: 10px; font-weight: 700; }

        .card-body { padding: 12px 14px; }

        /* شبكة الحقول */
        .fields-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 0;
        }
        .field-row {
            display: flex; justify-content: space-between;
            padding: 5px 6px; border-bottom: 1px dotted #e5e7eb;
            font-size: .86rem;
        }
        .field-row .fl { color: #6b7280; }
        .field-row .fv { font-weight: 700; color: #111; }

        /* صندوق الزيتون */
        .olive-bar {
            background: #2d5016; color: #fff;
            border-radius: 7px; padding: 8px 14px;
            display: flex; justify-content: space-between; align-items: center;
            margin: 10px 0;
        }
        .olive-bar .ob-lbl { font-size: .8rem; opacity: .85; }
        .olive-bar .ob-val { font-size: 1.3rem; font-weight: 900; color: #f0d060; }
        .olive-bar .ob-unit { font-size: .78rem; opacity: .75; }

        /* تفاصيل العصر */
        .oil-section {
            background: #fffbeb; border: 1px solid #fcd34d;
            border-radius: 7px; padding: 8px 12px; margin: 8px 0;
        }
        .oil-section .os-title { font-size: .78rem; font-weight: 800; color: #78350f; margin-bottom: 5px; }
        .oil-row { display: flex; justify-content: space-between; padding: 3px 0; font-size: .84rem; border-bottom: 1px dotted #fde68a; }
        .oil-row:last-child { border: none; }
        .oil-row .ol { color: #78350f; }
        .oil-row .ov { font-weight: 800; color: #92400e; }

        /* التنك */
        .can-section {
            background: #eff6ff; border: 1px solid #bfdbfe;
            border-radius: 7px; padding: 7px 12px; margin: 6px 0;
        }
        .can-section .cs-title { font-size: .78rem; font-weight: 800; color: #1e40af; margin-bottom: 4px; }
        .can-row { display: flex; justify-content: space-between; padding: 2px 0; font-size: .83rem; color: #1e40af; }

        /* رديات زيت */
        .oil-ret-section {
            background: #fffbeb; border: 1px solid #fcd34d;
            border-radius: 7px; padding: 7px 12px; margin: 6px 0;
        }
        .oil-ret-section .ors-title { font-size: .78rem; font-weight: 800; color: #92400e; margin-bottom: 4px; }

        /* الإجمالي */
        .total-bar {
            border-radius: 7px; padding: 8px 14px;
            display: flex; justify-content: space-between; align-items: center;
            margin-top: 10px;
        }
        .total-bar.cash    { background: #2d5016; }
        .total-bar.debt    { background: #991b1b; }
        .total-bar.oil_ret { background: #78350f; }
        .total-bar .tb-lbl { color: rgba(255,255,255,.85); font-size: .8rem; }
        .total-bar .tb-val { color: #f0d060; font-size: 1.3rem; font-weight: 900; }
        .total-bar .tb-unit { color: rgba(255,255,255,.7); font-size: .75rem; }

        /* ملاحظات */
        .notes-box {
            font-size: .8rem; color: #666; margin-top: 8px;
            padding: 5px 8px; background: #f9f9f9; border-radius: 5px;
        }

        /* فاصل */
        .report-footer {
            text-align: center; padding: 14px; font-size: .75rem;
            color: #aaa; border-top: 2px dashed #ccc; margin-top: 16px;
        }

        @media print {
            @page { size: A4; margin: 10mm 8mm; }
            body { background: #fff; font-size: 11.5px; }
            .no-print { display: none !important; }
            .wrapper { padding: 0; max-width: 100%; }
            .report-header { box-shadow: none; }
            .receipt-card { box-shadow: none; border: 1px solid #ccc; margin-bottom: 8px; }
            .sum-box { box-shadow: none; }
            .card-body { padding: 8px 12px; }
            .olive-bar .ob-val { font-size: 1.1rem; }
            .total-bar .tb-val { font-size: 1.1rem; }
            -webkit-print-color-adjust: exact;
            print-color-adjust: exact;
        }
    </style>
</head>
<body>

<div class="no-print">
    <button class="btn-p" onclick="window.print()">🖨️ طباعة</button>
    <button class="btn-c" onclick="window.close(); setTimeout(()=>history.back(),150)">✕ إغلاق</button>
</div>

<div class="wrapper">

    {{-- ── رأس التقرير ── --}}
    <div class="report-header">
        <div style="font-size:1.6rem">🫒</div>
        <h2>{{ $pressName }}</h2>
        @if($pressPhone)
        <div class="sub" style="color:#2d5016;font-weight:700">📞 {{ $pressPhone }}</div>
        @endif
        <div class="sub">تقرير الإيصالات الكامل — طُبع بتاريخ {{ now()->format('Y/m/d H:i') }}</div>
    </div>

    {{-- ── ملخص ── --}}
    <div class="summary-grid">
        <div class="sum-box">
            <div class="sv">{{ $receipts->count() }}</div>
            <div class="sl">📋 إجمالي الإيصالات</div>
        </div>
        <div class="sum-box">
            <div class="sv">{{ number_format($oliveTotal, 1) }}</div>
            <div class="sl">⚖️ كيلو زيتون</div>
        </div>
        <div class="sum-box">
            <div class="sv">{{ number_format($oilTotal, 1) }}</div>
            <div class="sl">🫙 كيلو زيت معصور</div>
        </div>
        <div class="sum-box" style="border-color:#fcd34d">
            <div class="sv" style="color:#d97706">{{ number_format($oilRetKg, 2) }}</div>
            <div class="sl">🫙 رديات الزيت (كيلو)</div>
        </div>
        <div class="sum-box" style="border-color:#fca5a5">
            @if($debtTotal > 0)
            <div class="sv" style="color:#b91c1c">{{ number_format($debtTotal, 2) }} <span style="font-size:.65em">د.أ</span></div>
            @endif
            @if($debtOilKg > 0)
            <div class="sv" style="color:#d97706;font-size:1rem">{{ number_format($debtOilKg, 2) }} <span style="font-size:.65em">كيلو زيت</span></div>
            @endif
            @if($debtTotal == 0 && $debtOilKg == 0)
            <div class="sv" style="color:#b91c1c">0</div>
            @endif
            <div class="sl">⚠️ إجمالي الديون</div>
        </div>
        <div class="sum-box" style="border-color:#334155;background:#1e293b">
            <div class="sv" style="color:#f0d060">{{ number_format($netCashFees, 2) }}</div>
            <div class="sl" style="color:#94a3b8">💰 صافي الأجور النقدية (د.أ)</div>
            <div class="sd" style="color:#64748b">
                أجرة: {{ number_format($cashTotal, 2) }}
                @if($oilBuyTotal > 0) − شراء: {{ number_format($oilBuyTotal, 2) }} @endif
                @if($oilSellTotal > 0) + بيع: {{ number_format($oilSellTotal, 2) }} @endif
                @if($expenseTotal > 0) − صادرات: {{ number_format($expenseTotal, 2) }} @endif
                @if($incomeTotal > 0) + واردات: {{ number_format($incomeTotal, 2) }} @endif
            </div>
        </div>
    </div>

    {{-- ── بطاقات الإيصالات ── --}}
    @forelse($receipts as $i => $receipt)
    @php
        $statusLabels = ['pending'=>'قيد الانتظار','processing'=>'جاري العصر','completed'=>'مكتمل','debt'=>'دَين'];
        $statusColors = ['pending'=>'#92400e','processing'=>'#1e40af','completed'=>'#065f46','debt'=>'#fff'];
        $statusBg     = ['pending'=>'#fef3c7','processing'=>'#dbeafe','completed'=>'#d1fae5','debt'=>'#ef4444'];
        $extractRate  = ($receipt->olive_weight_kg > 0 && $receipt->oil_weight_kg > 0)
                        ? round(($receipt->oil_weight_kg / $receipt->olive_weight_kg) * 100, 1) : 0;
    @endphp

    <div class="receipt-card">

        {{-- رأس البطاقة --}}
        <div class="card-head">
            <div>
                <div class="rnum">إيصال رقم: {{ $receipt->receipt_number }}</div>
                <div class="rdate">📅 {{ $receipt->created_at->format('Y/m/d — H:i') }}</div>
            </div>
            <div class="rstatus" style="background:{{ $statusBg[$receipt->status] ?? '#ddd' }};color:{{ $statusColors[$receipt->status] ?? '#333' }}">
                {{ $statusLabels[$receipt->status] ?? $receipt->status }}
            </div>
        </div>

        <div class="card-body">

            {{-- معلومات صاحب الزيتون والناقل --}}
            <div class="fields-grid">
                <div>
                    <div class="field-row">
                        <span class="fl">👤 صاحب الزيتون</span>
                        <span class="fv">{{ $receipt->owner->name }}</span>
                    </div>
                    <div class="field-row">
                        <span class="fl">📞 الهاتف</span>
                        <span class="fv">{{ $receipt->owner->phone ?? '—' }}</span>
                    </div>
                    <div class="field-row">
                        <span class="fl">🔢 رقم الدور</span>
                        <span class="fv" style="color:#2d5016">{{ $receipt->floor_number ?? '—' }}</span>
                    </div>
                </div>
                <div>
                    <div class="field-row">
                        <span class="fl">🚚 الناقل</span>
                        <span class="fv">{{ $receipt->carrier_name }}</span>
                    </div>
                    <div class="field-row">
                        <span class="fl">💵 سعر العصر</span>
                        <span class="fv">{{ number_format($receipt->fee_per_kg, 3) }} د.أ/كيلو</span>
                    </div>
                    <div class="field-row">
                        <span class="fl">💳 طريقة الدفع</span>
                        <span class="fv">
                            @if($receipt->payment_method === 'oil_return') رديات زيت
                            @elseif($receipt->payment_method === 'cash') نقداً
                            @else {{ $receipt->payment_method ?? '—' }}
                            @endif
                        </span>
                    </div>
                </div>
            </div>

            {{-- كمية الزيتون --}}
            <div class="olive-bar">
                <div>
                    <div class="ob-lbl">⚖️ كمية الزيتون الواردة</div>
                </div>
                <div style="text-align:center">
                    <div class="ob-val">{{ number_format($receipt->olive_weight_kg, 1) }}</div>
                    <div class="ob-unit">كيلوغرام</div>
                </div>
            </div>

            @if($receipt->oil_weight_kg > 0)
            {{-- تفاصيل العصر --}}
            <div class="oil-section">
                <div class="os-title">🫙 نتيجة العصر</div>
                <div class="oil-row">
                    <span class="ol">الزيت المعصور</span>
                    <span class="ov">{{ $receipt->oil_total_label }}</span>
                </div>
                @if($extractRate > 0)
                <div class="oil-row">
                    <span class="ol">نسبة الاستخلاص</span>
                    <span class="ov">{{ $extractRate }}%</span>
                </div>
                @endif
                <div class="oil-row">
                    <span class="ol">أجرة العصر</span>
                    <span class="ov">{{ number_format($receipt->press_fee, 3) }} د.أ</span>
                </div>
            </div>

            {{-- التنك --}}
            @if($receipt->can_total > 0)
            <div class="can-section">
                <div class="cs-title">📦 التنك</div>
                @if($receipt->can_16_count > 0)
                <div class="can-row">
                    <span>{{ $receipt->can_16_count }} × تنكة 16 كيلو</span>
                    <span>{{ number_format($receipt->can_16_count * $canPrices['can_16'], 3) }} د.أ</span>
                </div>
                @endif
                @if($receipt->can_8_count > 0)
                <div class="can-row">
                    <span>{{ $receipt->can_8_count }} × تنكة 8 كيلو</span>
                    <span>{{ number_format($receipt->can_8_count * $canPrices['can_8'], 3) }} د.أ</span>
                </div>
                @endif
                @if($receipt->can_4_count > 0)
                <div class="can-row">
                    <span>{{ $receipt->can_4_count }} × تنكة 4 كيلو</span>
                    <span>{{ number_format($receipt->can_4_count * $canPrices['can_4'], 3) }} د.أ</span>
                </div>
                @endif
                <div class="can-row" style="border-top:1px solid #bfdbfe;margin-top:4px;padding-top:4px;font-weight:800">
                    <span>مجموع التنك</span>
                    <span>{{ number_format($receipt->can_total, 3) }} د.أ</span>
                </div>
                @if($receipt->payment_method === 'oil_return' && $receipt->cans_in_return)
                <div style="font-size:.73rem;color:#1e40af;text-align:center;margin-top:3px">
                    ✓ مُسوَّاة ضمن رديات الزيت
                </div>
                @endif
            </div>
            @endif

            @if($receipt->discount_amount > 0)
            <div class="oil-row" style="background:#fef9c3;padding:5px 8px;margin:5px 0;border-radius:5px">
                <span class="ol">خصم من الإجمالي</span>
                <span class="ov">−{{ number_format($receipt->discount_amount, 3) }} د.أ</span>
            </div>
            @endif

            {{-- رديات الزيت --}}
            @if($receipt->payment_method === 'oil_return' && $receipt->oil_return_kg > 0)
            <div class="oil-ret-section">
                <div class="ors-title">🫙 رديات الزيت</div>
                <div class="oil-row">
                    <span class="ol">كمية الرديات</span>
                    <span class="ov">{{ number_format($receipt->oil_return_kg, 3) }} كيلو زيت</span>
                </div>
            </div>
            @endif

            {{-- الإجمالي --}}
            @if($receipt->status === 'debt')
            <div class="total-bar debt" @if($receipt->payment_method === 'oil_return') style="background:#78350f" @endif>
                <div>
                    <div class="tb-lbl">⚠️ دَين — {{ $receipt->payment_method === 'oil_return' ? 'رديات زيت غير مسددة' : 'الدفع لم يتم' }}</div>
                    <div class="tb-unit">مستحق السداد</div>
                </div>
                <div style="text-align:center">
                    @if($receipt->payment_method === 'oil_return' && $receipt->oil_return_kg > 0)
                        <div class="tb-val" style="color:#fde68a">{{ number_format($receipt->oil_return_kg, 3) }}</div>
                        <div class="tb-unit">كيلو زيت</div>
                        <div class="tb-unit" style="margin-top:2px">القيمة: {{ number_format($receipt->total_fee, 3) }} د.أ</div>
                    @else
                        <div class="tb-val">{{ number_format($receipt->total_fee, 3) }}</div>
                        <div class="tb-unit">دينار أردني</div>
                    @endif
                </div>
            </div>
            @elseif($receipt->payment_method === 'oil_return')
            <div class="total-bar oil_ret">
                <div>
                    <div class="tb-lbl">🫙 أجرة العصر — رديات زيت</div>
                    @if($receipt->press_fee > 0)
                    <div class="tb-unit">القيمة النقدية بعد الخصم: {{ number_format($receipt->discounted_press_fee, 3) }} د.أ</div>
                    @endif
                </div>
                <div style="text-align:center">
                    <div class="tb-val" style="color:#fde68a">{{ number_format($receipt->oil_return_kg, 3) }}</div>
                    <div class="tb-unit">كيلو زيت</div>
                </div>
            </div>
            @if($receipt->can_total > 0 && !$receipt->cans_in_return)
            <div class="total-bar cash" style="margin-top:6px">
                <div>
                    <div class="tb-lbl">💵 التنك — نقداً</div>
                    @if($receipt->can_16_count > 0)
                    <div class="tb-unit">{{ $receipt->can_16_count }}×16 كيلو = {{ number_format($receipt->can_16_count * $canPrices['can_16'], 3) }} د.أ</div>
                    @endif
                    @if($receipt->can_8_count > 0)
                    <div class="tb-unit">{{ $receipt->can_8_count }}×8 كيلو = {{ number_format($receipt->can_8_count * $canPrices['can_8'], 3) }} د.أ</div>
                    @endif
                    @if($receipt->can_4_count > 0)
                    <div class="tb-unit">{{ $receipt->can_4_count }}×4 كيلو = {{ number_format($receipt->can_4_count * $canPrices['can_4'], 3) }} د.أ</div>
                    @endif
                </div>
                <div style="text-align:center">
                    <div class="tb-val">{{ number_format($receipt->discounted_can_total, 3) }}</div>
                    <div class="tb-unit">دينار أردني</div>
                </div>
            </div>
            @endif
            @else
            <div class="total-bar cash">
                <div>
                    <div class="tb-lbl">💵 المبلغ المستحق نقداً</div>
                    @if($receipt->can_total > 0)
                    <div class="tb-unit">عصر {{ number_format($receipt->press_fee,3) }} + تنك {{ number_format($receipt->can_total,3) }}</div>
                    @endif
                    @if($receipt->discount_amount > 0)
                    <div class="tb-unit">− خصم {{ number_format($receipt->discount_amount,3) }} د.أ</div>
                    @endif
                </div>
                <div style="text-align:center">
                    <div class="tb-val">{{ number_format($receipt->total_fee, 3) }}</div>
                    <div class="tb-unit">دينار أردني</div>
                </div>
            </div>
            @endif

            @else
            {{-- لم يُعصر بعد --}}
            <div style="background:#fff3cd;color:#856404;text-align:center;padding:8px;border-radius:6px;font-weight:700;font-size:.83rem;margin-top:8px;border:1px solid #ffc107">
                📋 إيصال استلام — الأجرة تُحسب عند الكاشير بعد العصر
            </div>
            @endif

            @if($receipt->notes)
            <div class="notes-box">ملاحظة: {{ $receipt->notes }}</div>
            @endif

        </div>{{-- end card-body --}}
    </div>{{-- end receipt-card --}}

    @empty
    <div style="text-align:center;padding:40px;color:#999;background:#fff;border-radius:10px">
        لا توجد إيصالات
    </div>
    @endforelse

    <div class="report-footer">
        {{ $pressName }} — إجمالي {{ $receipts->count() }} إيصال — طُبع بتاريخ {{ now()->format('Y/m/d H:i') }}
    </div>

</div>
</body>
</html>
