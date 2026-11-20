<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <title>إيصال {{ $receipt->receipt_number }}</title>
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;600;700;800;900&display=swap" rel="stylesheet">
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body { font-family: 'Cairo', sans-serif; background: #fff; direction: rtl; color: #1a1a1a; font-size: 13px; }

        .no-print { text-align: center; padding: 10px; background: #f0f0f0; display: flex; gap: 10px; justify-content: center; }
        .btn-p { font-family:'Cairo',sans-serif; font-size:.9rem; padding:7px 20px; border-radius:6px; cursor:pointer; border:none; font-weight:700; background:#2d5016; color:#fff; }
        .btn-c { font-family:'Cairo',sans-serif; font-size:.9rem; padding:7px 20px; border-radius:6px; cursor:pointer; border:none; font-weight:700; background:#666; color:#fff; }

        .page { max-width: 420px; margin: 16px auto; padding: 20px; border-radius: 12px; }

        .doc-header { text-align: center; border-bottom: 2px dashed #7ab648; padding-bottom: 12px; margin-bottom: 12px; }
        .doc-header h2 { color: #2d5016; font-size: 1.35rem; font-weight: 900; }
        .doc-header .sub { color: #888; font-size: .78rem; margin-top: 3px; }

        .receipt-num { text-align: center; padding: 7px 10px; border-radius: 6px; font-size: .95rem; font-weight: 700; margin-bottom: 14px; }

        .section-title { font-weight: 700; padding: 4px 8px; border-radius: 4px; font-size: .8rem; margin-bottom: 6px; margin-top: 12px; }
        .info-row { display: flex; justify-content: space-between; padding: 5px 2px; border-bottom: 1px dotted #ddd; font-size: .87rem; }
        .info-row .label { color: #666; }
        .info-row .value { font-weight: 700; }

        .olive-box { text-align: center; padding: 12px; border-radius: 8px; margin: 12px 0; }
        .olive-box .amt { font-size: 2rem; font-weight: 900; line-height: 1.1; }
        .olive-box small { opacity: .85; font-size: .8rem; }

        .total-box { text-align: center; padding: 14px; border-radius: 8px; margin: 12px 0; }
        .total-box .lbl  { font-size: .82rem; opacity: .85; margin-bottom: 4px; }
        .total-box .amt  { font-size: 2.2rem; font-weight: 900; line-height: 1.1; }
        .total-box .unit { font-size: .8rem; opacity: .75; margin-top: 3px; }

        .oil-box { background: #fffbeb; border: 1.5px solid #fcd34d; border-radius: 8px; padding: 10px 12px; margin: 10px 0; }
        .oil-box .oil-title { font-size: .8rem; font-weight: 800; color: #78350f; margin-bottom: 6px; }
        .oil-row { display: flex; justify-content: space-between; padding: 3px 0; font-size: .87rem; border-bottom: 1px dotted #fcd34d; }
        .oil-row:last-child { border: none; }
        .oil-row .ol { color: #78350f; }
        .oil-row .ov { font-weight: 800; color: #92400e; }

        .footer { text-align: center; margin-top: 14px; font-size: .73rem; color: #aaa; border-top: 1px dashed #ccc; padding-top: 8px; }

        @media print {
            .no-print { display: none !important; }
            body { margin: 0; }
            .page { margin: 0; max-width: 100%; border-radius: 0; border-color: #000 !important; }
        }
    </style>
</head>
<body>

@php
    // واتساب
    $ownerPhone2 = $receipt->owner->phone ?? '';
    $waPhone2 = preg_replace('/\D/', '', $ownerPhone2);
    if (str_starts_with($waPhone2, '0')) { $waPhone2 = '962' . substr($waPhone2, 1); }
    elseif ($waPhone2 && !str_starts_with($waPhone2, '962')) { $waPhone2 = '962' . $waPhone2; }

    $extractRate2 = ($receipt->olive_weight_kg > 0 && $receipt->oil_weight_kg > 0)
        ? round(($receipt->oil_weight_kg / $receipt->olive_weight_kg) * 100, 1) : 0;

    if ($receipt->oil_weight_kg > 0) {
        $waMsg2  = "مرحباً {$receipt->owner->name} 🫒\n";
        if ($receipt->status === 'processing') {
            $waMsg2 .= "زيتونك قيد المعالجة حالياً\n";
        } else {
            $waMsg2 .= "تم بحمد الله عصر زيتونك\n";
        }
        $waMsg2 .= "في {$pressName}\n";
        $waMsg2 .= "━━━━━━━━━━━━━━━━\n";
        $waMsg2 .= "📋 رقم الإيصال: {$receipt->receipt_number}\n";
        $waMsg2 .= "📅 التاريخ: " . $receipt->created_at->format('Y/m/d') . "\n";
        if ($receipt->floor_number) $waMsg2 .= "🔢 رقم الدور: {$receipt->floor_number}\n";
        $waMsg2 .= "━━━━━━━━━━━━━━━━\n";
        $waMsg2 .= "⚖️ الزيتون: " . number_format($receipt->olive_weight_kg, 1) . " كيلو\n";
        $waMsg2 .= "🫙 الزيت المعصور: " . $receipt->oil_total_label . "\n";
        if ($extractRate2 > 0) $waMsg2 .= "📊 نسبة الاستخلاص: {$extractRate2}%\n";
        if ($receipt->olive_oil_ratio) $waMsg2 .= "📐 نسبة الزيتون/الزيت: " . number_format($receipt->olive_oil_ratio, 1) . " كيلو زيتون / كيلو زيت\n";
        $waMsg2 .= "━━━━━━━━━━━━━━━━\n";
        $waMsg2 .= "💰 أجرة العصر: " . number_format($receipt->press_fee, 3) . " د.أ\n";
        if ($receipt->can_total > 0) $waMsg2 .= "📦 التنك: " . number_format($receipt->can_total, 3) . " د.أ\n";
        if ($receipt->discount_amount > 0) $waMsg2 .= "🏷️ خصم: -" . number_format($receipt->discount_amount, 3) . " د.أ\n";
        if ($receipt->status === 'processing') {
            $waMsg2 .= "⚙️ الحالة: قيد المعالجة\n";
        }
        if ($receipt->payment_method === 'oil_return' && $receipt->status === 'debt') {
            $waMsg2 .= "🫙 دَين رديات الزيت: " . number_format($receipt->oil_return_kg, 3) . " كيلو — غير مسدَّد\n";
            $waMsg2 .= "💰 القيمة النقدية: " . number_format($receipt->total_fee, 3) . " د.أ\n";
        } elseif ($receipt->payment_method === 'oil_return') {
            $waMsg2 .= "🫙 رديات الزيت: " . number_format($receipt->oil_return_kg, 3) . " كيلو\n";
            if ($receipt->can_total > 0 && !$receipt->cans_in_return)
                $waMsg2 .= "💵 التنك نقداً بعد الخصم: " . number_format($receipt->discounted_can_total, 3) . " د.أ\n";
        } elseif ($receipt->status === 'debt') {
            $waMsg2 .= "⚠️ مبلغ الدين: " . number_format($receipt->total_fee, 3) . " د.أ\n";
        } else {
            $waMsg2 .= "💵 الإجمالي: " . number_format($receipt->total_fee, 3) . " د.أ\n";
        }
        if ($receipt->debt_paid_at) {
            $waMsg2 .= "━━━━━━━━━━━━━━━━\n";
            $waMsg2 .= "✅ تم سداد الدين\n";
            $waMsg2 .= "📅 تاريخ السداد: " . \Carbon\Carbon::parse($receipt->debt_paid_at)->format('Y/m/d') . "\n";
        }
        $waMsg2 .= "━━━━━━━━━━━━━━━━\n";
        $waMsg2 .= "شكراً لثقتكم •";
    } else {
        $waMsg2  = "مرحباً {$receipt->owner->name} 🫒\n";
        $waMsg2 .= "تم تسجيل دورك في المعصرة\n";
        $waMsg2 .= "{$pressName}\n";
        $waMsg2 .= "ترحب بكم\n";
        $waMsg2 .= "━━━━━━━━━━━━━━━━\n";
        $waMsg2 .= "📋 رقم الإيصال: {$receipt->receipt_number}\n";
        $waMsg2 .= "📅 التاريخ: " . $receipt->created_at->format('Y/m/d') . "\n";
        if ($receipt->floor_number) $waMsg2 .= "🔢 رقم الدور: {$receipt->floor_number}\n";
        $waMsg2 .= "⚖️ كمية الزيتون: " . number_format($receipt->olive_weight_kg, 1) . " كيلو\n";
        $waMsg2 .= "🚚 الناقل: {$receipt->carrier_name}\n";
        $waMsg2 .= "━━━━━━━━━━━━━━━━\n";
        $waMsg2 .= "شعارنا الصدق والامانة والاخلاص في العمل •";
    }
    $waUrl2 = $waPhone2 ? 'https://wa.me/' . $waPhone2 . '?text=' . rawurlencode($waMsg2) : '';
@endphp

<div class="no-print">
    <button class="btn-p" onclick="window.print()">🖨️ طباعة</button>
    @if($waUrl2)
    <a href="{{ $waUrl2 }}" target="_blank" style="font-family:'Cairo',sans-serif;font-size:.9rem;padding:7px 20px;border-radius:6px;font-weight:700;background:#25D366;color:#fff;border:none;text-decoration:none;cursor:pointer;display:inline-flex;align-items:center;gap:4px">
        <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 24 24" fill="white"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/></svg>
        واتساب
    </a>
    @endif
    <button class="btn-c" onclick="window.close(); setTimeout(()=>history.back(),150)">✕ إغلاق</button>
</div>

@php
    $isDebt         = $receipt->status === 'debt';
    $isOilReturn    = $receipt->payment_method === 'oil_return';
    $isOilReturnDebt = $isDebt && $isOilReturn; // دَين رديات زيت
    $isCompleted    = $receipt->status === 'completed';
    $isPending      = $receipt->status === 'pending';
    $isProc         = $receipt->status === 'processing';
    $isPaidDebt     = $receipt->debt_paid_at !== null && $receipt->status === 'completed';

    if ($isOilReturnDebt) {
        $borderColor  = '#92400e';
        $headerBg     = '#fffbeb';
        $headerBorder = '#fcd34d';
        $numBg        = '#78350f';
        $sectionBg    = '#fef3c7';
        $sectionColor = '#92400e';
        $oliveBg      = '#78350f';
        $totalBg      = '#78350f';
    } elseif ($isDebt) {
        $borderColor  = '#dc2626';
        $headerBg     = '#fef2f2';
        $headerBorder = '#fca5a5';
        $numBg        = '#991b1b';
        $sectionBg    = '#fee2e2';
        $sectionColor = '#991b1b';
        $oliveBg      = '#991b1b';
        $totalBg      = '#991b1b';
    } elseif ($isOilReturn) {
        $borderColor  = '#d97706';
        $headerBg     = '#fffbeb';
        $headerBorder = '#fcd34d';
        $numBg        = '#78350f';
        $sectionBg    = '#fef3c7';
        $sectionColor = '#92400e';
        $oliveBg      = '#78350f';
        $totalBg      = '#78350f';
    } else {
        $borderColor  = '#2d5016';
        $headerBg     = '#fff';
        $headerBorder = '#7ab648';
        $numBg        = '#2d5016';
        $sectionBg    = '#e8f5d4';
        $sectionColor = '#2d5016';
        $oliveBg      = '#2d5016';
        $totalBg      = '#2d5016';
    }
@endphp

<div class="page" style="border: 2px solid {{ $borderColor }}">

    {{-- رأس --}}
    <div class="doc-header" style="background:{{ $headerBg }};border-bottom-color:{{ $headerBorder }}">
        <div style="font-size:1.8rem">🫒</div>
        <h2>{{ $pressName }}</h2>
        @if($isPaidDebt)
        <div class="sub" style="color:#16a34a;font-weight:800;font-size:1rem">✅ تم سداد الدين</div>
        @elseif($isOilReturnDebt)
        <div class="sub" style="color:#d97706;font-weight:700">🫙 دَين رديات زيت — غير مسدَّد</div>
        @elseif($isDebt)
        <div class="sub" style="color:#dc2626;font-weight:700">وصل دَين — إيصال غير مسدَّد</div>
        @elseif($isOilReturn)
        <div class="sub" style="color:#d97706;font-weight:700">🫙 يُسدَّد برديات زيت</div>
        @elseif($isCompleted)
        <div class="sub" style="color:#2d5016;font-weight:700">✅ تم الدفع نقداً</div>
        @elseif($isProc)
        <div class="sub" style="color:#1e40af;font-weight:700">⚙️ قيد المعالجة</div>
        @else
        <div class="sub" style="color:#92400e;font-weight:700">⏳ في الانتظار</div>
        @endif
    </div>

    {{-- رقم الإيصال --}}
    <div class="receipt-num" style="background:{{ $numBg }};color:#fff">
        @if($isOilReturnDebt) 🫙 دَين رديات زيت —
        @elseif($isDebt) ⚠️ دَين — @endif
        رقم الإيصال: {{ $receipt->receipt_number }}
        &nbsp;|&nbsp;
        {{ $receipt->created_at->format('Y/m/d') }}
    </div>

    {{-- ختم قيد المعالجة --}}
    @if($isProc)
    <div style="background:#dbeafe;color:#1e40af;text-align:center;padding:10px;border-radius:8px;font-weight:800;font-size:.95rem;margin-bottom:12px;border:2px solid #93c5fd">
        ⚙️ قيد المعالجة — الإيصال تحت العمل حالياً
    </div>
    @endif

    {{-- ختم تم سداد الدين --}}
    @if($isPaidDebt)
    <div style="text-align:center;margin:10px 0;padding:10px 14px;border-radius:8px;background:#f0fdf4;border:2px solid #16a34a">
        <div style="font-size:1.3rem;font-weight:900;color:#15803d">✅ تم سداد الدين</div>
        <div style="font-size:.8rem;color:#166534;margin-top:3px">
            تاريخ السداد: {{ \Carbon\Carbon::parse($receipt->debt_paid_at)->format('Y/m/d') }}
        </div>
    </div>
    @endif

    {{-- صاحب الزيتون --}}
    <div class="section-title" style="background:{{ $sectionBg }};color:{{ $sectionColor }}">👤 صاحب الزيتون</div>
    <div class="info-row">
        <span class="label">الاسم</span>
        <span class="value">{{ $receipt->owner->name }}</span>
    </div>
    @if($receipt->owner->phone)
    <div class="info-row">
        <span class="label">الهاتف</span>
        <span class="value">{{ $receipt->owner->phone }}</span>
    </div>
    @endif
    <div class="info-row">
        <span class="label">رقم الدور</span>
        <span class="value" style="color:{{ $sectionColor }}">{{ $receipt->floor_number ?? '—' }}</span>
    </div>

    {{-- تفاصيل الإيصال --}}
    <div class="section-title" style="background:{{ $sectionBg }};color:{{ $sectionColor }}">📦 تفاصيل الإيصال</div>
    <div class="info-row">
        <span class="label">الناقل</span>
        <span class="value">{{ $receipt->carrier_name }}</span>
    </div>

    {{-- كمية الزيتون --}}
    <div class="olive-box" style="background:{{ $oliveBg }};color:#fff">
        <small>كمية الزيتون</small>
        <div class="amt" style="color:#f0d060">{{ number_format($receipt->olive_weight_kg, 1) }}</div>
        <small>كيلوغرام</small>
    </div>

    {{-- كمية الزيت المعصور --}}
    @if($receipt->oil_weight_kg > 0)
    <div class="oil-box">
        <div class="oil-title">🫙 نتيجة العصر</div>
        <div class="oil-row">
            <span class="ol">الزيت المعصور</span>
            <span class="ov">{{ number_format($receipt->oil_weight_kg,1) }} كيلو
                @if($receipt->oil_weight_gram) + {{ $receipt->oil_weight_gram }} غم @endif
            </span>
        </div>
        <div class="oil-row">
            <span class="ol">أجرة العصر</span>
            <span class="ov">{{ number_format($receipt->press_fee, 3) }} د.أ</span>
        </div>
    </div>

    {{-- التنك --}}
    @if($receipt->can_total > 0)
    <div class="oil-box" style="background:#eff6ff;border-color:#bfdbfe">
        <div class="oil-title" style="color:#1e40af">📦 التنك</div>
        @if($receipt->can_16_count > 0)
        <div class="oil-row" style="border-color:#bfdbfe">
            <span style="color:#1e40af">{{ $receipt->can_16_count }} × تنكة 16 كيلو</span>
            <span style="font-weight:800;color:#1e3a8a">{{ number_format($receipt->can_16_count * $canPrices['can_16'], 3) }} د.أ</span>
        </div>
        @endif
        @if($receipt->can_8_count > 0)
        <div class="oil-row" style="border-color:#bfdbfe">
            <span style="color:#1e40af">{{ $receipt->can_8_count }} × تنكة 8 كيلو</span>
            <span style="font-weight:800;color:#1e3a8a">{{ number_format($receipt->can_8_count * $canPrices['can_8'], 3) }} د.أ</span>
        </div>
        @endif
        @if($receipt->can_4_count > 0)
        <div class="oil-row" style="border-color:#bfdbfe">
            <span style="color:#1e40af">{{ $receipt->can_4_count }} × تنكة 4 كيلو</span>
            <span style="font-weight:800;color:#1e3a8a">{{ number_format($receipt->can_4_count * $canPrices['can_4'], 3) }} د.أ</span>
        </div>
        @endif
        <div class="oil-row" style="border-top:1.5px solid #bfdbfe;margin-top:4px;padding-top:5px">
            <span style="color:#1e40af;font-weight:800">مجموع التنك</span>
            <span style="font-weight:800;color:#1e3a8a">{{ number_format($receipt->can_total, 3) }} د.أ</span>
        </div>
        @if($isOilReturn && $receipt->cans_in_return)
        <div style="font-size:.73rem;color:#1e40af;text-align:center;margin-top:3px">✓ مُسوَّاة ضمن رديات الزيت</div>
        @endif
    </div>
    @endif
    @endif

    @if($receipt->discount_amount > 0)
    <div class="info-row" style="background:#fef9c3;padding:5px;border-radius:5px">
        <span class="label">خصم من الإجمالي</span>
        <span class="value">−{{ number_format($receipt->discount_amount, 3) }} د.أ</span>
    </div>
    @endif

    {{-- طريقة الدفع --}}
    <div class="info-row">
        <span class="label">طريقة الدفع</span>
        <span class="value">
            @if($isOilReturnDebt) 🫙 رديات زيت (دَين)
            @elseif($isDebt) ⚠️ دَين
            @elseif($isOilReturn) 🫙 رديات زيت
            @else 💵 نقدي
            @endif
        </span>
    </div>

    {{-- الإجمالي --}}
    @if($isOilReturnDebt)
    <div class="total-box" style="background:#78350f;color:#fff">
        <div class="lbl">🫙 دَين رديات زيت — غير مسدَّد</div>
        <div class="amt" style="color:#fde68a">{{ number_format($receipt->oil_return_kg, 3) }}</div>
        <div class="unit">كيلو زيت</div>
        <div style="font-size:.72rem;opacity:.75;margin-top:3px;border-top:1px solid rgba(255,255,255,.2);padding-top:3px">
            القيمة النقدية: {{ number_format($receipt->total_fee, 3) }} د.أ
        </div>
    </div>
    @elseif($isDebt)
    <div class="total-box" style="background:#991b1b;color:#fff">
        <div class="lbl">المبلغ المدين</div>
        <div class="amt" style="color:#fca5a5">{{ number_format($receipt->total_fee, 3) }}</div>
        <div class="unit">دينار أردني — غير مسدَّد</div>
    </div>
    @elseif($isOilReturn)
    <div class="total-box" style="background:#78350f;color:#fff">
        <div class="lbl">🫙 أجرة العصر — رديات زيت</div>
        <div class="amt" style="color:#fde68a">{{ number_format($receipt->oil_return_kg, 3) }}</div>
        <div class="unit">كيلو زيت</div>
        @if($receipt->press_fee > 0)
        <div style="font-size:.72rem;opacity:.75;margin-top:3px;border-top:1px solid rgba(255,255,255,.2);padding-top:3px">
            القيمة النقدية بعد الخصم: {{ number_format($receipt->discounted_press_fee, 3) }} د.أ
        </div>
        @endif
    </div>
    @if($receipt->can_total > 0 && !$receipt->cans_in_return)
    <div class="total-box" style="background:#2d5016;color:#fff;margin-top:8px">
        <div class="lbl">💵 التنك — نقداً</div>
        <div class="amt" style="color:#f0d060">{{ number_format($receipt->discounted_can_total, 3) }}</div>
        <div class="unit">دينار أردني</div>
    </div>
    @endif
    @elseif($receipt->total_fee > 0)
    <div class="total-box" style="background:#2d5016;color:#fff">
        <div class="lbl">💵 المبلغ المستحق نقداً</div>
        <div class="amt" style="color:#f0d060">{{ number_format($receipt->total_fee, 3) }}</div>
        <div class="unit">دينار أردني</div>
        @if($receipt->can_total > 0)
        <div style="font-size:.72rem;opacity:.75;margin-top:3px;border-top:1px solid rgba(255,255,255,.2);padding-top:3px">
            عصر {{ number_format($receipt->press_fee,3) }} + تنك {{ number_format($receipt->can_total,3) }}
        </div>
        @endif
    </div>
    @endif

    <div class="footer">
        {{ $pressName }} 🫒 — {{ $receipt->created_at->format('Y/m/d H:i') }}
    </div>

</div>
</body>
</html>
