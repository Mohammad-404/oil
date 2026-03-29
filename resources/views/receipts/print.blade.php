<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <title>إيصال {{ $receipt->receipt_number }}</title>
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;600;700;800&display=swap" rel="stylesheet">
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body { font-family: 'Cairo', sans-serif; background: #fff; direction: rtl; }
        .receipt { max-width: 420px; margin: 20px auto; padding: 20px; border: 2px solid #2d5016; border-radius: 12px; }
        .receipt-header { text-align: center; border-bottom: 2px dashed #7ab648; padding-bottom: 12px; margin-bottom: 12px; }
        .receipt-header h2 { color: #2d5016; font-size: 1.4rem; font-weight: 800; }
        .receipt-header p { color: #666; font-size: .8rem; }
        .receipt-number { background: #2d5016; color: #fff; text-align: center; padding: 6px; border-radius: 6px; font-size: .9rem; font-weight: 700; margin-bottom: 12px; }
        .section-title { background: #e8f5d4; color: #2d5016; font-weight: 700; padding: 4px 8px; border-radius: 4px; font-size: .82rem; margin-bottom: 6px; }
        .info-row { display: flex; justify-content: space-between; padding: 4px 2px; border-bottom: 1px dotted #ddd; font-size: .85rem; }
        .info-row .label { color: #666; }
        .info-row .value { font-weight: 600; }
        .fee-box { background: #2d5016; color: #fff; text-align: center; padding: 10px; border-radius: 8px; margin-top: 12px; }
        .fee-box .amount { font-size: 1.8rem; font-weight: 800; color: #f0d060; }
        .fee-box small { opacity: .8; font-size: .8rem; }
        .status-badge { display: inline-block; padding: 3px 12px; border-radius: 20px; font-size: .8rem; font-weight: 700; }
        .footer { text-align: center; margin-top: 12px; font-size: .75rem; color: #999; border-top: 1px dashed #ccc; padding-top: 8px; }
        .oil-section { background: #e0f2fe; border: 1px solid #7dd3fc; border-radius: 8px; padding: 10px; margin-top: 10px; }
        @media print {
            body { margin: 0; }
            .no-print { display: none; }
            .receipt { border: 2px solid #000; margin: 0; max-width: 100%; }
        }
    </style>
</head>
<body>

<div class="no-print" style="text-align:center; padding:10px; background:#f0f0f0">
    <button onclick="window.print()" style="background:#2d5016;color:#fff;border:none;padding:8px 20px;border-radius:6px;font-family:Cairo;font-size:1rem;cursor:pointer">
        🖨️ طباعة
    </button>
    <button onclick="window.close()" style="margin-right:10px;background:#666;color:#fff;border:none;padding:8px 20px;border-radius:6px;font-family:Cairo;font-size:1rem;cursor:pointer">
        ✕ إغلاق
    </button>
</div>

<!-- إيصال صاحب الزيتون -->
<div class="receipt">
    <div class="receipt-header">
        <div style="font-size:2rem">🫒</div>
        <h2>{{ $pressName }}</h2>
        <p>استمارة استلام الزيتون</p>
    </div>

    <div class="receipt-number">
        رقم الإيصال: {{ $receipt->receipt_number }}
        &nbsp;&nbsp;|&nbsp;&nbsp;
        التاريخ: {{ $receipt->created_at->format('Y/m/d') }}
    </div>

    <div class="section-title">👤 بيانات صاحب الزيتون</div>
    <div class="info-row"><span class="label">الاسم</span><span class="value">{{ $receipt->owner->name }}</span></div>
    <div class="info-row"><span class="label">المدينة</span><span class="value">{{ $receipt->owner->city }}</span></div>
    <div class="info-row"><span class="label">الشارع</span><span class="value">{{ $receipt->owner->street }}</span></div>
    <div class="info-row"><span class="label">الهاتف</span><span class="value">{{ $receipt->owner->phone }}</span></div>
    <div class="info-row"><span class="label">رقم الدور</span><span class="value">{{ $receipt->floor_number ?? '—' }}</span></div>

    <br>
    <div class="section-title">🚚 بيانات الشحنة</div>
    <div class="info-row"><span class="label">اسم حامل الزيتون</span><span class="value">{{ $receipt->carrier_name }}</span></div>
    <div class="info-row"><span class="label">كمية الزيتون</span><span class="value" style="color:#2d5016;font-size:1.1rem">{{ number_format($receipt->olive_weight_kg, 1) }} كيلو</span></div>
    <div class="info-row"><span class="label">الأجرة / كيلو</span><span class="value">{{ number_format($receipt->fee_per_kg, 2) }} د.أ</span></div>

    @if($receipt->oil_weight_kg)
    <div class="oil-section">
        <div class="section-title" style="background:#bae6fd">🫙 نتيجة العصر</div>
        <div class="info-row"><span class="label">كمية الزيت</span><span class="value">{{ number_format($receipt->oil_weight_kg, 1) }} كيلو @if($receipt->oil_weight_gram) + {{ $receipt->oil_weight_gram }} غم @endif</span></div>
    </div>
    @endif

    @if($receipt->notes)
    <div style="font-size:.82rem; color:#666; margin-top:8px; padding:6px; background:#f9f9f9; border-radius:6px">
        ملاحظة: {{ $receipt->notes }}
    </div>
    @endif

    <div class="fee-box">
        <small>إجمالي الأجرة</small>
        <div class="amount">{{ number_format($receipt->total_fee, 2) }}</div>
        <small>دينار أردني</small>
    </div>

    <div class="footer">
        شكراً لثقتكم بمعصرتنا 🫒<br>
        {{ $receipt->created_at->format('Y/m/d H:i') }}
    </div>
</div>

<!-- إيصال الكاشير (نسخة ثانية) -->
<div class="receipt" style="margin-top:20px; border-color:#c9a227">
    <div style="text-align:center; font-size:.8rem; color:#92400e; font-weight:700; margin-bottom:8px">
        ✂️ - - - - - - نسخة الكاشير - - - - - - ✂️
    </div>
    <div class="receipt-number" style="background:#c9a227">
        رقم الإيصال: {{ $receipt->receipt_number }}
    </div>

    <div class="section-title">بيانات صاحب الزيتون (الكاشير)</div>
    <div class="info-row"><span class="label">الاسم</span><span class="value">{{ $receipt->owner->name }}</span></div>
    <div class="info-row"><span class="label">المدينة</span><span class="value">{{ $receipt->owner->city }}</span></div>
    <div class="info-row"><span class="label">الشارع</span><span class="value">{{ $receipt->owner->street }}</span></div>
    <div class="info-row"><span class="label">الهاتف</span><span class="value">{{ $receipt->owner->phone }}</span></div>
    <div class="info-row"><span class="label">رقم الدور</span><span class="value">{{ $receipt->floor_number ?? '—' }}</span></div>
    <div class="info-row"><span class="label">الزيتون</span><span class="value">{{ number_format($receipt->olive_weight_kg, 1) }} كيلو</span></div>
    <div class="info-row"><span class="label">كمية الزيت (كيلو)</span><span class="value">{{ $receipt->oil_weight_kg ?? '........' }}</span></div>
    <div class="info-row"><span class="label">كمية الزيت (غرام)</span><span class="value">{{ $receipt->oil_weight_gram ?? '........' }}</span></div>

    <div class="fee-box" style="background:#c9a227">
        <small>الأجرة الإجمالية</small>
        <div class="amount">{{ number_format($receipt->total_fee, 2) }} د.أ</div>
    </div>

    <div class="footer">{{ $receipt->created_at->format('Y/m/d H:i') }}</div>
</div>

</body>
</html>
