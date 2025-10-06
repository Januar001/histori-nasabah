<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Aplikasi Penanganan Nasabah')</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
    @stack('styles')
</head>
<body>
    <div class="sidebar">
        <h4 class="text-white text-center mt-3">Histori Nasabah</h4>
        <hr class="text-white">
        <a href="{{ route('dashboard') }}" class="{{ request()->is('dashboard') ? 'active' : '' }}">
            <i class="fas fa-tachometer-alt"></i> Dashboard
        </a>
        <a href="{{ route('nasabah.index') }}" class="{{ request()->is('nasabah*') ? 'active' : '' }}">
            <i class="fas fa-users"></i> Daftar Nasabah
        </a>
        @if(auth()->user()->role == 'admin')
        <a href="{{ route('petugas.index') }}" class="{{ request()->is('petugas*') ? 'active' : '' }}">
            <i class="fas fa-user-tie"></i> Manajemen Petugas
        </a>
        <a href="{{ route('kolektibilitas.history') }}" class="{{ request()->is('kolektibilitas/history') ? 'active' : '' }}">
            <i class="fas fa-exchange-alt"></i> Riwayat Kolektibilitas
        </a>
        <a href="{{ route('assignment.index') }}" class="{{ request()->is('assignment*') ? 'active' : '' }}">
            <i class="fas fa-user-tag"></i> Penugasan Nasabah
        </a>
        <a href="{{ route('analisis.kolektibilitas-murni') }}" class="{{ request()->is('analisis*') ? 'active' : '' }}">
            <i class="fas fa-vial"></i> Analisis Kol Murni
        </a>
        <a href="{{ route('import.form') }}" class="{{ request()->is('import*') ? 'active' : '' }}">
            <i class="fas fa-file-excel"></i> Import Data
        </a>
        @endif
    </div>

    <div class="main-content">
        <nav class="navbar navbar-expand-lg navbar-light bg-light">
            <div class="container-fluid">
                <span class="navbar-brand">Selamat Datang, {{ auth()->user()->nama_lengkap }}</span>
                <form action="{{ route('logout') }}" method="post">
                    @csrf
                    <button type="submit" class="btn btn-danger">
                        <i class="fas fa-sign-out-alt"></i> Logout
                    </button>
                </form>
            </div>
        </nav>
        <main class="container-fluid mt-4">
            @if(session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif
            @if(session('error'))
                <div class="alert alert-danger">{{ session('error') }}</div>
            @endif
            @yield('content')
        </main>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    @stack('scripts')
</body>
</html>