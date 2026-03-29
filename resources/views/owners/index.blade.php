@extends('layouts.app')
@section('title', 'أصحاب الزيتون')
@section('page-title', 'إدارة أصحاب الزيتون')

@section('content')
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <span><i class="fas fa-users me-2"></i>قائمة أصحاب الزيتون</span>
        <a href="{{ route('owners.create') }}" class="btn btn-gold btn-sm">
            <i class="fas fa-user-plus me-1"></i>إضافة جديد
        </a>
    </div>
    <div class="card-body border-bottom">
        <form method="GET" class="row g-2 align-items-end">
            <div class="col-md-8">
                <input type="text" name="search" class="form-control" placeholder="ابحث بالاسم، الهاتف، أو المدينة..." value="{{ request('search') }}">
            </div>
            <div class="col-md-4 d-grid">
                <button type="submit" class="btn btn-olive"><i class="fas fa-search me-1"></i>بحث</button>
            </div>
        </form>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>الاسم</th>
                        <th>المدينة</th>
                        <th>الشارع</th>
                        <th>الهاتف</th>
                        <th>عدد الإيصالات</th>
                        <th>إجراءات</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($owners as $owner)
                    <tr>
                        <td class="text-muted small">{{ $owners->firstItem() + $loop->index }}</td>
                        <td>
                            <a href="{{ route('owners.show', $owner) }}" class="fw-bold text-decoration-none">
                                {{ $owner->name }}
                            </a>
                        </td>
                        <td>{{ $owner->city }}</td>
                        <td>{{ $owner->street }}</td>
                        <td>{{ $owner->phone }}</td>
                        <td>
                            <span class="badge bg-success rounded-pill">{{ $owner->receipts_count }}</span>
                        </td>
                        <td>
                            <div class="btn-group btn-group-sm">
                                <a href="{{ route('owners.show', $owner) }}" class="btn btn-olive" title="عرض">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <a href="{{ route('receipts.create', ['owner_id' => $owner->id]) }}" class="btn btn-gold" title="إيصال جديد">
                                    <i class="fas fa-plus"></i>
                                </a>
                                <a href="{{ route('owners.edit', $owner) }}" class="btn btn-warning text-white" title="تعديل">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <form action="{{ route('owners.destroy', $owner) }}" method="POST" onsubmit="return confirm('هل أنت متأكد؟')">
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
                        <td colspan="7" class="text-center py-5 text-muted">
                            <div class="fs-1">👥</div>
                            <p>لا يوجد أصحاب زيتون مسجلين</p>
                            <a href="{{ route('owners.create') }}" class="btn btn-olive">إضافة الأول</a>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @if($owners->hasPages())
    <div class="card-footer">{{ $owners->links() }}</div>
    @endif
</div>
@endsection
