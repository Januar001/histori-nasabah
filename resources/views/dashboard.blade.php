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

<div class="row g-3 mb-4">
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
                @foreach($performance as $perf)
                <div class="mb-3">
                    <div class="d-flex justify-content-between align-items-center mb-1">
                        <span class="fw-bold text-truncate" style="max-width: 120px;">{{ $perf->petugas }}</span>
                        <small class="text-muted">{{ $perf->berhasil_memperbaiki }}/{{ $perf->total_perubahan }}</small>
                    </div>
                    @php
                        $successRate = $perf->total_perubahan > 0 
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
                
                @if($performance->isEmpty())
                <div class="text-center text-muted py-3">
                    <i class="fas fa-chart-bar fa-2x mb-2"></i>
                    <p class="mb-0">Belum ada data performance</p>
                    <small>Assign petugas penanganan di detail nasabah</small>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>

<div class="row g-3 mb-4">
    <div class="col-12 col-md-6">
        <div class="card border-0">
            <div class="card-header bg-white py-2">
                <h6 class="mb-0"><i class="fas fa-chart-pie me-2 text-info"></i>Distribusi Kolektibilitas</h6>
            </div>
            <div class="card-body">
                <canvas id="distributionChart" height="200"></canvas>
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
</div>

<div class="row g-3">
    <div class="col-12 col-lg-8">
        <div class="card border-0">
            <div class="card-header bg-white py-2 d-flex justify-content-between align-items-center">
                <h6 class="mb-0"><i class="fas fa-history me-2 text-primary"></i>Aktivitas Terbaru Petugas</h6>
                <a href="{{ route('nasabah.index') }}" class="btn btn-sm btn-outline-primary">
                    Lihat Semua
                </a>
            </div>
            <div class="card-body p-0">
                <div class="list-group list-group-flush">
                    @foreach($recentChanges as $activity)
                    <div class="list-group-item">
                        <div class="d-flex justify-content-between align-items-start">
                            <div class="flex-grow-1">
                                <div class="d-flex justify-content-between align-items-center mb-1">
                                    <strong class="text-truncate me-2" style="max-width: 150px;">
                                        {{ $activity->nasabah->namadb }}
                                    </strong>
                                    <small class="text-muted">{{ $activity->created_at->format('d M H:i') }}</small>
                                </div>
                                <div class="d-flex align-items-center mb-1">
                                    <span class="badge bg-light text-dark me-2">
                                        {{ \App\Helpers\QualityHelper::getQualityLabel($activity->kolektibilitas_sebelum) }}
                                    </span>
                                    <i class="fas fa-arrow-right text-muted me-2"></i>
                                    <span class="badge {{ \App\Helpers\QualityHelper::getQualityBadge($activity->kolektibilitas_sesudah) }}">
                                        {{ \App\Helpers\QualityHelper::getQualityLabel($activity->kolektibilitas_sesudah) }}
                                    </span>
                                </div>
                                <div class="d-flex justify-content-between align-items-center">
                                    <small class="text-muted">
                                        <i class="fas fa-user-tie me-1"></i> {{ $activity->nama_petugas }}
                                        @if($activity->divisi_petugas)
                                        <span class="badge bg-secondary ms-1">{{ $activity->divisi_petugas }}</span>
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

    <div class="col-12 col-lg-4">
        <div class="card border-0">
            <div class="card-header bg-white py-2">
                <h6 class="mb-0"><i class="fas fa-exclamation-triangle me-2 text-danger"></i>Perubahan Signifikan</h6>
            </div>
            <div class="card-body">
                @foreach($topChanges as $change)
                <div class="border-start 
                    @if($change->kolektibilitas_sesudah == '5') border-danger
                    @elseif($change->kolektibilitas_sesudah == '1') border-success
                    @else border-warning @endif ps-2 mb-3">
                    
                    <div class="d-flex justify-content-between align-items-start mb-1">
                        <a href="{{ route('nasabah.show', $change->nasabah->id) }}" 
                           class="text-decoration-none flex-grow-1">
                            <strong class="d-block text-truncate">{{ $change->nasabah->namadb }}</strong>
                        </a>
                        <span class="badge {{ \App\Helpers\QualityHelper::getQualityBadge($change->kolektibilitas_sesudah) }}">
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

                @if($topChanges->isEmpty())
                <div class="text-center text-muted py-3">
                    <i class="fas fa-check-circle fa-2x mb-2"></i>
                    <p class="mb-0">Tidak ada perubahan signifikan</p>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
const trendCtx = document.getElementById('trendChart').getContext('2d');
const trendChart = new Chart(trendCtx, {
    type: 'line',
    data: {
        labels: {!! json_encode($monthlyTrend->pluck('month')->map(function($month) {
            return \Carbon\Carbon::create()->month($month)->translatedFormat('M');
        })) !!},
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
        labels: {!! json_encode($changesThisMonth->pluck('kualitas')) !!},
        datasets: [{
            data: {!! json_encode($changesThisMonth->pluck('total')) !!},
            backgroundColor: [
                '#198754',
                '#0dcaf0',
                '#ffc107',
                '#fd7e14',
                '#dc3545'
            ]
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: {
                position: 'bottom'
            }
        }
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