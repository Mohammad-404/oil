@extends('layouts.app')
@section('title', $owner->name)
@section('page-title', 'ملف صاحب الزيتون')

@section('content')
<div class="row g-3">
    <div class="col-lg-4">
        <div class="card mb-3">
            <div class="card-header"><i class="fas fa-user me-2"></i>البيانات الشخصية</div>
            <div class="card-body">
                <div class="text-center mb-3">
                    <div style="width:70px;height:70px;background:var(--olive-pale);border-radius:50%;display:flex;align-items:center;justify-content:center;margin:0 auto;font-size:2rem;border:2px solid var(--olive-main)">👤</div>
                    <h5 class="mt-2 fw-bold" style="color:var(--olive-dark)">{{ $owner->name }}</h5>
                </div>
                <table class="table table-sm table-borderless">
                    <tr><td class="text-muted">المدينة</td><td><strong>{{ $owner->city }}</strong></td></tr>
                    <tr><td class="text-muted">الشارع</td><td>{{ $owner->street }}</td></tr>
                    <tr><td class="text-muted">الهاتف</td><td>{{ $owner->phone }}</td></tr>
                    <tr><td class="text-muted">رقم الدور</td><td>{{ $owner->floor_number ?? '—' }}</td></tr>
                    <tr><td class="text-muted">منذ</td><td class="small">{{ $owner->created_at->format('Y/m/d') }}</td></tr>
                </table>
            </div>
        </div>
        <div class="card mb-3">
            <div class="card-header"><i class="fas fa-chart-bar me-2"></i>الإحصائيات</div>
            <div class="card-body text-center">
                <div class="row g-2">
                    <div class="col-6">
                        <div class="p-2 rounded" style="background:var(--olive-pale)">
                            <div class="fs-3 fw-bold" style="color:var(--olive-dark)">{{ $owner->receipts->count() }}</div>
                            <small class="text-muted">إيصال</small>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="p-2 rounded" style="background:#fff8e7">
                            <div class="fs-3 fw-bold" style="color:var(--gold)">{{ number_format($owner->total_fees, 0) }}</div>
                            <small class="text-muted">د.أ إجمالي</small>
                        </div>
                    </div>
                    <div class="col-12">
                        <div class="p-2 rounded" style="background:#e0f2fe">
                            <div class="fs-4 fw-bold text-primary">{{ number_format($owner->total_olive_weight, 1) }}</div>
                            <small class="text-muted">كيلو زيتون</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="d-grid gap-2">
            <a href="{{ route('receipts.create', ['owner_id' => $owner->id]) }}" class="btn btn-olive">
                <i class="fas fa-plus me-2"></i>إيصال جديد لهذا الشخص
            </a>
            <a href="{{ route('owners.edit', $owner) }}" class="btn btn-warning text-white">
                <i class="fas fa-edit me-2"></i>تعديل البيانات
            </a>
            <form action="{{ route('owners.destroy', $owner) }}" method="POST" class="d-inline"
                  onsubmit="return confirm('هل أنت متأكد من حذف هذا الشخص وكل إيصالاته؟')">
                @csrf @method('DELETE')
                <button type="submit" class="btn btn-outline-danger">
                    <i class="fas fa-trash me-2"></i>حذف
                </button>
            </form>
        </div>
    </div>

    <div class="col-lg-8">
        <div class="card">
            <div class="card-header"><i class="fas fa-file-alt me-2"></i>إيصالات {{ $owner->name }}</div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead>
                            <tr>
                                <th>رقم الإيصال</th>
                                <th>كمية الزيتون</th>
                                <th>كمية الزيت</th>
                                <th>الأجرة</th>
                                <th>الحالة</th>
                                <th>التاريخ</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($receipts as $receipt)
                            <tr>
                                <td><strong class="text-success">{{ $receipt->receipt_number }}</strong></td>
                                <td>{{ number_format($receipt->olive_weight_kg, 1) }} كيلو</td>
                                <td>{{ $receipt->oil_weight_kg ? number_format($receipt->oil_weight_kg, 1).' كيلو' : '—' }}</td>
                                <td>{{ number_format($receipt->total_fee, 2) }} د.أ</td>
                                <td><span class="badge badge-{{ $receipt->status }} rounded-pill px-2">{{ $receipt->status_label }}</span></td>
                                <td class="small text-muted">{{ $receipt->created_at->format('Y/m/d') }}</td>
                                <td>
                                    <a href="{{ route('receipts.show', $receipt) }}" class="btn btn-sm btn-olive">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="7" class="text-center py-4 text-muted">لا توجد إيصالات</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
            @if($receipts->hasPages())
            <div class="card-footer">{{ $receipts->links() }}</div>
            @endif
        </div>
    </div>
</div>
@endsection
