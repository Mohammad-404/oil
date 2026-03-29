@extends('layouts.app')
@section('title', 'الإيصالات')
@section('page-title', 'إدارة الإيصالات')

@section('content')
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <span><i class="fas fa-file-alt me-2"></i>قائمة الإيصالات</span>
        <a href="{{ route('receipts.create') }}" class="btn btn-gold btn-sm">
            <i class="fas fa-plus me-1"></i>إيصال جديد
        </a>
    </div>
    <div class="card-body border-bottom">
        <form method="GET" class="row g-2 align-items-end">
            <div class="col-md-4">
                <label class="form-label">بحث</label>
                <input type="text" name="search" class="form-control" placeholder="رقم الإيصال أو اسم الشخص..." value="{{ request('search') }}">
            </div>
            <div class="col-md-3">
                <label class="form-label">الحالة</label>
                <select name="status" class="form-select">
                    <option value="">الكل</option>
                    <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>في الانتظار</option>
                    <option value="processing" {{ request('status') == 'processing' ? 'selected' : '' }}>قيد المعالجة</option>
                    <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>مكتمل</option>
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label">التاريخ</label>
                <input type="date" name="date" class="form-control" value="{{ request('date') }}">
            </div>
            <div class="col-md-2 d-grid">
                <button type="submit" class="btn btn-olive">
                    <i class="fas fa-search me-1"></i>بحث
                </button>
            </div>
        </form>
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
                        <th>كمية الزيت</th>
                        <th>الأجرة</th>
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
                        <td>
                            @if($receipt->oil_weight_kg)
                                {{ number_format($receipt->oil_weight_kg, 1) }} كيلو
                                @if($receipt->oil_weight_gram)
                                    + {{ $receipt->oil_weight_gram }} غم
                                @endif
                            @else
                                <span class="text-muted">—</span>
                            @endif
                        </td>
                        <td><strong>{{ number_format($receipt->total_fee, 2) }}</strong> د.أ</td>
                        <td>
                            <span class="badge badge-{{ $receipt->status }} rounded-pill px-3 py-1">
                                {{ $receipt->status_label }}
                            </span>
                        </td>
                        <td class="text-muted small">{{ $receipt->created_at->format('Y/m/d') }}</td>
                        <td>
                            <div class="btn-group btn-group-sm">
                                <a href="{{ route('receipts.show', $receipt) }}" class="btn btn-olive" title="عرض">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <a href="{{ route('receipts.print', $receipt) }}" class="btn btn-info text-white" title="طباعة" target="_blank">
                                    <i class="fas fa-print"></i>
                                </a>
                                <a href="{{ route('receipts.edit', $receipt) }}" class="btn btn-warning text-white" title="تعديل">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <form action="{{ route('receipts.destroy', $receipt) }}" method="POST" onsubmit="return confirm('هل أنت متأكد من الحذف؟')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="btn btn-danger" title="حذف">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="10" class="text-center py-5 text-muted">
                            <div class="fs-1">📋</div>
                            <p>لا توجد إيصالات</p>
                            <a href="{{ route('receipts.create') }}" class="btn btn-olive">إضافة إيصال جديد</a>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @if($receipts->hasPages())
    <div class="card-footer">
        {{ $receipts->links() }}
    </div>
    @endif
</div>
@endsection
