<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <title>وصل دين — {{ $receipt->receipt_number }}</title>
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;600;700;800;900&display=swap" rel="stylesheet">
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body { font-family: 'Cairo', sans-serif; background: #fff; direction: rtl; color: #1a1a1a; font-size: 13px; }

        .page { max-width: 400px; margin: 20px auto; padding: 20px; border: 2px solid #b91c1c; border-radius: 12px; }

        .no-print {
            text-align: center; padding: 10px; background: #f0f0f0;
            display: flex; gap: 10px; justify-content: center; margin-bottom: 0;
        }
        .btn-p { font-family:'Cairo',sans-serif; font-size:.9rem; padding:7px 20px; border-radius:6px; cursor:pointer; border:none; font-weight:700; background:#b91c1c; color:#fff; }
        .btn-c { font-family:'Cairo',sans-serif; font-size:.9rem; padding:7px 20px; border-radius:6px; cursor:pointer; border:none; font-weight:700; background:#666; color:#fff; }

        .doc-header { text-align: center; border-bottom: 2px dashed #b91c1c; padding-bottom: 12px; margin-bottom: 14px; }
        .doc-header h2 { color: #7f1d1d; font-size: 1.3rem; font-weight: 900; }
        .doc-header .sub { color: #888; font-size: .78rem; margin-top: 3px; }

        .debt-stamp {
            background: #7f1d1d; color: #fff; text-align: center;
            padding: 8px 12px; border-radius: 7px; font-size: .95rem;
            font-weight: 900; margin-bottom: 14px; letter-spacing: .5px;
        }

        .section-title {
            background: #fee2e2; color: #7f1d1d; font-weight: 700;
            padding: 4px 8px; border-radius: 4px; font-size: .8rem; margin-bottom: 6px; margin-top: 10px;
        }
        .info-row {
            display: flex; justify-content: space-between;
            padding: 5px 2px; border-bottom: 1px dotted #fecaca; font-size: .87rem;
        }
        .info-row .label { color: #777; }
        .info-row .value { font-weight: 700; }

        .amount-box {
            background: #7f1d1d; color: #fff; text-align: center;
            padding: 14px; border-radius: 8px; margin: 14px 0;
        }
        .amount-box .lbl  { font-size: .82rem; opacity: .8; margin-bottom: 4px; }
        .amount-box .amt  { font-size: 2.2rem; font-weight: 900; color: #fca5a5; line-height: 1.1; }
        .amount-box .unit { font-size: .8rem; opacity: .75; margin-top: 3px; }

        .total-owner-box {
            background: #fef2f2; border: 1.5px solid #fca5a5; border-radius: 7px;
            padding: 8px 12px; display: flex; justify-content: space-between;
            font-size: .85rem; margin-bottom: 12px;
        }
        .total-owner-box .tl { color: #7f1d1d; font-weight: 700; }
        .total-owner-box .tv { font-weight: 900; color: #b91c1c; font-size: 1rem; }

        .sig-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 16px; margin-top: 20px; }
        .sig-box { border-top: 1.5px solid #b91c1c; padding-top: 5px; text-align: center; font-size: .75rem; color: #777; }
        .sig-box .sig-space { height: 36px; }

        .footer { text-align: center; font-size: .72rem; color: #aaa; margin-top: 12px; border-top: 1px dashed #fecaca; padding-top: 8px; }

        @media print {
            .no-print { display: none !important; }
            body { margin: 0; }
            .page { border: 2px solid #000; margin: 0; max-width: 100%; border-radius: 0; }
        }
    </style>
</head>
<body>

@php
    $dpPhone = preg_replace('/\D/', '', $owner->phone ?? '');
    if (str_starts_with($dpPhone, '0')) { $dpPhone = '962' . substr($dpPhone, 1); }
    elseif ($dpPhone && !str_starts_with($dpPhone, '962')) { $dpPhone = '962' . $dpPhone; }

    $isPaid = $receipt->debt_paid_at !== null;

    $dpMsg  = "مرحباً {$owner->name} 🫒\n";
    $dpMsg .= $isPaid ? "إشعار سداد دَين — {$pressName}\n" : "وصل دَين من {$pressName}\n";
    $dpMsg .= "━━━━━━━━━━━━━━━━\n";
    $dpMsg .= "📋 رقم الإيصال: {$receipt->receipt_number}\n";
    $dpMsg .= "📅 تاريخ الإيصال: " . $receipt->created_at->format('Y/m/d') . "\n";
    if ($owner->floor_number) $dpMsg .= "🔢 رقم الدور: {$owner->floor_number}\n";
    $dpMsg .= "━━━━━━━━━━━━━━━━\n";
    $dpMsg .= "⚖️ الزيتون: " . number_format($receipt->olive_weight_kg, 1) . " كيلو\n";
    if ($receipt->oil_weight_kg > 0) $dpMsg .= "🫙 الزيت: " . $receipt->oil_total_label . "\n";
    if ($receipt->press_fee > 0) $dpMsg .= "💰 أجرة العصر: " . number_format($receipt->press_fee, 3) . " د.أ\n";
    if ($receipt->can_total > 0) $dpMsg .= "📦 التنك: " . number_format($receipt->can_total, 3) . " د.أ\n";
    if ($receipt->discount_amount > 0) $dpMsg .= "🏷️ خصم: -" . number_format($receipt->discount_amount, 3) . " د.أ\n";
    $dpMsg .= "━━━━━━━━━━━━━━━━\n";
    if ($receipt->payment_method === 'oil_return' && $receipt->oil_return_kg > 0)
        $dpMsg .= "🫙 كمية رديات الزيت: " . number_format($receipt->oil_return_kg, 2) . " كيلو زيت\n";
    $dpMsg .= "💸 المبلغ " . ($isPaid ? "المسدَّد" : "المدين") . ": " . number_format($receipt->total_fee, 3) . " د.أ\n";
    if ($isPaid) {
        $dpMsg .= "━━━━━━━━━━━━━━━━\n";
        $dpMsg .= "✅ تم سداد الدين بالكامل\n";
        $dpMsg .= "📅 تاريخ السداد: " . \Carbon\Carbon::parse($receipt->debt_paid_at)->format('Y/m/d') . "\n";
    }
    $dpMsg .= "━━━━━━━━━━━━━━━━\n";
    $dpMsg .= "شكراً لثقتكم •";
    $dpUrl = $dpPhone ? 'https://wa.me/' . $dpPhone . '?text=' . rawurlencode($dpMsg) : '';
@endphp

<div class="no-print">
    <button class="btn-p" onclick="window.print()">🖨️ طباعة</button>
    @if($dpUrl)
    <a href="{{ $dpUrl }}" target="_blank" style="font-family:'Cairo',sans-serif;font-size:.9rem;padding:7px 20px;border-radius:6px;font-weight:700;background:#25D366;color:#fff;border:none;text-decoration:none;cursor:pointer;display:inline-flex;align-items:center;gap:4px">
        <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 24 24" fill="white"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/></svg>
        واتساب
    </a>
    @endif
    <button class="btn-c" onclick="window.close()">✕ إغلاق</button>
</div>

<div class="page">

    <div class="doc-header">
        <div style="font-size:1.8rem">🫒</div>
        <h2>{{ $pressName }}</h2>
        @if(!empty($pressPhone))
        <div class="sub" style="color:#7f1d1d;font-weight:700">📞 {{ $pressPhone }}</div>
        @endif
        <div class="sub">وصل دَين — إيصال غير مسدَّد</div>
    </div>

    <div class="debt-stamp">
        💸 دَين — {{ $receipt->receipt_number }}
        &nbsp;|&nbsp; {{ $receipt->created_at->format('Y/m/d') }}
    </div>

    <div class="section-title">👤 صاحب الزيتون</div>
    <div class="info-row">
        <span class="label">الاسم</span>
        <span class="value">{{ $owner->name }}</span>
    </div>
    <div class="info-row">
        <span class="label">رقم الدور</span>
        <span class="value" style="color:#7f1d1d;font-size:1rem">{{ $owner->floor_number }}</span>
    </div>
    <div class="info-row">
        <span class="label">الهاتف</span>
        <span class="value">{{ $owner->phone }}</span>
    </div>

    <div class="section-title">📦 تفاصيل الإيصال</div>
    <div class="info-row">
        <span class="label">الناقل</span>
        <span class="value">{{ $receipt->carrier_name }}</span>
    </div>
    <div class="info-row">
        <span class="label">كمية الزيتون</span>
        <span class="value">{{ number_format($receipt->olive_weight_kg, 1) }} كيلو</span>
    </div>
    @if($receipt->oil_weight_kg > 0)
    <div class="info-row">
        <span class="label">الزيت المعصور</span>
        <span class="value">{{ $receipt->oil_total_label }}</span>
    </div>
    @endif
    <div class="info-row">
        <span class="label">أجرة العصر</span>
        <span class="value" style="color:#1d4ed8">{{ number_format($receipt->press_fee, 3) }} د.أ</span>
    </div>
    @if($receipt->can_total > 0)
    <div class="info-row">
        <span class="label">قيمة التنك</span>
        <span class="value" style="color:#1e40af">
            {{ number_format($receipt->can_total, 3) }} د.أ
            @if($receipt->can_16_count > 0) {{ $receipt->can_16_count }}×16كيلو ({{ number_format($receipt->can_16_count * $canPrices['can_16'], 3) }} د.أ)@endif
            @if($receipt->can_8_count  > 0) {{ $receipt->can_8_count  }}×8كيلو ({{ number_format($receipt->can_8_count * $canPrices['can_8'], 3) }} د.أ)@endif
            @if($receipt->can_4_count  > 0) {{ $receipt->can_4_count  }}×4كيلو ({{ number_format($receipt->can_4_count * $canPrices['can_4'], 3) }} د.أ)@endif
        </span>
    </div>
    @endif
    @if($receipt->discount_amount > 0)
    <div class="info-row">
        <span class="label">خصم من الإجمالي</span>
        <span class="value">−{{ number_format($receipt->discount_amount, 3) }} د.أ</span>
    </div>
    @endif
    <div class="info-row">
        <span class="label">طريقة الدفع</span>
        <span class="value">{{ $receipt->payment_method_label }}</span>
    </div>
    @if($receipt->payment_method === 'oil_return' && $receipt->oil_return_kg > 0)
    <div class="info-row">
        <span class="label">كمية رديات الزيت</span>
        <span class="value" style="color:#d97706;font-size:1.05rem">{{ number_format($receipt->oil_return_kg, 2) }} كيلو زيت</span>
    </div>
    @endif

    {{-- المبلغ المدين --}}
    <div class="amount-box" @if($receipt->payment_method === 'oil_return') style="background:#92400e" @endif>
        @if($receipt->payment_method === 'oil_return' && $receipt->oil_return_kg > 0)
            <div class="lbl">كمية رديات الزيت المدينة</div>
            <div class="amt">{{ number_format($receipt->oil_return_kg, 2) }}</div>
            <div class="unit">كيلو زيت — غير مسدَّد</div>
            <div style="margin-top:6px;font-size:.8rem;opacity:.75">المبلغ: {{ number_format($receipt->total_fee, 3) }} د.أ</div>
        @else
            <div class="lbl">المبلغ المدين</div>
            <div class="amt">{{ number_format($receipt->total_fee, 3) }}</div>
            <div class="unit">دينار أردني — غير مسدَّد</div>
        @endif
    </div>

    {{-- إجمالي ديون المالك --}}
    @if($totalOwnerDebt > $receipt->total_fee)
    <div class="total-owner-box">
        <span class="tl">إجمالي ديون {{ $owner->name }}</span>
        <span class="tv">{{ number_format($totalOwnerDebt, 3) }} د.أ</span>
    </div>
    @endif

    @if($receipt->notes)
    <div style="font-size:.8rem;color:#666;padding:6px 8px;background:#fef9f9;border-radius:5px;margin-bottom:8px">
        ملاحظة: {{ $receipt->notes }}
    </div>
    @endif

    <div class="sig-grid">
        <div class="sig-box">
            <div class="sig-space"></div>
            توقيع صاحب الزيتون
        </div>
        <div class="sig-box">
            <div class="sig-space"></div>
            توقيع الكاشير
        </div>
    </div>

    <div class="footer">
        {{ $pressName }} — {{ now()->format('Y/m/d H:i') }}<br>
        هذا الوصل يُثبت وجود دين غير مسدَّد
    </div>

</div>
</body>
</html>
