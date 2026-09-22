<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Campus Connect — Sistema de Gestión de Solicitudes Institucionales">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Panel') — Campus Connect</title>

    {{-- Bootstrap 5.3 --}}
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    {{-- Bootstrap Icons --}}
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    {{-- Google Fonts --}}
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <style>
        /* ── Design tokens ─────────────────────────────────────────── */
        :root {
            --cc-primary:   #2563EB;
            --cc-primary-d: #1D4ED8;
            --cc-sidebar:   #0F172A;
            --cc-sidebar-l: #1E293B;
            --cc-accent:    #38BDF8;
            --cc-text:      #F8FAFC;
            --cc-muted:     #94A3B8;
            --sidebar-w:    260px;
        }

        * { font-family: 'Inter', sans-serif; }

        body { background: #F1F5F9; min-height: 100vh; }

        /* ── Sidebar ────────────────────────────────────────────────── */
        #sidebar {
            width: var(--sidebar-w);
            min-height: 100vh;
            background: var(--cc-sidebar);
            position: fixed;
            top: 0; left: 0;
            z-index: 1000;
            display: flex;
            flex-direction: column;
            transition: transform .3s ease;
        }

        .sidebar-brand {
            padding: 1.5rem 1.25rem 1rem;
            border-bottom: 1px solid rgba(255,255,255,.08);
        }

        .sidebar-brand h5 {
            color: var(--cc-accent);
            font-weight: 700;
            font-size: 1.1rem;
            margin: 0;
            letter-spacing: -.3px;
        }

        .sidebar-brand small {
            color: var(--cc-muted);
            font-size: .72rem;
        }

        .sidebar-nav { padding: .75rem 0; flex: 1; }

        .nav-section-label {
            color: var(--cc-muted);
            font-size: .65rem;
            font-weight: 600;
            letter-spacing: .12em;
            text-transform: uppercase;
            padding: .75rem 1.25rem .25rem;
        }

        .sidebar-nav .nav-link {
            color: #CBD5E1;
            padding: .6rem 1.25rem;
            border-radius: 0;
            display: flex;
            align-items: center;
            gap: .65rem;
            font-size: .875rem;
            transition: background .15s, color .15s;
        }

        .sidebar-nav .nav-link:hover,
        .sidebar-nav .nav-link.active {
            background: var(--cc-sidebar-l);
            color: #fff;
        }

        .sidebar-nav .nav-link.active {
            border-left: 3px solid var(--cc-accent);
        }

        .sidebar-nav .nav-link i { font-size: 1rem; width: 1.25rem; }

        .sidebar-footer {
            padding: 1rem 1.25rem;
            border-top: 1px solid rgba(255,255,255,.08);
        }

        /* ── Main content ───────────────────────────────────────────── */
        #main-content {
            margin-left: var(--sidebar-w);
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }

        /* ── Top navbar ─────────────────────────────────────────────── */
        #topbar {
            background: #fff;
            border-bottom: 1px solid #E2E8F0;
            padding: .75rem 1.5rem;
            display: flex;
            align-items: center;
            justify-content: space-between;
            position: sticky;
            top: 0;
            z-index: 999;
            box-shadow: 0 1px 4px rgba(0,0,0,.06);
        }

        #topbar .page-title {
            font-size: 1rem;
            font-weight: 600;
            color: #1E293B;
        }

        /* ── Page content ───────────────────────────────────────────── */
        .page-content { padding: 1.75rem; flex: 1; }

        /* ── Cards KPI ──────────────────────────────────────────────── */
        .kpi-card {
            border: none;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 1px 6px rgba(0,0,0,.07);
            transition: transform .2s, box-shadow .2s;
        }
        .kpi-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(0,0,0,.10);
        }
        .kpi-card .card-body { padding: 1.35rem; }
        .kpi-icon {
            width: 48px; height: 48px;
            border-radius: 10px;
            display: flex; align-items: center; justify-content: center;
            font-size: 1.4rem;
        }
        .kpi-value {
            font-size: 2rem;
            font-weight: 700;
            line-height: 1.1;
            color: #0F172A;
        }
        .kpi-label {
            font-size: .78rem;
            color: #64748B;
            font-weight: 500;
        }

        /* ── Tables ─────────────────────────────────────────────────── */
        .table { font-size: .875rem; }
        .table thead th {
            background: #F8FAFC;
            font-weight: 600;
            font-size: .75rem;
            letter-spacing: .05em;
            text-transform: uppercase;
            color: #64748B;
            border-bottom-width: 1px;
        }

        /* ── Badges ─────────────────────────────────────────────────── */
        .badge { font-size: .72rem; font-weight: 500; padding: .35em .65em; }

        /* ── Timeline ───────────────────────────────────────────────── */
        .timeline { position: relative; padding-left: 2rem; }
        .timeline::before {
            content: '';
            position: absolute;
            left: .5rem;
            top: 0; bottom: 0;
            width: 2px;
            background: #E2E8F0;
        }
        .timeline-item { position: relative; margin-bottom: 1.25rem; }
        .timeline-dot {
            position: absolute;
            left: -1.85rem;
            top: .2rem;
            width: 14px; height: 14px;
            border-radius: 50%;
            background: var(--cc-primary);
            border: 2px solid #fff;
            box-shadow: 0 0 0 2px var(--cc-primary);
        }

        /* ── Alerts ─────────────────────────────────────────────────── */
        .alert { font-size: .875rem; border-radius: 8px; }

        /* ── Responsive ─────────────────────────────────────────────── */
        @media (max-width: 992px) {
            #sidebar { transform: translateX(-100%); }
            #sidebar.show { transform: translateX(0); }
            #main-content { margin-left: 0; }
        }
    </style>
    @stack('styles')
</head>
<body>

{{-- ═══════════════ SIDEBAR ═══════════════ --}}
<nav id="sidebar">
    <div class="sidebar-brand">
        <div class="d-flex align-items-center gap-2 mb-1">
            <div style="background:var(--cc-accent);width:30px;height:30px;border-radius:8px;display:flex;align-items:center;justify-content:center;">
                <i class="bi bi-building-fill" style="color:#0F172A;font-size:.9rem;"></i>
            </div>
            <h5>Campus Connect</h5>
        </div>
        <small>Sistema de Gestión Universitaria</small>
    </div>

    <div class="sidebar-nav">
        <div class="nav-section-label">Principal</div>
        <a href="{{ route('dashboard') }}" class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">
            <i class="bi bi-speedometer2"></i> Dashboard
        </a>
        <a href="{{ route('requests.index') }}" class="nav-link {{ request()->routeIs('requests.*') ? 'active' : '' }}">
            <i class="bi bi-ticket-perforated"></i> Solicitudes
        </a>

        <div class="nav-section-label mt-2">Gestión</div>
        <a href="{{ route('resources.index') }}" class="nav-link {{ request()->routeIs('resources.*') ? 'active' : '' }}">
            <i class="bi bi-buildings"></i> Recursos
        </a>
        <a href="{{ route('reports.index') }}" class="nav-link {{ request()->routeIs('reports.*') ? 'active' : '' }}">
            <i class="bi bi-bar-chart-line"></i> Reportes
        </a>
    </div>

    <div class="sidebar-footer">
        <div class="d-flex align-items-center gap-2 mb-2">
            <div style="width:34px;height:34px;border-radius:50%;background:var(--cc-primary);display:flex;align-items:center;justify-content:center;color:#fff;font-weight:600;font-size:.8rem;">
                {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
            </div>
            <div>
                <div style="color:#fff;font-size:.8rem;font-weight:500;">{{ auth()->user()->name }}</div>
                <div style="color:var(--cc-muted);font-size:.7rem;">{{ ucfirst(auth()->user()->role) }}</div>
            </div>
        </div>
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="btn btn-sm w-100" style="background:rgba(255,255,255,.08);color:#CBD5E1;border:none;">
                <i class="bi bi-box-arrow-left me-1"></i> Cerrar Sesión
            </button>
        </form>
    </div>
</nav>

{{-- ═══════════════ MAIN CONTENT ═══════════════ --}}
<div id="main-content">
    {{-- Topbar --}}
    <div id="topbar">
        <div class="d-flex align-items-center gap-3">
            <button class="btn btn-sm d-lg-none" id="sidebarToggle" style="border:none;">
                <i class="bi bi-list fs-5"></i>
            </button>
            <span class="page-title">@yield('page-title', 'Panel Administrativo')</span>
        </div>
        <div class="d-flex align-items-center gap-2">
            <span class="badge bg-primary">{{ ucfirst(auth()->user()->role) }}</span>
            <small class="text-muted d-none d-md-inline">{{ now()->format('d M Y') }}</small>
        </div>
    </div>

    {{-- Page Content --}}
    <div class="page-content">
        {{-- Flash Messages --}}
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show mb-3" role="alert">
                <i class="bi bi-check-circle-fill me-2"></i>{{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif
        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show mb-3" role="alert">
                <i class="bi bi-exclamation-triangle-fill me-2"></i>{{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @yield('content')
    </div>

    <footer class="text-center text-muted py-3" style="font-size:.75rem;border-top:1px solid #E2E8F0;background:#fff;">
        © {{ date('Y') }} Campus Connect — Sistema Universitario de Gestión de Solicitudes
    </footer>
</div>

{{-- Bootstrap JS --}}
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
    // Sidebar mobile toggle
    const sidebarToggle = document.getElementById('sidebarToggle');
    const sidebar = document.getElementById('sidebar');
    if (sidebarToggle) {
        sidebarToggle.addEventListener('click', () => sidebar.classList.toggle('show'));
    }
    // Auto-dismiss alerts
    setTimeout(() => {
        document.querySelectorAll('.alert').forEach(el => {
            const bsAlert = bootstrap.Alert.getOrCreateInstance(el);
            bsAlert.close();
        });
    }, 5000);
</script>
@stack('scripts')
</body>
</html>
