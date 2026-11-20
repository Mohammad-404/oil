@extends('layouts.app')
@section('title', 'المصروفات')
@section('page-title', 'المصروفات — الواردات والصادرات')

@section('styles')
<style>
    .exp-card { border-radius:14px; overflow:hidden; box-shadow:0 2px 12px rgba(0,0,0,.07); }
    .exp-card.income  { border-top:4px solid #22c55e; }
    .exp-card.expense { border-top:4px solid #ef4444; }
    .exp-header-income  { background:#f0fdf4; color:#15803d; font-weight:800; padding:.85rem 1.2rem; }
    .exp-header-expense { background:#fef2f2; color:#991b1b; font-weight:800; padding:.85rem 1.2rem; }
    .stat-exp { border-radius:12px; padding:1rem; text-align:center; }
    .stat-exp .sv { font-size:1.6rem; font-weight:900; line-height:1.1; }
    .stat-exp .sl { font-size:.75rem; color:#666; margin-top:4px; }
</style>
@endsection

@section('content')

{{-- بطاقات الملخص --}}
<div class="row g-3 mb-3">
    {{-- صافي الأجور الشامل --}}
    <div class="col-12">
        <div class="p-3 rounded-3" style="background:linear-gradient(135deg,#f0fdf4,#dcfce7);border:1.5px solid #86efac">
            <div class="row g-2 align-items-center text-center">
                <div class="col-6 col-md-3">
                    <div style="font-size:.72rem;color:#166534;font-weight:700">💵 أجرة إيصالات</div>
                    <div style="font-size:1.25rem;font-weight:900;color:#15803d">{{ number_format($receiptCashFees, 3) }}</div>
                </div>
                @if($receiptDebtFees > 0)
                <div class="col-6 col-md-2">
                    <div style="font-size:.72rem;color:#166534;font-weight:700">+ ديون مسددة</div>
                    <div style="font-size:1.1rem;font-weight:800;color:#16a34a">{{ number_format($receiptDebtFees, 3) }}</div>
                </div>
                @endif
                <div class="col-6 col-md-2">
                    <div style="font-size:.72rem;color:#166534;font-weight:700">+ واردات</div>
                    <div style="font-size:1.1rem;font-weight:800;color:#16a34a">{{ number_format($totalIncome, 3) }}</div>
                </div>
                <div class="col-6 col-md-2">
                    <div style="font-size:.72rem;color:#991b1b;font-weight:700">− صادرات</div>
                    <div style="font-size:1.1rem;font-weight:800;color:#dc2626">{{ number_format($totalExpense, 3) }}</div>
                </div>
                <div class="col-12 col-md-3 border-top border-md-top-0 pt-2 pt-md-0">
                    <div style="font-size:.76rem;color:#166534;font-weight:700">= صافي الأجور النقدية</div>
                    <div style="font-size:1.6rem;font-weight:900;color:{{ $netReceiptFees >= 0 ? '#15803d' : '#dc2626' }}">
                        {{ number_format($netReceiptFees, 3) }}
                        <span style="font-size:.78rem">د.أ</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row g-3 mb-4">
    <div class="col-6 col-md-3">
        <div class="stat-exp" style="background:#f0fdf4;border:1.5px solid #86efac">
            <div class="sv" style="color:#15803d">{{ number_format($totalIncome, 3) }}</div>
            <div class="sl">📥 إجمالي الواردات (د.أ)</div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="stat-exp" style="background:#fef2f2;border:1.5px solid #fca5a5">
            <div class="sv" style="color:#dc2626">{{ number_format($totalExpense, 3) }}</div>
            <div class="sl">📤 إجمالي الصادرات (د.أ)</div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="stat-exp" style="background:#f8fafc;border:1.5px solid #cbd5e1">
            <div class="sv" style="color:{{ $netBalance >= 0 ? '#15803d' : '#dc2626' }}">
                {{ number_format($netBalance, 3) }}
            </div>
            <div class="sl">💰 صافي المصروفات (د.أ)</div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="stat-exp" style="background:#fffbeb;border:1.5px solid #fcd34d">
            <div class="sv" style="color:#d97706">{{ $incomes->total() + $expenses->total() }}</div>
            <div class="sl">📋 إجمالي السجلات</div>
        </div>
    </div>
</div>

{{-- فلتر البحث --}}
<div class="card mb-4">
    <div class="card-body py-3">
        <form method="GET" action="{{ route('cashier.expenses') }}" class="row g-2 align-items-end">
            <div class="col-12 col-md-4">
                <label class="form-label fw-bold" style="font-size:.82rem">🔍 بحث بالوصف</label>
                <input type="text" name="search" class="form-control"
                       placeholder="ابحث عن وصف..." value="{{ $search ?? '' }}">
            </div>
            <div class="col-6 col-md-3">
                <label class="form-label fw-bold" style="font-size:.82rem">📅 من تاريخ</label>
                <input type="date" name="from_date" class="form-control" value="{{ $fromDate ?? '' }}">
            </div>
            <div class="col-6 col-md-3">
                <label class="form-label fw-bold" style="font-size:.82rem">📅 إلى تاريخ</label>
                <input type="date" name="to_date" class="form-control" value="{{ $toDate ?? '' }}">
            </div>
            <div class="col-12 col-md-2 d-flex gap-2">
                <button type="submit" class="btn btn-olive fw-bold flex-grow-1">
                    <i class="fas fa-search me-1"></i>بحث
                </button>
                @if($isFiltered ?? false)
                <a href="{{ route('cashier.expenses') }}" class="btn btn-outline-secondary" title="إلغاء الفلتر">
                    <i class="fas fa-times"></i>
                </a>
                @endif
            </div>
        </form>
        @if($isFiltered ?? false)
        <div class="mt-2" style="font-size:.8rem;color:#6b7280">
            <i class="fas fa-filter me-1"></i>
            النتائج مفلترة
            @if($search) — الوصف: <strong>{{ $search }}</strong> @endif
            @if($fromDate) — من: <strong>{{ $fromDate }}</strong> @endif
            @if($toDate) — إلى: <strong>{{ $toDate }}</strong> @endif
            — الإجماليات أدناه تعكس الفلتر فقط
        </div>
        @endif
    </div>
</div>

{{-- فورمان الإدخال --}}
<div class="row g-3 mb-4">

    {{-- فورم الوارد --}}
    <div class="col-md-6">
        <div class="card exp-card income">
            <div class="exp-header-income">
                <i class="fas fa-arrow-down me-2"></i>تسجيل وارد
            </div>
            <div class="card-body">
                <form action="{{ route('cashier.expenses.store') }}" method="POST">
                    @csrf
                    <input type="hidden" name="type" value="income">
                    <div class="row g-2">
                        <div class="col-8">
                            <label class="form-label fw-bold" style="font-size:.83rem">الوصف</label>
                            <input type="text" name="description" class="form-control" placeholder="مثال: إيرادات إضافية" required>
                        </div>
                        <div class="col-4">
                            <label class="form-label fw-bold" style="font-size:.83rem">المبلغ (د.أ)</label>
                            <input type="number" name="amount" class="form-control" step="0.001" min="0.001" placeholder="0.000" id="incAmt" oninput="calcInc()" required>
                        </div>
                        <div class="col-12">
                            <div class="d-flex align-items-center justify-content-between p-2 rounded"
                                 style="background:#dcfce7;border:1px solid #86efac">
                                <span style="font-size:.83rem;font-weight:700;color:#15803d">الإجمالي:</span>
                                <span id="incTotal" style="font-size:1.1rem;font-weight:900;color:#15803d">0.000 د.أ</span>
                            </div>
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-bold" style="font-size:.83rem">ملاحظات (اختياري)</label>
                            <input type="text" name="notes" class="form-control form-control-sm" placeholder="تفاصيل إضافية...">
                        </div>
                        <div class="col-12">
                            <button type="submit" class="btn btn-success w-100 fw-bold">
                                <i class="fas fa-plus me-2"></i>تسجيل الوارد
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- فورم الصادر --}}
    <div class="col-md-6">
        <div class="card exp-card expense">
            <div class="exp-header-expense">
                <i class="fas fa-arrow-up me-2"></i>تسجيل صادر
            </div>
            <div class="card-body">
                <form action="{{ route('cashier.expenses.store') }}" method="POST">
                    @csrf
                    <input type="hidden" name="type" value="expense">
                    <div class="row g-2">
                        <div class="col-8">
                            <label class="form-label fw-bold" style="font-size:.83rem">الوصف</label>
                            <input type="text" name="description" class="form-control" placeholder="مثال: رواتب، صيانة..." required>
                        </div>
                        <div class="col-4">
                            <label class="form-label fw-bold" style="font-size:.83rem">المبلغ (د.أ)</label>
                            <input type="number" name="amount" class="form-control" step="0.001" min="0.001" placeholder="0.000" id="expAmt" oninput="calcExp()" required>
                        </div>
                        <div class="col-12">
                            <div class="d-flex align-items-center justify-content-between p-2 rounded"
                                 style="background:#fee2e2;border:1px solid #fca5a5">
                                <span style="font-size:.83rem;font-weight:700;color:#991b1b">الإجمالي:</span>
                                <span id="expTotal" style="font-size:1.1rem;font-weight:900;color:#dc2626">0.000 د.أ</span>
                            </div>
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-bold" style="font-size:.83rem">ملاحظات (اختياري)</label>
                            <input type="text" name="notes" class="form-control form-control-sm" placeholder="تفاصيل إضافية...">
                        </div>
                        <div class="col-12">
                            <button type="submit" class="btn btn-danger w-100 fw-bold">
                                <i class="fas fa-plus me-2"></i>تسجيل الصادر
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

{{-- الجدولان --}}
<div class="row g-3">

    {{-- جدول الواردات --}}
    <div class="col-md-6">
        <div class="card exp-card income">
            <div class="exp-header-income">
                <i class="fas fa-list me-2"></i>سجل الواردات
                <span class="badge bg-success ms-2">{{ number_format($totalIncome, 3) }} د.أ</span>
            </div>
            <div class="card-body p-0">
                <table class="table table-sm table-hover mb-0" style="font-size:.85rem">
                    <thead style="background:#f0fdf4">
                        <tr>
                            <th>التاريخ</th>
                            <th>الوصف</th>
                            <th>المبلغ</th>
                            <th>ملاحظات</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($incomes as $record)
                        <tr>
                            <td class="text-muted small">{{ $record->created_at->format('Y/m/d') }}</td>
                            <td><strong class="text-success">{{ $record->description }}</strong></td>
                            <td><strong>{{ number_format($record->amount, 3) }} د.أ</strong></td>
                            <td class="text-muted small">{{ $record->notes ?: '—' }}</td>
                            <td>
                                <button type="button" class="btn btn-xs btn-outline-danger"
                                        style="font-size:.7rem;padding:.15rem .45rem"
                                        onclick="openExpenseDeleteModal({{ $record->id }}, 'وارد')">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="text-center text-muted py-4">لا توجد واردات</td>
                        </tr>
                        @endforelse
                    </tbody>
                    @if($incomes->count() > 0)
                    <tfoot style="background:#f0fdf4;font-weight:800">
                        <tr>
                            <td colspan="2" class="text-end">المجموع:</td>
                            <td class="text-success">{{ number_format($totalIncome, 3) }} د.أ</td>
                            <td colspan="2"></td>
                        </tr>
                    </tfoot>
                    @endif
                </table>
            </div>
            @if($incomes->hasPages())
            <div class="card-footer py-2">{{ $incomes->links() }}</div>
            @endif
        </div>
    </div>

    {{-- جدول الصادرات --}}
    <div class="col-md-6">
        <div class="card exp-card expense">
            <div class="exp-header-expense">
                <i class="fas fa-list me-2"></i>سجل الصادرات
                <span class="badge bg-danger ms-2">{{ number_format($totalExpense, 3) }} د.أ</span>
            </div>
            <div class="card-body p-0">
                <table class="table table-sm table-hover mb-0" style="font-size:.85rem">
                    <thead style="background:#fef2f2">
                        <tr>
                            <th>التاريخ</th>
                            <th>الوصف</th>
                            <th>المبلغ</th>
                            <th>ملاحظات</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($expenses as $record)
                        <tr>
                            <td class="text-muted small">{{ $record->created_at->format('Y/m/d') }}</td>
                            <td><strong class="text-danger">{{ $record->description }}</strong></td>
                            <td><strong>{{ number_format($record->amount, 3) }} د.أ</strong></td>
                            <td class="text-muted small">{{ $record->notes ?: '—' }}</td>
                            <td>
                                <button type="button" class="btn btn-xs btn-outline-danger"
                                        style="font-size:.7rem;padding:.15rem .45rem"
                                        onclick="openExpenseDeleteModal({{ $record->id }}, 'صادر')">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="text-center text-muted py-4">لا توجد صادرات</td>
                        </tr>
                        @endforelse
                    </tbody>
                    @if($expenses->count() > 0)
                    <tfoot style="background:#fef2f2;font-weight:800">
                        <tr>
                            <td colspan="2" class="text-end">المجموع:</td>
                            <td class="text-danger">{{ number_format($totalExpense, 3) }} د.أ</td>
                            <td colspan="2"></td>
                        </tr>
                    </tfoot>
                    @endif
                </table>
            </div>
            @if($expenses->hasPages())
            <div class="card-footer py-2">{{ $expenses->links() }}</div>
            @endif
        </div>
    </div>

</div>

{{-- مودال الحذف --}}
<div class="modal fade" id="expenseDeleteModal" tabindex="-1">
    <div class="modal-dialog modal-sm">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white py-2">
                <h6 class="modal-title fw-bold mb-0"><i class="fas fa-trash me-2"></i>حذف السجل</h6>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form id="expenseDeleteForm" method="POST">
                @csrf @method('DELETE')
                <div class="modal-body">
                    <p class="text-center fw-bold mb-3" id="expenseDeleteLabel" style="color:#dc2626;font-size:.9rem"></p>
                    @if(session('expense_delete_error'))
                    <div class="alert alert-danger py-2 text-center" style="font-size:.85rem">
                        {{ session('expense_delete_error') }}
                    </div>
                    @endif
                    <label class="form-label fw-bold">كلمة مرور الحذف</label>
                    <input type="password" name="delete_password" class="form-control text-center fw-bold"
                           placeholder="••••••" required id="expenseDeletePasswordInput">
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

@endsection

@section('scripts')
<script>
function calcInc() {
    const v = parseFloat(document.getElementById('incAmt').value) || 0;
    document.getElementById('incTotal').textContent = v.toFixed(3) + ' د.أ';
}
function calcExp() {
    const v = parseFloat(document.getElementById('expAmt').value) || 0;
    document.getElementById('expTotal').textContent = v.toFixed(3) + ' د.أ';
}
function openExpenseDeleteModal(id, type) {
    const url = '{{ route("cashier.expenses.destroy", ":id") }}'.replace(':id', id);
    document.getElementById('expenseDeleteForm').action = url;
    document.getElementById('expenseDeleteLabel').textContent = 'حذف سجل ' + type;
    document.getElementById('expenseDeletePasswordInput').value = '';
    new bootstrap.Modal(document.getElementById('expenseDeleteModal')).show();
}
@if(session('failed_expense'))
openExpenseDeleteModal({{ session('failed_expense') }}, '');
@endif
</script>
@endsection
