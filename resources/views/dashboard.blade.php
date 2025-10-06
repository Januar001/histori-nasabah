@extends('layouts.app')

@section('title', 'Dashboard Analitis')

@section('content')
<div class="row">
    <div class="col-12 mb-3">
        <h4 class="mb-0"><i class="fas fa-tachometer-alt me-2 text-primary"></i>Dashboard Analitis</h4>
        <small class="text-muted">Ringkasan Kinerja Keseluruhan per {{ now()->translatedFormat('d F Y') }}</small>
    </div>
</div>

<div class="row g-3 mb-4">
    <div class="col-6 col-md-4 col-lg-2">
        <div class="card border-0 h-100"><div class="card-body p-3 text-center"><div class="text-primary mb-2"><i class="fas fa-users fa-2x"></i></div><h4 class="mb-1">{{ number_format($totalNasabah) }}</h4><small class="text-muted">Total Nasabah</small></div></div>
    </div>
    <div class="col-6 col-md-4 col-lg-2">
        <div class="card border-0 h-100"><div class="card-body p-3 text-center"><div class="text-success mb-2"><i class="fas fa-wallet fa-2x"></i></div><h4 class="mb-1">Rp {{ number_format($totalBakiDebet / 1000000000, 2, ',', '.') }} M</h4><small class="text-muted">Total Baki Debet</small></div></div>
    </div>
    <div class="col-6 col-md-4 col-lg-2">
        <div class="card border-0 h-100"><div class="card-body p-3 text-center"><div class="text-warning mb-2"><i class="fas fa-user-tie fa-2x"></i></div><h4 class="mb-1">{{ $petugasAktif }}</h4><small class="text-muted">Petugas Aktif</small></div></div>
    </div>
    <div class="col-6 col-md-4 col-lg-2">
        <div class="card border-0 h-100"><div class="card-body p-3 text-center"><div class="text-info mb-2"><i class="fas fa-calendar-alt fa-2x"></i></div><h4 class="mb-1">{{ $janjiBayarAktif }}</h4><small class="text-muted">Janji Bayar Aktif</small></div></div>
    </div>
    <div class="col-6 col-md-4 col-lg-2">
        <div class="card border-0 h-100"><div class="card-body p-3 text-center"><div class="text-cyan mb-2"><i class="fas fa-calendar-check fa-2x"></i></div><h4 class="mb-1">{{ $janjiBayarDitepatiBulanIni }}</h4><small class="text-muted">Ditepati Bln Ini</small></div></div>
    </div>
    <div class="col-6 col-md-4 col-lg-2">
        <div class="card border-0 h-100"><div class="card-body p-3 text-center"><div class="text-danger mb-2"><i class="fas fa-calendar-times fa-2x"></i></div><h4 class="mb-1">{{ $janjiBayarIngkarBulanIni }}</h4><small class="text-muted">Ingkar Bln Ini</small></div></div>
    </div>
</div>

<div class="row g-3 mb-4">
    <div class="col-12 col-lg-7">
        <div class="card border-0 h-100"><div class="card-header bg-white py-2"><ul class="nav nav-pills" id="pills-tab" role="tablist"><li class="nav-item" role="presentation"><button class="nav-link active" id="pills-jumlah-tab" data-bs-toggle="pill" data-bs-target="#pills-jumlah" type="button" role="tab">Distribusi per Jumlah Nasabah</button></li><li class="nav-item" role="presentation"><button class="nav-link" id="pills-bakidebet-tab" data-bs-toggle="pill" data-bs-target="#pills-bakidebet" type="button" role="tab">Distribusi per Baki Debet</button></li></ul></div><div class="card-body tab-content" id="pills-tabContent"><div class="tab-pane fade show active" id="pills-jumlah" role="tabpanel"><canvas id="jumlahChart" style="min-height: 280px;"></canvas></div><div class="tab-pane fade" id="pills-bakidebet" role="tabpanel"><canvas id="bakidebetChart" style="min-height: 280px;"></canvas></div></div></div>
    </div>
    <div class="col-12 col-lg-5">
        <div class="card border-0 mb-3"><div class="card-header bg-white py-2"><h6 class="mb-0"><i class="fas fa-chart-line me-2 text-primary"></i>Tren Perubahan (vs Bulan Lalu)</h6></div><div class="card-body p-3"><div class="row"><div class="col-6 text-center border-end"><h5 class="mb-0">{{ $trenPerubahan['total_membaik'] }}</h5><small class="text-muted">Nasabah Membaik</small>@if($trenPerubahan['membaik'] >= 0)<small class="d-block text-success"><i class="fas fa-arrow-up"></i> {{ number_format($trenPerubahan['membaik'], 1) }}%</small>@else<small class="d-block text-danger"><i class="fas fa-arrow-down"></i> {{ number_format(abs($trenPerubahan['membaik']), 1) }}%</small>@endif</div><div class="col-6 text-center"><h5 class="mb-0">{{ $trenPerubahan['total_memburuk'] }}</h5><small class="text-muted">Nasabah Memburuk</small>@if($trenPerubahan['memburuk'] > 0)<small class="d-block text-danger"><i class="fas fa-arrow-up"></i> {{ number_format($trenPerubahan['memburuk'], 1) }}%</small>@else<small class="d-block text-success"><i class="fas fa-arrow-down"></i> {{ number_format(abs($trenPerubahan['memburuk']), 1) }}%</small>@endif</div></div></div></div>
        <div class="card border-0"><div class="card-header bg-white py-2"><h6 class="mb-0"><i class="fas fa-trophy me-2 text-warning"></i>Top 5 Performa Petugas (Bulan Ini)</h6></div><div class="card-body p-3">@forelse($performance as $perf)<div class="mb-2"><div class="d-flex justify-content-between"><span class="fw-bold">{{ $perf->petugas }}</span><small class="text-muted">{{ $perf->berhasil_memperbaiki }}/{{ $perf->total_perubahan }} Perbaikan</small></div></div>@empty<div class="text-center text-muted py-3"><p class="mb-0">Belum ada data performa</p></div>@endforelse</div></div>
    </div>
</div>

<div class="row mb-4">
    <div class="col-12">
        <div class="card border-0">
            <div class="card-header bg-white py-2">
                <div class="d-flex flex-wrap justify-content-between align-items-center">
                    <h6 class="mb-0 me-3"><i class="fas fa-clock me-2 text-info"></i>Jadwal Janji Bayar Mendatang</h6>
                    <form method="GET" action="{{ route('dashboard') }}" class="d-flex flex-wrap align-items-center">
                        <input type="date" name="start_date" class="form-control form-control-sm me-2" value="{{ $startDate }}" style="width: auto;">
                        <span class="me-2">s/d</span>
                        <input type="date" name="end_date" class="form-control form-control-sm me-2" value="{{ $endDate }}" style="width: auto;">
                        <button type="submit" class="btn btn-primary btn-sm"><i class="fas fa-search"></i></button>
                    </form>
                </div>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead>
                            <tr class="table-light">
                                <th>Nama Nasabah</th>
                                <th>Tanggal Janji</th>
                                <th>Petugas</th>
                                <th>Nominal</th>
                                <th class="text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($janjiBayarList as $jb)
                            <tr>
                                <td>
                                    <div class="fw-bold">{{ $jb->nasabah->namadb }}</div>
                                    <small class="text-muted">{{ $jb->nasabah->nocif }}</small>
                                </td>
                                <td>
                                    <span class="badge bg-info text-dark">{{ \Carbon\Carbon::parse($jb->tanggal_janji)->translatedFormat('d F Y') }}</span>
                                </td>
                                <td>{{ $jb->petugas->nama_petugas ?? 'N/A' }}</td>
                                <td>Rp {{ number_format($jb->nominal, 0, ',', '.') }}</td>
                                <td class="text-center">
                                    <a href="{{ route('nasabah.show', $jb->nasabah_id) }}" class="btn btn-xs btn-outline-primary">
                                        <i class="fas fa-eye"></i> Detail
                                    </a>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="5" class="text-center py-5 text-muted">
                                    <i class="fas fa-calendar-check fa-2x mb-2"></i>
                                    <p class="mb-0">Tidak ada jadwal janji bayar pada periode ini.</p>
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


<div class="row">
    <div class="col-12">
        <div class="card border-0"><div class="card-header bg-white py-2 d-flex justify-content-between align-items-center"><h6 class="mb-0"><i class="fas fa-history me-2 text-primary"></i>Aktivitas Perubahan Terbaru</h6><a href="{{ route('kolektibilitas.history') }}" class="btn btn-sm btn-outline-primary">Lihat Semua</a></div><div class="card-body p-0"><div class="table-responsive"><table class="table table-hover mb-0"><tbody>@forelse($aktivitasPerubahan as $aktivitas)<tr><td><a href="{{ route('nasabah.show', $aktivitas->nasabah_id) }}" class="text-decoration-none fw-bold">{{ $aktivitas->nasabah->namadb }}</a><small class="text-muted d-block">{{ $aktivitas->nasabah->nocif }}</small></td><td><span class="badge {{ \App\Helpers\QualityHelper::getQualityBadge($aktivitas->kolektibilitas_sebelum) }}">{{ \App\Helpers\QualityHelper::getQualityLabel($aktivitas->kolektibilitas_sebelum) }}</span><i class="fas fa-arrow-right text-muted mx-1 small"></i><span class="badge {{ \App\Helpers\QualityHelper::getQualityBadge($aktivitas->kolektibilitas_sesudah) }}">{{ \App\Helpers\QualityHelper::getQualityLabel($aktivitas->kolektibilitas_sesudah) }}</span></td><td><small class="text-muted"><i class="fas fa-user-tie me-1"></i> {{ $aktivitas->nama_petugas }}</small></td><td><small class="text-muted">{{ $aktivitas->created_at->diffForHumans() }}</small></td></tr>@empty<tr><td colspan="4" class="text-center py-4 text-muted">Belum ada aktivitas perubahan.</td></tr>@endforelse</tbody></table></div></div></div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener("DOMContentLoaded", function() {
    const chartColors = ['#198754', '#0dcaf0', '#ffc107', '#fd7e14', '#dc3545'];
    
    const jumlahCtx = document.getElementById('jumlahChart').getContext('2d');
    new Chart(jumlahCtx, {
        type: 'doughnut',
        data: {
            labels: {!! json_encode($distribusiPerJumlah->keys()) !!},
            datasets: [{
                data: {!! json_encode($distribusiPerJumlah->values()) !!},
                backgroundColor: chartColors,
                borderColor: '#fff', borderWidth: 2
            }]
        },
        options: { responsive: true, maintainAspectRatio: false, plugins: { legend: { position: 'bottom' } } }
    });

    const bakidebetCtx = document.getElementById('bakidebetChart').getContext('2d');
    new Chart(bakidebetCtx, {
        type: 'doughnut',
        data: {
            labels: {!! json_encode($distribusiPerBakiDebet->keys()) !!},
            datasets: [{
                data: {!! json_encode($distribusiPerBakiDebet->values()) !!},
                backgroundColor: chartColors,
                borderColor: '#fff', borderWidth: 2
            }]
        },
        options: {
            responsive: true, maintainAspectRatio: false,
            plugins: {
                legend: { position: 'bottom' },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            let label = context.label || '';
                            if (label) { label += ': '; }
                            if (context.parsed !== null) {
                                label += new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', minimumFractionDigits: 0 }).format(context.parsed);
                            }
                            return label;
                        }
                    }
                }
            }
        }
    });
});
</script>
@endpush