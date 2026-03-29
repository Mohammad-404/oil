<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8"> 
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'نظام معصرة الزيتون') }} - @yield('title', 'الرئيسية')</title>

    <!-- Google Fonts Arabic -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Tajawal:wght@300;400;500;700;800&family=Cairo:wght@300;400;600;700;900&display=swap" rel="stylesheet">

    <!-- Bootstrap 5 RTL -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.rtl.min.css">

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <!-- DataTables -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">

    <style>
        :root {
            --olive-dark:   #2d4a1e;
            --olive-mid:    #4a7c2f;
            --olive-light:  #7ab648;
            --gold:         #c9a84c;
            --gold-light:   #f0d080;
            --cream:        #faf6ee;
            --sidebar-w:    260px;
        }

        * { box-sizing: border-box; }

        body {
            font-family: 'Tajawal', 'Cairo', sans-serif;
            background: var(--cream);
            color: #1a2e0e;
            min-height: 100vh;
        }

        /* ── Sidebar ── */
        #sidebar {
            position: fixed;
            top: 0; right: 0;
            width: var(--sidebar-w);
            height: 100vh;
            background: linear-gradient(180deg, var(--olive-dark) 0%, #1a2e0e 100%);
            z-index: 1000;
            overflow-y: auto;
            transition: transform .3s ease;
            box-shadow: -4px 0 20px rgba(0,0,0,.3);
        }

        #sidebar .brand {
            padding: 24px 20px 16px;
            border-bottom: 1px solid rgba(201,168,76,.25);
            text-align: center;
        }
        #sidebar .brand .olive-icon {
            font-size: 2.4rem;
            color: var(--gold);
            display: block;
            margin-bottom: 6px;
            animation: sway 4s ease-in-out infinite;
        }
        @keyframes sway {
            0%,100% { transform: rotate(-4deg); }
            50%      { transform: rotate(4deg); }
        }
        #sidebar .brand h5 {
            color: #fff;
            font-family: 'Cairo', sans-serif;
            font-weight: 700;
            margin: 0;
            font-size: 1.1rem;
        }
        #sidebar .brand small {
            color: var(--gold-light);
            font-size: .75rem;
        }

        #sidebar .nav-section {
            padding: 14px 16px 4px;
            font-size: .7rem;
            font-weight: 700;
            letter-spacing: .08em;
            color: rgba(201,168,76,.6);
            text-transform: uppercase;
        }

        #sidebar .nav-link {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 10px 20px;
            color: rgba(255,255,255,.75);
            border-radius: 8px;
            margin: 2px 10px;
            font-size: .93rem;
            font-weight: 500;
            transition: all .2s;
            text-decoration: none;
        }
        #sidebar .nav-link i { width: 20px; text-align: center; color: var(--gold-light); }
        #sidebar .nav-link:hover,
        #sidebar .nav-link.active {
            background: rgba(201,168,76,.18);
            color: #fff;
        }
        #sidebar .nav-link.active { border-right: 3px solid var(--gold); }

        /* ── Main content ── */
        #main {
            margin-right: var(--sidebar-w);
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }

        /* ── Top bar ── */
        #topbar {
            background: #fff;
            border-bottom: 2px solid var(--gold-light);
            padding: 12px 28px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            position: sticky;
            top: 0;
            z-index: 900;
            box-shadow: 0 2px 12px rgba(0,0,0,.07);
        }
        #topbar .page-title {
            font-family: 'Cairo', sans-serif;
            font-weight: 700;
            font-size: 1.25rem;
            color: var(--olive-dark);
            margin: 0;
        }
        #topbar .user-info { display: flex; align-items: center; gap: 10px; }
        #topbar .user-avatar {
            width: 36px; height: 36px;
            background: var(--olive-mid);
            border-radius: 50%;
            display: flex; align-items: center; justify-content: center;
            color: #fff; font-size: 1rem;
        }

        /* ── Page content ── */
        .page-content { padding: 28px; flex: 1; }

        /* ── Cards ── */
        .card {
            border: none;
            border-radius: 14px;
            box-shadow: 0 2px 16px rgba(0,0,0,.07);
            background: #fff;
        }
        .card-header {
            background: linear-gradient(135deg, var(--olive-dark), var(--olive-mid));
            color: #fff;
            border-radius: 14px 14px 0 0 !important;
            padding: 14px 20px;
            font-family: 'Cairo', sans-serif;
            font-weight: 700;
            font-size: 1.05rem;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        .card-header i { color: var(--gold-light); }

        /* ── Stat cards ── */
        .stat-card {
            border-radius: 14px;
            padding: 22px;
            color: #fff;
            position: relative;
            overflow: hidden;
            transition: transform .2s;
        }
        .stat-card:hover { transform: translateY(-3px); }
        .stat-card .stat-icon {
            position: absolute;
            left: 16px; top: 16px;
            font-size: 2.8rem;
            opacity: .18;
        }
        .stat-card h3 { font-size: 2rem; font-weight: 800; margin: 0; }
        .stat-card p  { margin: 4px 0 0; font-size: .9rem; opacity: .85; }
        .stat-green  { background: linear-gradient(135deg, #2d4a1e, #4a7c2f); }
        .stat-gold   { background: linear-gradient(135deg, #8a6420, #c9a84c); }
        .stat-blue   { background: linear-gradient(135deg, #1a3a5c, #2d6fa8); }
        .stat-orange { background: linear-gradient(135deg, #7a3010, #c95020); }

        /* ── Buttons ── */
        .btn-olive {
            background: linear-gradient(135deg, var(--olive-dark), var(--olive-mid));
            color: #fff; border: none;
            font-weight: 600; border-radius: 8px;
            padding: 8px 20px;
            transition: all .2s;
        }
        .btn-olive:hover { filter: brightness(1.15); color: #fff; }
        .btn-gold {
            background: linear-gradient(135deg, #8a6420, var(--gold));
            color: #fff; border: none;
            font-weight: 600; border-radius: 8px;
            padding: 8px 20px;
            transition: all .2s;
        }
        .btn-gold:hover { filter: brightness(1.1); color: #fff; }

        /* ── Forms ── */
        .form-label { font-weight: 600; color: var(--olive-dark); font-size: .9rem; }
        .form-control, .form-select {
            border: 1.5px solid #dde5d4;
            border-radius: 8px;
            padding: 9px 14px;
            font-family: 'Tajawal', sans-serif;
            transition: border-color .2s, box-shadow .2s;
        }
        .form-control:focus, .form-select:focus {
            border-color: var(--olive-mid);
            box-shadow: 0 0 0 3px rgba(74,124,47,.15);
            outline: none;
        }

        /* ── Tables ── */
        .table th {
            background: var(--olive-dark);
            color: #fff;
            font-weight: 600;
            font-size: .88rem;
            border: none;
        }
        .table td { vertical-align: middle; font-size: .92rem; }
        .table-hover tbody tr:hover { background: rgba(74,124,47,.06); }

        /* ── Badges ── */
        .badge-olive { background: var(--olive-mid); color: #fff; }
        .badge-gold  { background: var(--gold); color: #fff; }

        /* ── Print receipt ── */
        @media print {
            #sidebar, #topbar { display: none !important; }
            #main { margin: 0 !important; }
            .no-print { display: none !important; }
            .page-content { padding: 0 !important; }
        }

        /* ── Alerts ── */
        .alert { border-radius: 10px; font-weight: 500; }

        /* ── Mobile ── */
        @media (max-width: 768px) {
            #sidebar { transform: translateX(100%); }
            #sidebar.open { transform: translateX(0); }
            #main { margin-right: 0; }
        }

        /* ── Olive decorative ── */
        .olive-divider {
            text-align: center;
            position: relative;
            margin: 6px 0;
            color: var(--gold);
            font-size: .8rem;
            letter-spacing: .3em;
        }
    </style>
    @stack('styles')
</head>
<body>

<!-- Sidebar -->
<nav id="sidebar">
    <div class="brand">
        <span class="olive-icon">🫒</span>
        <h5>معصرة الزيتون</h5>
        <small>نظام إدارة متكامل</small>
    </div>

    <div class="mt-2">
        <div class="nav-section">القائمة الرئيسية</div>
        <a href="{{ route('dashboard') }}" class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">
            <i class="fas fa-tachometer-alt"></i> لوحة التحكم
        </a>

        <div class="nav-section">إدارة الزيتون</div>
        <a href="{{ route('sessions.index') }}" class="nav-link {{ request()->routeIs('sessions.*') ? 'active' : '' }}">
            <i class="fas fa-clipboard-list"></i> جلسات العصر
        </a>
        <a href="{{ route('sessions.create') }}" class="nav-link {{ request()->routeIs('sessions.create') ? 'active' : '' }}">
            <i class="fas fa-plus-circle"></i> جلسة جديدة
        </a>

        <div class="nav-section">إدارة أصحاب الزيتون</div>
        <a href="{{ route('owners.index') }}" class="nav-link {{ request()->routeIs('owners.*') ? 'active' : '' }}">
            <i class="fas fa-users"></i> أصحاب الزيتون
        </a>
        <a href="{{ route('owners.create') }}" class="nav-link {{ request()->routeIs('owners.create') ? 'active' : '' }}">
            <i class="fas fa-user-plus"></i> إضافة صاحب
        </a>

        <div class="nav-section">التقارير والحسابات</div>
        <a href="{{ route('reports.daily') }}" class="nav-link {{ request()->routeIs('reports.daily') ? 'active' : '' }}">
            <i class="fas fa-calendar-day"></i> التقرير اليومي
        </a>
        <a href="{{ route('reports.calculations') }}" class="nav-link {{ request()->routeIs('reports.calculations') ? 'active' : '' }}">
            <i class="fas fa-calculator"></i> حساب الأجرة
        </a>
        <a href="{{ route('reports.index') }}" class="nav-link {{ request()->routeIs('reports.index') ? 'active' : '' }}">
            <i class="fas fa-chart-bar"></i> التقارير العامة
        </a>

        <div class="nav-section">الإعدادات</div>
        <a href="{{ route('settings.index') }}" class="nav-link {{ request()->routeIs('settings.*') ? 'active' : '' }}">
            <i class="fas fa-cog"></i> إعدادات النظام
        </a>
    </div>
</nav>

<!-- Main -->
<div id="main">
    <!-- Topbar -->
    <div id="topbar">
        <div class="d-flex align-items-center gap-3">
            <button class="btn btn-sm d-md-none" onclick="document.getElementById('sidebar').classList.toggle('open')">
                <i class="fas fa-bars"></i>
            </button>
            <h1 class="page-title">🫒 @yield('title', 'لوحة التحكم')</h1>
        </div>
        <div class="user-info">
            <span class="text-muted small">{{ now()->format('Y/m/d') }}</span>
            <div class="user-avatar"><i class="fas fa-user"></i></div>
            <a href="{{ route('logout') }}" class="btn btn-sm btn-outline-danger">
                <i class="fas fa-sign-out-alt"></i> خروج
            </a>
        </div>
    </div>

    <!-- Flash messages -->
    <div class="px-4 pt-3">
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show">
                <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif
        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show">
                <i class="fas fa-exclamation-circle me-2"></i>{{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif
        @if($errors->any())
            <div class="alert alert-danger alert-dismissible fade show">
                <i class="fas fa-exclamation-triangle me-2"></i>
                <ul class="mb-0 mt-1">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif
    </div>

    <!-- Content -->
    <div class="page-content">
        @yield('content')
    </div>

    <footer class="text-center py-3 text-muted small border-top mt-auto">
        نظام معصرة الزيتون &copy; {{ date('Y') }} — جميع الحقوق محفوظة
    </footer>
</div>

<!-- Scripts -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
<script>
    // Init DataTables
    $(document).ready(function() {
        $('.datatable').DataTable({
            language: {
                url: 'https://cdn.datatables.net/plug-ins/1.13.6/i18n/ar.json'
            },
            order: [[0, 'desc']]
        });
    });
</script>
@stack('scripts')
</body>
</html>
