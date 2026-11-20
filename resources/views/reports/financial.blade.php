@extends('layouts.app')
@section('title', 'التقرير المالي اليومي')
@section('page-title', 'التقرير المالي اليومي')

@section('content')

@if(isset($locked) && $locked)
{{-- شاشة القفل --}}
<div class="row justify-content-center mt-5">
<div class="col-sm-8 col-md-5 col-lg-4">
<div class="card shadow">
    <div class="card-header text-center py-3" style="background:var(--olive-dark);color:#fff">
        <i class="fas fa-lock fa-lg me-2"></i><strong>التقرير المالي محمي</strong>
    </div>
    <div class="card-body py-4">
        <p class="text-center text-muted mb-4">أدخل كلمة المرور للوصول إلى التقرير المالي</p>
        <form action="{{ route('reports.financial.unlock') }}" method="POST">
            @csrf
            <div class="mb-3">
                <input type="password" name="financial_password"
                       class="form-control form-control-lg text-center fw-bold @error('financial_password') is-invalid @enderror"
                       placeholder="••••••" autofocus required>
                @error('financial_password')
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

{{-- فلتر التاريخ (ثابت، لا يُستبدل بـ AJAX) --}}
<div class="card mb-4" id="financial-filter-card">
    <div class="card-body py-3">
        <form id="financial-filter-form" class="row g-2 align-items-end">
            <div class="col-auto">
                <label class="form-label mb-1 small fw-bold">من تاريخ</label>
                <input type="date" name="from_date" id="fin-from" class="form-control" value="{{ $fromDate }}">
            </div>
            <div class="col-auto">
                <label class="form-label mb-1 small fw-bold">إلى تاريخ</label>
                <input type="date" name="to_date" id="fin-to" class="form-control" value="{{ $toDate }}">
            </div>
            <div class="col-auto d-flex gap-2">
                <button type="submit" class="btn btn-olive">
                    <i class="fas fa-search me-1"></i>عرض
                </button>
                <button type="button" class="btn btn-outline-secondary" id="btn-today">اليوم</button>
                <button type="button" class="btn btn-outline-secondary" id="btn-month">هذا الشهر</button>
            </div>
            <div class="col-auto ms-auto no-print">
                <button onclick="window.print()" type="button" class="btn btn-outline-secondary">
                    <i class="fas fa-print me-1"></i>طباعة
                </button>
            </div>
        </form>
    </div>
</div>

{{-- منطقة النتائج — تُستبدل بـ AJAX --}}
<div id="financial-results">
    @include('reports._financial_results')
</div>

@endif

@endsection

@section('scripts')
@if(!isset($locked) || !$locked)
<script>
(function () {
    const form    = document.getElementById('financial-filter-form');
    const results = document.getElementById('financial-results');
    const fromIn  = document.getElementById('fin-from');
    const toIn    = document.getElementById('fin-to');

    function todayStr() {
        return new Date().toLocaleDateString('en-CA'); // YYYY-MM-DD
    }
    function firstOfMonthStr() {
        const d = new Date();
        return new Date(d.getFullYear(), d.getMonth(), 1).toLocaleDateString('en-CA');
    }

    async function fetchResults(params) {
        results.innerHTML = `
            <div class="text-center py-5">
                <div class="spinner-border text-olive" role="status"></div>
                <div class="mt-2 text-muted">جاري التحميل...</div>
            </div>`;
        try {
            const res = await fetch('{{ url("/financial-report") }}?' + params.toString(), {
                headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }
            });
            const data = await res.json();
            results.innerHTML = data.html;
        } catch (e) {
            results.innerHTML = '<div class="alert alert-danger">حدث خطأ أثناء التحميل. حاول مرة أخرى.</div>';
        }
    }

    form.addEventListener('submit', e => {
        e.preventDefault();
        fetchResults(new URLSearchParams(new FormData(form)));
    });

    document.getElementById('btn-today').addEventListener('click', () => {
        const t = todayStr();
        fromIn.value = t; toIn.value = t;
        fetchResults(new URLSearchParams({ from_date: t, to_date: t }));
    });

    document.getElementById('btn-month').addEventListener('click', () => {
        const f = firstOfMonthStr(), t = todayStr();
        fromIn.value = f; toIn.value = t;
        fetchResults(new URLSearchParams({ from_date: f, to_date: t }));
    });
})();
</script>
@endif
@endsection
