<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <title>وصل استلام زيتون - رقم {{ $session->queue_number }}</title>
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;600;700;900&display=swap" rel="stylesheet">
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body { font-family: 'Cairo', sans-serif; background: #fff; color: #111; font-size: 13px; }

        .receipt {
            width: 80mm;
            margin: 0 auto;
            padding: 8mm 6mm;
            border: 2px solid #2d4a1e;
        }

        .header { text-align: center; border-bottom: 2px dashed #2d4a1e; padding-bottom: 8px; margin-bottom: 10px; }
        .header .logo { font-size: 2rem; }
        .header h2 { font-size: 16px; font-weight: 900; color: #2d4a1e; }
        .header p { font-size: 11px; color: #555; }

        .queue-badge {
            text-align: center;
            background: #2d4a1e;
            color: #fff;
            font-size: 28px;
            font-weight: 900;
            padding: 8px;
            border-radius: 6px;
            margin: 10px 0;
        }
        .queue-badge small { font-size: 11px; display: block; font-weight: 400; }

        .section-title {
            font-size: 11px;
            font-weight: 700;
            color: #2d4a1e;
            background: #f0f7e8;
            padding: 3px 8px;
            border-right: 3px solid #4a7c2f;
            margin: 10px 0 6px;
        }

        .row { display: flex; justify-content: space-between; margin: 3px 0; }
        .row .label { color: #555; }
        .row .value { font-weight: 700; }

        .total-box {
            background: #2d4a1e;
            color: #fff;
            text-align: center;
            border-radius: 6px;
            padding: 10px;
            margin-top: 12px;
        }
        .total-box .amount { font-size: 22px; font-weight: 900; }
        .total-box small { font-size: 11px; opacity: .8; }

        .divider { border-top: 1px dashed #999; margin: 10px 0; }

        .footer { text-align: center; font-size: 10px; color: #777; margin-top: 10px; }
        .footer .olive { font-size: 16px; }

        @media print {
            body { margin: 0; }
            .no-print { display: none !important; }
        }
    </style>
</head>
<body>
<div class="receipt">
    <div class="header">
        <div class="logo">🫒</div>
        <h2>معصرة الزيتون</h2>
        <p>{{ config('app.press_name', 'معصرة الزيتون الأهلية') }}</p>
        <p>{{ config('app.press_phone', '') }}</p>
    </div>

    <div class="queue-badge">
        <small>رقم الدور</small>
        {{ $session->queue_number }}
    </div>

    <div class="section-title">بيانات صاحب الزيتون</div>
    <div class="row">
        <span class="label">الاسم:</span>
        <span class="value">{{ $session->owner->name }}</span>
    </div>
    <div class="row">
        <span class="label">المدينة:</span>
        <span class="value">{{ $session->owner->city }}</span>
    </div>
    @if($session->shares)
    <div class="row">
        <span class="label">الأسهم:</span>
        <span class="value">{{ $session->shares }}</span>
    </div>
    @endif

    @if($session->carrier_name)
    <div class="section-title">حامل الزيتون</div>
    <div class="row">
        <span class="label">الاسم:</span>
        <span class="value">{{ $session->carrier_name }}</span>
    </div>
    @endif

    <div class="section-title">الكميات</div>
    <div class="row">
        <span class="label">الزيتون الواصل:</span>
        <span class="value">{{ number_format($session->olives_weight_kg, 1) }} كغم</span>
    </div>
    @if($session->olives_weight_gram)
    <div class="row">
        <span class="label">+ غرام:</span>
        <span class="value">{{ $session->olives_weight_gram }} غ</span>
    </div>
    @endif
    @if($session->extracted_oil_kg)
    <div class="row">
        <span class="label">الزيت المستخرج:</span>
        <span class="value">{{ number_format($session->extracted_oil_kg, 1) }} كغم</span>
    </div>
    @endif
    @if($session->filtered_oil_kg)
    <div class="row">
        <span class="label">الزيت المصفى:</span>
        <span class="value">{{ number_format($session->filtered_oil_kg, 1) }} كغم</span>
    </div>
    @endif

    <div class="divider"></div>

    <div class="section-title">حساب الأجرة</div>
    <div class="row">
        <span class="label">السعر/كغم:</span>
        <span class="value">{{ number_format($session->fee_per_kg, 3) }} د</span>
    </div>
    <div class="row">
        <span class="label">{{ number_format($session->olives_weight_kg,1) }} كغم × {{ number_format($session->fee_per_kg,3) }}:</span>
        <span class="value">{{ number_format($session->olives_weight_kg * $session->fee_per_kg, 3) }} د</span>
    </div>
    @if($session->extra_fee)
    <div class="row">
        <span class="label">أجرة إضافية:</span>
        <span class="value">{{ number_format($session->extra_fee, 3) }} د</span>
    </div>
    @endif

    <div class="total-box">
        <small>إجمالي الأجرة</small>
        <div class="amount">{{ number_format($session->total_fee, 3) }} د</div>
        <small>حالة الدفع: {{ $session->payment_status == 'paid' ? '✅ مدفوع' : ($session->payment_status == 'partial' ? '🔶 جزئي' : '⏳ انتظار') }}</small>
    </div>

    <div class="divider"></div>
    <div class="row">
        <span class="label">التاريخ:</span>
        <span class="value">{{ $session->session_date->format('Y/m/d') }}</span>
    </div>

    <div class="footer">
        <div class="olive">🫒 🫒 🫒</div>
        شكراً لثقتكم — وصل رقم: {{ $session->id }}
    </div>
</div>

<div class="no-print text-center mt-3">
    <button onclick="window.print()" style="padding:10px 30px; background:#2d4a1e; color:#fff; border:none; border-radius:8px; font-size:16px; cursor:pointer; font-family:'Cairo',sans-serif;">
        🖨️ طباعة الوصل
    </button>
    <button onclick="window.close()" style="padding:10px 20px; background:#999; color:#fff; border:none; border-radius:8px; font-size:16px; cursor:pointer; font-family:'Cairo',sans-serif; margin-right:10px;">
        إغلاق
    </button>
</div>
</body>
</html>
