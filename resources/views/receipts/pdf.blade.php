<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
<meta charset="UTF-8">
<style>
    body { font-family: 'DejaVu Sans', sans-serif; direction: rtl; font-size: 13px; }
    .header { text-align: center; border-bottom: 2px solid #2d5016; padding-bottom: 10px; margin-bottom: 10px; }
    .header h2 { color: #2d5016; font-size: 18px; margin: 0; }
    .rnum { background: #2d5016; color: #fff; text-align: center; padding: 5px; margin: 8px 0; }
    table { width: 100%; border-collapse: collapse; margin-bottom: 10px; }
    th { background: #e8f5d4; color: #2d5016; padding: 5px 8px; text-align: right; border: 1px solid #ccc; }
    td { padding: 5px 8px; border: 1px solid #eee; }
    .fee-box { background: #2d5016; color: #fff; text-align: center; padding: 10px; }
    .fee-box .amount { font-size: 22px; color: #f0d060; font-weight: bold; }
    .footer { text-align: center; font-size: 11px; color: #999; margin-top: 10px; }
</style>
</head>
<body>
<div class="header">
    <h2>{{ $pressName }}</h2>
    <p>إيصال استلام زيتون</p>
</div>
<div class="rnum">رقم الإيصال: {{ $receipt->receipt_number }} | التاريخ: {{ $receipt->created_at->format('Y/m/d') }}</div>

<table>
    <tr><th colspan="2">بيانات صاحب الزيتون</th></tr>
    <tr><td>الاسم</td><td>{{ $receipt->owner->name }}</td></tr>
    <tr><td>المدينة</td><td>{{ $receipt->owner->city }}</td></tr>
    <tr><td>الشارع</td><td>{{ $receipt->owner->street }}</td></tr>
    <tr><td>الهاتف</td><td>{{ $receipt->owner->phone }}</td></tr>
    <tr><td>رقم الدور</td><td>{{ $receipt->floor_number ?? '—' }}</td></tr>
</table>

<table>
    <tr><th colspan="2">بيانات الشحنة</th></tr>
    <tr><td>حامل الزيتون</td><td>{{ $receipt->carrier_name }}</td></tr>
    <tr><td>كمية الزيتون (كيلو)</td><td>{{ number_format($receipt->olive_weight_kg, 1) }}</td></tr>
    <tr><td>الأجرة / كيلو</td><td>{{ number_format($receipt->fee_per_kg, 2) }} د.أ</td></tr>
    @if($receipt->oil_weight_kg)
    <tr><td>كمية الزيت</td><td>{{ number_format($receipt->oil_weight_kg, 1) }} كيلو @if($receipt->oil_weight_gram) + {{ $receipt->oil_weight_gram }} غم @endif</td></tr>
    @endif
</table>

<div class="fee-box">
    <div>إجمالي الأجرة</div>
    <div class="amount">{{ number_format($receipt->total_fee, 2) }} دينار أردني</div>
</div>
<div class="footer">{{ $pressName }} | {{ $receipt->created_at->format('Y/m/d H:i') }}</div>
</body>
</html>
