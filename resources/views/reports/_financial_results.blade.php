{{-- Partial: summary cards + daily table — rendered via include OR AJAX --}}

{{-- بطاقات الملخص --}}
<div class="row g-3 mb-4">
    <div class="col-6 col-md-3">
        <div class="stat-card text-center" style="background:#f0fdf4;border:1px solid #86efac">
            <span class="stat-icon"><i class="fas fa-arrow-down text-success"></i></span>
            <div class="stat-value" style="color:#16a34a">{{ number_format($totals['total_in'], 2) }}</div>
            <div class="stat-label" style="color:#166534">إجمالي الدخل (د.أ)</div>
            <div class="small mt-1" style="color:#166534;font-size:.78rem">
                أجرة: {{ number_format($totals['cash_fees'],2) }}
                @if($totals['income'] > 0) | وارد: {{ number_format($totals['income'],2) }} @endif
                @if($totals['oil_sell'] > 0) | بيع زيت: {{ number_format($totals['oil_sell'],2) }} @endif
            </div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="stat-card text-center" style="background:#fef2f2;border:1px solid #fca5a5">
            <span class="stat-icon"><i class="fas fa-arrow-up text-danger"></i></span>
            <div class="stat-value" style="color:#dc2626">{{ number_format($totals['total_out'], 2) }}</div>
            <div class="stat-label" style="color:#7f1d1d">إجمالي الصرف (د.أ)</div>
            <div class="small mt-1" style="color:#7f1d1d;font-size:.78rem">
                صادر: {{ number_format($totals['expense'],2) }} |
                شراء زيت: {{ number_format($totals['oil_buy'],2) }}
            </div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="stat-card text-center" style="background:{{ $totals['net'] >= 0 ? '#f0fdf4;border:1px solid #86efac' : '#fef2f2;border:1px solid #fca5a5' }}">
            <span class="stat-icon"><i class="fas fa-balance-scale"></i></span>
            <div class="stat-value" style="color:{{ $totals['net'] >= 0 ? '#16a34a' : '#dc2626' }}">
                {{ number_format($totals['net'], 2) }}
            </div>
            <div class="stat-label" style="color:{{ $totals['net'] >= 0 ? '#166534' : '#7f1d1d' }}">صافي الفترة (د.أ)</div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="stat-card text-center" style="background:#fef2f2;border:1px solid #fca5a5">
            <span class="stat-icon"><i class="fas fa-exclamation-triangle text-danger"></i></span>
            <div class="stat-value" style="color:#b91c1c">{{ number_format($totals['debt_total'], 2) }}</div>
            <div class="stat-label" style="color:#7f1d1d">ديون ({{ $totals['debt_count'] }} إيصال)</div>
        </div>
    </div>
    @if($totals['paid_debt_total'] > 0)
    <div class="col-6 col-md-3">
        <div class="stat-card text-center" style="background:#f0fdf4;border:1px solid #86efac">
            <span class="stat-icon"><i class="fas fa-check-circle text-success"></i></span>
            <div class="stat-value" style="color:#16a34a">{{ number_format($totals['paid_debt_total'], 2) }}</div>
            <div class="stat-label" style="color:#166534">ديون مسددة ({{ $totals['paid_debt_count'] }} إيصال)</div>
            <div class="small mt-1" style="color:#166534;font-size:.72rem">مضمّنة في إجمالي الدخل</div>
        </div>
    </div>
    @endif
</div>

{{-- جدول التفاصيل اليومية --}}
<div class="row g-3 mb-4">
    <div class="col-md-6">
        <div class="stat-card text-center" style="background:#fef2f2;border:1px solid #fca5a5">
            <div class="stat-value text-danger">{{ number_format($totals['oil_buy_debt'], 3) }}</div>
            <div class="stat-label">ديون شراء الزيت علينا (د.أ) — عمليات الفترة غير المسددة</div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="stat-card text-center" style="background:#fffbeb;border:1px solid #fcd34d">
            <div class="stat-value" style="color:#92400e">{{ number_format($totals['oil_sell_debt'], 3) }}</div>
            <div class="stat-label">ديون بيع الزيت إلنا (د.أ) — عمليات الفترة غير المسددة</div>
        </div>
    </div>
</div>
<p class="small text-muted">مبالغ بيع وشراء الزيت في الدخل والصرف نقدية فقط؛ تسديد ديون الزيت يظهر بتاريخ التسديد.</p>
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <span><i class="fas fa-table me-2"></i>التفاصيل اليومية</span>
        <span class="text-muted small">{{ $fromDate }} — {{ $toDate }}</span>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0" style="font-size:.88rem">
                <thead style="background:var(--olive-pale)">
                    <tr>
                        <th rowspan="2" class="align-middle text-center" style="min-width:95px">التاريخ</th>
                        <th rowspan="2" class="align-middle text-center">إيصالات</th>
                        <th colspan="3" class="text-center text-success" style="background:#f0fdf4;border-bottom:2px solid #86efac">
                            <i class="fas fa-arrow-down me-1"></i>الدخل
                        </th>
                        <th rowspan="2" class="align-middle text-center text-success fw-bold" style="background:#dcfce7">مجموع الدخل</th>
                        <th colspan="2" class="text-center text-danger" style="background:#fef2f2;border-bottom:2px solid #fca5a5">
                            <i class="fas fa-arrow-up me-1"></i>الصرف
                        </th>
                        <th rowspan="2" class="align-middle text-center text-danger fw-bold" style="background:#fee2e2">مجموع الصرف</th>
                        <th rowspan="2" class="align-middle text-center fw-bold" style="background:#fffbeb;min-width:90px">الصافي</th>
                        <th rowspan="2" class="align-middle text-center text-danger small">ديون</th>
                        <th rowspan="2" class="align-middle text-center text-danger small">ديون شراء زيت علينا</th>
                        <th rowspan="2" class="align-middle text-center small">ديون بيع زيت إلنا</th>
                    </tr>
                    <tr>
                        <th class="text-center text-success small" style="background:#f0fdf4">أجرة نقدية</th>
                        <th class="text-center text-success small" style="background:#f0fdf4">وارد</th>
                        <th class="text-center text-success small" style="background:#f0fdf4">بيع زيت</th>
                        <th class="text-center text-danger small" style="background:#fef2f2">صادر</th>
                        <th class="text-center text-danger small" style="background:#fef2f2">شراء زيت</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($rows as $row)
                    <tr>
                        <td class="text-center fw-bold">{{ $row['date'] }}</td>
                        <td class="text-center"><span class="badge bg-secondary">{{ $row['receipt_count'] }}</span></td>
                        <td class="text-center" style="background:#f9fff5">
                            @if($row['cash_fees'] > 0)
                                <strong class="text-success">{{ number_format($row['cash_fees'], 2) }}</strong>
                                @if($row['paid_debt_total'] > 0)
                                    <div class="text-muted" style="font-size:.7rem">منها ديون: {{ number_format($row['paid_debt_total'],2) }}</div>
                                @endif
                            @else<span class="text-muted">—</span>@endif
                        </td>
                        <td class="text-center" style="background:#f9fff5">
                            @if($row['income'] > 0)<span class="text-success">{{ number_format($row['income'], 2) }}</span>
                            @else<span class="text-muted">—</span>@endif
                        </td>
                        <td class="text-center" style="background:#f9fff5">
                            @if($row['oil_sell'] > 0)<span class="text-success">{{ number_format($row['oil_sell'], 2) }}</span>
                            @else<span class="text-muted">—</span>@endif
                        </td>
                        <td class="text-center fw-bold" style="background:#dcfce7;color:#16a34a">
                            {{ number_format($row['total_in'], 2) }}
                        </td>
                        <td class="text-center" style="background:#fff9f9">
                            @if($row['expense'] > 0)<span class="text-danger">{{ number_format($row['expense'], 2) }}</span>
                            @else<span class="text-muted">—</span>@endif
                        </td>
                        <td class="text-center" style="background:#fff9f9">
                            @if($row['oil_buy'] > 0)<span class="text-danger">{{ number_format($row['oil_buy'], 2) }}</span>
                            @else<span class="text-muted">—</span>@endif
                        </td>
                        <td class="text-center fw-bold" style="background:#fee2e2;color:#dc2626">
                            {{ $row['total_out'] > 0 ? number_format($row['total_out'], 2) : '—' }}
                        </td>
                        <td class="text-center fw-bold" style="background:#fffbeb">
                            <span style="color:{{ $row['net'] >= 0 ? '#16a34a' : '#dc2626' }}">
                                {{ number_format($row['net'], 2) }}
                            </span>
                        </td>
                        <td class="text-center small">
                            @if($row['debt_count'] > 0)
                                <span class="text-danger">{{ number_format($row['debt_total'], 2) }}</span>
                                <div class="text-muted" style="font-size:.75rem">{{ $row['debt_count'] }} إيصال</div>
                            @else<span class="text-muted">—</span>@endif
                        </td>
                        <td class="text-center text-danger">{{ number_format($row['oil_buy_debt'], 3) }}</td>
                        <td class="text-center">{{ number_format($row['oil_sell_debt'], 3) }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="13" class="text-center py-5 text-muted">
                            <div class="fs-2 mb-2"><i class="fas fa-inbox"></i></div>
                            <p>لا توجد بيانات في هذه الفترة</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
                @if(count($rows) > 1)
                <tfoot style="background:var(--olive-dark);color:#fff;font-weight:700">
                    <tr>
                        <td class="text-center" colspan="2">الإجمالي</td>
                        <td class="text-center" style="color:#86efac">{{ number_format($totals['cash_fees'],2) }}</td>
                        <td class="text-center" style="color:#86efac">{{ number_format($totals['income'],2) }}</td>
                        <td class="text-center" style="color:#86efac">{{ number_format($totals['oil_sell'],2) }}</td>
                        <td class="text-center" style="color:#4ade80;font-size:1rem">{{ number_format($totals['total_in'],2) }}</td>
                        <td class="text-center" style="color:#fca5a5">{{ number_format($totals['expense'],2) }}</td>
                        <td class="text-center" style="color:#fca5a5">{{ number_format($totals['oil_buy'],2) }}</td>
                        <td class="text-center" style="color:#f87171;font-size:1rem">{{ number_format($totals['total_out'],2) }}</td>
                        <td class="text-center" style="color:{{ $totals['net'] >= 0 ? '#fde047' : '#f87171' }};font-size:1rem">
                            {{ number_format($totals['net'],2) }}
                        </td>
                        <td class="text-center" style="color:#fca5a5">{{ number_format($totals['debt_total'],2) }}</td>
                        <td class="text-center" style="color:#fca5a5">{{ number_format($totals['oil_buy_debt'],3) }}</td>
                        <td class="text-center">{{ number_format($totals['oil_sell_debt'],3) }}</td>
                    </tr>
                </tfoot>
                @endif
            </table>
        </div>
    </div>
</div>
