<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ config('app.name', 'Absensi Pegawai') }}</title>
    <link rel="icon" type="image/x-icon" href="{{ asset('/images/logo.png') }}">

    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link
        href="https://fonts.googleapis.com/css2?family=DM+Sans:ital,opsz,wght@0,9..40,300;0,9..40,400;0,9..40,500;0,9..40,600;0,9..40,700;1,9..40,300&family=Playfair+Display:ital,wght@0,700;1,600&display=swap"
        rel="stylesheet" />
    <!-- Template CSS -->
    <link rel="stylesheet" href="{{ asset('css/templatemo-622-clearwave.css') }}" />
    <!-- Select2 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css"
        rel="stylesheet" />
    <style>
        :root {
            --sidebar-width: 280px;
        }

        body {
            font-family: 'DM Sans', sans-serif;
            background: var(--bg);
            color: var(--text-1);
        }

        /* Subtle Noise Texture */
        body::before {
            content: '';
            position: fixed;
            inset: 0;
            pointer-events: none;
            z-index: 0;
            opacity: 0.022;
            background-image: url("data:image/svg+xml,%3Csvg viewBox='0 0 256 256' xmlns='http://www.w3.org/2000/svg'%3E%3Cfilter id='noise'%3E%3CfeTurbulence type='fractalNoise' baseFrequency='0.9' numOctaves='4' stitchTiles='stitch'/%3E%3C/filter%3E%3Crect width='100%25' height='100%25' filter='url(%23noise)'/%3E%3C/svg%3E");
            background-size: 128px 128px;
        }

        /* Sidebar Styling */
        .sidebar {
            height: 100vh;
            width: var(--sidebar-width);
            position: fixed;
            top: 0;
            left: 0;
            background: var(--surface-2);
            border-right: 1px solid var(--border);
            padding: 30px 20px;
            z-index: 1001;
            transition: all 0.4s var(--silk);
            box-shadow: var(--shadow-sm);
            overflow-y: auto;
        }

        .sidebar-brand {
            margin-bottom: 30px;
            padding: 0 10px;
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .sidebar-brand img {
            max-height: auto;
            width: 150px;
            object-fit: contain;
        }

        .sidebar-brand span {
            color: var(--accent);
        }

        .sidebar-link {
            padding: 12px 18px;
            color: var(--text-2);
            text-decoration: none;
            display: flex;
            align-items: center;
            gap: 12px;
            border-radius: 12px;
            transition: all 0.3s var(--silk);
            font-weight: 500;
            font-size: 0.95rem;
            margin-bottom: 4px;
        }

        .sidebar-link i {
            width: 20px;
            font-size: 1.1rem;
            color: var(--text-3);
            transition: color 0.3s;
        }

        .sidebar-link:hover {
            background: var(--accent-ghost);
            color: var(--accent);
        }

        .sidebar-link:hover i {
            color: var(--accent);
        }

        .sidebar-link.active {
            background: var(--accent);
            color: white;
            box-shadow: 0 4px 12px rgba(26, 122, 110, 0.2);
        }

        .sidebar-link.active i {
            color: white;
        }

        .sidebar-section-title {
            font-size: 0.7rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.1em;
            color: var(--text-3);
            margin: 25px 0 10px 18px;
        }

        /* Main Layout */
        .main-wrapper {
            margin-left: var(--sidebar-width);
            transition: all 0.4s var(--silk);
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }

        .navbar {
            background: rgba(232, 244, 242, 0.8);
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
            border-bottom: 1px solid var(--border);
            padding: 15px 30px;
            position: sticky;
            top: 0;
            z-index: 1000;
        }

        .main-content {
            padding: 40px;
            flex-grow: 1;
        }

        /* Responsive */
        @media (max-width: 992px) {
            .sidebar {
                transform: translateX(-100%);
            }

            .sidebar.active {
                transform: translateX(0);
            }

            .main-wrapper {
                margin-left: 0;
            }
        }

        /* Premium Elements */
        .card {
            background: var(--surface-2);
            border-radius: var(--radius);
            border: 1px solid var(--border);
            box-shadow: var(--shadow-sm);
            transition: all 0.3s var(--silk);
            overflow: hidden;
        }

        .card:hover {
            box-shadow: var(--shadow-md);
            transform: translateY(-2px);
        }

        .btn-primary {
            background: var(--accent);
            border: none;
            border-radius: 100px;
            padding: 10px 24px;
            font-weight: 600;
            box-shadow: 0 4px 12px rgba(26, 122, 110, 0.2);
            transition: all 0.3s var(--silk);
        }

        .btn-primary:hover {
            background: var(--accent-mid);
            transform: scale(1.02);
            box-shadow: 0 6px 16px rgba(26, 122, 110, 0.3);
        }

        .text-primary {
            color: var(--accent) !important;
        }

        .alert {
            border-radius: var(--radius);
            border: 1px solid transparent;
        }

        .alert-success {
            background: var(--accent-ghost);
            border-color: var(--accent-border);
            color: var(--accent);
        }
    </style>
</head>

<body>

    <!-- Sidebar -->
    <div class="sidebar" id="sidebar">
        <div class="sidebar-brand">
            <img src="{{ asset('images/logo.png') }}" alt="Logo">
        </div>

        <div class="sidebar-section-title">Menu Utama</div>

        @can('view-dashboard')
            <a href="{{ route('dashboard') }}" class="sidebar-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                <i class="fas fa-tachometer-alt"></i> Dashboard
            </a>
        @endcan

        <div class="sidebar-section-title">Master Data</div>

        @can('manage-ruangan')
            <a href="{{ route('ruangan.index') }}"
                class="sidebar-link {{ request()->routeIs('ruangan.*') ? 'active' : '' }}">
                <i class="fas fa-door-open"></i> Ruangan
            </a>
        @endcan

        @can('manage-pegawai')
            <a href="{{ route('pegawai.index') }}"
                class="sidebar-link {{ request()->routeIs('pegawai.*') ? 'active' : '' }}">
                <i class="fas fa-users"></i> Pegawai
            </a>
        @endcan

        @can('manage-shift')
            <a href="{{ route('shift.index') }}" class="sidebar-link {{ request()->routeIs('shift.*') ? 'active' : '' }}">
                <i class="fas fa-calendar-alt"></i> Master Jadwal
            </a>
        @endcan

        <div class="sidebar-section-title">Report</div>
        @if (auth()->user()->isAdmin())
            <a href="{{ route('report.index', 'shift') }}"
                class="sidebar-link {{ request()->fullUrlIs(route('report.index', 'shift')) ? 'active' : '' }}">
                <i class="fas fa-file-invoice"></i> Report Jadwal Shift
            </a>
            <a href="{{ route('report.index', 'non_shift') }}"
                class="sidebar-link {{ request()->fullUrlIs(route('report.index', 'non_shift')) ? 'active' : '' }}">
                <i class="fas fa-file-alt"></i> Report Jadwal Non Shift
            </a>
            <a href="{{ route('cuti.index') }}"
                class="sidebar-link {{ request()->routeIs('cuti.*') ? 'active' : '' }}">
                <i class="fas fa-user-clock"></i> Data Cuti
            </a>
        @endif

        <div class="sidebar-section-title">Operasional</div>
        @can('manage-jadwal')
            <a href="{{ route('jadwal.index') }}"
                class="sidebar-link {{ request()->routeIs('jadwal.*') ? 'active' : '' }}">
                <i class="fas fa-calendar-check"></i> Jadwal Kerja
            </a>
        @endcan

        @if (auth()->user()->can('manage-users') || auth()->user()->can('manage-roles'))
            <div class="sidebar-section-title">Akses & Keamanan</div>
            @can('manage-users')
                <a href="{{ route('user.index') }}"
                    class="sidebar-link {{ request()->routeIs('user.*') ? 'active' : '' }}">
                    <i class="fas fa-user-shield"></i> Manajemen User
                </a>
            @endcan
            @can('manage-roles')
                <a href="{{ route('role.index') }}"
                    class="sidebar-link {{ request()->routeIs('role.*') ? 'active' : '' }}">
                    <i class="fas fa-key"></i> Role & Permission
                </a>
            @endcan
        @endif

        <div class="sidebar-section-title">Lainnya</div>
        <form action="{{ route('logout') }}" method="POST">
            @csrf
            <button type="submit" class="sidebar-link bg-transparent border-0 w-100 text-start">
                <i class="fas fa-sign-out-alt"></i> Logout
            </button>
        </form>
    </div>

    <div class="main-wrapper">
        <!-- Top Navbar -->
        <nav class="navbar navbar-expand-lg" id="mainNav">
            <div class="container-fluid p-0">
                <button class="btn btn-light d-lg-none me-3" id="sidebarToggle">
                    <i class="fas fa-bars"></i>
                </button>
                <div class="ms-auto d-flex align-items-center">
                    <span class="me-3 text-muted d-none d-md-block">Welcome,
                        <strong>{{ auth()->user()->name }}</strong>
                        ({{ ucfirst(auth()->user()->role) }})</span>
                    <div class="dropdown">
                        <img src="https://ui-avatars.com/api/?name={{ auth()->user()->name }}&background=1A7A6E&color=fff"
                            class="rounded-circle shadow-sm" width="40" height="40" data-bs-toggle="dropdown"
                            style="cursor:pointer">
                        <ul class="dropdown-menu dropdown-menu-end shadow border-0 mt-3 p-2"
                            style="border-radius: 12px;">
                            <li><a class="dropdown-item py-2 px-3" href="#"><i class="fas fa-user me-2"></i>
                                    Profile</a></li>
                            <li>
                                <hr class="dropdown-divider mx-2">
                            </li>
                            <li>
                                <form action="{{ route('logout') }}" method="POST">
                                    @csrf
                                    <button type="submit" class="dropdown-item text-danger py-2 px-3"><i
                                            class="fas fa-sign-out-alt me-2"></i> Logout</button>
                                </form>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </nav>

        <!-- Main Content -->
        <main class="main-content">
            @if (session('success'))
                <div class="alert alert-success alert-dismissible fade show shadow-sm mb-4" role="alert">
                    <div class="d-flex align-items-center">
                        <i class="fas fa-check-circle me-3 fs-4"></i>
                        <div>
                            <strong>Berhasil!</strong><br>
                            {{ session('success') }}
                        </div>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            @if (session('error'))
                <div class="alert alert-danger alert-dismissible fade show shadow-sm mb-4" role="alert">
                    <div class="d-flex align-items-center">
                        <i class="fas fa-exclamation-circle me-3 fs-4"></i>
                        <div>
                            <strong>Error!</strong><br>
                            {{ session('error') }}
                        </div>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            @yield('content')
        </main>
    </div>

    <!-- Scripts -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script src="{{ asset('js/templatemo-622-clearwave.js') }}"></script>
    <script>
        document.getElementById('sidebarToggle')?.addEventListener('click', function() {
            document.getElementById('sidebar').classList.toggle('active');
        });
    </script>
    @stack('scripts')
</body>

</html>
