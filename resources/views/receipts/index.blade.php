@extends('layouts.app')
@section('title', auth()->user()->isCashier() ? 'التقارير' : 'الإيصالات')
@section('page-title', auth()->user()->isCashier() ? 'التقارير' : 'إدارة الإيصالات')

@section('content')

{{-- ── شاشة القفل (كاشير + غير مفتوح) ── --}}
@if(isset($locked) && $locked)
<div class="row justify-content-center mt-5">
<div class="col-sm-8 col-md-5 col-lg-4">
<div class="card shadow">
    <div class="card-header text-center py-3" style="background:var(--olive-dark);color:#fff">
        <i class="fas fa-lock fa-lg me-2"></i><strong>صفحة التقارير محمية</strong>
    </div>
    <div class="card-body py-4">
        <p class="text-center text-muted mb-4">أدخل كلمة المرور للوصول إلى التقارير</p>
        <form action="{{ route('receipts.unlock') }}" method="POST">
            @csrf
            <div class="mb-3">
                <input type="password" name="reports_password"
                       class="form-control form-control-lg text-center fw-bold @error('reports_password') is-invalid @enderror"
                       placeholder="••••••" autofocus required>
                @error('reports_password')
                    <div class="invalid-feedback text-center">{{ $message }}</div>
                @enderror
            </div>
            <div class="d-grid">
                <button type="submit" class="btn btn-olive btn-lg">
                    <i class="fas fa-unlock me-2"></i>دخول
                </button>
            </div>
        </form>
    </div>
</div>
</div>
</div>
@else

{{-- فلتر البحث (ثابت، لا يُستبدل) --}}
<div class="card mb-3" id="receipts-filter-card">
    <div class="card-body py-2">
        <form id="receipts-filter-form" class="row g-2 align-items-end">
            <div class="col-md-3">
                <label class="form-label">بحث</label>
                <input type="text" name="search" class="form-control"
                       placeholder="{{ auth()->user()->role === 'user' ? 'اسم صاحب الزيتون أو رقم الدور...' : 'رقم الإيصال أو اسم الشخص...' }}"
                       value="{{ request('search') }}">
            </div>
            <div class="col-md-2">
                <label class="form-label">الحالة</label>
                <select name="status" class="form-select">
                    <option value="">الكل</option>
                    <option value="pending"    {{ request('status') == 'pending'    ? 'selected' : '' }}>في الانتظار</option>
                    <option value="processing" {{ request('status') == 'processing' ? 'selected' : '' }}>قيد المعالجة</option>
                    <option value="completed"  {{ request('status') == 'completed'  ? 'selected' : '' }}>مكتمل</option>
                    <option value="debt"       {{ request('status') == 'debt'       ? 'selected' : '' }}>دَين</option>
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label">من تاريخ</label>
                <input type="date" name="from_date" class="form-control" value="{{ request('from_date') }}">
            </div>
            <div class="col-md-2">
                <label class="form-label">إلى تاريخ</label>
                <input type="date" name="to_date" class="form-control" value="{{ request('to_date') }}">
            </div>
            <div class="col-md-3 d-flex gap-2">
                <button type="submit" class="btn btn-olive flex-fill">
                    <i class="fas fa-search me-1"></i>بحث
                </button>
                <button type="button" class="btn btn-outline-secondary" id="btn-clear-filter" title="مسح الفلتر">✕</button>
            </div>
        </form>
    </div>
</div>

{{-- منطقة النتائج — تُستبدل بـ AJAX --}}
<div id="receipts-results">
    @include('receipts._results')
</div>

{{-- مودال الحذف (ثابت دائماً) --}}
@if(auth()->user()->isCashier())
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
                    <div id="delete-error-msg" class="alert alert-danger py-2 text-center d-none" style="font-size:.85rem"></div>
                    <label class="form-label fw-bold">كلمة مرور الحذف</label>
                    <input type="password" name="delete_password" class="form-control text-center fw-bold"
                           placeholder="••••••" required id="deletePasswordInput">
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
@endif

@endif {{-- end lock screen else --}}

@endsection

@section('scripts')
@if(!isset($locked) || !$locked)
<script>
const deleteModal = new bootstrap.Modal(document.getElementById('deleteModal'));
const deleteRouteTemplate = '{{ route("cashier.delete", ":id") }}';
let currentDeleteId = null;

function openDeleteModal(id, num) {
    currentDeleteId = id;
    document.getElementById('deleteReceiptNum').textContent = 'إيصال رقم: ' + num;
    document.getElementById('deletePasswordInput').value = '';
    document.getElementById('delete-error-msg').textContent = '';
    document.getElementById('delete-error-msg').classList.add('d-none');
    deleteModal.show();
}

document.getElementById('deleteForm').addEventListener('submit', async function (e) {
    e.preventDefault();
    const btn = this.querySelector('button[type="submit"]');
    const errDiv = document.getElementById('delete-error-msg');
    const password = document.getElementById('deletePasswordInput').value;

    btn.disabled = true;
    btn.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i>جاري...';

    try {
        const url = deleteRouteTemplate.replace(':id', currentDeleteId);
        const fd  = new FormData();
        fd.append('delete_password', password);
        fd.append('_token', document.querySelector('meta[name="csrf-token"]').content);

        const res  = await fetch(url, {
            method: 'POST',
            headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
            body: fd
        });
        const data = await res.json();

        if (data.success) {
            deleteModal.hide();
            // remove the row from the table directly
            const row = document.querySelector(`button[onclick*="openDeleteModal(${currentDeleteId},"]`)?.closest('tr');
            if (row) row.remove();
            // show brief success toast
            showToast(data.message, 'success');
        } else {
            errDiv.textContent = data.message;
            errDiv.classList.remove('d-none');
        }
    } catch (err) {
        errDiv.textContent = 'حدث خطأ، حاول مرة أخرى';
        errDiv.classList.remove('d-none');
    } finally {
        btn.disabled = false;
        btn.innerHTML = '<i class="fas fa-trash me-1"></i>تأكيد الحذف';
    }
});

function showToast(message, type) {
    const div = document.createElement('div');
    div.className = `alert alert-${type === 'success' ? 'success' : 'danger'} position-fixed fw-bold py-2 px-4 shadow`;
    div.style.cssText = 'top:80px;left:50%;transform:translateX(-50%);z-index:9999;min-width:300px;text-align:center';
    div.innerHTML = `<i class="fas fa-${type === 'success' ? 'check-circle' : 'times-circle'} me-1"></i>${message}`;
    document.body.appendChild(div);
    setTimeout(() => div.remove(), 3500);
}

(function () {
    const form    = document.getElementById('receipts-filter-form');
    const results = document.getElementById('receipts-results');

    async function fetchResults(params) {
        results.innerHTML = `
            <div class="text-center py-5">
                <div class="spinner-border text-olive" role="status"></div>
                <div class="mt-2 text-muted">جاري التحميل...</div>
            </div>`;
        try {
            const res = await fetch('{{ url("/receipts") }}?' + params.toString(), {
                headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }
            });
            const data = await res.json();
            results.innerHTML = data.html;
            bindPagination();
            // sync filter form values
            syncFormFromParams(params);
        } catch (e) {
            results.innerHTML = '<div class="alert alert-danger">حدث خطأ أثناء التحميل. حاول مرة أخرى.</div>';
        }
    }

    function bindPagination() {
        results.querySelectorAll('.pagination a[href]').forEach(link => {
            link.addEventListener('click', e => {
                e.preventDefault();
                const url = new URL(link.href);
                fetchResults(url.searchParams);
            });
        });
    }

    function syncFormFromParams(params) {
        ['search', 'status', 'from_date', 'to_date'].forEach(key => {
            const el = form.querySelector('[name="' + key + '"]');
            if (el) el.value = params.get(key) || '';
        });
        // update print-table link if exists
        const printBtn = results.querySelector('#print-table-btn');
        if (printBtn) printBtn.href = '/receipts/print-table?' + params.toString();
    }

    form.addEventListener('submit', e => {
        e.preventDefault();
        fetchResults(new URLSearchParams(new FormData(form)));
    });

    document.getElementById('btn-clear-filter').addEventListener('click', () => {
        form.reset();
        fetchResults(new URLSearchParams());
    });

    // Handle "عرض الكل" link inside results (event delegation)
    results.addEventListener('click', e => {
        const link = e.target.closest('[data-clear-filter]');
        if (link) {
            e.preventDefault();
            form.reset();
            fetchResults(new URLSearchParams());
        }
    });

    // Bind pagination on initial load
    bindPagination();
})();
</script>
@endif
@endsection
