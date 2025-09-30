@extends('layouts.app')

@section('content')
<div class="row">
    <div class="col-12 mb-3">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h4 class="mb-0"><i class="fas fa-chart-bar me-2 text-primary"></i>Import Statistics</h4>
                <small class="text-muted">Analytics dan statistik data import</small>
            </div>
            <div class="d-flex gap-2">
                <a href="{{ route('import.history') }}" class="btn btn-outline-primary">
                    <i class="fas fa-history me-1"></i> History
                </a>
                <a href="{{ route('import.show') }}" class="btn btn-outline-success">
                    <i class="fas fa-upload me-1"></i> Import Baru
                </a>
                <a href="{{ route('dashboard') }}" class="btn btn-outline-secondary">
                    <i class="fas fa-arrow-left me-1"></i> Kembali
                </a>
            </div>
        </div>
    </div>
</div>

<!-- Stats Overview -->
<div class="row g-2 mb-4">
    <div class="col-6 col-md-3">
        <div class="card border-0 h-100">
            <div class="card-body p-3 text-center">
                <div class="text-primary mb-2">
                    <i class="fas fa-calendar-alt fa-2x"></i>
                </div>
                <h4 class="mb-1">{{ $stats->count() }}</h4>
                <small class="text-muted">Hari dengan Import</small>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="card border-0 h-100">
            <div class="card-body p-3 text-center">
                <div class="text-success mb-2">
                    <i class="fas fa-exchange-alt fa-2x"></i>
                </div>
                <h4 class="mb-1">{{ $stats->sum('changes') }}</h4>
                <small class="text-muted">Total Perubahan</small>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="card border-0 h-100">
            <div class="card-body p-3 text-center">
                <div class="text-warning mb-2">
                    <i class="fas fa-users fa-2x"></i>
                </div>
                <h4 class="mb-1">{{ $stats->sum('unique_nasabahs') }}</h4>
                <small class="text-muted">Nasabah Terdampak</small>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="card border-0 h-100">
            <div class="card-body p-3 text-center">
                <div class="text-info mb-2">
                    <i class="fas fa-chart-line fa-2x"></i>
                </div>
                <h4 class="mb-1">
                    @if($stats->count() > 0)
                        {{ number_format($stats->sum('changes') / $stats->count(), 1) }}
                    @else
                        0
                    @endif
                </h4>
                <small class="text-muted">Rata-rata/Hari</small>
            </div>
        </div>
    </div>
</div>

<!-- Charts Row -->
<div class="row g-3 mb-4">
    <div class="col-12 col-lg-8">
        <div class="card border-0">
            <div class="card-header bg-white py-2">
                <h6 class="mb-0"><i class="fas fa-chart-line me-2 text-primary"></i>Trend Import (30 Hari)</h6>
            </div>
            <div class="card-body">
                <canvas id="importTrendChart" height="120"></canvas>
            </div>
        </div>
    </div>
    <div class="col-12 col-lg-4">
        <div class="card border-0 h-100">
            <div class="card-header bg-white py-2">
                <h6 class="mb-0"><i class="fas fa-trophy me-2 text-warning"></i>Top Changes</h6>
            </div>
            <div class="card-body">
                @foreach($topChanges as $change)
                <div class="d-flex justify-content-between align-items-center mb-3 p-2 rounded bg-light">
                    <div class="flex-grow-1">
                        <strong class="d-block text-truncate" style="max-width: 150px;">{{ $change->namadb }}</strong>
                        <small class="text-muted">{{ $change->nocif }}</small>
                    </div>
                    <span class="badge bg-primary rounded-pill">{{ $change->change_count }}</span>
                </div>
                @endforeach
                
                @if($topChanges->isEmpty())
                <div class="text-center text-muted py-3">
                    <i class="fas fa-chart-bar fa-2x mb-2"></i>
                    <p class="mb-0">Belum ada data changes</p>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Daily Stats Table -->
<div class="row">
    <div class="col-12">
        <div class="card border-0">
            <div class="card-header bg-white py-2">
                <h6 class="mb-0"><i class="fas fa-table me-2 text-success"></i>Detail Harian (30 Hari Terakhir)</h6>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Tanggal</th>
                                <th>Total Perubahan</th>
                                <th>Unique Nasabah</th>
                                <th>Rata-rata Perubahan</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($stats as $stat)
                            <tr>
                                <td>
                                    <strong>{{ \Carbon\Carbon::parse($stat->date)->format('d M Y') }}</strong>
                                </td>
                                <td>
                                    <span class="badge bg-primary rounded-pill">{{ $stat->changes }}</span>
                                </td>
                                <td>
                                    <span class="badge bg-info rounded-pill">{{ $stat->unique_nasabahs }}</span>
                                </td>
                                <td>
                                    <small class="text-muted">
                                        {{ number_format($stat->unique_nasabahs > 0 ? $stat->changes / $stat->unique_nasabahs : 0, 1) }} perubahan/nasabah
                                    </small>
                                </td>
                                <td>
                                    @if($stat->changes > 10)
                                    <span class="badge bg-success">High Activity</span>
                                    @elseif($stat->changes > 5)
                                    <span class="badge bg-warning">Medium Activity</span>
                                    @else
                                    <span class="badge bg-secondary">Low Activity</span>
                                    @endif
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="5" class="text-center py-4">
                                    <i class="fas fa-chart-bar fa-3x text-muted mb-3"></i>
                                    <h5 class="text-muted">Belum ada data statistik</h5>
                                    <p class="text-muted">Data akan muncul setelah melakukan import</p>
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
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
const trendCtx = document.getElementById('importTrendChart').getContext('2d');
const trendChart = new Chart(trendCtx, {
    type: 'line',
    data: {
        labels: {!! json_encode($stats->pluck('date')->map(function($date) {
            return \Carbon\Carbon::parse($date)->format('d M');
        })) !!},
        datasets: [{
            label: 'Perubahan Kolektibilitas',
            data: {!! json_encode($stats->pluck('changes')) !!},
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
                beginAtZero: true
            }
        }
    }
});
</script>
@endpush