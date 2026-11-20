@extends('layouts.app')
@section('title', 'لوحة التحكم')
@section('page-title', 'لوحة التحكم')

@section('content')

<div class="small text-muted mb-3">بيانات يوم {{ \Carbon\Carbon::parse($day)->format('Y/m/d') }}</div>

<!-- إحصائيات سريعة — ملخص اليوم -->
<div class="row g-3 mb-4">
    <div class="col-6 col-md-4">
        <div class="stat-card bg-teal text-center">
            <span class="stat-icon"><i class="fas fa-file-alt"></i></span>
            <div class="stat-value">{{ $todayCount }}</div>
            <div class="stat-label">إيصالات اليوم</div>
        </div>
    </div>
    <div class="col-6 col-md-4">
        <div class="stat-card bg-olive text-center">
            <span class="stat-icon"><i class="fas fa-seedling"></i></span>
            <div class="stat-value">{{ number_format($todayOlive, 0) }}</div>
            <div class="stat-label">كيلو زيتون اليوم</div>
        </div>
    </div>
    <div class="col-6 col-md-4">
        <div class="stat-card bg-gold text-center">
            <span class="stat-icon"><i class="fas fa-flask"></i></span>
            <div class="stat-value">{{ number_format($todayOil, 0) }}</div>
            <div class="stat-label">كيلو زيت معصور اليوم</div>
        </div>
    </div>
    <div class="col-6 col-md-4">
        <div class="stat-card text-center" style="background:#fffbeb;border:1px solid #fcd34d;color:#78350f">
            <span class="stat-icon" style="color:#d97706"><i class="fas fa-recycle"></i></span>
            <div class="stat-value" style="color:#d97706">{{ number_format($todayOilReturnKg, 2) }}</div>
            <div class="stat-label" style="color:#92400e">رديات الزيت اليوم (كيلو)</div>
        </div>
    </div>
    <div class="col-6 col-md-4">
        <div class="stat-card text-center" style="background:#fee2e2;border:1px solid #fca5a5">
            <span class="stat-icon" style="color:#b91c1c"><i class="fas fa-exclamation-triangle"></i></span>
            @if($todayDebtTotal > 0)
            <div class="stat-value" style="color:#b91c1c">{{ number_format($todayDebtTotal, 2) }} <small style="font-size:.55em">د.أ</small></div>
            @endif
            @if($todayDebtOilKg > 0)
            <div style="font-size:1.1rem;font-weight:900;color:#d97706">{{ number_format($todayDebtOilKg, 2) }} <small style="font-size:.6em">كيلو زيت</small></div>
            @endif
            @if($todayDebtTotal == 0 && $todayDebtOilKg == 0)
            <div class="stat-value" style="color:#b91c1c">0</div>
            @endif
            <div class="stat-label" style="color:#7f1d1d">ديون اليوم — {{ $todayDebtCount }} إيصال</div>
        </div>
    </div>
    @if($todayDebtPaidTotal > 0)
    <div class="col-6 col-md-4">
        <div class="stat-card text-center" style="background:#dcfce7;border:1px solid #86efac">
            <span class="stat-icon" style="color:#16a34a"><i class="fas fa-check-circle"></i></span>
            <div class="stat-value" style="color:#16a34a">{{ number_format($todayDebtPaidTotal, 2) }}</div>
            <div class="stat-label" style="color:#166534">ديون مسددة اليوم — {{ $todayDebtPaidCount }} إيصال</div>
        </div>
    </div>
    @endif
    <div class="col-6 col-md-4">
        <div class="stat-card text-center" style="background:{{ $netOilKg >= 0 ? '#f0fdf4' : '#fff5f5' }};border:1px solid {{ $netOilKg >= 0 ? '#86efac' : '#fca5a5' }}">
            <span class="stat-icon" style="color:{{ $netOilKg >= 0 ? '#15803d' : '#b91c1c' }}"><i class="fas fa-bottle-droplet"></i></span>
            <div class="stat-value" style="color:{{ $netOilKg >= 0 ? '#15803d' : '#b91c1c' }}">{{ number_format($netOilKg, 2) }}</div>
            <div class="stat-label" style="color:{{ $netOilKg >= 0 ? '#166534' : '#7f1d1d' }}">🫙 صافي حركة الزيت اليوم (كيلو)</div>
            <div style="font-size:.65rem;color:#888;margin-top:2px">
                {{ number_format($oilReturnKg, 2) }} رديات
                @if($oilBuyKg > 0) + {{ number_format($oilBuyKg, 2) }} شراء @endif
                @if($oilSellKg > 0) − {{ number_format($oilSellKg, 2) }} مبيعات @endif
            </div>
        </div>
    </div>
</div>

{{-- صافي الأجور — تفصيل كامل --}}
<div class="row g-3 mb-4">
    <div class="col-md-6">
        <div class="stat-card text-center" style="background:#fef2f2;border:1px solid #fca5a5">
            <div class="stat-value text-danger">{{ number_format($todayOilBuyDebt, 3) }} <small>د.أ</small></div>
            <div class="stat-label">ديون شراء الزيت علينا — عمليات اليوم غير المسددة</div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="stat-card text-center" style="background:#fffbeb;border:1px solid #fcd34d">
            <div class="stat-value" style="color:#92400e">{{ number_format($todayOilSellDebt, 3) }} <small>د.أ</small></div>
            <div class="stat-label">ديون بيع الزيت إلنا — عمليات اليوم غير المسددة</div>
        </div>
    </div>
</div>
<div class="row g-3 mb-4">
    <div class="col-12">
        <div class="stat-card bg-slate" style="padding:14px 20px">
            <div class="d-flex align-items-center justify-content-between flex-wrap gap-3">
                <div style="font-size:.8rem;color:#94a3b8;font-weight:700;white-space:nowrap">
                    <i class="fas fa-coins me-1"></i> صافي الأجور النقدية اليوم
                </div>
                <div class="d-flex align-items-center gap-4 flex-wrap flex-grow-1 justify-content-center">
                    <div style="text-align:center">
                        <div style="font-size:.68rem;color:#86efac;margin-bottom:2px">+ أجرة نقدية</div>
                        <div style="font-size:1rem;font-weight:800;color:#fff">{{ number_format($todayFees,2) }}</div>
                    </div>
                    @if($todayOilBuyTotal > 0)
                    <div style="color:#94a3b8;font-size:1.1rem">−</div>
                    <div style="text-align:center">
                        <div style="font-size:.68rem;color:#fca5a5;margin-bottom:2px">شراء زيت</div>
                        <div style="font-size:1rem;font-weight:800;color:#fca5a5">{{ number_format($todayOilBuyTotal,2) }}</div>
                    </div>
                    @endif
                    @if($todayOilSellTotal > 0)
                    <div style="color:#94a3b8;font-size:1.1rem">+</div>
                    <div style="text-align:center">
                        <div style="font-size:.68rem;color:#86efac;margin-bottom:2px">بيع زيت</div>
                        <div style="font-size:1rem;font-weight:800;color:#86efac">{{ number_format($todayOilSellTotal,2) }}</div>
                    </div>
                    @endif
                    @if($todayExpenseTotal > 0)
                    <div style="color:#94a3b8;font-size:1.1rem">−</div>
                    <div style="text-align:center">
                        <div style="font-size:.68rem;color:#fca5a5;margin-bottom:2px">صادرات</div>
                        <div style="font-size:1rem;font-weight:800;color:#fca5a5">{{ number_format($todayExpenseTotal,2) }}</div>
                    </div>
                    @endif
                    @if($todayIncomeTotal > 0)
                    <div style="color:#94a3b8;font-size:1.1rem">+</div>
                    <div style="text-align:center">
                        <div style="font-size:.68rem;color:#86efac;margin-bottom:2px">واردات</div>
                        <div style="font-size:1rem;font-weight:800;color:#86efac">{{ number_format($todayIncomeTotal,2) }}</div>
                    </div>
                    @endif
                    <div style="width:1px;background:rgba(255,255,255,.15);align-self:stretch;min-height:36px"></div>
                    <div style="text-align:center">
                        <div style="font-size:.68rem;color:#f0d060;margin-bottom:2px">= الصافي</div>
                        <div style="font-size:1.4rem;font-weight:900;color:#f0d060;white-space:nowrap">
                            {{ number_format($todayNetFees,2) }}
                            <small style="font-size:.6rem;opacity:.75">د.أ</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- أزرار سريعة + حالة + ملخص اليوم -->
<div class="row g-3 mb-4">

    <!-- إجراءات سريعة -->
    <div class="col-md-4">
        <div class="card h-100">
            <div class="card-header">
                <i class="fas fa-bolt me-2"></i>إجراءات سريعة
            </div>
            <div class="card-body d-grid gap-2 py-3">
                <a href="{{ route('receipts.index') }}" class="btn btn-outline-secondary">
                    <i class="fas fa-file-alt me-2"></i>التقارير
                </a>
            </div>
        </div>
    </div>

    <!-- حالة الإيصالات -->
    <div class="col-md-4">
        <div class="card h-100">
            <div class="card-header">
                <i class="fas fa-chart-pie me-2"></i>حالة الإيصالات اليوم
            </div>
            <div class="card-body py-3">
                <div class="d-flex justify-content-between align-items-center py-2 border-bottom">
                    <span class="badge badge-pending px-3 py-2 rounded-pill fs-6">⏳ في الانتظار</span>
                    <strong class="fs-4 text-warning">{{ $todayPendingCount }}</strong>
                </div>
                <div class="d-flex justify-content-between align-items-center py-2 border-bottom">
                    <span class="badge badge-processing px-3 py-2 rounded-pill fs-6">⚙️ قيد المعالجة</span>
                    <strong class="fs-4 text-primary">{{ $todayProcessingCount }}</strong>
                </div>
                <div class="d-flex justify-content-between align-items-center py-2">
                    <span class="badge badge-completed px-3 py-2 rounded-pill fs-6">✅ مكتملة</span>
                    <strong class="fs-4 text-success">{{ $todayCompletedCount }}</strong>
                </div>
            </div>
        </div>
    </div>

    <!-- ملخص اليوم -->
    <div class="col-md-4">
        <div class="card h-100">
            <div class="card-header">
                <i class="fas fa-sun me-2"></i>ملخص اليوم
            </div>
            <div class="card-body py-3">
<div class="text-center py-2">
                    <div style="font-size:2.5rem">🫒</div>
                    <div class="fs-3 fw-bold mt-1" style="color:var(--olive-dark)">
                        {{ number_format($todayOlive, 1) }} <small class="fs-6">كيلو</small>
                    </div>
                    <div class="text-muted small mb-3">زيتون استُلم اليوم</div>
                    <hr>
                    <div class="fs-3 fw-bold" style="color:var(--gold)">
                        {{ number_format($todayNetFees, 2) }} <small class="fs-6">د.أ</small>
                    </div>
                    <div class="text-muted small">صافي الأجور النقدية اليوم</div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- آخر الإيصالات -->
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <span><i class="fas fa-list me-2"></i>إيصالات اليوم</span>
        <div class="d-flex gap-2">
            <a href="{{ route('receipts.print-table', ['from_date' => today()->toDateString(), 'to_date' => today()->toDateString()]) }}"
               target="_blank" class="btn btn-sm btn-outline-secondary">
                <i class="fas fa-print me-1"></i>طباعة
            </a>
            <a href="{{ route('receipts.index') }}" class="btn btn-sm btn-light">
                <i class="fas fa-arrow-left me-1"></i>عرض الكل
            </a>
        </div>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead>
                    <tr>
                        <th>رقم الإيصال</th>
                        <th>صاحب الزيتون</th>
                        <th>الكمية (كيلو)</th>
                        <th>كمية الزيت</th>
                        <th>نسبة ز/ز</th>
                        <th>الأجرة (نقدي)</th>
                        <th class="text-warning">🫙 رديات الزيت</th>
                        <th class="text-danger">⚠️ الديون</th>
                        <th>الحالة</th>
                        <th>التاريخ</th>
                        <th class="text-center">إجراء</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($recentReceipts as $receipt)
                    <tr>
                        <td><strong class="text-success">{{ $receipt->receipt_number }}</strong></td>
                        <td>{{ $receipt->owner->name }}</td>
                        <td><strong>{{ number_format($receipt->olive_weight_kg, 1) }}</strong></td>
                        <td>
                            @if($receipt->oil_weight_kg)
                                {{ number_format($receipt->oil_weight_kg, 1) }} كيلو
                            @else<span class="text-muted">—</span>@endif
                        </td>
                        <td style="color:#7c3aed;font-weight:700">
                            {{ $receipt->olive_oil_ratio ? number_format($receipt->olive_oil_ratio, 1) : '—' }}
                        </td>
                        {{-- الأجرة النقدية: فقط غير الديون وغير رديات الزيت --}}
                        <td>
                            @if($receipt->status === 'debt')
                                <span class="text-muted">—</span>
                            @elseif($receipt->payment_method === 'oil_return')
                                @if(!$receipt->cans_in_return && $receipt->discounted_can_total > 0)
                                    <strong>{{ number_format($receipt->discounted_can_total, 2) }}</strong> <small class="text-muted">د.أ (تنك بعد الخصم)</small>
                                @else
                                    <span class="text-muted">—</span>
                                @endif
                            @else
                                <strong>{{ number_format($receipt->total_fee, 2) }}</strong> <small class="text-muted">د.أ</small>
                            @endif
                        </td>
                        {{-- رديات الزيت: كل ما ليس ديناً معلقاً --}}
                        <td>
                            @if($receipt->payment_method === 'oil_return' && $receipt->status !== 'debt')
                                <strong class="text-warning">{{ number_format($receipt->oil_return_kg, 3) }}</strong> <small class="text-muted">كيلو</small>
                            @else
                                <span class="text-muted">—</span>
                            @endif
                        </td>
                        {{-- الديون: دنانير أو كيلو زيت --}}
                        <td>
                            @if($receipt->status === 'debt')
                                @if($receipt->payment_method === 'oil_return')
                                    <strong style="color:#d97706">{{ number_format($receipt->oil_return_kg, 2) }}</strong> <small class="text-muted">كيلو زيت</small>
                                @else
                                    <strong class="text-danger">{{ number_format($receipt->total_fee, 2) }}</strong> <small class="text-muted">د.أ</small>
                                @endif
                            @else
                                <span class="text-muted">—</span>
                            @endif
                        </td>
                        <td>
                            <span class="badge badge-{{ $receipt->status }} rounded-pill px-3 py-1">
                                {{ $receipt->status_label }}
                            </span>
                            @if($receipt->debt_paid_at)
                            <div class="small mt-1" style="color:#16a34a;font-weight:700;font-size:.72rem">
                                ✅ تم السداد
                            </div>
                            @endif
                        </td>
                        <td class="text-muted">{{ $receipt->created_at->format('Y/m/d') }}</td>
                        <td class="text-center">
                            <div class="btn-group btn-group-sm">
                                <a href="{{ route('receipts.print-row', $receipt) }}" target="_blank"
                                   class="btn btn-info text-white" title="طباعة">
                                    <i class="fas fa-print"></i>
                                </a>
                                @if($receipt->status === 'pending')
                                <button type="button" class="btn btn-danger"
                                        onclick="openDeleteModal({{ $receipt->id }}, '{{ $receipt->receipt_number }}')">
                                    <i class="fas fa-trash"></i>
                                </button>
                                @else
                                <button type="button" class="btn btn-secondary" disabled
                                        title="لا يمكن الحذف — مسموح فقط للانتظار" style="opacity:.4;cursor:not-allowed">
                                    <i class="fas fa-lock"></i>
                                </button>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="11" class="text-center py-5 text-muted">
                            <div class="fs-1 mb-2">📋</div>
                            <p class="mb-2">لا توجد إيصالات بعد</p>
                            <a href="{{ route('receipts.create') }}" class="btn btn-olive btn-sm">إضافة أول إيصال</a>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
                {{-- مجاميع الأعمدة --}}
                @if($recentReceipts->count() > 0)
                <tfoot class="table-light fw-bold">
                    <tr>
                        <td colspan="3" class="text-end text-muted small">مجموع اليوم:</td>
                        <td><span>{{ number_format($todayOil, 1) }}</span> <small class="text-muted">كيلو</small></td>
                        <td style="color:#7c3aed">
                            @if($todayOil > 0)
                                {{ number_format($todayOlive / $todayOil, 1) }}
                            @else —
                            @endif
                        </td>
                        <td><span class="text-dark">{{ number_format($tableCashTotal, 2) }}</span> <small class="text-muted">د.أ</small></td>
                        <td><span class="text-warning">{{ number_format($tableOilRetTotal, 3) }}</span> <small class="text-muted">كيلو</small></td>
                        <td>
                            @if($tableDebtTotal > 0)<span class="text-danger">{{ number_format($tableDebtTotal, 2) }}</span> <small class="text-muted">د.أ</small>@endif
                            @if($tableDebtOilKg > 0)<span style="color:#d97706;display:block">{{ number_format($tableDebtOilKg, 2) }}</span> <small class="text-muted">كيلو زيت</small>@endif
                            @if($tableDebtTotal == 0 && $tableDebtOilKg == 0)<span class="text-muted">—</span>@endif
                        </td>
                        <td colspan="3" class="text-center">
                            @if($todayDebtPaidTotal > 0)
                                <span class="text-success fw-bold">✅ {{ number_format($todayDebtPaidTotal, 2) }}</span>
                                <small class="text-muted d-block" style="font-weight:400">ديون مسددة ({{ $todayDebtPaidCount }})</small>
                            @endif
                        </td>
                    </tr>
                </tfoot>
                @endif
            </table>
        </div>
    </div>
</div>

{{-- جدول الديون المسددة اليوم (من أيام سابقة) --}}
@if($todayPaidDebts->count() > 0)
<div class="card mt-4" style="border:1px solid #86efac">
    <div class="card-header d-flex justify-content-between align-items-center" style="background:#dcfce7;color:#166534">
        <span><i class="fas fa-check-circle me-2"></i>ديون مسددة اليوم <span class="badge" style="background:#16a34a;color:#fff">{{ $todayPaidDebts->count() }}</span></span>
        <small class="text-muted">من إيصالات أيام سابقة — تم تسديدها اليوم</small>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead>
                    <tr>
                        <th>رقم الإيصال</th>
                        <th>صاحب الزيتون</th>
                        <th>تاريخ الدين</th>
                        <th class="text-success">المبلغ المسدد</th>
                        <th>إجراء</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($todayPaidDebts as $receipt)
                    <tr>
                        <td><strong class="text-success">{{ $receipt->receipt_number }}</strong></td>
                        <td>{{ $receipt->owner->name }}</td>
                        <td class="text-muted small">{{ $receipt->created_at->format('Y/m/d') }}</td>
                        <td><strong class="text-success">{{ number_format($receipt->total_fee, 2) }}</strong> <small class="text-muted">د.أ</small></td>
                        <td>
                            <a href="{{ route('receipts.print-row', $receipt) }}" target="_blank"
                               class="btn btn-sm btn-info text-white" title="طباعة">
                                <i class="fas fa-print"></i>
                            </a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
                <tfoot class="table-light fw-bold">
                    <tr>
                        <td colspan="3" class="text-end text-muted small">إجمالي المسدد اليوم:</td>
                        <td><span class="text-success">{{ number_format($todayDebtPaidTotal, 2) }}</span> <small class="text-muted">د.أ</small></td>
                        <td></td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
</div>
@endif

{{-- Modal حذف الإيصال بكلمة مرور --}}
<div class="modal fade" id="deleteModal" tabindex="-1">
    <div class="modal-dialog modal-sm">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white py-2">
                <h6 class="modal-title fw-bold mb-0"><i class="fas fa-trash me-2"></i>حذف الإيصال</h6>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form id="deleteForm" method="POST">
                @csrf
                <div class="modal-body">
                    <p class="text-center fw-bold mb-3" id="deleteReceiptNum" style="color:#dc2626"></p>
                    @if(session('delete_error'))
                    <div class="alert alert-danger py-2 text-center" style="font-size:.85rem">
                        {{ session('delete_error') }}
                    </div>
                    @endif
                    <label class="form-label fw-bold">كلمة مرور الحذف</label>
                    <input type="password" name="delete_password" class="form-control text-center fw-bold"
                           placeholder="••••••" autofocus required
                           id="deletePasswordInput">
                </div>
                <div class="modal-footer py-2">
                    <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">إلغاء</button>
                    <button type="submit" class="btn btn-danger btn-sm fw-bold">
                        <i class="fas fa-trash me-1"></i>تأكيد الحذف
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@section('scripts')
<script>
function openDeleteModal(id, num) {
    const url = '{{ route("cashier.delete", ":id") }}'.replace(':id', id);
    document.getElementById('deleteForm').action = url;
    document.getElementById('deleteReceiptNum').textContent = 'إيصال رقم: ' + num;
    document.getElementById('deletePasswordInput').value = '';
    new bootstrap.Modal(document.getElementById('deleteModal')).show();
}
@if(session('failed_receipt'))
openDeleteModal({{ session('failed_receipt') }}, '');
@endif
</script>
@endsection

@endsection
