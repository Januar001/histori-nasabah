@extends('layouts.app')

@section('content')
    <div class="row">
        <div class="col-12 mb-3">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h4 class="mb-0"><i class="fas fa-tachometer-alt me-2 text-primary"></i>Dashboard</h4>
                    <small class="text-muted">Ringkasan {{ now()->translatedFormat('F Y') }}</small>
                </div>
                <div class="d-flex gap-2">
                    <button class="btn btn-sm btn-outline-secondary" onclick="location.reload()">
                        <i class="fas fa-refresh"></i>
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Statistik Utama -->
    <div class="row g-2 mb-4">
        <div class="col-6 col-md-3">
            <div class="card border-0 h-100">
                <div class="card-body p-3 text-center">
                    <div class="text-primary mb-2">
                        <i class="fas fa-users fa-2x"></i>
                    </div>
                    <h4 class="mb-1">{{ $totalNasabah }}</h4>
                    <small class="text-muted">Total Nasabah</small>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="card border-0 h-100">
                <div class="card-body p-3 text-center">
                    <div class="text-warning mb-2">
                        <i class="fas fa-exchange-alt fa-2x"></i>
                    </div>
                    <h4 class="mb-1">{{ $totalPerubahan }}</h4>
                    <small class="text-muted">Perubahan Bulan Ini</small>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="card border-0 h-100">
                <div class="card-body p-3 text-center">
                    <div class="text-success mb-2">
                        <i class="fas fa-user-check fa-2x"></i>
                    </div>
                    <h4 class="mb-1">{{ $nasabahDitangani }}</h4>
                    <small class="text-muted">Ditangani Petugas</small>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="card border-0 h-100">
                <div class="card-body p-3 text-center">
                    <div class="text-info mb-2">
                        <i class="fas fa-users-cog fa-2x"></i>
                    </div>
                    <h4 class="mb-1">{{ $petugasAktif }}</h4>
                    <small class="text-muted">Petugas Aktif</small>
                </div>
            </div>
        </div>
    </div>

    <!-- Tab Navigation -->
    <ul class="nav nav-tabs mb-4" id="dashboardTabs" role="tablist">
        <li class="nav-item" role="presentation">
            <button class="nav-link active" id="overview-tab" data-bs-toggle="tab" data-bs-target="#overview" type="button"
                role="tab">
                <i class="fas fa-chart-pie me-1"></i>Overview
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="janji-tab" data-bs-toggle="tab" data-bs-target="#janji" type="button"
                role="tab">
                <i class="fas fa-calendar-alt me-1"></i>Janji Bayar
                <span
                    class="badge bg-primary ms-1">{{ $nasabahJanjiHariIni->count() + $nasabahJanjiMendatang->count() + $janjiPending->count() }}</span>
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="aktivitas-tab" data-bs-toggle="tab" data-bs-target="#aktivitas" type="button"
                role="tab">
                <i class="fas fa-exchange-alt me-1"></i>Aktivitas
                <span class="badge bg-warning ms-1">{{ $aktivitasPerubahan->count() }}</span>
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="performance-tab" data-bs-toggle="tab" data-bs-target="#performance" type="button"
                role="tab">
                <i class="fas fa-trophy me-1"></i>Performance
            </button>
        </li>
    </ul>

    <!-- Tab Content -->
    <div class="tab-content" id="dashboardTabsContent">

        <!-- Tab 1: Overview -->
        <div class="tab-pane fade show active" id="overview" role="tabpanel">
            <div class="row g-3">
                <!-- Charts -->
                <div class="col-12 col-lg-8">
                    <div class="card border-0">
                        <div class="card-header bg-white py-2">
                            <h6 class="mb-0"><i class="fas fa-chart-line me-2 text-primary"></i>Trend Perubahan</h6>
                        </div>
                        <div class="card-body">
                            <canvas id="trendChart" height="120"></canvas>
                        </div>
                    </div>
                </div>

                <div class="col-12 col-lg-4">
                    <div class="card border-0 h-100">
                        <div class="card-header bg-white py-2 d-flex justify-content-between align-items-center">
                            <h6 class="mb-0"><i class="fas fa-trophy me-2 text-warning"></i>Performance Petugas</h6>
                            <small class="text-muted">Bulan Ini</small>
                        </div>
                        <div class="card-body">
                            @foreach ($performance as $perf)
                                <div class="mb-3">
                                    <div class="d-flex justify-content-between align-items-center mb-1">
                                        <span class="fw-bold text-truncate"
                                            style="max-width: 120px;">{{ $perf->petugas }}</span>
                                        <small
                                            class="text-muted">{{ $perf->berhasil_memperbaiki }}/{{ $perf->total_perubahan }}</small>
                                    </div>
                                    @php
                                        $successRate =
                                            $perf->total_perubahan > 0
                                                ? ($perf->berhasil_memperbaiki / $perf->total_perubahan) * 100
                                                : 0;
                                    @endphp
                                    <div class="progress mb-1" style="height: 6px;">
                                        <div class="progress-bar bg-success" style="width: {{ $successRate }}%"></div>
                                    </div>
                                    <small class="text-muted d-flex justify-content-between">
                                        <span>Success Rate</span>
                                        <span class="fw-bold">{{ number_format($successRate, 1) }}%</span>
                                    </small>
                                    <small class="text-muted d-block">
                                        <i class="fas fa-users me-1"></i> {{ $perf->divisi }}
                                    </small>
                                </div>
                            @endforeach

                            @if ($performance->isEmpty())
                                <div class="text-center text-muted py-3">
                                    <i class="fas fa-chart-bar fa-2x mb-2"></i>
                                    <p class="mb-0">Belum ada data performance</p>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Distribution Charts -->
                <div class="col-12 col-md-6">
                    <div class="card border-0">
                        <div class="card-header bg-white py-2">
                            <h6 class="mb-0"><i class="fas fa-chart-pie me-2 text-info"></i>Distribusi Kolektibilitas
                            </h6>
                        </div>
                        <div class="card-body">
                            <canvas id="distributionChart" height="200"></canvas>
                        </div>
                        <div class="card-footer bg-white py-2">
                            <div class="row text-center">
                                @foreach ($distribusiKolektibilitas as $dist)
                                    <div class="col">
                                        <small class="text-muted d-block">{{ $dist['kualitas'] }}</small>
                                        <strong class="text-dark">{{ $dist['total'] }}</strong>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-12 col-md-6">
                    <div class="card border-0">
                        <div class="card-header bg-white py-2">
                            <h6 class="mb-0"><i class="fas fa-chart-bar me-2 text-success"></i>Status Janji Bayar</h6>
                        </div>
                        <div class="card-body">
                            <canvas id="janjiStatusChart" height="200"></canvas>
                        </div>
                    </div>
                </div>

                <!-- Statistik Perubahan -->
                <div class="col-12">
                    <div class="row g-3">
                        <div class="col-12 col-md-6 col-lg-3">
                            <div class="card border-0">
                                <div class="card-body text-center p-3">
                                    @php
                                        $memperbaiki = $statistikPerubahan
                                            ->where('jenis_perubahan', 'Memperbaiki')
                                            ->first();
                                        $totalMemperbaiki = $memperbaiki ? $memperbaiki->total : 0;
                                        $totalAll = $statistikPerubahan->sum('total');
                                        $percentage = $totalAll > 0 ? ($totalMemperbaiki / $totalAll) * 100 : 0;
                                    @endphp
                                    <div class="text-success mb-2">
                                        <i class="fas fa-arrow-up fa-2x"></i>
                                    </div>
                                    <h4 class="mb-1">{{ $totalMemperbaiki }}</h4>
                                    <small class="text-muted">Perubahan Membaik</small>
                                    <div class="progress mt-2" style="height: 4px;">
                                        <div class="progress-bar bg-success" style="width: {{ $percentage }}%"></div>
                                    </div>
                                    <small class="text-muted">{{ number_format($percentage, 1) }}% dari total</small>
                                </div>
                            </div>
                        </div>
                        <div class="col-12 col-md-6 col-lg-3">
                            <div class="card border-0">
                                <div class="card-body text-center p-3">
                                    @php
                                        $memburuk = $statistikPerubahan->where('jenis_perubahan', 'Memburuk')->first();
                                        $totalMemburuk = $memburuk ? $memburuk->total : 0;
                                        $percentage2 = $totalAll > 0 ? ($totalMemburuk / $totalAll) * 100 : 0;
                                    @endphp
                                    <div class="text-danger mb-2">
                                        <i class="fas fa-arrow-down fa-2x"></i>
                                    </div>
                                    <h4 class="mb-1">{{ $totalMemburuk }}</h4>
                                    <small class="text-muted">Perubahan Memburuk</small>
                                    <div class="progress mt-2" style="height: 4px;">
                                        <div class="progress-bar bg-danger" style="width: {{ $percentage2 }}%"></div>
                                    </div>
                                    <small class="text-muted">{{ number_format($percentage2, 1) }}% dari total</small>
                                </div>
                            </div>
                        </div>
                        <div class="col-12 col-md-6 col-lg-3">
                            <div class="card border-0">
                                <div class="card-body text-center p-3">
                                    @php
                                        $tidakBerubah = $statistikPerubahan
                                            ->where('jenis_perubahan', 'Tidak Berubah')
                                            ->first();
                                        $totalTidakBerubah = $tidakBerubah ? $tidakBerubah->total : 0;
                                        $percentage3 = $totalAll > 0 ? ($totalTidakBerubah / $totalAll) * 100 : 0;
                                    @endphp
                                    <div class="text-warning mb-2">
                                        <i class="fas fa-minus fa-2x"></i>
                                    </div>
                                    <h4 class="mb-1">{{ $totalTidakBerubah }}</h4>
                                    <small class="text-muted">Tidak Berubah</small>
                                    <div class="progress mt-2" style="height: 4px;">
                                        <div class="progress-bar bg-warning" style="width: {{ $percentage3 }}%"></div>
                                    </div>
                                    <small class="text-muted">{{ number_format($percentage3, 1) }}% dari total</small>
                                </div>
                            </div>
                        </div>
                        <div class="col-12 col-md-6 col-lg-3">
                            <div class="card border-0">
                                <div class="card-body text-center p-3">
                                    <div class="text-info mb-2">
                                        <i class="fas fa-exchange-alt fa-2x"></i>
                                    </div>
                                    <h4 class="mb-1">{{ $totalAll }}</h4>
                                    <small class="text-muted">Total Perubahan</small>
                                    <div class="progress mt-2" style="height: 4px;">
                                        <div class="progress-bar bg-info" style="width: 100%"></div>
                                    </div>
                                    <small class="text-muted">Bulan {{ now()->translatedFormat('F Y') }}</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Tab 2: Janji Bayar -->
        <div class="tab-pane fade" id="janji" role="tabpanel">
            <div class="row g-3">
                <!-- Janji Bayar Hari Ini -->
                <div class="col-12 col-lg-4">
                    <div class="card border-0 h-100">
                        <div class="card-header bg-white py-2 d-flex justify-content-between align-items-center">
                            <h6 class="mb-0"><i class="fas fa-calendar-day me-2 text-primary"></i>Janji Bayar Hari Ini
                            </h6>
                            <span class="badge bg-primary">{{ $nasabahJanjiHariIni->count() }}</span>
                        </div>
                        <div class="card-body p-0">
                            <div class="list-group list-group-flush">
                                @forelse($nasabahJanjiHariIni as $nasabah)
                                    <a href="{{ route('nasabah.show', $nasabah->id) }}"
                                        class="list-group-item list-group-item-action">
                                        <div class="d-flex justify-content-between align-items-start">
                                            <div class="flex-grow-1">
                                                <strong class="d-block text-truncate">{{ $nasabah->namadb }}</strong>
                                                <small class="text-muted">
                                                    @foreach ($nasabah->janjiBayar as $janji)
                                                        @if ($janji->tanggal_janji->isToday())
                                                            <div>
                                                                <i class="fas fa-clock me-1"></i>
                                                                Rp {{ number_format($janji->nominal_janji, 0, ',', '.') }}
                                                                @if ($janji->keterangan)
                                                                    • {{ Str::limit($janji->keterangan, 30) }}
                                                                @endif
                                                            </div>
                                                        @endif
                                                    @endforeach
                                                </small>
                                            </div>
                                            <div class="text-end">
                                                <small class="text-muted d-block">
                                                    {{ $nasabah->nocif }}
                                                </small>
                                                <span class="badge bg-warning text-dark">Hari Ini</span>
                                            </div>
                                        </div>
                                    </a>
                                @empty
                                    <div class="list-group-item text-center text-muted py-3">
                                        <i class="fas fa-calendar-times fa-2x mb-2"></i>
                                        <p class="mb-0">Tidak ada janji bayar hari ini</p>
                                    </div>
                                @endforelse
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Janji Bayar Mendatang -->
                <div class="col-12 col-lg-4">
                    <div class="card border-0 h-100">
                        <div class="card-header bg-white py-2 d-flex justify-content-between align-items-center">
                            <h6 class="mb-0"><i class="fas fa-calendar-alt me-2 text-info"></i>Janji Bayar Mendatang
                            </h6>
                            <span class="badge bg-info">{{ $nasabahJanjiMendatang->count() }}</span>
                        </div>
                        <div class="card-body p-0">
                            <div class="list-group list-group-flush">
                                @forelse($nasabahJanjiMendatang as $nasabah)
                                    <a href="{{ route('nasabah.show', $nasabah->id) }}"
                                        class="list-group-item list-group-item-action">
                                        <div class="d-flex justify-content-between align-items-start">
                                            <div class="flex-grow-1">
                                                <strong class="d-block text-truncate">{{ $nasabah->namadb }}</strong>
                                                <small class="text-muted">
                                                    @foreach ($nasabah->janjiBayar->take(1) as $janji)
                                                        <div>
                                                            <i class="fas fa-calendar me-1"></i>
                                                            {{ $janji->tanggal_janji->translatedFormat('d M Y') }}
                                                        </div>
                                                        <div>
                                                            <i class="fas fa-money-bill me-1"></i>
                                                            Rp {{ number_format($janji->nominal_janji, 0, ',', '.') }}
                                                        </div>
                                                    @endforeach
                                                </small>
                                            </div>
                                            <div class="text-end">
                                                <small class="text-muted d-block">
                                                    {{ $nasabah->nocif }}
                                                </small>
                                                @foreach ($nasabah->janjiBayar->take(1) as $janji)
                                                    <span class="badge bg-secondary">
                                                        {{ $janji->tanggal_janji->diffForHumans() }}
                                                    </span>
                                                @endforeach
                                            </div>
                                        </div>
                                    </a>
                                @empty
                                    <div class="list-group-item text-center text-muted py-3">
                                        <i class="fas fa-calendar-plus fa-2x mb-2"></i>
                                        <p class="mb-0">Tidak ada janji bayar mendatang</p>
                                    </div>
                                @endforelse
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Janji Bayar Pending -->
                <div class="col-12 col-lg-4">
                    <div class="card border-0 h-100">
                        <div class="card-header bg-white py-2 d-flex justify-content-between align-items-center">
                            <h6 class="mb-0"><i class="fas fa-clock me-2 text-warning"></i>Menunggu Konfirmasi</h6>
                            <span class="badge bg-warning">{{ $janjiPending->count() }}</span>
                        </div>
                        <div class="card-body p-0">
                            <div class="list-group list-group-flush">
                                @forelse($janjiPending as $janji)
                                    <a href="{{ route('nasabah.show', $janji->nasabah->id) }}"
                                        class="list-group-item list-group-item-action">
                                        <div class="d-flex justify-content-between align-items-start">
                                            <div class="flex-grow-1">
                                                <strong
                                                    class="d-block text-truncate">{{ $janji->nasabah->namadb }}</strong>
                                                <small class="text-muted">
                                                    <div>
                                                        <i class="fas fa-calendar me-1"></i>
                                                        {{ $janji->tanggal_janji->translatedFormat('d M Y') }}
                                                    </div>
                                                    <div>
                                                        <i class="fas fa-money-bill me-1"></i>
                                                        Rp {{ number_format($janji->nominal_janji, 0, ',', '.') }}
                                                    </div>
                                                    @if ($janji->keterangan)
                                                        <div class="text-truncate" style="max-width: 200px;">
                                                            {{ $janji->keterangan }}
                                                        </div>
                                                    @endif
                                                </small>
                                            </div>
                                            <div class="text-end">
                                                <small class="text-muted d-block">
                                                    {{ $janji->nasabah->nocif }}
                                                </small>
                                                <span class="badge bg-warning text-dark">Pending</span>
                                            </div>
                                        </div>
                                    </a>
                                @empty
                                    <div class="list-group-item text-center text-muted py-3">
                                        <i class="fas fa-check-circle fa-2x mb-2"></i>
                                        <p class="mb-0">Tidak ada janji menunggu konfirmasi</p>
                                    </div>
                                @endforelse
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Tab 3: Aktivitas -->
        <div class="tab-pane fade" id="aktivitas" role="tabpanel">
            <div class="row g-3">
                <!-- Aktivitas Terbaru -->
                <div class="col-12 col-lg-8">
                    <div class="card border-0">
                        <div class="card-header bg-white py-2 d-flex justify-content-between align-items-center">
                            <h6 class="mb-0"><i class="fas fa-history me-2 text-primary"></i>Aktivitas Terbaru Petugas
                            </h6>
                            <a href="{{ route('nasabah.index') }}" class="btn btn-sm btn-outline-primary">
                                Lihat Semua
                            </a>
                        </div>
                        <div class="card-body p-0">
                            <div class="list-group list-group-flush">
                                @foreach ($recentChanges as $activity)
                                    <div class="list-group-item">
                                        <div class="d-flex justify-content-between align-items-start">
                                            <div class="flex-grow-1">
                                                <div class="d-flex justify-content-between align-items-center mb-1">
                                                    <strong class="text-truncate me-2" style="max-width: 150px;">
                                                        {{ $activity->nasabah->namadb }}
                                                    </strong>
                                                    <small
                                                        class="text-muted">{{ $activity->created_at->format('d M H:i') }}</small>
                                                </div>
                                                <div class="d-flex align-items-center mb-1">
                                                    <span class="badge bg-light text-dark me-2">
                                                        {{ \App\Helpers\QualityHelper::getQualityLabel($activity->kolektibilitas_sebelum) }}
                                                    </span>
                                                    <i class="fas fa-arrow-right text-muted me-2"></i>
                                                    <span
                                                        class="badge {{ \App\Helpers\QualityHelper::getQualityBadge($activity->kolektibilitas_sesudah) }}">
                                                        {{ \App\Helpers\QualityHelper::getQualityLabel($activity->kolektibilitas_sesudah) }}
                                                    </span>
                                                </div>
                                                <div class="d-flex justify-content-between align-items-center">
                                                    <small class="text-muted">
                                                        <i class="fas fa-user-tie me-1"></i> {{ $activity->nama_petugas }}
                                                        @if ($activity->divisi_petugas)
                                                            <span
                                                                class="badge bg-secondary ms-1">{{ $activity->divisi_petugas }}</span>
                                                        @endif
                                                    </small>
                                                </div>
                                            </div>
                                            <a href="{{ route('nasabah.show', $activity->nasabah->id) }}"
                                                class="btn btn-sm btn-outline-primary ms-2">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Perubahan Signifikan -->
                <div class="col-12 col-lg-4">
                    <div class="card border-0">
                        <div class="card-header bg-white py-2">
                            <h6 class="mb-0"><i class="fas fa-exclamation-triangle me-2 text-danger"></i>Perubahan
                                Signifikan</h6>
                        </div>
                        <div class="card-body">
                            @foreach ($topChanges as $change)
                                <div
                                    class="border-start 
                            @if ($change->kolektibilitas_sesudah == '5') border-danger
                            @elseif($change->kolektibilitas_sesudah == '1') border-success
                            @else border-warning @endif ps-2 mb-3">

                                    <div class="d-flex justify-content-between align-items-start mb-1">
                                        <a href="{{ route('nasabah.show', $change->nasabah->id) }}"
                                            class="text-decoration-none flex-grow-1">
                                            <strong class="d-block text-truncate">{{ $change->nasabah->namadb }}</strong>
                                        </a>
                                        <span
                                            class="badge {{ \App\Helpers\QualityHelper::getQualityBadge($change->kolektibilitas_sesudah) }}">
                                            {{ \App\Helpers\QualityHelper::getQualityLabel($change->kolektibilitas_sesudah) }}
                                        </span>
                                    </div>

                                    <div class="d-flex align-items-center mb-1">
                                        <small class="text-danger me-2">
                                            {{ \App\Helpers\QualityHelper::getQualityLabel($change->kolektibilitas_sebelum) }}
                                        </small>
                                        <i class="fas fa-arrow-right text-muted me-2"></i>
                                        <small class="text-success">
                                            {{ \App\Helpers\QualityHelper::getQualityLabel($change->kolektibilitas_sesudah) }}
                                        </small>
                                    </div>
                                    <small class="text-muted d-block">
                                        <i class="fas fa-user-tie me-1"></i> {{ $change->nama_petugas }}
                                        • {{ $change->created_at->format('d M') }}
                                    </small>
                                </div>
                            @endforeach

                            @if ($topChanges->isEmpty())
                                <div class="text-center text-muted py-3">
                                    <i class="fas fa-check-circle fa-2x mb-2"></i>
                                    <p class="mb-0">Tidak ada perubahan signifikan</p>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Tabel Aktivitas Perubahan -->
                <div class="col-12">
                    <div class="card border-0">
                        <div class="card-header bg-white py-2 d-flex justify-content-between align-items-center">
                            <h6 class="mb-0"><i class="fas fa-exchange-alt me-2 text-primary"></i>Riwayat Perubahan
                                Kolektibilitas</h6>
                            <a href="{{ route('kolektibilitas.history') }}" class="btn btn-sm btn-outline-primary">
                                Lihat Semua Riwayat
                            </a>
                        </div>
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table table-hover mb-0">
                                    <thead class="table-light">
                                        <tr>
                                            <th width="20%">Nasabah</th>
                                            <th width="15%">Perubahan</th>
                                            <th width="15%">Petugas</th>
                                            <th width="15%">Divisi</th>
                                            <th width="20%">Keterangan</th>
                                            <th width="15%">Waktu</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($aktivitasPerubahan as $aktivitas)
                                            <tr>
                                                <td>
                                                    <div class="d-flex align-items-center">
                                                        <a href="{{ route('nasabah.show', $aktivitas->nasabah->id) }}"
                                                            class="text-decoration-none">
                                                            <strong>{{ $aktivitas->nasabah->namadb }}</strong>
                                                        </a>
                                                    </div>
                                                    <small class="text-muted d-block">
                                                        CIF: {{ $aktivitas->nasabah->nocif }}
                                                    </small>
                                                </td>
                                                <td>
                                                    <div class="d-flex align-items-center">
                                                        <span
                                                            class="badge {{ \App\Helpers\QualityHelper::getQualityBadge($aktivitas->kolektibilitas_sebelum) }} me-1">
                                                            {{ \App\Helpers\QualityHelper::getQualityLabel($aktivitas->kolektibilitas_sebelum) }}
                                                        </span>
                                                        <i class="fas fa-arrow-right text-muted mx-1 small"></i>
                                                        <span
                                                            class="badge {{ \App\Helpers\QualityHelper::getQualityBadge($aktivitas->kolektibilitas_sesudah) }}">
                                                            {{ \App\Helpers\QualityHelper::getQualityLabel($aktivitas->kolektibilitas_sesudah) }}
                                                        </span>
                                                    </div>
                                                    @php
                                                        $statusPerubahan =
                                                            $aktivitas->kolektibilitas_sesudah -
                                                            $aktivitas->kolektibilitas_sebelum;
                                                    @endphp
                                                    @if ($statusPerubahan < 0)
                                                        <small class="text-success">
                                                            <i class="fas fa-arrow-up"></i> Membaik
                                                        </small>
                                                    @elseif($statusPerubahan > 0)
                                                        <small class="text-danger">
                                                            <i class="fas fa-arrow-down"></i> Memburuk
                                                        </small>
                                                    @else
                                                        <small class="text-muted">
                                                            <i class="fas fa-minus"></i> Tidak berubah
                                                        </small>
                                                    @endif
                                                </td>
                                                <td>
                                                    @if ($aktivitas->petugasRelasi)
                                                        <span
                                                            class="fw-medium">{{ $aktivitas->petugasRelasi->nama_petugas }}</span>
                                                    @else
                                                        <span class="text-muted">-</span>
                                                    @endif
                                                </td>
                                                <td>
                                                    @if ($aktivitas->petugasRelasi)
                                                        <span
                                                            class="badge bg-secondary">{{ $aktivitas->petugasRelasi->divisi }}</span>
                                                    @else
                                                        <span class="text-muted">-</span>
                                                    @endif
                                                </td>
                                                <td>
                                                    <small class="text-muted">
                                                        {{ $aktivitas->keterangan ?: 'Perubahan kolektibilitas' }}
                                                    </small>
                                                </td>
                                                <td>
                                                    <small class="text-muted">
                                                        {{ $aktivitas->created_at->format('d M Y') }}<br>
                                                        <span
                                                            class="text-muted">{{ $aktivitas->created_at->format('H:i') }}</span>
                                                    </small>
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="6" class="text-center py-4">
                                                    <i class="fas fa-exchange-alt fa-2x text-muted mb-2"></i>
                                                    <p class="text-muted mb-0">Belum ada aktivitas perubahan</p>
                                                </td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Tab 4: Performance -->
        <div class="tab-pane fade" id="performance" role="tabpanel">
            <div class="row g-3">
                <!-- Performance Detail -->
                <div class="col-12 col-lg-8">
                    <div class="card border-0">
                        <div class="card-header bg-white py-2">
                            <h6 class="mb-0"><i class="fas fa-chart-bar me-2 text-primary"></i>Detail Performance
                                Petugas</h6>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead class="table-light">
                                        <tr>
                                            <th>Petugas</th>
                                            <th>Divisi</th>
                                            <th>Total Perubahan</th>
                                            <th>Berhasil Memperbaiki</th>
                                            <th>Success Rate</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($performance as $perf)
                                            @php
                                                $successRate =
                                                    $perf->total_perubahan > 0
                                                        ? ($perf->berhasil_memperbaiki / $perf->total_perubahan) * 100
                                                        : 0;
                                            @endphp
                                            <tr>
                                                <td>
                                                    <strong>{{ $perf->petugas }}</strong>
                                                </td>
                                                <td>
                                                    <span class="badge bg-secondary">{{ $perf->divisi }}</span>
                                                </td>
                                                <td>{{ $perf->total_perubahan }}</td>
                                                <td>{{ $perf->berhasil_memperbaiki }}</td>
                                                <td>
                                                    <div class="d-flex align-items-center">
                                                        <div class="progress flex-grow-1 me-2" style="height: 8px;">
                                                            <div class="progress-bar bg-success"
                                                                style="width: {{ $successRate }}%"></div>
                                                        </div>
                                                        <span class="fw-bold">{{ number_format($successRate, 1) }}%</span>
                                                    </div>
                                                </td>
                                                <td>
                                                    <a href="{{ route('nasabah.index', ['petugas_id' => $perf->petugas_id ?? '']) }}"
                                                        class="btn btn-sm btn-outline-primary">
                                                        Lihat Nasabah 
                                                    </a>
                                                </td>
                                            </tr>
                                        @endforeach

                                        @if ($performance->isEmpty())
                                            <tr>
                                                <td colspan="6" class="text-center py-4">
                                                    <i class="fas fa-chart-bar fa-2x text-muted mb-2"></i>
                                                    <p class="text-muted mb-0">Belum ada data performance</p>
                                                </td>
                                            </tr>
                                        @endif
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Performance Summary -->
                <div class="col-12 col-lg-4">
                    <div class="card border-0">
                        <div class="card-header bg-white py-2">
                            <h6 class="mb-0"><i class="fas fa-chart-pie me-2 text-info"></i>Summary Performance</h6>
                        </div>
                        <div class="card-body">
                            @php
                                $totalPerubahanAll = $performance->sum('total_perubahan');
                                $totalBerhasilAll = $performance->sum('berhasil_memperbaiki');
                                $avgSuccessRate =
                                    $totalPerubahanAll > 0 ? ($totalBerhasilAll / $totalPerubahanAll) * 100 : 0;
                            @endphp
                            <div class="text-center mb-4">
                                <div class="display-4 fw-bold text-primary">{{ number_format($avgSuccessRate, 1) }}%</div>
                                <small class="text-muted">Average Success Rate</small>
                            </div>

                            <div class="mb-3">
                                <div class="d-flex justify-content-between mb-1">
                                    <small class="text-muted">Total Perubahan</small>
                                    <small class="fw-bold">{{ $totalPerubahanAll }}</small>
                                </div>
                                <div class="progress" style="height: 6px;">
                                    <div class="progress-bar bg-info" style="width: 100%"></div>
                                </div>
                            </div>

                            <div class="mb-3">
                                <div class="d-flex justify-content-between mb-1">
                                    <small class="text-muted">Berhasil Memperbaiki</small>
                                    <small class="fw-bold text-success">{{ $totalBerhasilAll }}</small>
                                </div>
                                <div class="progress" style="height: 6px;">
                                    <div class="progress-bar bg-success" style="width: {{ $avgSuccessRate }}%"></div>
                                </div>
                            </div>

                            <div class="mt-4">
                                <h6 class="mb-3">Top Performers</h6>
                                @foreach ($performance->sortByDesc('berhasil_memperbaiki')->take(3) as $topPerf)
                                    @php
                                        $topSuccessRate =
                                            $topPerf->total_perubahan > 0
                                                ? ($topPerf->berhasil_memperbaiki / $topPerf->total_perubahan) * 100
                                                : 0;
                                    @endphp
                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                        <small class="fw-bold">{{ $topPerf->petugas }}</small>
                                        <small class="text-success">{{ number_format($topSuccessRate, 1) }}%</small>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Inisialisasi tab Bootstrap
        var triggerTabList = [].slice.call(document.querySelectorAll('#dashboardTabs button'))
        triggerTabList.forEach(function(triggerEl) {
            var tabTrigger = new bootstrap.Tab(triggerEl)
            triggerEl.addEventListener('click', function(event) {
                event.preventDefault()
                tabTrigger.show()
            })
        })

        // Chart scripts tetap sama seperti sebelumnya
        const trendCtx = document.getElementById('trendChart').getContext('2d');
        const trendChart = new Chart(trendCtx, {
            type: 'line',
            data: {
                labels: {!! json_encode(
                    $monthlyTrend->pluck('month')->map(function ($month) {
                        return \Carbon\Carbon::create()->month($month)->translatedFormat('M');
                    }),
                ) !!},
                datasets: [{
                    label: 'Perubahan Kolektibilitas',
                    data: {!! json_encode($monthlyTrend->pluck('total_changes')) !!},
                    borderColor: '#0d6efd',
                    backgroundColor: 'rgba(13, 110, 253, 0.1)',
                    tension: 0.4,
                    fill: true
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            stepSize: 1
                        }
                    }
                }
            }
        });

        const distCtx = document.getElementById('distributionChart').getContext('2d');
        const distributionChart = new Chart(distCtx, {
            type: 'doughnut',
            data: {
                labels: {!! json_encode($distribusiKolektibilitas->pluck('kualitas')) !!},
                datasets: [{
                    data: {!! json_encode($distribusiKolektibilitas->pluck('total')) !!},
                    backgroundColor: [
                        '#198754', // Lancar - Hijau
                        '#0dcaf0', // Dalam Perhatian - Biru
                        '#ffc107', // Kurang Lancar - Kuning
                        '#fd7e14', // Diragukan - Orange
                        '#dc3545' // Macet - Merah
                    ],
                    borderWidth: 2,
                    borderColor: '#fff'
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: {
                            padding: 20,
                            usePointStyle: true,
                            pointStyle: 'circle'
                        }
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                const label = context.label || '';
                                const value = context.raw || 0;
                                const total = context.dataset.data.reduce((a, b) => a + b, 0);
                                const percentage = Math.round((value / total) * 100);
                                return `${label}: ${value} nasabah (${percentage}%)`;
                            }
                        }
                    }
                },
                cutout: '50%'
            }
        });

        const janjiCtx = document.getElementById('janjiStatusChart').getContext('2d');
        const janjiStatusChart = new Chart(janjiCtx, {
            type: 'bar',
            data: {
                labels: {!! json_encode($janjiStatus->pluck('status')) !!},
                datasets: [{
                    label: 'Jumlah Janji',
                    data: {!! json_encode($janjiStatus->pluck('total')) !!},
                    backgroundColor: [
                        '#ffc107',
                        '#198754',
                        '#dc3545'
                    ]
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            stepSize: 1
                        }
                    }
                }
            }
        });
    </script>
@endpush
