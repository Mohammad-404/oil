{{-- Partial: summary + status + table — rendered via include OR AJAX --}}

@if($summary)
@if($summary['isFiltered'])
<div class="alert alert-warning py-2 px-3 mb-3" style="font-size:.85rem;border-radius:8px">
    <i class="fas fa-filter me-1"></i> الأرقام أدناه تعكس نتائج الفلتر المطبّق فقط
    — <a href="{{ route('receipts.index') }}" class="alert-link" data-clear-filter>عرض الكل</a>
</div>
@endif

{{-- بطاقات الإحصاء --}}
<div class="row g-3 mb-4 justify-content-center">
    <div class="col-6 col-md-4">
        <div class="stat-card bg-teal text-center">
            <span class="stat-icon"><i class="fas fa-file-alt"></i></span>
            <div class="stat-value">{{ number_format($summary['totalReceipts']) }}</div>
            <div class="stat-label">{{ $summary['isFiltered'] ? 'إيصالات الفلتر' : 'إجمالي الإيصالات' }}</div>
        </div>
    </div>
    <div class="col-6 col-md-4">
        <div class="stat-card bg-olive text-center">
            <span class="stat-icon"><i class="fas fa-seedling"></i></span>
            <div class="stat-value">{{ number_format($summary['totalOlive'], 0) }}</div>
            <div class="stat-label">كيلو زيتون</div>
        </div>
    </div>
    <div class="col-6 col-md-4">
        <div class="stat-card bg-gold text-center">
            <span class="stat-icon"><i class="fas fa-flask"></i></span>
            <div class="stat-value">{{ number_format($summary['totalOil'], 0) }}</div>
            <div class="stat-label">كيلو زيت معصور</div>
        </div>
    </div>
    <div class="col-6 col-md-4">
        <div class="stat-card text-center" style="background:#fffbeb;border:1px solid #fcd34d;color:#78350f">
            <span class="stat-icon" style="color:#d97706"><i class="fas fa-recycle"></i></span>
            <div class="stat-value" style="color:#d97706">{{ number_format($summary['oilReturnKg'], 2) }}</div>
            <div class="stat-label" style="color:#92400e">رديات الزيت (كيلو)</div>
        </div>
    </div>
    <div class="col-6 col-md-4">
        <div class="stat-card text-center" style="background:#fee2e2;border:1px solid #fca5a5">
            <span class="stat-icon" style="color:#b91c1c"><i class="fas fa-exclamation-triangle"></i></span>
            <div class="stat-value" style="color:#b91c1c">{{ number_format($summary['debtTotal'], 0) }}</div>
            <div class="stat-label" style="color:#7f1d1d">ديون (د.أ) — {{ $summary['debtCount'] }} إيصال</div>
        </div>
    </div>
    @if($summary['debtPaidTotal'] > 0)
    <div class="col-6 col-md-4">
        <div class="stat-card text-center" style="background:#dcfce7;border:1px solid #86efac">
            <span class="stat-icon" style="color:#16a34a"><i class="fas fa-check-circle"></i></span>
            <div class="stat-value" style="color:#16a34a">{{ number_format($summary['debtPaidTotal'], 2) }}</div>
            <div class="stat-label" style="color:#166534">ديون مسددة (د.أ) — {{ $summary['debtPaidCount'] }} إيصال</div>
        </div>
    </div>
    @endif
    <div class="col-6 col-md-4">
        @php $netOil = $summary['oilStock']; $buyKg = $summary['oilBuyKg']; $sellKg = $summary['oilSellKg']; @endphp
        <div class="stat-card text-center" style="background:{{ $netOil >= 0 ? '#f0fdf4' : '#fff5f5' }};border:1px solid {{ $netOil >= 0 ? '#86efac' : '#fca5a5' }}">
            <span class="stat-icon" style="color:{{ $netOil >= 0 ? '#15803d' : '#b91c1c' }}"><i class="fas fa-bottle-droplet"></i></span>
            <div class="stat-value" style="color:{{ $netOil >= 0 ? '#15803d' : '#b91c1c' }}">{{ number_format($netOil, 2) }}</div>
            <div class="stat-label" style="color:{{ $netOil >= 0 ? '#166534' : '#7f1d1d' }}">🫙 صافي مخزون الزيت (كيلو)</div>
            <div style="font-size:.65rem;color:#888;margin-top:2px">
                {{ number_format($summary['oilReturnKg'], 2) }} رديات
                @if($buyKg > 0) + {{ number_format($buyKg, 2) }} شراء @endif
                @if($sellKg > 0) − {{ number_format($sellKg, 2) }} مبيعات @endif
            </div>
        </div>
    </div>
</div>

{{-- صافي الأجور --}}
<div class="row g-3 mb-3">
    <div class="col-12">
        <div class="stat-card bg-slate" style="padding:14px 20px">
            <div class="d-flex align-items-center justify-content-between flex-wrap gap-3">
                <div style="font-size:.8rem;color:#94a3b8;font-weight:700;white-space:nowrap">
                    <i class="fas fa-coins me-1"></i> صافي الأجور النقدية
                </div>
                <div class="d-flex align-items-center gap-4 flex-wrap flex-grow-1 justify-content-center">
                    <div style="text-align:center">
                        <div style="font-size:.68rem;color:#86efac;margin-bottom:2px">+ أجرة نقدية</div>
                        <div style="font-size:1rem;font-weight:800;color:#fff">{{ number_format($summary['cashFees'],2) }}</div>
                    </div>
                    @if($summary['oilBuyTotal'] > 0)
                    <div style="color:#94a3b8;font-size:1.1rem">−</div>
                    <div style="text-align:center">
                        <div style="font-size:.68rem;color:#fca5a5;margin-bottom:2px">شراء زيت</div>
                        <div style="font-size:1rem;font-weight:800;color:#fca5a5">{{ number_format($summary['oilBuyTotal'],2) }}</div>
                    </div>
                    @endif
                    @if($summary['oilSellTotal'] > 0)
                    <div style="color:#94a3b8;font-size:1.1rem">+</div>
                    <div style="text-align:center">
                        <div style="font-size:.68rem;color:#86efac;margin-bottom:2px">بيع زيت</div>
                        <div style="font-size:1rem;font-weight:800;color:#86efac">{{ number_format($summary['oilSellTotal'],2) }}</div>
                    </div>
                    @endif
                    @if($summary['expenseTotal'] > 0)
                    <div style="color:#94a3b8;font-size:1.1rem">−</div>
                    <div style="text-align:center">
                        <div style="font-size:.68rem;color:#fca5a5;margin-bottom:2px">صادرات</div>
                        <div style="font-size:1rem;font-weight:800;color:#fca5a5">{{ number_format($summary['expenseTotal'],2) }}</div>
                    </div>
                    @endif
                    @if($summary['incomeTotal'] > 0)
                    <div style="color:#94a3b8;font-size:1.1rem">+</div>
                    <div style="text-align:center">
                        <div style="font-size:.68rem;color:#86efac;margin-bottom:2px">واردات</div>
                        <div style="font-size:1rem;font-weight:800;color:#86efac">{{ number_format($summary['incomeTotal'],2) }}</div>
                    </div>
                    @endif
                    <div style="width:1px;background:rgba(255,255,255,.15);align-self:stretch;min-height:36px"></div>
                    <div style="text-align:center">
                        <div style="font-size:.68rem;color:#f0d060;margin-bottom:2px">= الصافي</div>
                        <div style="font-size:1.4rem;font-weight:900;color:#f0d060;white-space:nowrap">
                            {{ number_format($summary['netCashFees'],2) }}
                            <small style="font-size:.6rem;opacity:.75">د.أ</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- حالة الإيصالات --}}
<div class="card mb-3">
    <div class="card-header py-2">
        <i class="fas fa-chart-pie me-2"></i>حالة الإيصالات
        @if($summary['isFiltered'])<small class="text-muted ms-1">(حسب الفلتر)</small>@endif
    </div>
    <div class="card-body py-3">
        <div class="row g-2 text-center">
            <div class="col-6 col-md-3">
                <div class="rounded-3 py-3 px-2 h-100" style="background:#fffbeb;border:2px solid #fde68a">
                    <div style="font-size:1.6rem;line-height:1">⏳</div>
                    <div style="font-size:1.8rem;font-weight:900;color:#d97706;line-height:1.2">{{ $summary['pendingCount'] }}</div>
                    <div style="font-size:.75rem;color:#92400e;font-weight:700;margin-top:3px">في الانتظار</div>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div class="rounded-3 py-3 px-2 h-100" style="background:#eff6ff;border:2px solid #bfdbfe">
                    <div style="font-size:1.6rem;line-height:1">⚙️</div>
                    <div style="font-size:1.8rem;font-weight:900;color:#2563eb;line-height:1.2">{{ $summary['processingCount'] }}</div>
                    <div style="font-size:.75rem;color:#1e40af;font-weight:700;margin-top:3px">قيد المعالجة</div>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div class="rounded-3 py-3 px-2 h-100" style="background:#f0fdf4;border:2px solid #bbf7d0">
                    <div style="font-size:1.6rem;line-height:1">✅</div>
                    <div style="font-size:1.8rem;font-weight:900;color:#16a34a;line-height:1.2">{{ $summary['completedCount'] }}</div>
                    <div style="font-size:.75rem;color:#166534;font-weight:700;margin-top:3px">مكتملة</div>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div class="rounded-3 py-3 px-2 h-100" style="background:#fef2f2;border:2px solid #fecaca">
                    <div style="font-size:1.6rem;line-height:1">⚠️</div>
                    <div style="font-size:1.8rem;font-weight:900;color:#dc2626;line-height:1.2">{{ $summary['debtCount'] }}</div>
                    <div style="font-size:.75rem;color:#991b1b;font-weight:700;margin-top:3px">ديون</div>
                </div>
            </div>
        </div>
    </div>
</div>
@endif

{{-- جدول الإيصالات --}}
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <span><i class="fas fa-file-alt me-2"></i>قائمة الإيصالات</span>
        @if(auth()->user()->isCashier())
        <a href="{{ route('receipts.print-table', request()->query()) }}" target="_blank"
           class="btn btn-sm btn-outline-secondary no-print" id="print-table-btn">
            <i class="fas fa-print me-1"></i>طباعة الكل
        </a>
        @endif
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>رقم الإيصال</th>
                        <th>صاحب الزيتون</th>
                        <th>حامل الزيتون</th>
                        <th>الكمية (كيلو)</th>
                        @if(auth()->user()->isCashier())
                        <th>كمية الزيت</th>
                        <th>نسبة ز/ز</th>
                        <th>الأجرة (نقدي)</th>
                        <th>رديات الزيت (كيلو)</th>
                        <th>الديون (د.أ)</th>
                        @endif
                        <th>الحالة</th>
                        <th>التاريخ</th>
                        <th>إجراءات</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($receipts as $receipt)
                    <tr>
                        <td class="text-muted small">{{ $receipts->firstItem() + $loop->index }}</td>
                        <td><strong class="text-success">{{ $receipt->receipt_number }}</strong></td>
                        <td>
                            <a href="{{ route('owners.show', $receipt->owner) }}" class="text-decoration-none fw-bold">
                                {{ $receipt->owner->name }}
                            </a>
                            <div class="text-muted small">{{ $receipt->owner->city }}</div>
                        </td>
                        <td>{{ $receipt->carrier_name }}</td>
                        <td><strong>{{ number_format($receipt->olive_weight_kg, 1) }}</strong></td>
                        @if(auth()->user()->isCashier())
                        <td>
                            @if($receipt->oil_weight_kg)
                                {{ number_format($receipt->oil_weight_kg, 1) }} كيلو
                                @if($receipt->oil_weight_gram)+ {{ $receipt->oil_weight_gram }} غم@endif
                            @else<span class="text-muted">—</span>@endif
                        </td>
                        <td>
                            @if($receipt->olive_oil_ratio)
                                <span style="color:#7c3aed;font-weight:700">{{ number_format($receipt->olive_oil_ratio, 1) }}</span>
                            @else<span class="text-muted">—</span>@endif
                        </td>
                        <td>
                            @if($receipt->status === 'debt')
                                <span class="text-muted">—</span>
                            @elseif($receipt->payment_method === 'oil_return')
                                @if(!$receipt->cans_in_return && $receipt->can_total > 0)
                                    <strong>{{ number_format($receipt->can_total, 2) }}</strong> د.أ
                                    <small class="text-muted d-block">تنك نقدي</small>
                                @else<span class="text-muted">—</span>@endif
                            @else
                                <strong>{{ number_format($receipt->total_fee, 2) }}</strong> د.أ
                            @endif
                        </td>
                        <td>
                            {{-- رديات الزيت: كل ما ليس ديناً معلقاً --}}
                            @if($receipt->payment_method === 'oil_return' && $receipt->oil_return_kg > 0 && $receipt->status !== 'debt')
                                <span class="text-warning fw-bold">{{ number_format($receipt->oil_return_kg, 2) }}</span>
                            @else<span class="text-muted">—</span>@endif
                        </td>
                        <td>
                            @if($receipt->status === 'debt')
                                @if($receipt->payment_method === 'oil_return')
                                    {{-- دين رديات زيت: يظهر بالكيلو --}}
                                    <span class="fw-bold" style="color:#d97706">{{ number_format($receipt->oil_return_kg, 2) }} كيلو</span>
                                    <small class="text-muted d-block" style="font-size:.72rem">زيت</small>
                                @else
                                    <span class="text-danger fw-bold">{{ number_format($receipt->total_fee, 2) }}</span>
                                @endif
                            @else<span class="text-muted">—</span>@endif
                        </td>
                        @endif
                        <td>
                            <span class="badge badge-{{ $receipt->status }} rounded-pill px-3 py-1"
                                  @if($receipt->status === 'debt') style="background:#dc2626;color:#fff" @endif>
                                {{ $receipt->status_label }}
                            </span>
                            @if($receipt->debt_paid_at)
                            <div class="text-success" style="font-size:.72rem;margin-top:2px">
                                <i class="fas fa-check-circle me-1"></i>تم السداد
                            </div>
                            @endif
                        </td>
                        <td class="text-muted small">{{ $receipt->created_at->format('Y/m/d') }}</td>
                        <td>
                            <div class="btn-group btn-group-sm">
                                @if(auth()->user()->isCashier())
                                <a href="{{ route('receipts.print-row', $receipt) }}" target="_blank"
                                   class="btn btn-info text-white" title="طباعة">
                                    <i class="fas fa-print"></i>
                                </a>
                                @if($receipt->status === 'pending')
                                <button type="button" class="btn btn-danger" title="حذف"
                                        onclick="openDeleteModal({{ $receipt->id }}, '{{ $receipt->receipt_number }}')">
                                    <i class="fas fa-trash"></i>
                                </button>
                                @else
                                <button type="button" class="btn btn-secondary" disabled style="opacity:.45;cursor:not-allowed" title="الحذف مسموح للإيصالات المعلّقة فقط">
                                    <i class="fas fa-lock"></i>
                                </button>
                                @endif
                                @else
                                <a href="{{ route('receipts.show', $receipt) }}" class="btn btn-olive" title="عرض">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <a href="{{ route('receipts.print', $receipt) }}" class="btn btn-info text-white" title="طباعة" target="_blank">
                                    <i class="fas fa-print"></i>
                                </a>
                                <a href="{{ route('receipts.edit', $receipt) }}" class="btn btn-warning text-white" title="تعديل">
                                    <i class="fas fa-edit"></i>
                                </a>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="{{ auth()->user()->isCashier() ? 12 : 8 }}" class="text-center py-5 text-muted">
                            <div class="fs-1"><i class="fas fa-inbox"></i></div>
                            <p>لا توجد إيصالات</p>
                            <a href="{{ route('receipts.create') }}" class="btn btn-olive">إضافة إيصال جديد</a>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
                @if(auth()->user()->isCashier() && $summary && $receipts->count() > 0)
                <tfoot>
                    <tr style="background:var(--olive-pale); font-weight:700; font-size:.9rem">
                        <td colspan="4" class="text-end">الإجمالي الكلي:</td>
                        <td>{{ number_format($summary['totalOlive'], 1) }} كيلو</td>
                        <td>{{ number_format($summary['totalOil'], 1) }} كيلو</td>
                        <td style="color:#7c3aed;font-weight:800">
                            @if($summary['totalOil'] > 0)
                                {{ number_format($summary['totalOlive'] / $summary['totalOil'], 1) }}
                            @else —
                            @endif
                        </td>
                        <td>
                            <span class="fw-bold">{{ number_format($summary['netCashFees'], 2) }}</span> <small class="text-muted">د.أ</small>
                            <div style="font-size:.7rem;color:#888">صافي الأجور</div>
                        </td>
                        <td style="color:#d97706">{{ number_format($summary['oilReturnKg'], 2) }} كيلو</td>
                        <td style="color:#dc2626">
                            @if($summary['debtTotal'] > 0){{ number_format($summary['debtTotal'], 2) }} د.أ@endif
                            @if(($summary['debtOilKg'] ?? 0) > 0)
                                <span style="color:#d97706;display:block;font-size:.85rem">{{ number_format($summary['debtOilKg'], 2) }} كيلو زيت</span>
                            @endif
                            @if($summary['debtTotal'] == 0 && ($summary['debtOilKg'] ?? 0) == 0)—@endif
                        </td>
                        <td colspan="3"></td>
                    </tr>
                </tfoot>
                @endif
            </table>
        </div>
    </div>
    @if($receipts->hasPages())
    <div class="card-footer" id="receipts-pagination">
        {{ $receipts->links() }}
    </div>
    @endif
</div>
