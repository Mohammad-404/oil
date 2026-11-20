<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', $pressName) - نظام الإدارة</title>

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.rtl.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@300;400;600;700;800;900&family=Tajawal:wght@300;400;500;700&display=swap" rel="stylesheet">

    <style>
        :root {
            --olive-dark:  #1e3a0f;
            --olive-main:  #3d6b1a;
            --olive-light: #6aab30;
            --olive-pale:  #eaf5d5;
            --gold:        #c8952a;
            --gold-light:  #f0c84a;
            --cream:       #f7f5ee;
            --sidebar-w:   270px;
            --header-h:    72px;
        }

        * { box-sizing: border-box; margin: 0; padding: 0; }

        body {
            font-family: 'Cairo', 'Tajawal', sans-serif;
            background: var(--cream);
            color: #2c3e50;
            min-height: 100vh;
        }

        /* ===== TOP HEADER BANNER ===== */
        #press-header {
            background: linear-gradient(135deg, var(--olive-dark) 0%, #2d5a14 50%, var(--olive-dark) 100%);
            color: #fff;
            height: var(--header-h);
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 1.5rem;
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            z-index: 1100;
            border-bottom: 3px solid var(--gold);
            box-shadow: 0 3px 15px rgba(0,0,0,.35);
        }

        .press-header-logo {
            font-size: 2.2rem;
            line-height: 1;
        }

        .press-header-info {
            text-align: center;
        }

        .press-header-info .press-name {
            font-size: 1.45rem;
            font-weight: 900;
            color: var(--gold-light);
            letter-spacing: .5px;
            line-height: 1.2;
        }

        .press-header-info .press-phones {
            font-size: .82rem;
            color: rgba(255,255,255,.8);
            margin-top: 2px;
            direction: ltr;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: .5rem;
        }

        .press-header-info .press-phones i {
            color: var(--gold-light);
        }

        .press-header-divider {
            width: 2px;
            height: 38px;
            background: rgba(255,255,255,.2);
        }

        /* ===== SIDEBAR ===== */
        #sidebar {
            position: fixed;
            top: var(--header-h);
            right: 0;
            width: var(--sidebar-w);
            height: calc(100vh - var(--header-h));
            background: linear-gradient(180deg, #1a3009 0%, #0f2005 100%);
            z-index: 1000;
            overflow-y: auto;
            transition: transform .3s ease;
            box-shadow: -3px 0 18px rgba(0,0,0,.25);
        }

        .sidebar-nav { padding: .75rem 0 2rem; }

        .nav-section-title {
            color: rgba(255,255,255,.35);
            font-size: .68rem;
            font-weight: 700;
            letter-spacing: 1.5px;
            padding: 1rem 1.3rem .35rem;
            text-transform: uppercase;
        }

        .sidebar-nav .nav-link {
            color: rgba(255,255,255,.72);
            padding: .72rem 1.3rem;
            border-radius: 10px;
            margin: 2px 10px;
            font-size: .92rem;
            font-weight: 600;
            transition: all .2s;
            display: flex;
            align-items: center;
            gap: .75rem;
        }

        .sidebar-nav .nav-link i {
            width: 22px;
            text-align: center;
            font-size: .95rem;
            opacity: .9;
        }

        .sidebar-nav .nav-link:hover {
            background: rgba(255,255,255,.1);
            color: var(--gold-light);
            transform: translateX(-3px);
        }

        .sidebar-nav .nav-link.active {
            background: linear-gradient(135deg, var(--olive-main), #2d5014);
            color: #fff;
            box-shadow: 0 3px 10px rgba(61,107,26,.4);
        }

        .sidebar-nav .nav-link.active i { color: var(--gold-light); }

        /* ===== MAIN CONTENT ===== */
        #main-content {
            margin-right: var(--sidebar-w);
            padding-top: var(--header-h);
            min-height: 100vh;
        }

        /* ===== TOPBAR ===== */
        #topbar {
            background: #fff;
            border-bottom: 2px solid var(--olive-pale);
            padding: .75rem 1.5rem;
            display: flex;
            align-items: center;
            justify-content: space-between;
            position: sticky;
            top: var(--header-h);
            z-index: 100;
            box-shadow: 0 2px 8px rgba(0,0,0,.06);
        }

        .topbar-title {
            font-weight: 800;
            font-size: 1.15rem;
            color: var(--olive-dark);
            display: flex;
            align-items: center;
            gap: .5rem;
        }

        .topbar-title::before {
            content: '';
            display: inline-block;
            width: 4px;
            height: 22px;
            background: var(--olive-light);
            border-radius: 4px;
        }

        .topbar-date {
            color: #888;
            font-size: .82rem;
            background: var(--olive-pale);
            padding: .35rem .8rem;
            border-radius: 20px;
        }

        /* ===== PAGE CONTENT ===== */
        .page-content { padding: 1.4rem 1.6rem; }

        /* ===== CARDS ===== */
        .card {
            border: none;
            border-radius: 14px;
            box-shadow: 0 2px 14px rgba(0,0,0,.07);
            overflow: clip;
        }

        /* ===== MOBILE TABLE SCROLL ===== */
        .table-responsive {
            -webkit-overflow-scrolling: touch;
        }

        /* ===== MOBILE RESPONSIVE ===== */
        @media (max-width: 767.98px) {
            .page-content { padding: .85rem .6rem; }

            /* إخفاء التاريخ في الـ topbar على الموبايل */
            .topbar-date { display: none !important; }

            /* الـ topbar أكثر اتساعاً */
            #topbar { padding: .6rem .9rem; }
            .topbar-title { font-size: 1rem; }

            /* sub-menu أزرار الكاشير تلتف بشكل صحيح */
            .cashier-search-box .d-flex.gap-2 {
                flex-wrap: wrap;
                justify-content: center !important;
            }
            .cashier-search-box .btn { flex: 1 1 auto; min-width: 120px; }

            /* حالة الإيصالات — 2 في الصف */
            .status-badges-grid { grid-template-columns: 1fr 1fr !important; }
        }

        .card-header {
            background: linear-gradient(135deg, var(--olive-dark) 0%, var(--olive-main) 100%);
            color: #fff;
            font-weight: 700;
            font-size: 1rem;
            padding: .9rem 1.4rem;
            border: none;
        }

        .card-header .fa, .card-header .fas { color: var(--gold-light); }

        /* ===== STAT CARDS ===== */
        .stat-card {
            border-radius: 16px;
            padding: 1.3rem 1.4rem;
            color: #fff;
            position: relative;
            overflow: hidden;
            box-shadow: 0 4px 16px rgba(0,0,0,.15);
            transition: transform .2s, box-shadow .2s;
        }

        .stat-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 24px rgba(0,0,0,.2);
        }

        .stat-card::after {
            content: '';
            position: absolute;
            bottom: -25px;
            left: -25px;
            width: 90px;
            height: 90px;
            border-radius: 50%;
            background: rgba(255,255,255,.08);
        }

        .stat-card .stat-icon {
            font-size: 2.2rem;
            margin-bottom: .45rem;
            display: block;
        }

        .stat-card .stat-value {
            font-size: 1.9rem;
            font-weight: 900;
            line-height: 1;
        }

        .stat-card .stat-label {
            font-size: .82rem;
            opacity: .88;
            margin-top: .35rem;
        }

        .bg-olive  { background: linear-gradient(135deg, #3d6b1a, #1e3a0f); }
        .bg-gold   { background: linear-gradient(135deg, #c8952a, #7a5510); }
        .bg-teal   { background: linear-gradient(135deg, #0d9488, #0f6e68); }
        .bg-amber  { background: linear-gradient(135deg, #d97706, #8a4000); }
        .bg-slate  { background: linear-gradient(135deg, #475569, #1e293b); }

        /* ===== TABLES ===== */
        .table thead th {
            background: var(--olive-pale);
            color: var(--olive-dark);
            font-weight: 700;
            border: none;
            font-size: .86rem;
            padding: .85rem 1rem;
            white-space: nowrap;
        }

        .table tbody td {
            padding: .75rem 1rem;
            vertical-align: middle;
            font-size: .9rem;
            border-color: #f0f0e8;
        }

        .table tbody tr:hover { background: #f3f9ea; }

        /* ===== STATUS BADGES ===== */
        .badge-pending    { background: #fef3c7; color: #92400e; font-weight: 700; }
        .badge-processing { background: #dbeafe; color: #1e40af; font-weight: 700; }
        .badge-completed  { background: #d1fae5; color: #065f46; font-weight: 700; }
        .badge-debt       { background: #dc2626; color: #fff;    font-weight: 700; }

        /* ===== BUTTONS ===== */
        .btn-olive {
            background: var(--olive-main);
            color: #fff;
            border: none;
            font-weight: 700;
            transition: background .2s;
        }
        .btn-olive:hover { background: var(--olive-dark); color: #fff; }

        .btn-gold {
            background: var(--gold);
            color: #fff;
            border: none;
            font-weight: 700;
        }
        .btn-gold:hover { background: #9a6e18; color: #fff; }

        /* ===== FORMS ===== */
        .form-control, .form-select {
            border-radius: 9px;
            border: 1.5px solid #dde3d5;
            padding: .65rem 1rem;
            font-family: 'Cairo', sans-serif;
            font-size: .92rem;
            transition: border-color .2s, box-shadow .2s;
        }
        .form-control:focus, .form-select:focus {
            border-color: var(--olive-light);
            box-shadow: 0 0 0 3px rgba(106,171,48,.18);
        }

        .form-label {
            font-weight: 700;
            color: var(--olive-dark);
            font-size: .88rem;
            margin-bottom: .3rem;
        }

        .form-section-title {
            font-size: 1rem;
            font-weight: 800;
            color: var(--olive-dark);
            border-bottom: 2px solid var(--olive-pale);
            padding-bottom: .5rem;
            margin-bottom: 1rem;
        }

        /* ===== ALERTS ===== */
        .alert-success {
            background: #d1fae5;
            border-color: #6ee7b7;
            color: #065f46;
            border-radius: 10px;
        }
        .alert-danger {
            background: #fee2e2;
            border-color: #fca5a5;
            color: #991b1b;
            border-radius: 10px;
        }

        /* ===== PRINT ===== */
        @media print {
            #press-header, #sidebar, #topbar, .no-print { display: none !important; }
            #main-content { margin: 0 !important; padding: 0 !important; }
            .page-content { padding: 0 !important; }
        }

        /* ===== MOBILE ===== */
        @media (max-width: 768px) {
            #sidebar { transform: translateX(100%); }
            #sidebar.open { transform: translateX(0); }
            #main-content { margin-right: 0; }
            .press-header-info .press-name { font-size: 1.1rem; }
        }

        /* زر إغلاق الـ sidebar */
        #sidebar-close {
            display: none;
            position: absolute;
            top: .6rem;
            left: .7rem;
            z-index: 10;
            background: rgba(255,255,255,.12);
            border: none;
            color: #fff;
            border-radius: 8px;
            width: 34px; height: 34px;
            font-size: 1.1rem;
            cursor: pointer;
            align-items: center;
            justify-content: center;
            transition: background .2s;
        }
        #sidebar-close:hover { background: rgba(255,255,255,.22); }
        @media (max-width: 768px) {
            #sidebar-close { display: flex; }
            #sidebar { position: fixed; }
        }

        /* Overlay خلف الـ sidebar */
        #sidebar-overlay {
            display: none;
            position: fixed;
            inset: 0;
            background: rgba(0,0,0,.45);
            z-index: 999;
        }
        #sidebar-overlay.show { display: block; }
    </style>

    @yield('styles')
</head>
<body>

<!-- ===== TOP PRESS HEADER ===== -->
<div id="press-header">
    <div class="press-header-logo"><i class="fas fa-leaf" style="color:#6aab30"></i></div>
    <div class="press-header-divider"></div>
    <div class="press-header-info">
        <div class="press-name">{{ $pressName }}</div>
        @if($pressPhone)
        <div class="press-phones">
            <i class="fas fa-phone-alt"></i>
            <span dir="ltr">{{ $pressPhone }}</span>
        </div>
        @endif
    </div>
    <div class="press-header-divider"></div>
    <div class="press-header-logo"><i class="fas fa-leaf" style="color:#6aab30"></i></div>
</div>

<!-- Overlay يغلق الـ sidebar عند الضغط خارجه -->
<div id="sidebar-overlay"></div>

<!-- ===== SIDEBAR ===== -->
<nav id="sidebar">
    <button id="sidebar-close" onclick="closeSidebar()" title="إغلاق">
        <i class="fas fa-times"></i>
    </button>
    <div class="sidebar-nav">
        @if(auth()->user()->isCashier())
        <div class="nav-section-title">الرئيسية</div>
        <a href="{{ route('dashboard') }}" class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">
            <i class="fas fa-tachometer-alt"></i> لوحة التحكم
        </a>

        <div class="nav-section-title">الكاشير</div>
        <a href="{{ route('cashier.index') }}" class="nav-link {{ request()->routeIs('cashier.index') ? 'active' : '' }}">
            <i class="fas fa-cash-register"></i> الكاشير
        </a>
        <a href="{{ route('cashier.debts') }}" class="nav-link {{ request()->routeIs('cashier.debts') || request()->routeIs('cashier.debt') ? 'active' : '' }}">
            <i class="fas fa-exclamation-triangle"></i> الديون
            @php $debtCount = \App\Models\Receipt::where('status','debt')->count(); @endphp
            @if($debtCount > 0)
                <span class="badge bg-danger ms-1" style="font-size:.7rem">{{ $debtCount }}</span>
            @endif
        </a>
        <a href="{{ route('cashier.oil-sales') }}" class="nav-link {{ request()->routeIs('cashier.oil-sales') ? 'active' : '' }}">
            <i class="fas fa-bottle-droplet"></i> رديات الزيتون
        </a>
        <a href="{{ route('cashier.oil-trade') }}" class="nav-link {{ request()->routeIs('cashier.oil-trade*') ? 'active' : '' }}">
            <i class="fas fa-scale-balanced"></i> بيع وشراء الزيت
        </a>
        <a href="{{ route('cashier.expenses') }}" class="nav-link {{ request()->routeIs('cashier.expenses*') ? 'active' : '' }}">
            <i class="fas fa-wallet"></i> المصروفات
        </a>
        <a href="{{ route('cashier.driver-fees') }}" class="nav-link {{ request()->routeIs('cashier.driver-fees') ? 'active' : '' }}">
            <i class="fas fa-truck"></i> أجور الناقلين
        </a>

        <div class="nav-section-title">الإدارة</div>
        <a href="{{ route('receipts.index') }}" class="nav-link {{ request()->routeIs('receipts.index') ? 'active' : '' }}">
            <i class="fas fa-chart-bar"></i> التقارير
        </a>
        <a href="{{ route('reports.financial') }}" class="nav-link {{ request()->routeIs('reports.financial') ? 'active' : '' }}">
            <i class="fas fa-coins"></i> التقرير المالي
        </a>
        <a href="{{ route('settings') }}" class="nav-link {{ request()->routeIs('settings') ? 'active' : '' }}">
            <i class="fas fa-cog"></i> الإعدادات
        </a>

        @else
        {{-- يوزر عادي --}}
        <div class="nav-section-title">العمليات</div>
        <a href="{{ route('receipts.create') }}" class="nav-link {{ request()->routeIs('receipts.create') ? 'active' : '' }}">
            <i class="fas fa-plus-circle"></i> إيصال جديد
        </a>
        <a href="{{ route('receipts.index') }}" class="nav-link {{ request()->routeIs('receipts.index') ? 'active' : '' }}">
            <i class="fas fa-file-alt"></i> الإيصالات
        </a>
        <a href="{{ route('owners.index') }}" class="nav-link {{ request()->routeIs('owners.*') ? 'active' : '' }}">
            <i class="fas fa-users"></i> أصحاب الزيتون
        </a>
        <a href="{{ route('user.receipts') }}" class="nav-link {{ request()->routeIs('user.receipts') ? 'active' : '' }}">
            <i class="fas fa-print"></i> طباعة الدور
        </a>
        <div class="nav-section-title">الإدارة</div>
        <a href="{{ route('settings') }}" class="nav-link {{ request()->routeIs('settings') ? 'active' : '' }}">
            <i class="fas fa-cog"></i> الإعدادات
        </a>
        @endif
    </div>
</nav>

<!-- ===== MAIN CONTENT ===== -->
<div id="main-content">
    <!-- TOPBAR -->
    <div id="topbar">
        <div class="d-flex align-items-center gap-3">
            <button class="btn btn-sm btn-light d-md-none" id="sidebarToggle">
                <i class="fas fa-bars"></i>
            </button>
            <span class="topbar-title">@yield('page-title', 'لوحة التحكم')</span>
        </div>
        <div class="d-flex align-items-center gap-2">
            <div class="topbar-date">
                <i class="fas fa-calendar-alt me-1"></i>
                {{ now()->locale('ar')->isoFormat('dddd، D MMMM YYYY') }}
            </div>
            @php $activeYear = \DB::table('settings')->where('key','active_year')->value('value'); @endphp
            @if($activeYear && $activeYear !== 'all')
            <div style="background:#2d5016;color:#fff;padding:3px 8px;border-radius:20px;font-size:.75rem;font-weight:700;white-space:nowrap">
                <i class="fas fa-filter me-1 d-none d-md-inline"></i>سنة {{ $activeYear }}
            </div>
            @endif
            <div class="d-flex align-items-center gap-2" style="border-right:2px solid var(--olive-pale); padding-right:.75rem">
                <span class="small fw-bold text-muted d-none d-md-inline">
                    <i class="fas fa-user-circle me-1" style="color:var(--olive-main)"></i>
                    {{ auth()->user()->name }}
                </span>
                <form method="POST" action="{{ route('logout') }}" class="d-inline">
                    @csrf
                    <button type="submit" class="btn btn-sm btn-outline-danger" title="تسجيل الخروج">
                        <i class="fas fa-sign-out-alt"></i>
                        <span class="d-none d-md-inline ms-1">خروج</span>
                    </button>
                </form>
            </div>
        </div>
    </div>

    <!-- ALERTS -->
    <div class="page-content pb-0">
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif
        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="fas fa-exclamation-circle me-2"></i>{{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif
        @if(session('info'))
            <div class="alert alert-warning alert-dismissible fade show" role="alert">
                <i class="fas fa-info-circle me-2"></i>{{ session('info') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif
        @if(session('warning'))
            <div class="alert alert-warning alert-dismissible fade show fw-bold" role="alert" style="border-right:4px solid #d97706">
                <i class="fas fa-exclamation-triangle me-2"></i>{{ session('warning') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif
    </div>

    <!-- PAGE CONTENT -->
    <div class="page-content">
        @yield('content')
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
    function closeSidebar() {
        document.getElementById('sidebar').classList.remove('open');
        document.getElementById('sidebar-overlay').classList.remove('show');
    }

    document.getElementById('sidebarToggle')?.addEventListener('click', () => {
        document.getElementById('sidebar').classList.toggle('open');
        document.getElementById('sidebar-overlay').classList.toggle('show');
    });

    document.getElementById('sidebar-overlay')?.addEventListener('click', closeSidebar);

    // تمرير أفقي للجداول على الموبايل — يلف كل جدول بدون table-responsive تلقائياً
    document.querySelectorAll('table.table').forEach(function(t) {
        if (!t.closest('.table-responsive') && !t.closest('.no-scroll')) {
            var d = document.createElement('div');
            d.className = 'table-responsive';
            t.parentNode.insertBefore(d, t);
            d.appendChild(t);
        }
    });
</script>
@yield('scripts')
</body>
</html>
