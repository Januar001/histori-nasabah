<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Histori Nasabah</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        body {
            font-size: 0.9rem;
            background-color: #f8f9fa;
            display: flex;
            min-height: 100vh;
            overflow-x: hidden;
        }
        .sidebar {
            width: 280px;
            flex-shrink: 0;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
            z-index: 1000;
            background-color: white;
        }
        .sidebar .nav-link {
            color: #333;
            padding: 0.7rem 1rem;
            margin: 0.1rem 0;
            border-radius: 0.25rem;
        }
        .sidebar .nav-link:hover, .sidebar .nav-link.active {
            background-color: #0d6efd;
            color: white;
        }
        .sidebar .nav-link i {
            width: 20px;
            text-align: center;
            margin-right: 10px;
        }
        .main-content {
            flex-grow: 1;
            min-width: 0;
            display: flex;
            flex-direction: column;
            min-height: 100vh;
        }
        .navbar-brand {
            font-weight: 600;
        }
        .card {
            border: none;
            box-shadow: 0 0.15rem 1rem rgba(0, 0, 0, 0.05);
        }
        .table-responsive {
            font-size: 0.85rem;
        }

        .bg-custom-orange {
            background-color: #fc7d14 !important;
            color: white !important;
        }
        
        /* Mobile Styles */
        @media (max-width: 768px) {
            body {
                flex-direction: column;
            }
            .sidebar {
                position: fixed;
                top: 0;
                left: -100%;
                width: 280px;
                height: 100vh;
                transition: all 0.3s;
                z-index: 1050;
            }
            .sidebar.show {
                left: 0;
            }
            .main-content {
                width: 100%;
            }
            .mobile-menu-btn {
                display: block;
            }
            
            /* Overlay untuk mobile */
            .sidebar-overlay {
                display: none;
                position: fixed;
                top: 0;
                left: 0;
                width: 100%;
                height: 100%;
                background: rgba(0,0,0,0.5);
                z-index: 1040;
            }
            .sidebar-overlay.show {
                display: block;
            }
        }
        
        /* Desktop Styles */
        @media (min-width: 769px) {
            .mobile-menu-btn {
                display: none;
            }
            .sidebar-overlay {
                display: none !important;
            }
        }
        
        /* Content area styling */
        .content-area {
            flex-grow: 1;
            padding: 1rem;
        }
        
        /* Footer styling */
        .main-footer {
            background-color: white;
            border-top: 1px solid #dee2e6;
            padding: 0.75rem 1rem;
            text-align: center;
            color: #6c757d;
            font-size: 0.85rem;
            margin-top: auto;
        }
    </style>
</head>
<body>
    <!-- Overlay untuk mobile -->
    <div class="sidebar-overlay" id="sidebarOverlay" onclick="toggleSidebar()"></div>

    <div class="sidebar" id="sidebar">
        <div class="p-3 border-bottom">
            <h5 class="text-primary mb-0">
                <i class="fas fa-history me-2"></i>Histori Nasabah
            </h5>
            <small class="text-muted">Management System</small>
        </div>
        
        <nav class="nav flex-column p-3">
            <a href="{{ route('dashboard') }}" class="nav-link {{ request()->is('dashboard') || request()->is('/') ? 'active' : '' }}">
                <i class="fas fa-tachometer-alt"></i> Dashboard
            </a>
            
            <a href="{{ route('nasabah.index') }}" class="nav-link {{ request()->is('nasabah*') && !request()->is('nasabah?kualitas=5') ? 'active' : '' }}">
                <i class="fas fa-users"></i> Data Nasabah
            </a>
            
            <a href="{{ route('petugas.index') }}" class="nav-link {{ request()->is('petugas*') ? 'active' : '' }}">
                <i class="fas fa-user-tie"></i> Data Petugas
            </a>
            
            <div class="nav-link text-muted small mt-3">FILTER CEPAT</div>
            
            <a href="{{ route('nasabah.index') }}?kualitas=5" class="nav-link {{ request()->get('kualitas') == '5' ? 'active' : '' }}">
                <i class="fas fa-exclamation-triangle"></i> Nasabah Macet
            </a>
            
            <a href="{{ route('nasabah.index') }}?kualitas=4" class="nav-link {{ request()->get('kualitas') == '4' ? 'active' : '' }}">
                <i class="fas fa-exclamation-circle"></i> Nasabah Diragukan
            </a>
            
            <a href="{{ route('nasabah.index') }}?kualitas=1" class="nav-link {{ request()->get('kualitas') == '1' ? 'active' : '' }}">
                <i class="fas fa-check-circle"></i> Nasabah Lancar
            </a>

            @if(auth()->user()->role == 'admin')
            <div class="nav-link text-muted small mt-3">ADMIN</div>

            <a href="{{ route('import.show') }}" class="nav-link {{ request()->is('import') ? 'active' : '' }}">
                <i class="fas fa-upload"></i> Import Excel
            </a>

            <a href="{{ route('import.history') }}" class="nav-link {{ request()->is('import/history') ? 'active' : '' }}">
                <i class="fas fa-history"></i> History Import
            </a>

            <a href="{{ route('import.stats') }}" class="nav-link {{ request()->is('import/stats') ? 'active' : '' }}">
                <i class="fas fa-chart-bar"></i> Import Stats
            </a>

            <!-- TAMBAHAN: Menu Riwayat Kolektibilitas -->
            <a href="{{ route('kolektibilitas.history') }}" class="nav-link {{ request()->is('kolektibilitas/history') ? 'active' : '' }}">
                <i class="fas fa-exchange-alt"></i> Riwayat Kolektibilitas
            </a>

            <a href="{{ route('analisis.pergerakan-kol') }}" class="nav-link {{ request()->is('analisis/pergerakan-kol') ? 'active' : '' }}">
                <i class="fas fa-analytics"></i> Analisis Pergerakan
            </a>
            @endif

            <div class="mt-auto p-3 border-top">
                <div class="d-flex align-items-center">
                    <div class="flex-grow-1">
                        <small class="text-muted">Login sebagai</small>
                        <div class="fw-bold">{{ auth()->user()->name }}</div>
                    </div>
                    <form action="{{ route('logout') }}" method="POST" class="d-inline">
                        @csrf
                        <button type="submit" class="btn btn-sm btn-outline-danger">
                            <i class="fas fa-sign-out-alt"></i>
                        </button>
                    </form>
                </div>
            </div>
        </nav>
    </div>

    <div class="main-content">
        <nav class="navbar navbar-light bg-white border-bottom">
            <div class="container-fluid">
                <button class="btn mobile-menu-btn" type="button" onclick="toggleSidebar()">
                    <i class="fas fa-bars"></i>
                </button>
                
                <div class="d-flex align-items-center">
                    <span class="navbar-text me-3 d-none d-md-block">
                        <i class="fas fa-user me-1"></i> {{ auth()->user()->name }}
                    </span>
                    <form action="{{ route('logout') }}" method="POST" class="d-inline">
                        @csrf
                        <button type="submit" class="btn btn-outline-danger btn-sm">
                            <i class="fas fa-sign-out-alt me-1"></i> Logout
                        </button>
                    </form>
                </div>
            </div>
        </nav>

        <div class="content-area">
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="fas fa-check-circle me-2"></i> {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="fas fa-exclamation-triangle me-2"></i> {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            @yield('content')
        </div>
        
        <!-- Footer yang diperbaiki -->
        <footer class="main-footer">
            &copy; {{ date('Y') }} Histori Nasabah &mdash; PT. BPR Buduran Deltapurnama - Januar BPRDP.
        </footer>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function toggleSidebar() {
            const sidebar = document.getElementById('sidebar');
            const overlay = document.getElementById('sidebarOverlay');
            
            sidebar.classList.toggle('show');
            overlay.classList.toggle('show');
        }

        // Close sidebar when clicking on overlay (mobile)
        document.getElementById('sidebarOverlay').addEventListener('click', function() {
            toggleSidebar();
        });

        // Close sidebar when clicking on a link (mobile)
        document.querySelectorAll('.sidebar .nav-link').forEach(link => {
            link.addEventListener('click', function() {
                if (window.innerWidth <= 768) {
                    toggleSidebar();
                }
            });
        });

        // Handle window resize
        window.addEventListener('resize', function() {
            if (window.innerWidth > 768) {
                const sidebar = document.getElementById('sidebar');
                const overlay = document.getElementById('sidebarOverlay');
                sidebar.classList.remove('show');
                overlay.classList.remove('show');
            }
        });
    </script>
    @stack('scripts')
</body>
</html>