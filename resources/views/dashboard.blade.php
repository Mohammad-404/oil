@extends('layouts.app')
@section('title', 'لوحة التحكم')
@section('page-title', 'لوحة التحكم')

@section('content')

<!-- إحصائيات سريعة -->
<div class="row g-3 mb-4">
    <div class="col-6 col-md-4 col-lg-2">
        <div class="stat-card bg-olive text-center">
            <span class="stat-icon">👥</span>
            <div class="stat-value">{{ number_format($totalOwners) }}</div>
            <div class="stat-label">أصحاب الزيتون</div>
        </div>
    </div>
    <div class="col-6 col-md-4 col-lg-2">
        <div class="stat-card bg-teal text-center">
            <span class="stat-icon">📋</span>
            <div class="stat-value">{{ number_format($totalReceipts) }}</div>
            <div class="stat-label">إجمالي الإيصالات</div>
        </div>
    </div>
    <div class="col-6 col-md-4 col-lg-2">
        <div class="stat-card bg-amber text-center">
            <span class="stat-icon">📅</span>
            <div class="stat-value">{{ number_format($todayReceipts) }}</div>
            <div class="stat-label">إيصالات اليوم</div>
        </div>
    </div>
    <div class="col-6 col-md-4 col-lg-2">
        <div class="stat-card bg-olive text-center">
            <span class="stat-icon">🫒</span>
            <div class="stat-value">{{ number_format($totalOlive, 0) }}</div>
            <div class="stat-label">كيلو زيتون</div>
        </div>
    </div>
    <div class="col-6 col-md-4 col-lg-2">
        <div class="stat-card bg-gold text-center">
            <span class="stat-icon">🫙</span>
            <div class="stat-value">{{ number_format($totalOil, 0) }}</div>
            <div class="stat-label">كيلو زيت</div>
        </div>
    </div>
    <div class="col-6 col-md-4 col-lg-2">
        <div class="stat-card bg-slate text-center">
            <span class="stat-icon">💰</span>
            <div class="stat-value">{{ number_format($totalFees, 0) }}</div>
            <div class="stat-label">إجمالي الأجور (د.أ)</div>
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
                <a href="{{ route('receipts.create') }}" class="btn btn-olive btn-lg">
                    <i class="fas fa-plus-circle me-2"></i>إيصال جديد
                </a>
                <a href="{{ route('owners.create') }}" class="btn btn-outline-success">
                    <i class="fas fa-user-plus me-2"></i>إضافة صاحب زيتون
                </a>
                <a href="{{ route('reports') }}" class="btn btn-outline-secondary">
                    <i class="fas fa-file-export me-2"></i>التقارير
                </a>
            </div>
        </div>
    </div>

    <!-- حالة الإيصالات -->
    <div class="col-md-4">
        <div class="card h-100">
            <div class="card-header">
                <i class="fas fa-chart-pie me-2"></i>حالة الإيصالات
            </div>
            <div class="card-body py-3">
                @php $processingCount = $totalReceipts - $pendingCount - $completedCount; @endphp

                <div class="d-flex justify-content-between align-items-center py-2 border-bottom">
                    <span class="badge badge-pending px-3 py-2 rounded-pill fs-6">⏳ في الانتظار</span>
                    <strong class="fs-4 text-warning">{{ $pendingCount }}</strong>
                </div>
                <div class="d-flex justify-content-between align-items-center py-2 border-bottom">
                    <span class="badge badge-processing px-3 py-2 rounded-pill fs-6">⚙️ قيد المعالجة</span>
                    <strong class="fs-4 text-primary">{{ $processingCount }}</strong>
                </div>
                <div class="d-flex justify-content-between align-items-center py-2">
                    <span class="badge badge-completed px-3 py-2 rounded-pill fs-6">✅ مكتملة</span>
                    <strong class="fs-4 text-success">{{ $completedCount }}</strong>
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
                @php
                    $todayOlive = \App\Models\Receipt::whereDate('created_at', today())->sum('olive_weight_kg');
                    $todayFees  = \App\Models\Receipt::whereDate('created_at', today())->sum('total_fee');
                @endphp
                <div class="text-center py-2">
                    <div style="font-size:2.5rem">🫒</div>
                    <div class="fs-3 fw-bold mt-1" style="color:var(--olive-dark)">
                        {{ number_format($todayOlive, 1) }} <small class="fs-6">كيلو</small>
                    </div>
                    <div class="text-muted small mb-3">زيتون استُلم اليوم</div>
                    <hr>
                    <div class="fs-3 fw-bold" style="color:var(--gold)">
                        {{ number_format($todayFees, 2) }} <small class="fs-6">د.أ</small>
                    </div>
                    <div class="text-muted small">إجمالي الأجور اليوم</div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- آخر الإيصالات -->
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <span><i class="fas fa-list me-2"></i>آخر الإيصالات</span>
        <a href="{{ route('receipts.index') }}" class="btn btn-sm btn-light">
            <i class="fas fa-arrow-left me-1"></i>عرض الكل
        </a>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead>
                    <tr>
                        <th>رقم الإيصال</th>
                        <th>صاحب الزيتون</th>
                        <th>الكمية (كيلو)</th>
                        <th>الأجرة</th>
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
                        <td><strong>{{ number_format($receipt->total_fee, 2) }}</strong> د.أ</td>
                        <td>
                            <span class="badge badge-{{ $receipt->status }} rounded-pill px-3 py-1">
                                {{ $receipt->status_label }}
                            </span>
                        </td>
                        <td class="text-muted">{{ $receipt->created_at->format('Y/m/d') }}</td>
                        <td class="text-center">
                            <a href="{{ route('receipts.show', $receipt) }}" class="btn btn-sm btn-olive">
                                <i class="fas fa-eye"></i>
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="text-center py-5 text-muted">
                            <div class="fs-1 mb-2">📋</div>
                            <p class="mb-2">لا توجد إيصالات بعد</p>
                            <a href="{{ route('receipts.create') }}" class="btn btn-olive btn-sm">إضافة أول إيصال</a>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

@endsection
