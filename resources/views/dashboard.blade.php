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
                <small class="text-muted">Status semua nasabah saat ini</small>
            </div>
            <div class="card-body">
                <canvas id="distributionChart" height="200"></canvas>
            </div>
            <div class="card-footer bg-white py-2">
                <div class="row text-center">
                    @foreach($distribusiKolektibilitas as $dist)
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
                <small class="text-muted">Bulan {{ now()->translatedFormat('F Y') }}</small>
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

<!-- TAMBAHAN: Section Aktivitas Perubahan Kolektibilitas -->
<div class="row g-3 mt-4">
    <div class="col-12">
        <div class="card border-0">
            <div class="card-header bg-white py-2 d-flex justify-content-between align-items-center">
                <h6 class="mb-0"><i class="fas fa-exchange-alt me-2 text-primary"></i>Aktivitas Perubahan Kolektibilitas</h6>
                <small class="text-muted">Riwayat perubahan terbaru</small>
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
                                        <span class="badge {{ \App\Helpers\QualityHelper::getQualityBadge($aktivitas->kolektibilitas_sebelum) }} me-1">
                                            {{ \App\Helpers\QualityHelper::getQualityLabel($aktivitas->kolektibilitas_sebelum) }}
                                        </span>
                                        <i class="fas fa-arrow-right text-muted mx-1 small"></i>
                                        <span class="badge {{ \App\Helpers\QualityHelper::getQualityBadge($aktivitas->kolektibilitas_sesudah) }}">
                                            {{ \App\Helpers\QualityHelper::getQualityLabel($aktivitas->kolektibilitas_sesudah) }}
                                        </span>
                                    </div>
                                    @php
                                        $statusPerubahan = $aktivitas->kolektibilitas_sesudah - $aktivitas->kolektibilitas_sebelum;
                                    @endphp
                                    @if($statusPerubahan < 0)
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
                                    @if($aktivitas->petugasRelasi)
                                        <span class="fw-medium">{{ $aktivitas->petugasRelasi->nama_petugas }}</span>
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                                <td>
                                    @if($aktivitas->petugasRelasi)
                                        <span class="badge bg-secondary">{{ $aktivitas->petugasRelasi->divisi }}</span>
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
                                        <span class="text-muted">{{ $aktivitas->created_at->format('H:i') }}</span>
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
            @if($aktivitasPerubahan->count() > 0)
            <div class="card-footer bg-white py-2">
                <div class="d-flex justify-content-between align-items-center">
                    <small class="text-muted">
                        Menampilkan {{ $aktivitasPerubahan->count() }} perubahan terbaru
                    </small>
                    <a href="{{ route('kolektibilitas.history') }}" class="btn btn-sm btn-outline-primary">
                        Lihat Semua Riwayat
                    </a>
                </div>
            </div>
            @endif
        </div>
    </div>
</div>

<!-- TAMBAHAN: Statistik Perubahan -->
<div class="row g-3 mt-3">
    <div class="col-12 col-md-6 col-lg-3">
        <div class="card border-0">
            <div class="card-body text-center p-3">
                @php
                    $memperbaiki = $statistikPerubahan->where('jenis_perubahan', 'Memperbaiki')->first();
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
                    $tidakBerubah = $statistikPerubahan->where('jenis_perubahan', 'Tidak Berubah')->first();
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
        labels: {!! json_encode($distribusiKolektibilitas->pluck('kualitas')) !!},
        datasets: [{
            data: {!! json_encode($distribusiKolektibilitas->pluck('total')) !!},
            backgroundColor: [
                '#198754', // Lancar - Hijau
                '#0dcaf0', // Dalam Perhatian - Biru
                '#ffc107', // Kurang Lancar - Kuning
                '#fd7e14', // Diragukan - Orange
                '#dc3545'  // Macet - Merah
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