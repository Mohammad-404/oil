@extends('layouts.app')
@section('title', 'بيع وشراء الزيت')
@section('page-title', 'بيع وشراء الزيت')

@section('styles')
<style>
    .trade-card {
        border-radius: 14px; overflow: hidden;
        box-shadow: 0 2px 12px rgba(0,0,0,.07);
    }
    .trade-card.buy  { border-top: 4px solid #22c55e; }
    .trade-card.sell { border-top: 4px solid #f59e0b; }
    .trade-header-buy  { background:#f0fdf4; color:#15803d; font-weight:800; padding:.85rem 1.2rem; }
    .trade-header-sell { background:#fffbeb; color:#92400e; font-weight:800; padding:.85rem 1.2rem; }

    .stat-trade { border-radius:12px; padding:1rem; text-align:center; }
    .stat-trade .sv { font-size:1.6rem; font-weight:900; line-height:1.1; }
    .stat-trade .sl { font-size:.75rem; color:#666; margin-top:4px; }
</style>
@endsection

@section('content')

@if($errors->any())
<div class="alert alert-danger">{{ $errors->first() }}</div>
@endif

{{-- ── بطاقات الملخص ── --}}
<div class="row g-3 mb-4">
    <div class="col-6 col-md-3">
        <div class="stat-trade" style="background:#f0fdf4;border:1.5px solid #86efac">
            <div class="sv" style="color:#15803d">{{ number_format($oilStock, 3) }}</div>
            <div class="sl">🫙 مخزون الزيت الكلي (كيلو)</div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="stat-trade" style="background:#f0fdf4;border:1.5px solid #86efac">
            <div class="sv" style="color:#15803d">{{ number_format($totalBuyKg, 3) }}</div>
            <div class="sl">📥 إجمالي الزيت المشترى (كيلو)</div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="stat-trade" style="background:#fffbeb;border:1.5px solid #fcd34d">
            <div class="sv" style="color:#d97706">{{ number_format($totalSellKg, 3) }}</div>
            <div class="sl">📤 إجمالي الزيت المباع (كيلو)</div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="stat-trade" style="background:#f8fafc;border:1.5px solid #cbd5e1">
            <div class="sv" style="color:#334155">
                {{ number_format($totalSellPaid - $totalBuyPaid, 3) }}
            </div>
            <div class="sl">💰 صافي التداول النقدي (د.أ)</div>
            <div class="small text-muted mt-1">مقبوض: {{ number_format($totalSellPaid, 3) }} — مدفوع: {{ number_format($totalBuyPaid, 3) }}</div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="stat-trade" style="background:#fef2f2;border:1.5px solid #fca5a5">
            <div class="sv text-danger">{{ number_format($totalBuyDebt, 3) }}</div>
            <div class="sl">ديون شراء الزيت علينا للمورّدين (د.أ)</div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="stat-trade" style="background:#fffbeb;border:1.5px solid #fcd34d">
            <div class="sv" style="color:#92400e">{{ number_format($totalSellDebt, 3) }}</div>
            <div class="sl">ديون بيع الزيت إلنا عند الزبائن (د.أ)</div>
        </div>
    </div>
</div>
<p class="small text-muted">المجاميع حسب السجلات المعروضة. الشراء يزيد المخزون والبيع ينقصه، سواء نقدًا أو بالدين. الدين يدخل في النقد عند تسديده.</p>

{{-- ── الفورمان ── --}}
<div class="row g-3 mb-4">

    {{-- فورم الشراء --}}
    <div class="col-md-6">
        <div class="card trade-card buy">
            <div class="trade-header-buy">
                <i class="fas fa-cart-plus me-2"></i>شراء زيت
            </div>
            <div class="card-body">
                <form action="{{ route('cashier.oil-trade.store') }}" method="POST">
                    @csrf
                    <input type="hidden" name="type" value="buy">
                    <div class="row g-2">
                        <div class="col-6">
                            <label class="form-label fw-bold" style="font-size:.83rem">الكمية (كيلو)</label>
                            <input type="number" name="quantity_kg" class="form-control"
                                   step="0.001" min="0.001" placeholder="0.000"
                                   id="buyQty" value="{{ old('type') === 'buy' ? old('quantity_kg') : '' }}" required>
                        </div>
                        <div class="col-6">
                            <label class="form-label fw-bold" style="font-size:.83rem">السعر الإجمالي (د.أ)</label>
                            <input type="number" name="total_amount" class="form-control"
                                   step="0.001" min="0" placeholder="0.000"
                                   id="buyAmount" value="{{ old('type') === 'buy' ? old('total_amount') : '' }}" oninput="calcBuy()" required>
                        </div>
                        <div class="col-6">
                            <label for="buyPaymentStatus" class="form-label fw-bold" style="font-size:.83rem">الدفع</label>
                            <select name="payment_status" id="buyPaymentStatus" class="form-select">
                                <option value="paid">نقدي — مدفوع</option>
                                <option value="debt" {{ old('type') === 'buy' && old('payment_status') === 'debt' ? 'selected' : '' }}>دين علينا للمورّد</option>
                            </select>
                        </div>
                        <div class="col-6">
                            <label for="buyCounterparty" class="form-label fw-bold" style="font-size:.83rem">اسم المورّد (مطلوب للدين)</label>
                            <input type="text" name="counterparty_name" id="buyCounterparty" class="form-control" maxlength="150"
                                   value="{{ old('type') === 'buy' ? old('counterparty_name') : '' }}">
                        </div>
                        <div class="col-12">
                            <div class="d-flex align-items-center justify-content-between p-2 rounded"
                                 style="background:#dcfce7;border:1px solid #86efac">
                                <span style="font-size:.83rem;font-weight:700;color:#15803d">المبلغ المدخل:</span>
                                <span id="buyTotal" style="font-size:1.1rem;font-weight:900;color:#15803d">0.000 د.أ</span>
                            </div>
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-bold" style="font-size:.83rem">ملاحظات (اختياري)</label>
                            <input type="text" name="notes" class="form-control form-control-sm"
                                   placeholder="مثال: شراء من المورد X">
                        </div>
                        <div class="col-12">
                            <button type="submit" class="btn btn-success w-100 fw-bold">
                                <i class="fas fa-plus me-2"></i>تسجيل الشراء
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- فورم البيع --}}
    <div class="col-md-6">
        <div class="card trade-card sell">
            <div class="trade-header-sell">
                <i class="fas fa-hand-holding-dollar me-2"></i>بيع زيت
            </div>
            <div class="card-body">
                <form action="{{ route('cashier.oil-trade.store') }}" method="POST">
                    @csrf
                    <input type="hidden" name="type" value="sell">
                    <div class="row g-2">
                        <div class="col-6">
                            <label class="form-label fw-bold" style="font-size:.83rem">الكمية (كيلو)</label>
                            <input type="number" name="quantity_kg" class="form-control"
                                   step="0.001" min="0.001" placeholder="0.000"
                                   id="sellQty" value="{{ old('type') === 'sell' ? old('quantity_kg') : '' }}" required>
                        </div>
                        <div class="col-6">
                            <label class="form-label fw-bold" style="font-size:.83rem">السعر الإجمالي (د.أ)</label>
                            <input type="number" name="total_amount" class="form-control"
                                   step="0.001" min="0" placeholder="0.000"
                                   id="sellAmount" value="{{ old('type') === 'sell' ? old('total_amount') : '' }}" oninput="calcSell()" required>
                        </div>
                        <div class="col-6">
                            <label for="sellPaymentStatus" class="form-label fw-bold" style="font-size:.83rem">الدفع</label>
                            <select name="payment_status" id="sellPaymentStatus" class="form-select">
                                <option value="paid">نقدي — مقبوض</option>
                                <option value="debt" {{ old('type') === 'sell' && old('payment_status') === 'debt' ? 'selected' : '' }}>دين إلنا عند الزبون</option>
                            </select>
                        </div>
                        <div class="col-6">
                            <label for="sellCounterparty" class="form-label fw-bold" style="font-size:.83rem">اسم الزبون (مطلوب للدين)</label>
                            <input type="text" name="counterparty_name" id="sellCounterparty" class="form-control" maxlength="150"
                                   value="{{ old('type') === 'sell' ? old('counterparty_name') : '' }}">
                        </div>
                        <div class="col-12">
                            <div class="d-flex align-items-center justify-content-between p-2 rounded"
                                 style="background:#fef3c7;border:1px solid #fcd34d">
                                <span style="font-size:.83rem;font-weight:700;color:#92400e">المبلغ المدخل:</span>
                                <span id="sellTotal" style="font-size:1.1rem;font-weight:900;color:#92400e">0.000 د.أ</span>
                            </div>
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-bold" style="font-size:.83rem">ملاحظات (اختياري)</label>
                            <input type="text" name="notes" class="form-control form-control-sm"
                                   placeholder="مثال: بيع لعميل X">
                        </div>
                        <div class="col-12">
                            <button type="submit" class="btn btn-warning w-100 fw-bold text-dark">
                                <i class="fas fa-plus me-2"></i>تسجيل البيع
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

</div>

{{-- ── فلتر البحث ── --}}
<div class="card mb-3">
    <div class="card-body py-3">
        <form method="GET" action="{{ route('cashier.oil-trade') }}" class="row g-2 align-items-end">
            <div class="col-md-3">
                <label class="form-label small fw-bold mb-1">بحث بالاسم أو الملاحظات</label>
                <input type="text" name="search" class="form-control form-control-sm"
                       placeholder="اسم المورّد / الزبون أو الملاحظات..." value="{{ $search }}">
            </div>
            <div class="col-md-2">
                <label class="form-label small fw-bold mb-1">من تاريخ</label>
                <input type="date" name="from_date" class="form-control form-control-sm" value="{{ $fromDate }}">
            </div>
            <div class="col-md-2">
                <label class="form-label small fw-bold mb-1">إلى تاريخ</label>
                <input type="date" name="to_date" class="form-control form-control-sm" value="{{ $toDate }}">
            </div>
            <div class="col-md-3">
                <label for="tradePaymentFilter" class="form-label small fw-bold mb-1">حالة الدفع</label>
                <select name="payment_status" id="tradePaymentFilter" class="form-select form-select-sm">
                    <option value="">الكل</option>
                    <option value="debt" {{ $paymentStatus === 'debt' ? 'selected' : '' }}>ديون غير مسددة</option>
                    <option value="paid" {{ $paymentStatus === 'paid' ? 'selected' : '' }}>مدفوع / مقبوض</option>
                </select>
            </div>
            <div class="col-md-2 d-flex gap-1">
                <button type="submit" class="btn btn-olive btn-sm flex-fill">
                    <i class="fas fa-search me-1"></i>بحث
                </button>
                @if($isFiltered)
                <a href="{{ route('cashier.oil-trade') }}" class="btn btn-outline-secondary btn-sm" title="إلغاء الفلتر">✕</a>
                @endif
            </div>
        </form>
        @if($isFiltered)
        <div class="mt-2 small text-muted">
            <i class="fas fa-filter me-1"></i>
            الأرقام تعكس الفلتر المطبَّق فقط
            @if($search) — بحث: <strong>{{ $search }}</strong> @endif
            @if($fromDate) — من: <strong>{{ $fromDate }}</strong> @endif
            @if($toDate)   — إلى: <strong>{{ $toDate }}</strong> @endif
        </div>
        @endif
    </div>
</div>

{{-- ── الجدولان ── --}}
<div class="row g-3">

    {{-- جدول الشراء --}}
    <div class="col-md-6">
        <div class="card trade-card buy">
            <div class="trade-header-buy">
                <i class="fas fa-list me-2"></i>سجل الشراء
                <span class="badge bg-success ms-2">{{ $totalBuyKg > 0 ? number_format($totalBuyKg,3).' كيلو' : '0' }}</span>
            </div>
            <div class="card-body p-0 table-responsive">
                <table class="table table-sm table-hover mb-0" style="font-size:.85rem">
                    <thead style="background:#f0fdf4">
                        <tr>
                            <th>التاريخ</th>
                            <th>الكمية (كيلو)</th>
                            <th>متوسط السعر/كيلو</th>
                            <th>الإجمالي</th>
                            <th>الطرف / الدفع</th>
                            <th>ملاحظات</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($buys as $trade)
                        <tr>
                            <td class="text-muted small">{{ $trade->created_at->format('Y/m/d') }}</td>
                            <td><strong class="text-success">{{ number_format($trade->quantity_kg,3) }}</strong></td>
                            <td>{{ number_format($trade->price_per_kg,3) }}</td>
                            <td><strong>{{ number_format($trade->total_amount,3) }} د.أ</strong></td>
                            <td>@include('cashier._oil_trade_payment')</td>
                            <td class="text-muted small">{{ $trade->notes ?: '—' }}</td>
                            <td>
                                <button type="button" class="btn btn-xs btn-outline-danger" style="font-size:.7rem;padding:.15rem .45rem"
                                        onclick="openTradeDeleteModal({{ $trade->id }}, 'شراء')">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="text-center text-muted py-4">لا توجد عمليات شراء</td>
                        </tr>
                        @endforelse
                    </tbody>
                    @if($buys->count() > 0)
                    <tfoot style="background:#f0fdf4;font-weight:800">
                        <tr>
                            <td colspan="3" class="text-end">المجموع:</td>
                            <td class="text-success">{{ number_format($totalBuyVal, 3) }} د.أ</td>
                            <td colspan="3"></td>
                        </tr>
                    </tfoot>
                    @endif
                </table>
            </div>
            @if($buys->hasPages())
            <div class="card-footer py-2">{{ $buys->links() }}</div>
            @endif
        </div>
    </div>

    {{-- جدول البيع --}}
    <div class="col-md-6">
        <div class="card trade-card sell">
            <div class="trade-header-sell">
                <i class="fas fa-list me-2"></i>سجل البيع
                <span class="badge bg-warning text-dark ms-2">{{ $totalSellKg > 0 ? number_format($totalSellKg,3).' كيلو' : '0' }}</span>
            </div>
            <div class="card-body p-0 table-responsive">
                <table class="table table-sm table-hover mb-0" style="font-size:.85rem">
                    <thead style="background:#fffbeb">
                        <tr>
                            <th>التاريخ</th>
                            <th>الكمية (كيلو)</th>
                            <th>متوسط السعر/كيلو</th>
                            <th>الإجمالي</th>
                            <th>الطرف / الدفع</th>
                            <th>ملاحظات</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($sells as $trade)
                        <tr>
                            <td class="text-muted small">{{ $trade->created_at->format('Y/m/d') }}</td>
                            <td><strong style="color:#d97706">{{ number_format($trade->quantity_kg,3) }}</strong></td>
                            <td>{{ number_format($trade->price_per_kg,3) }}</td>
                            <td><strong>{{ number_format($trade->total_amount,3) }} د.أ</strong></td>
                            <td>@include('cashier._oil_trade_payment')</td>
                            <td class="text-muted small">{{ $trade->notes ?: '—' }}</td>
                            <td>
                                <button type="button" class="btn btn-xs btn-outline-danger" style="font-size:.7rem;padding:.15rem .45rem"
                                        onclick="openTradeDeleteModal({{ $trade->id }}, 'بيع')">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="text-center text-muted py-4">لا توجد عمليات بيع</td>
                        </tr>
                        @endforelse
                    </tbody>
                    @if($sells->count() > 0)
                    <tfoot style="background:#fffbeb;font-weight:800">
                        <tr>
                            <td colspan="3" class="text-end">المجموع:</td>
                            <td style="color:#d97706">{{ number_format($totalSellVal, 3) }} د.أ</td>
                            <td colspan="3"></td>
                        </tr>
                    </tfoot>
                    @endif
                </table>
            </div>
            @if($sells->hasPages())
            <div class="card-footer py-2">{{ $sells->links() }}</div>
            @endif
        </div>
    </div>

</div>

{{-- مودال حذف سجل التداول --}}
<div class="modal fade" id="tradeDeleteModal" tabindex="-1">
    <div class="modal-dialog modal-sm">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white py-2">
                <h6 class="modal-title fw-bold mb-0"><i class="fas fa-trash me-2"></i>حذف السجل</h6>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form id="tradeDeleteForm" method="POST">
                @csrf @method('DELETE')
                <div class="modal-body">
                    <p class="text-center fw-bold mb-3" id="tradeDeleteLabel" style="color:#dc2626;font-size:.9rem"></p>
                    @if(session('trade_delete_error'))
                    <div class="alert alert-danger py-2 text-center" style="font-size:.85rem">
                        {{ session('trade_delete_error') }}
                    </div>
                    @endif
                    <label class="form-label fw-bold">كلمة مرور الحذف</label>
                    <input type="password" name="delete_password" class="form-control text-center fw-bold"
                           placeholder="••••••" required id="tradeDeletePasswordInput">
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
function calcBuy() {
    const p = parseFloat(document.getElementById('buyAmount').value) || 0;
    document.getElementById('buyTotal').textContent = p.toFixed(3) + ' د.أ';
}
function calcSell() {
    const p = parseFloat(document.getElementById('sellAmount').value) || 0;
    document.getElementById('sellTotal').textContent = p.toFixed(3) + ' د.أ';
}
calcBuy();
calcSell();
function openTradeDeleteModal(id, type) {
    const url = '{{ route("cashier.oil-trade.destroy", ":id") }}'.replace(':id', id);
    document.getElementById('tradeDeleteForm').action = url;
    document.getElementById('tradeDeleteLabel').textContent = 'حذف سجل ' + type;
    document.getElementById('tradeDeletePasswordInput').value = '';
    new bootstrap.Modal(document.getElementById('tradeDeleteModal')).show();
}
@if(session('failed_trade'))
openTradeDeleteModal({{ session('failed_trade') }}, '');
@endif
</script>
@endsection
