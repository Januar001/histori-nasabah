@extends('layouts.app')

@section('content')
<div class="row">
    <div class="col-12 mb-4">
        <h1><i class="fas fa-tachometer-alt me-2"></i>Dashboard</h1>
        <p class="text-muted">Ringkasan aktivitas dan performance</p>
    </div>
</div>

<div class="row">
    <!-- Stats Cards -->
    <div class="col-md-4 mb-4">
        <div class="card border-0 shadow-sm">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <h5 class="card-title text-muted">Total Nasabah</h5>
                        <h2 class="text-primary">{{ $totalNasabah }}</h2>
                    </div>
                    <div class="align-self-center">
                        <i class="fas fa-users fa-2x text-primary"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-4 mb-4">
        <div class="card border-0 shadow-sm">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <h5 class="card-title text-muted">Perubahan Bulan Ini</h5>
                        <h2 class="text-warning">{{ $totalPerubahan }}</h2>
                    </div>
                    <div class="align-self-center">
                        <i class="fas fa-exchange-alt fa-2x text-warning"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-4 mb-4">
        <div class="card border-0 shadow-sm">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <h5 class="card-title text-muted">Janji Hari Ini</h5>
                        <h2 class="text-success">{{ $janjiHariIni }}</h2>
                    </div>
                    <div class="align-self-center">
                        <i class="fas fa-handshake fa-2x text-success"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <!-- Performance -->
    <div class="col-md-6 mb-4">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white">
                <h5 class="card-title mb-0"><i class="fas fa-chart-line me-2"></i>Performance Petugas</h5>
            </div>
            <div class="card-body">
                @foreach($performance as $perf)
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <span>{{ $perf->petugas }}</span>
                    <span class="badge bg-primary">{{ $perf->total_perubahan }} perubahan</span>
                </div>
                @endforeach
            </div>
        </div>
    </div>

    <!-- Recent Activities -->
    <div class="col-md-6 mb-4">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white">
                <h5 class="card-title mb-0"><i class="fas fa-history me-2"></i>Aktivitas Terbaru</h5>
            </div>
            <div class="card-body">
                @foreach($recentActivities as $activity)
                <div class="border-start border-primary ps-3 mb-3">
                    <small class="text-muted">{{ $activity->created_at->format('d M Y H:i') }}</small>
                    <p class="mb-1">
                        <strong>{{ $activity->nasabah->namadb }}</strong> - 
                        {{ $activity->kolektibilitas_sebelum }} → {{ $activity->kolektibilitas_sesudah }}
                    </p>
                    <small class="text-muted">Oleh: {{ $activity->petugas }}</small>
                </div>
                @endforeach
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-12">
        <div class="card border-0 shadow-sm">
            <div class="card-body text-center">
                <div class="row">
                    <div class="col-md-4 mb-3">
                        <a href="{{ route('nasabah.index') }}" class="btn btn-outline-primary btn-lg w-100">
                            <i class="fas fa-list me-2"></i>Data Nasabah
                        </a>
                    </div>
                    @if(auth()->user()->role == 'admin')
                    <div class="col-md-4 mb-3">
                        <a href="{{ route('import.show') }}" class="btn btn-outline-success btn-lg w-100">
                            <i class="fas fa-upload me-2"></i>Import Excel
                        </a>
                    </div>
                    @endif
                    <div class="col-md-4 mb-3">
                        <a href="{{ route('nasabah.index') }}?search=" class="btn btn-outline-info btn-lg w-100">
                            <i class="fas fa-search me-2"></i>Cari Nasabah
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection