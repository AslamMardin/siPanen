<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'SiPanen') – Dinas Pertanian Kab. Polman</title>

    <!-- Bootstrap 5 -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">

    <style>
        :root {
            --sidebar-width: 260px;
            --primary: #2e7d32;
            --primary-dark: #1b5e20;
        }
        body { background: #f4f6f9; }

        /* Sidebar */
        #sidebar {
            width: var(--sidebar-width);
            min-height: 100vh;
            background: var(--primary-dark);
            position: fixed;
            top: 0; left: 0;
            transition: transform .3s;
            z-index: 1040;
            overflow-y: auto;
        }
        #sidebar .sidebar-brand {
            background: var(--primary);
            padding: 1rem 1.25rem;
            display: flex; align-items: center; gap: .6rem;
            color: #fff; font-weight: 700; font-size: 1.1rem;
            text-decoration: none;
        }
        #sidebar .nav-link {
            color: rgba(255,255,255,.78);
            padding: .55rem 1.25rem;
            border-radius: 0;
            display: flex; align-items: center; gap: .6rem;
            font-size: .93rem;
        }
        #sidebar .nav-link:hover,
        #sidebar .nav-link.active {
            background: rgba(255,255,255,.12);
            color: #fff;
        }
        #sidebar .nav-section {
            color: rgba(255,255,255,.45);
            font-size: .75rem;
            text-transform: uppercase;
            letter-spacing: .08em;
            padding: 1rem 1.25rem .35rem;
        }

        /* Main content */
        #main {
            margin-left: var(--sidebar-width);
            min-height: 100vh;
        }
        #topbar {
            background: #fff;
            border-bottom: 1px solid #e0e0e0;
            padding: .6rem 1.5rem;
            display: flex; align-items: center; justify-content: space-between;
            position: sticky; top: 0; z-index: 1030;
        }
        .content-area { padding: 1.5rem; }

        /* Cards */
        .stat-card { border: none; border-radius: .75rem; box-shadow: 0 2px 8px rgba(0,0,0,.08); }
        .stat-card .icon-wrap {
            width: 48px; height: 48px; border-radius: .5rem;
            display: flex; align-items: center; justify-content: center;
            font-size: 1.4rem;
        }

        /* Badge colors */
        .badge-draft { background: #6c757d; }
        .badge-menunggu { background: #fd7e14; }
        .badge-disetujui { background: #198754; }
        .badge-ditolak { background: #dc3545; }

        @media (max-width: 768px) {
            #sidebar { transform: translateX(-100%); }
            #sidebar.show { transform: translateX(0); }
            #main { margin-left: 0; }
        }
    </style>
    @stack('styles')
</head>
<body>

{{-- Sidebar --}}
<nav id="sidebar">
    <a href="#" class="sidebar-brand">
        <i class="bi bi-basket2-fill"></i> SiPanen
    </a>

    @auth
    <div class="mt-2">
        <div class="nav-section">Menu Utama</div>

        @if(auth()->user()->isAdmin())
            <a href="{{ route('admin.dashboard') }}" class="nav-link @activeRoute('admin.dashboard')">
                <i class="bi bi-speedometer2"></i> Dashboard
            </a>
            <a href="{{ route('admin.users.index') }}" class="nav-link @activeRoute('admin.users.*')">
                <i class="bi bi-people-fill"></i> Pengguna
            </a>
            <a href="{{ route('admin.validasi.index') }}" class="nav-link @activeRoute('admin.validasi.*')">
                <i class="bi bi-clipboard2-check-fill"></i> Validasi Laporan
            </a>
        @endif

        @if(auth()->user()->isPetugas())
            <a href="{{ route('petugas.dashboard') }}" class="nav-link @activeRoute('petugas.dashboard')">
                <i class="bi bi-speedometer2"></i> Dashboard
            </a>
            <a href="{{ route('petugas.laporan.index') }}" class="nav-link @activeRoute('petugas.laporan.*')">
                <i class="bi bi-file-earmark-text-fill"></i> Laporan Panen
            </a>
        @endif

        @if(auth()->user()->isPimpinan())
            <a href="{{ route('pimpinan.dashboard') }}" class="nav-link @activeRoute('pimpinan.dashboard')">
                <i class="bi bi-speedometer2"></i> Dashboard
            </a>
        @endif

        <div class="nav-section mt-2">Akun</div>
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="nav-link w-100 border-0 bg-transparent text-start">
                <i class="bi bi-box-arrow-left"></i> Keluar
            </button>
        </form>
    </div>
    @endauth
</nav>

{{-- Main --}}
<div id="main">
    {{-- Topbar --}}
    <div id="topbar">
        <button class="btn btn-sm btn-outline-secondary d-md-none" id="sidebarToggle">
            <i class="bi bi-list fs-5"></i>
        </button>
        <span class="fw-semibold text-success d-none d-md-block">
            Dinas Pertanian Tanaman Pangan Kab. Polewali Mandar
        </span>
        @auth
        <div class="d-flex align-items-center gap-2">
            <i class="bi bi-person-circle fs-5 text-success"></i>
            <span class="small fw-semibold">{{ auth()->user()->name }}</span>
            <span class="badge bg-success-subtle text-success border border-success-subtle text-capitalize">
                {{ auth()->user()->role }}
            </span>
        </div>
        @endauth
    </div>

    {{-- Content --}}
    <div class="content-area">
        {{-- Flash messages --}}
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="bi bi-check-circle-fill me-2"></i>{{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif
        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="bi bi-exclamation-triangle-fill me-2"></i>{{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @yield('content')
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script>
    document.getElementById('sidebarToggle')?.addEventListener('click', () => {
        document.getElementById('sidebar').classList.toggle('show');
    });
</script>
@stack('scripts')
</body>
</html>
