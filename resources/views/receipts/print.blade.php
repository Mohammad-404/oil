<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <title>إيصال {{ $receipt->receipt_number }}</title>
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;600;700;800;900&display=swap" rel="stylesheet">
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body { font-family: 'Cairo', sans-serif; background: #fff; direction: rtl; color: #1a1a1a; font-size: 13px; }

        .no-print {
            text-align: center; padding: 10px; background: #f0f0f0;
            display: flex; gap: 10px; justify-content: center;
        }
        .btn-p { font-family:'Cairo',sans-serif; font-size:.9rem; padding:7px 20px; border-radius:6px; cursor:pointer; border:none; font-weight:700; background:#2d5016; color:#fff; }
        .btn-c { font-family:'Cairo',sans-serif; font-size:.9rem; padding:7px 20px; border-radius:6px; cursor:pointer; border:none; font-weight:700; background:#666; color:#fff; }
        .btn-new { font-family:'Cairo',sans-serif; font-size:.9rem; padding:7px 20px; border-radius:6px; font-weight:700; background:#3d6b1a; color:#fff; border:none; text-decoration:none; cursor:pointer; }
        .btn-wa  { font-family:'Cairo',sans-serif; font-size:.9rem; padding:7px 20px; border-radius:6px; font-weight:700; background:#25D366; color:#fff; border:none; text-decoration:none; cursor:pointer; display:inline-flex; align-items:center; gap:4px; }

        .page { max-width: 420px; margin: 16px auto; padding: 20px; border: 2px solid #2d5016; border-radius: 12px; }

        /* رأس */
        .doc-header { text-align: center; border-bottom: 2px dashed #7ab648; padding-bottom: 12px; margin-bottom: 12px; }
        .doc-header h2 { color: #2d5016; font-size: 1.35rem; font-weight: 900; }
        .doc-header .sub { color: #888; font-size: .78rem; margin-top: 3px; }

        /* رقم الإيصال */
        .receipt-num {
            background: #2d5016; color: #fff; text-align: center;
            padding: 7px 10px; border-radius: 6px; font-size: .95rem;
            font-weight: 700; margin-bottom: 14px; letter-spacing: .5px;
        }

        /* أقسام */
        .section-title {
            background: #e8f5d4; color: #2d5016; font-weight: 700;
            padding: 4px 8px; border-radius: 4px; font-size: .8rem;
            margin-bottom: 6px; margin-top: 12px;
        }
        .info-row {
            display: flex; justify-content: space-between;
            padding: 5px 2px; border-bottom: 1px dotted #ddd; font-size: .87rem;
        }
        .info-row .label { color: #666; }
        .info-row .value { font-weight: 700; }

        /* كمية الزيتون */
        .olive-box {
            background: #2d5016; color: #fff; text-align: center;
            padding: 12px; border-radius: 8px; margin: 12px 0;
        }
        .olive-box .amt { font-size: 2rem; font-weight: 900; color: #f0d060; line-height: 1.1; }
        .olive-box small { opacity: .85; font-size: .8rem; }

        /* تفاصيل العصر */
        .oil-box {
            background: #fffbeb; border: 1.5px solid #fcd34d;
            border-radius: 8px; padding: 10px 12px; margin: 10px 0;
        }
        .oil-box .oil-title { font-size: .8rem; font-weight: 800; color: #78350f; margin-bottom: 6px; }
        .oil-row { display: flex; justify-content: space-between; padding: 3px 0; font-size: .87rem; border-bottom: 1px dotted #fcd34d; }
        .oil-row:last-child { border: none; }
        .oil-row .ol { color: #78350f; }
        .oil-row .ov { font-weight: 800; color: #92400e; }

        /* التنك */
        .can-box {
            background: #eff6ff; border: 1px solid #bfdbfe;
            border-radius: 7px; padding: 8px 12px; margin: 8px 0; font-size: .85rem;
        }
        .can-box .can-title { font-size: .78rem; font-weight: 800; color: #1e40af; margin-bottom: 4px; }
        .can-row { display: flex; justify-content: space-between; padding: 2px 0; color: #1e40af; }

        /* الإجمالي */
        .total-box {
            background: #2d5016; color: #fff; text-align: center;
            padding: 14px; border-radius: 8px; margin: 12px 0;
        }
        .total-box .lbl  { font-size: .82rem; opacity: .8; margin-bottom: 4px; }
        .total-box .amt  { font-size: 2.2rem; font-weight: 900; color: #f0d060; line-height: 1.1; }
        .total-box .unit { font-size: .8rem; opacity: .75; margin-top: 3px; }

        /* حالة انتظار */
        .pending-badge {
            background: #fff3cd; color: #856404; text-align: center;
            padding: 7px; border-radius: 6px; font-weight: 700; font-size: .85rem;
            margin-top: 10px; border: 1px solid #ffc107;
        }

        /* رديات زيت */
        .oil-return-box {
            background: #fffbeb; border: 1px solid #fcd34d;
            border-radius: 7px; padding: 8px 12px; margin: 8px 0; font-size: .85rem;
        }
        .oil-return-box .ort { font-size: .78rem; font-weight: 800; color: #92400e; margin-bottom: 4px; }

        .footer {
            text-align: center; margin-top: 14px;
            font-size: .73rem; color: #aaa;
            border-top: 1px dashed #ccc; padding-top: 8px;
        }

        @media print {
            @page { size: 80mm auto; margin: 0; }
            html { margin: 0; padding: 0; }
            body {
                margin: 0; padding: 2mm 0;
                font-size: 11px;
                display: flex;
                flex-direction: column;
                align-items: center;
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }
            .no-print { display: none !important; }
            .page {
                width: 76mm;
                border: none; border-radius: 0; box-shadow: none;
                padding: 2mm 2mm;
            }
            .doc-header { padding-bottom: 5px; margin-bottom: 6px; }
            .doc-header h2 { font-size: .93rem; }
            .doc-header .sub { font-size: .67rem; }
            .receipt-num { font-size: .79rem; padding: 4px 6px; margin-bottom: 7px; }
            .section-title { font-size: .69rem; padding: 2px 5px; margin-top: 7px; margin-bottom: 3px; }
            .info-row { font-size: .75rem; padding: 3px 1px; }
            .olive-box { padding: 7px 5px; margin: 6px 0; }
            .olive-box .amt { font-size: 1.45rem; }
            .olive-box small { font-size: .64rem; }
            .oil-box { padding: 5px 7px; margin: 5px 0; }
            .oil-box .oil-title { font-size: .67rem; margin-bottom: 3px; }
            .oil-row { font-size: .75rem; }
            .total-box { padding: 8px 5px; margin: 7px 0; }
            .total-box .lbl { font-size: .69rem; }
            .total-box .amt { font-size: 1.55rem; }
            .total-box .unit { font-size: .67rem; }
            .pending-badge { font-size: .72rem; padding: 5px; margin-top: 7px; }
            .footer { font-size: .62rem; margin-top: 7px; padding-top: 5px; }
        }
    </style>
</head>
<body>

@php
    // رقم واتساب بصيغة دولية
    $ownerPhone = $receipt->owner->phone ?? '';
    $waPhone = preg_replace('/\D/', '', $ownerPhone);
    if (str_starts_with($waPhone, '0')) {
        $waPhone = '962' . substr($waPhone, 1);
    } elseif ($waPhone && !str_starts_with($waPhone, '962')) {
        $waPhone = '962' . $waPhone;
    }

    $extractRate = ($receipt->olive_weight_kg > 0 && $receipt->oil_weight_kg > 0)
        ? round(($receipt->oil_weight_kg / $receipt->olive_weight_kg) * 100, 1) : 0;

    if ($receipt->oil_weight_kg > 0) {
        // ── رسالة بعد العصر ──
        $waMsg  = "مرحباً {$receipt->owner->name} 🫒\n";
        $waMsg .= "تم بحمد الله عصر زيتونك\n";
        $waMsg .= "في {$pressName}\n";
        $waMsg .= "━━━━━━━━━━━━━━━━\n";
        $waMsg .= "📋 رقم الإيصال: {$receipt->receipt_number}\n";
        $waMsg .= "📅 التاريخ: " . $receipt->created_at->format('Y/m/d') . "\n";
        if ($receipt->floor_number) $waMsg .= "🔢 رقم الدور: {$receipt->floor_number}\n";
        $waMsg .= "━━━━━━━━━━━━━━━━\n";
        $waMsg .= "⚖️ الزيتون: " . number_format($receipt->olive_weight_kg, 1) . " كيلو\n";
        $waMsg .= "🫙 الزيت المعصور: " . $receipt->oil_total_label . "\n";
        if ($extractRate > 0) $waMsg .= "📊 نسبة الاستخلاص: {$extractRate}%\n";
        if ($receipt->olive_oil_ratio) $waMsg .= "📐 نسبة الزيتون/الزيت: " . number_format($receipt->olive_oil_ratio, 1) . " كيلو زيتون / كيلو زيت\n";
        $waMsg .= "━━━━━━━━━━━━━━━━\n";
        $waMsg .= "💰 أجرة العصر: " . number_format($receipt->press_fee, 3) . " د.أ\n";
        if ($receipt->can_total > 0) {
            $waMsg .= "📦 التنك: " . number_format($receipt->can_total, 3) . " د.أ\n";
        }
        if ($receipt->discount_amount > 0) $waMsg .= "🏷️ خصم: -" . number_format($receipt->discount_amount, 3) . " د.أ\n";
        if ($receipt->payment_method === 'oil_return') {
            $waMsg .= "🫙 رديات الزيت: " . number_format($receipt->oil_return_kg, 3) . " كيلو\n";
            if ($receipt->can_total > 0 && !$receipt->cans_in_return) {
                $waMsg .= "💵 التنك نقداً بعد الخصم: " . number_format($receipt->discounted_can_total, 3) . " د.أ\n";
            }
        } elseif ($receipt->status === 'debt') {
            $waMsg .= "⚠️ مبلغ الدين: " . number_format($receipt->total_fee, 3) . " د.أ\n";
        } else {
            $waMsg .= "💵 الإجمالي: " . number_format($receipt->total_fee, 3) . " د.أ\n";
        }
        $waMsg .= "━━━━━━━━━━━━━━━━\n";
        if ($receipt->status === 'processing') {
            $waMsg .= "⚙️ الحالة: قيد المعالجة\n";
            $waMsg .= "━━━━━━━━━━━━━━━━\n";
        }
        $waMsg .= "شكراً لثقتكم •";
    } else {
        // ── رسالة تسجيل الدور ──
        $waMsg  = "مرحباً {$receipt->owner->name} 🫒\n";
        $waMsg .= "تم تسجيل دورك في المعصرة\n";
        $waMsg .= "{$pressName}\n";
        $waMsg .= "ترحب بكم\n";
        $waMsg .= "━━━━━━━━━━━━━━━━\n";
        $waMsg .= "📋 رقم الإيصال: {$receipt->receipt_number}\n";
        $waMsg .= "📅 التاريخ: " . $receipt->created_at->format('Y/m/d') . "\n";
        if ($receipt->floor_number) $waMsg .= "🔢 رقم الدور: {$receipt->floor_number}\n";
        $waMsg .= "⚖️ كمية الزيتون: " . number_format($receipt->olive_weight_kg, 1) . " كيلو\n";
        $waMsg .= "🚚 الناقل: {$receipt->carrier_name}\n";
        $waMsg .= "━━━━━━━━━━━━━━━━\n";
        $waMsg .= "شعارنا الصدق والامانة والاخلاص في العمل •";
    }

    $waUrl = $waPhone ? 'https://wa.me/' . $waPhone . '?text=' . rawurlencode($waMsg) : '';
@endphp

<div class="no-print">
    <button class="btn-p" onclick="window.print()">🖨️ طباعة</button>
    @if($ownerPhone)
    <a href="{{ $waUrl }}" target="_blank" class="btn-wa">
        <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 24 24" fill="white" style="vertical-align:middle;margin-left:4px"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/></svg>
        واتساب
    </a>
    @endif
    <button class="btn-c" onclick="window.close(); setTimeout(()=>history.back(),150)">✕ إغلاق</button>
    @if(auth()->check() && !auth()->user()->isCashier())
    <a href="{{ route('receipts.create') }}" class="btn-new">+ إيصال جديد</a>
    @endif
</div>

<div class="page">

    {{-- رأس --}}
    <div class="doc-header">
        <div style="font-size:1.8rem">🫒</div>
        <h2>{{ $pressName }}</h2>
        @if($pressPhone)
        <div class="sub" style="color:#2d5016;font-weight:700">📞 {{ $pressPhone }}</div>
        @endif
        <div class="sub">
            @if($receipt->oil_weight_kg > 0)
                فاتورة العصر — التفاصيل الكاملة
            @else
                إيصال استلام الزيتون
            @endif
        </div>
    </div>

    {{-- رقم الإيصال --}}
    <div class="receipt-num">
        رقم الإيصال: {{ $receipt->receipt_number }}
        &nbsp;|&nbsp;
        {{ $receipt->created_at->format('Y/m/d') }}
    </div>

    {{-- صاحب الزيتون --}}
    <div class="section-title">👤 صاحب الزيتون</div>
    <div class="info-row">
        <span class="label">الاسم</span>
        <span class="value">{{ $receipt->owner->name }}</span>
    </div>
    <div class="info-row">
        <span class="label">الهاتف</span>
        <span class="value">{{ $receipt->owner->phone }}</span>
    </div>
    <div class="info-row">
        <span class="label">رقم الدور</span>
        <span class="value" style="color:#2d5016;font-size:1rem">{{ $receipt->floor_number ?? '—' }}</span>
    </div>

    {{-- بيانات الشحنة --}}
    <div class="section-title">🚚 بيانات الشحنة</div>
    <div class="info-row">
        <span class="label">الناقل</span>
        <span class="value">{{ $receipt->carrier_name }}</span>
    </div>

    {{-- كمية الزيتون --}}
    <div class="olive-box">
        <small>كمية الزيتون الواردة</small>
        <div class="amt">{{ number_format($receipt->olive_weight_kg, 1) }}</div>
        <small>كيلوغرام</small>
    </div>

    @if($receipt->oil_weight_kg > 0 && auth()->check() && auth()->user()->isCashier())
    {{-- ── تفاصيل العصر (الكاشير فقط) ── --}}

    {{-- الزيت المعصور --}}
    <div class="oil-box">
        <div class="oil-title">🫙 نتيجة العصر</div>
        <div class="oil-row">
            <span class="ol">الزيت المعصور</span>
            <span class="ov">{{ $receipt->oil_total_label }}</span>
        </div>
        @php $extractRate = $receipt->olive_weight_kg > 0 ? round(($receipt->oil_weight_kg / $receipt->olive_weight_kg) * 100, 1) : 0; @endphp
        @if($extractRate > 0)
        <div class="oil-row">
            <span class="ol">نسبة الاستخلاص</span>
            <span class="ov">{{ $extractRate }}%</span>
        </div>
        @endif
        <div class="oil-row">
            <span class="ol">سعر العصر / كيلو</span>
            <span class="ov">{{ number_format($receipt->fee_per_kg, 3) }} د.أ</span>
        </div>
        <div class="oil-row" style="border-top:1.5px solid #fcd34d;margin-top:3px;padding-top:5px">
            <span class="ol" style="font-weight:800">أجرة العصر</span>
            <span class="ov" style="color:#b45309;font-size:1rem">{{ number_format($receipt->press_fee, 3) }} د.أ</span>
        </div>
    </div>

    {{-- التنك --}}
    @if($receipt->can_total > 0)
    <div class="can-box">
        <div class="can-title">📦 التنك</div>
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
        <div style="font-size:.75rem;color:#1e40af;margin-top:3px;text-align:center">
            ✓ مُسوَّاة ضمن رديات الزيت
        </div>
        @endif
    </div>
    @endif

    @if($receipt->discount_amount > 0)
    <div class="oil-row" style="background:#fef9c3;padding:7px 10px;margin:7px 0;border-radius:6px">
        <span class="ol">خصم من الإجمالي {{ number_format((float)$receipt->press_fee + (float)$receipt->can_total, 3) }} د.أ</span>
        <span class="ov">−{{ number_format($receipt->discount_amount, 3) }} د.أ</span>
    </div>
    @endif

    {{-- رديات الزيت --}}
    @if($receipt->payment_method === 'oil_return' && $receipt->oil_return_kg > 0)
    <div class="oil-return-box">
        <div class="ort">🫙 رديات الزيت</div>
        <div class="oil-row">
            <span class="ol">كمية الرديات</span>
            <span class="ov">{{ number_format($receipt->oil_return_kg, 3) }} كيلو زيت</span>
        </div>
    </div>
    @endif

    {{-- الإجمالي --}}
    @if($receipt->status === 'debt')
    <div class="total-box" style="background:#991b1b">
        <div class="lbl">⚠️ دَين — الدفع لم يتم</div>
        <div class="amt">{{ number_format($receipt->total_fee, 3) }}</div>
        <div class="unit">دينار أردني — مستحق السداد</div>
    </div>
    @elseif($receipt->payment_method === 'oil_return')
    <div class="total-box" style="background:#78350f">
        <div class="lbl">🫙 يُسدَّد برديات زيت</div>
        <div class="amt" style="color:#fde68a">{{ number_format($receipt->oil_return_kg, 3) }}</div>
        <div class="unit">كيلو زيت رديات</div>
        @if($receipt->total_fee > 0)
        <div style="font-size:.75rem;opacity:.75;margin-top:4px;border-top:1px solid rgba(255,255,255,.2);padding-top:4px">
            القيمة النقدية المعادلة: {{ number_format($receipt->cans_in_return ? $receipt->total_fee : $receipt->discounted_press_fee, 3) }} د.أ
        </div>
        @endif
    </div>
    @if($receipt->can_total > 0 && !$receipt->cans_in_return)
    <div class="oil-row" style="background:#eff6ff;padding:7px 10px;margin:7px 0;border-radius:6px">
        <span class="ol">التنك نقداً بعد الخصم</span>
        <span class="ov">{{ number_format($receipt->discounted_can_total, 3) }} د.أ</span>
    </div>
    @endif
    @else
    <div class="total-box">
        <div class="lbl">
            @if($receipt->payment_method === 'cash') 💵 المبلغ المستحق نقداً
            @else 💰 المبلغ المستحق
            @endif
        </div>
        <div class="amt">{{ number_format($receipt->total_fee, 3) }}</div>
        <div class="unit">دينار أردني</div>
        @if($receipt->can_total > 0)
        <div style="font-size:.75rem;opacity:.75;margin-top:4px;border-top:1px solid rgba(255,255,255,.2);padding-top:4px">
            أجرة عصر {{ number_format($receipt->press_fee,3) }}
            + تنك {{ number_format($receipt->can_total,3) }}
            @if($receipt->discount_amount > 0) − خصم {{ number_format($receipt->discount_amount,3) }} @endif
            = {{ number_format($receipt->total_fee,3) }}
        </div>
        @endif
    </div>
    @endif

    @if($receipt->status === 'pending')
    <div class="pending-badge">
        ⚠️ هذا الإيصال معلّق — الدفع لم يتم بعد
    </div>
    @endif

    @if($receipt->status === 'processing')
    <div style="background:#dbeafe;color:#1e40af;text-align:center;padding:10px;border-radius:8px;font-weight:800;font-size:.95rem;margin-top:10px;border:2px solid #93c5fd">
        ⚙️ قيد المعالجة — الإيصال تحت العمل حالياً
    </div>
    @endif

    @else
    {{-- ── قبل المعالجة (حجز الدور فقط) ── --}}
    <div style="background:#fff3cd;color:#856404;text-align:center;padding:8px;border-radius:6px;font-weight:700;font-size:.85rem;margin-top:8px;border:1px solid #ffc107">
        📋 هذا الإيصال يُثبت حجز الدور فقط<br>
        <small style="font-weight:400">الأجرة تُحسب عند الكاشير بعد العصر</small>
    </div>
    @endif

    @if($receipt->notes)
    <div style="font-size:.8rem;color:#666;margin-top:8px;padding:6px;background:#f9f9f9;border-radius:6px">
        ملاحظة: {{ $receipt->notes }}
    </div>
    @endif

    <div class="footer">
        شكراً لثقتكم بـ {{ $pressName }} 🫒<br>
        {{ $receipt->created_at->format('Y/m/d H:i') }}
    </div>

</div>
</body>
</html>
