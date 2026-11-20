@extends('layouts.app')
@section('title', 'الإعدادات')
@section('page-title', 'إعدادات المعصرة')

@section('content')

@if($locked)
{{-- شاشة القفل --}}
<div class="row justify-content-center mt-5">
<div class="col-sm-8 col-md-5 col-lg-4">
<div class="card shadow">
    <div class="card-header text-center py-3" style="background:var(--olive-dark);color:#fff">
        <i class="fas fa-lock fa-lg me-2"></i><strong>الإعدادات محمية</strong>
    </div>
    <div class="card-body py-4">
        <p class="text-center text-muted mb-4">أدخل كلمة مرور الحذف للمتابعة</p>
        <form action="{{ route('settings.unlock') }}" method="POST">
            @csrf
            <div class="mb-3">
                <input type="password" name="settings_password"
                       class="form-control form-control-lg text-center fw-bold @error('settings_password') is-invalid @enderror"
                       placeholder="••••••" autofocus required>
                @error('settings_password')
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
{{-- الإعدادات الفعلية --}}
<div class="row justify-content-center">
<div class="col-lg-7">
<div class="card">
    <div class="card-header">
        <i class="fas fa-cog me-2"></i>إعدادات النظام
    </div>
    <div class="card-body">
        <div id="settings-toast" class="alert alert-success d-none mb-3 py-2 text-center fw-bold">
            <i class="fas fa-check-circle me-1"></i><span id="settings-toast-msg"></span>
        </div>
        <form id="settings-form" action="{{ route('settings.update') }}" method="POST">
            @csrf
            <div class="row g-3">
                <div class="col-12">
                    <label class="form-label">🫒 اسم المعصرة</label>
                    <input type="text" name="press_name" class="form-control" value="{{ $settings['press_name'] ?? 'معصرة الزيتون' }}" placeholder="اسم المعصرة">
                </div>
                <div class="col-md-6">
                    <label class="form-label">💰 الأجرة الافتراضية / كيلو (د.أ)</label>
                    <input type="number" name="fee_per_kg" class="form-control" step="0.001" value="{{ $settings['fee_per_kg'] ?? '2.000' }}" placeholder="2.000">
                    <small class="text-muted">تظهر تلقائياً عند إنشاء إيصال جديد</small>
                </div>
                <div class="col-md-6">
                    <label class="form-label">📞 هاتف المعصرة</label>
                    <input type="text" name="press_phone" class="form-control" value="{{ $settings['press_phone'] ?? '' }}" placeholder="07xxxxxxxx">
                </div>
                <div class="col-12">
                    <label class="form-label">📍 عنوان المعصرة</label>
                    <input type="text" name="press_address" class="form-control" value="{{ $settings['press_address'] ?? '' }}" placeholder="عنوان المعصرة">
                </div>

                {{-- أسعار التنك --}}
                <div class="col-12">
                    <hr class="my-1">
                    <div class="fw-bold text-olive mb-2" style="color:var(--olive-dark)">
                        📦 أسعار التنكات (الحجم ثابت — السعر قابل للتعديل)
                    </div>
                    <div class="row g-2">
                        <div class="col-4">
                            <label class="form-label" style="font-size:.85rem">🛢️ تنكة 16 كيلو (د.أ)</label>
                            <input type="number" name="can_16_price" class="form-control text-center fw-bold"
                                   step="0.001" min="0"
                                   value="{{ $settings['can_16_price'] ?? '2.000' }}"
                                   placeholder="2.000">
                        </div>
                        <div class="col-4">
                            <label class="form-label" style="font-size:.85rem">🛢️ تنكة 8 كيلو (د.أ)</label>
                            <input type="number" name="can_8_price" class="form-control text-center fw-bold"
                                   step="0.001" min="0"
                                   value="{{ $settings['can_8_price'] ?? '1.500' }}"
                                   placeholder="1.500">
                        </div>
                        <div class="col-4">
                            <label class="form-label" style="font-size:.85rem">🛢️ تنكة 4 كيلو (د.أ)</label>
                            <input type="number" name="can_4_price" class="form-control text-center fw-bold"
                                   step="0.001" min="0"
                                   value="{{ $settings['can_4_price'] ?? '0.750' }}"
                                   placeholder="0.750">
                        </div>
                    </div>
                    <small class="text-muted">تؤثر على الإيصالات الجديدة فقط — الإيصالات المحفوظة مسبقاً لا تتغير</small>
                </div>

                {{-- السنة النشطة --}}
                <div class="col-12">
                    <hr class="my-1">
                    <div class="fw-bold mb-2" style="color:var(--olive-dark)">
                        📅 السنة النشطة (تؤثر على جميع البيانات المعروضة)
                    </div>
                    <select name="active_year" class="form-select">
                        <option value="all" {{ ($settings['active_year'] ?? 'all') === 'all' ? 'selected' : '' }}>
                            الكل — عرض جميع السنوات
                        </option>
                        @for($y = 2040; $y >= 2026; $y--)
                        <option value="{{ $y }}" {{ ($settings['active_year'] ?? '') == $y ? 'selected' : '' }}>
                            {{ $y }}
                        </option>
                        @endfor
                    </select>
                    <small class="text-muted">عند اختيار سنة معينة، كل الصفحات ستعرض بيانات تلك السنة فقط</small>
                </div>

                <div class="col-12">
                    <hr>
                    <label class="form-label fw-bold">🔐 كلمة مرور الحذف (للكاشير)</label>
                    <input type="password" name="cashier_delete_password" class="form-control"
                           value="{{ $settings['cashier_delete_password'] ?? '' }}"
                           placeholder="اكتب كلمة مرور خاصة لتأكيد الحذف">
                    <small class="text-muted">يستخدمها الكاشير عند حذف الإيصالات من لوحة التحكم</small>
                </div>

                <div class="col-12 d-flex gap-2 justify-content-end mt-2">
                    <button type="submit" class="btn btn-olive px-4">
                        <i class="fas fa-save me-2"></i>حفظ الإعدادات
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

<div class="card mt-3 border-danger">
    <div class="card-header text-white" style="background:#991b1b">
        <i class="fas fa-trash-alt me-2"></i>حذف البيانات حسب التاريخ
    </div>
    <div class="card-body">

        <div id="bulk-toast" class="d-none py-2 mb-2" style="border-radius:6px;padding:10px 14px;font-size:.9rem"></div>

        <div class="alert alert-warning py-2 mb-3" style="font-size:.83rem">
            <i class="fas fa-exclamation-triangle me-1"></i>
            <strong>تحذير:</strong> هذه العملية لا يمكن التراجع عنها. سيتم حذف جميع السجلات في النطاق الزمني المحدد.
        </div>

        <form id="bulk-delete-form" action="{{ route('settings.bulk-delete') }}" method="POST">
            @csrf
            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label fw-bold">📅 من تاريخ</label>
                    <input type="date" name="from_date" class="form-control" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-bold">📅 إلى تاريخ</label>
                    <input type="date" name="to_date" class="form-control" required>
                </div>

                <div class="col-12">
                    <label class="form-label fw-bold">🗂️ اختر الجداول المراد حذفها</label>
                    <div class="row g-2">
                        <div class="col-md-4">
                            <div class="form-check p-3 border border-danger rounded" style="background:#fff5f5">
                                <input class="form-check-input" type="checkbox" name="del_receipts" id="del_receipts" value="1">
                                <label class="form-check-label fw-bold text-danger" for="del_receipts">
                                    <i class="fas fa-file-alt me-1"></i>الإيصالات
                                </label>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-check p-3 border border-warning rounded" style="background:#fffbeb">
                                <input class="form-check-input" type="checkbox" name="del_oil_trades" id="del_oil_trades" value="1">
                                <label class="form-check-label fw-bold text-warning" for="del_oil_trades">
                                    <i class="fas fa-balance-scale me-1"></i>بيع وشراء الزيت
                                </label>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-check p-3 border border-info rounded" style="background:#eff6ff">
                                <input class="form-check-input" type="checkbox" name="del_expenses" id="del_expenses" value="1">
                                <label class="form-check-label fw-bold text-info" for="del_expenses">
                                    <i class="fas fa-coins me-1"></i>المصروفات والواردات
                                </label>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-12">
                    <label class="form-label fw-bold">🔐 كلمة المرور للتأكيد</label>
                    <input type="password" name="confirm_password" class="form-control" required
                           placeholder="أدخل كلمة مرور الحذف للتأكيد">
                    <small class="text-muted">نفس كلمة مرور الكاشير المحفوظة في الإعدادات</small>
                </div>

                <div class="col-12">
                    <button type="submit" id="bulk-delete-btn" class="btn btn-danger px-4 fw-bold">
                        <i class="fas fa-trash-alt me-2"></i>تنفيذ الحذف
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

<div class="card mt-3">
    <div class="card-header"><i class="fas fa-info-circle me-2"></i>معلومات النظام</div>
    <div class="card-body small text-muted">
        <div class="row">
            <div class="col-6"><strong>الإصدار:</strong> 1.0.0</div>
            <div class="col-6"><strong>Laravel:</strong> 10.x</div>
            <div class="col-12 mt-2">نظام إدارة معصرة الزيتون — جميع الحقوق محفوظة</div>
        </div>
    </div>
</div>
</div>
</div>

@endif

@endsection

@section('scripts')
@if(!isset($locked) || !$locked)
<script>
(function () {
    const form  = document.getElementById('settings-form');
    const toast = document.getElementById('settings-toast');
    const msg   = document.getElementById('settings-toast-msg');
    const btn   = form ? form.querySelector('button[type="submit"]') : null;
    if (!form) return;

    form.addEventListener('submit', async function (e) {
        e.preventDefault();
        if (btn) { btn.disabled = true; btn.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i>جاري الحفظ...'; }
        try {
            const res = await fetch('{{ route("settings.update") }}', {
                method: 'POST',
                headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content, 'X-Requested-With': 'XMLHttpRequest' },
                body: new FormData(form)
            });
            const data = await res.json();
            if (data.success) { msg.textContent = data.message; toast.classList.remove('d-none'); setTimeout(() => toast.classList.add('d-none'), 4000); }
        } catch (err) { alert('حدث خطأ، حاول مرة أخرى'); }
        finally { if (btn) { btn.disabled = false; btn.innerHTML = '<i class="fas fa-save me-2"></i>حفظ الإعدادات'; } }
    });
})();

// Bulk delete AJAX
(function () {
    const form  = document.getElementById('bulk-delete-form');
    if (!form) return;
    const btn   = document.getElementById('bulk-delete-btn');
    const toastDiv = document.getElementById('bulk-toast');

    function showBulkMsg(message, success) {
        toastDiv.innerHTML = `<i class="fas fa-${success ? 'check-circle' : 'times-circle'} me-1"></i>${message}`;
        toastDiv.className = `py-2 mb-2 alert alert-${success ? 'success' : 'danger'}`;
        toastDiv.classList.remove('d-none');
        if (success) setTimeout(() => toastDiv.classList.add('d-none'), 5000);
    }

    form.addEventListener('submit', async function (e) {
        e.preventDefault();
        if (!confirm('⚠️ تأكيد الحذف النهائي؟\nلا يمكن التراجع عن هذه العملية!')) return;

        btn.disabled = true;
        btn.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i>جاري الحذف...';

        try {
            const res = await fetch('{{ route("settings.bulk-delete") }}', {
                method: 'POST',
                headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content, 'X-Requested-With': 'XMLHttpRequest' },
                body: new FormData(form)
            });
            const data = await res.json();
            showBulkMsg(data.message, data.success);
            if (data.success) form.reset();
        } catch (err) {
            showBulkMsg('حدث خطأ، حاول مرة أخرى', false);
        } finally {
            btn.disabled = false;
            btn.innerHTML = '<i class="fas fa-trash-alt me-2"></i>تنفيذ الحذف';
        }
    });
})();
</script>
@endif
@endsection
