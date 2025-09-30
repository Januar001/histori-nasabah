@extends('layouts.app')

@section('content')
<div class="row">
    <div class="col-12 mb-3">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h4 class="mb-0"><i class="fas fa-history me-2 text-primary"></i>Riwayat Perubahan Kolektibilitas</h4>
                <small class="text-muted">Tracking semua perubahan status kolektibilitas nasabah</small>
            </div>
            <div class="d-flex gap-2">
                @if(request()->anyFilled(['start_date', 'end_date', 'nasabah_id', 'petugas_id', 'jenis_perubahan', 'kualitas_sebelum', 'kualitas_sesudah']))
                <a href="{{ route('kolektibilitas.history') }}" class="btn btn-sm btn-outline-secondary">
                    <i class="fas fa-refresh"></i> Reset
                </a>
                @endif
                <button class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#filterModal">
                    <i class="fas fa-filter"></i> Filter
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Statistics Cards -->
<div class="row g-2 mb-4">
    <div class="col-6 col-md-3">
        <div class="card border-0 bg-primary text-white">
            <div class="card-body p-3 text-center">
                <div class="mb-2">
                    <i class="fas fa-exchange-alt fa-2x"></i>
                </div>
                <h4 class="mb-1">{{ $statistics['total'] }}</h4>
                <small>Total Perubahan</small>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="card border-0 bg-success text-white">
            <div class="card-body p-3 text-center">
                <div class="mb-2">
                    <i class="fas fa-arrow-up fa-2x"></i>
                </div>
                <h4 class="mb-1">{{ $statistics['membaik'] }}</h4>
                <small>Membaik ({{ $statistics['persen_membaik'] }}%)</small>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="card border-0 bg-danger text-white">
            <div class="card-body p-3 text-center">
                <div class="mb-2">
                    <i class="fas fa-arrow-down fa-2x"></i>
                </div>
                <h4 class="mb-1">{{ $statistics['memburuk'] }}</h4>
                <small>Memburuk ({{ $statistics['persen_memburuk'] }}%)</small>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="card border-0 bg-warning text-white">
            <div class="card-body p-3 text-center">
                <div class="mb-2">
                    <i class="fas fa-minus fa-2x"></i>
                </div>
                <h4 class="mb-1">{{ $statistics['tetap'] }}</h4>
                <small>Tetap ({{ $statistics['persen_tetap'] }}%)</small>
            </div>
        </div>
    </div>
</div>

<!-- Filter Modal -->
<div class="modal fade" id="filterModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="fas fa-filter me-2"></i>Filter Riwayat</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="GET" action="{{ route('kolektibilitas.history') }}">
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Tanggal Mulai</label>
                            <input type="date" name="start_date" class="form-control" 
                                   value="{{ request('start_date') }}">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Tanggal Akhir</label>
                            <input type="date" name="end_date" class="form-control" 
                                   value="{{ request('end_date') }}">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Nasabah</label>
                            <select name="nasabah_id" class="form-select">
                                <option value="">Semua Nasabah</option>
                                @foreach($nasabahs as $nasabah)
                                <option value="{{ $nasabah->id }}" 
                                    {{ request('nasabah_id') == $nasabah->id ? 'selected' : '' }}>
                                    {{ $nasabah->namadb }} ({{ $nasabah->nocif }})
                                </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Petugas</label>
                            <select name="petugas_id" class="form-select">
                                <option value="">Semua Petugas</option>
                                <option value="null" {{ request('petugas_id') === 'null' ? 'selected' : '' }}>
                                    Tanpa Petugas
                                </option>
                                @foreach($petugas as $p)
                                <option value="{{ $p->id }}" 
                                    {{ request('petugas_id') == $p->id ? 'selected' : '' }}>
                                    {{ $p->nama_petugas }} ({{ $p->divisi }})
                                </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Jenis Perubahan</label>
                            <select name="jenis_perubahan" class="form-select">
                                <option value="">Semua Jenis</option>
                                <option value="membaik" {{ request('jenis_perubahan') == 'membaik' ? 'selected' : '' }}>
                                    Membaik
                                </option>
                                <option value="memburuk" {{ request('jenis_perubahan') == 'memburuk' ? 'selected' : '' }}>
                                    Memburuk
                                </option>
                                <option value="tetap" {{ request('jenis_perubahan') == 'tetap' ? 'selected' : '' }}>
                                    Tidak Berubah
                                </option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Kualitas Sebelum</label>
                            <select name="kualitas_sebelum" class="form-select">
                                <option value="">Semua Kualitas</option>
                                @foreach($qualityOptions as $value => $label)
                                <option value="{{ $value }}" 
                                    {{ request('kualitas_sebelum') == $value ? 'selected' : '' }}>
                                    {{ $label }}
                                </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Kualitas Sesudah</label>
                            <select name="kualitas_sesudah" class="form-select">
                                <option value="">Semua Kualitas</option>
                                @foreach($qualityOptions as $value => $label)
                                <option value="{{ $value }}" 
                                    {{ request('kualitas_sesudah') == $value ? 'selected' : '' }}>
                                    {{ $label }}
                                </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-filter me-1"></i> Terapkan Filter
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Active Filters -->
@if(request()->anyFilled(['start_date', 'end_date', 'nasabah_id', 'petugas_id', 'jenis_perubahan', 'kualitas_sebelum', 'kualitas_sesudah']))
<div class="row mb-3">
    <div class="col-12">
        <div class="alert alert-info py-2">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <i class="fas fa-info-circle me-2"></i>
                    <strong>Filter Aktif:</strong>
                    @if(request('start_date') && request('end_date'))
                        <span class="badge bg-primary me-1">
                            {{ \Carbon\Carbon::parse(request('start_date'))->format('d M Y') }} - 
                            {{ \Carbon\Carbon::parse(request('end_date'))->format('d M Y') }}
                        </span>
                    @endif
                    @if(request('nasabah_id'))
                        <span class="badge bg-primary me-1">
                            Nasabah: {{ $nasabahs->where('id', request('nasabah_id'))->first()->namadb ?? '' }}
                        </span>
                    @endif
                    @if(request('petugas_id') === 'null')
                        <span class="badge bg-primary me-1">Tanpa Petugas</span>
                    @elseif(request('petugas_id'))
                        <span class="badge bg-primary me-1">
                            Petugas: {{ $petugas->where('id', request('petugas_id'))->first()->nama_petugas ?? '' }}
                        </span>
                    @endif
                    @if(request('jenis_perubahan'))
                        <span class="badge bg-primary me-1">
                            Jenis: {{ ucfirst(request('jenis_perubahan')) }}
                        </span>
                    @endif
                    @if(request('kualitas_sebelum'))
                        <span class="badge bg-primary me-1">
                            Sebelum: {{ \App\Helpers\QualityHelper::getQualityLabel(request('kualitas_sebelum')) }}
                        </span>
                    @endif
                    @if(request('kualitas_sesudah'))
                        <span class="badge bg-primary me-1">
                            Sesudah: {{ \App\Helpers\QualityHelper::getQualityLabel(request('kualitas_sesudah')) }}
                        </span>
                    @endif
                </div>
                <small>Menampilkan {{ $histories->total() }} hasil</small>
            </div>
        </div>
    </div>
</div>
@endif

<!-- History Table -->
<div class="row">
    <div class="col-12">
        <div class="card border-0">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th width="15%">Waktu</th>
                                <th width="20%">Nasabah</th>
                                <th width="15%">Perubahan</th>
                                <th width="15%">Petugas</th>
                                <th width="25%">Keterangan</th>
                                <th width="10%">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($histories as $history)
                            <tr>
                                <td>
                                    <small class="text-muted d-block">
                                        {{ $history->created_at->format('d M Y') }}
                                    </small>
                                    <small class="text-muted">
                                        {{ $history->created_at->format('H:i:s') }}
                                    </small>
                                </td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <a href="{{ route('nasabah.show', $history->nasabah_id) }}" 
                                           class="text-decoration-none fw-bold">
                                            {{ $history->nasabah->namadb }}
                                        </a>
                                    </div>
                                    <small class="text-muted d-block">
                                        CIF: {{ $history->nasabah->nocif }}
                                    </small>
                                </td>
                                <td>
                                    <div class="d-flex align-items-center mb-1">
                                        <span class="badge bg-light text-dark">
                                            {{ \App\Helpers\QualityHelper::getQualityLabel($history->kolektibilitas_sebelum) }}
                                        </span>
                                        <i class="fas fa-arrow-right mx-2 text-muted small"></i>
                                        <span class="badge {{ \App\Helpers\QualityHelper::getQualityBadge($history->kolektibilitas_sesudah) }}">
                                            {{ \App\Helpers\QualityHelper::getQualityLabel($history->kolektibilitas_sesudah) }}
                                        </span>
                                    </div>
                                    @php
                                        $statusPerubahan = $history->kolektibilitas_sesudah - $history->kolektibilitas_sebelum;
                                    @endphp
                                    @if($statusPerubahan < 0)
                                        <span class="badge bg-success">
                                            <i class="fas fa-arrow-up me-1"></i> Membaik
                                        </span>
                                    @elseif($statusPerubahan > 0)
                                        <span class="badge bg-danger">
                                            <i class="fas fa-arrow-down me-1"></i> Memburuk
                                        </span>
                                    @else
                                        <span class="badge bg-warning">
                                            <i class="fas fa-minus me-1"></i> Tetap
                                        </span>
                                    @endif
                                </td>
                                <td>
                                    @if($history->petugasRelasi)
                                        <div class="fw-medium">{{ $history->petugasRelasi->nama_petugas }}</div>
                                        <small class="text-muted">{{ $history->petugasRelasi->divisi }}</small>
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                                <td>
                                    <small class="text-muted">
                                        {{ $history->keterangan ?: 'Perubahan kolektibilitas' }}
                                    </small>
                                    @if($history->catatan_tambahan)
                                        <br>
                                        <small class="text-info">
                                            <i class="fas fa-sticky-note me-1"></i> {{ $history->catatan_tambahan }}
                                        </small>
                                    @endif
                                </td>
                                <td>
                                    <a href="{{ route('nasabah.show', $history->nasabah_id) }}" 
                                       class="btn btn-sm btn-outline-primary"
                                       title="Lihat Detail Nasabah">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="6" class="text-center py-5">
                                    <i class="fas fa-history fa-3x text-muted mb-3"></i>
                                    <h5 class="text-muted">Tidak ada data riwayat</h5>
                                    <p class="text-muted mb-0">
                                        @if(request()->anyFilled(['start_date', 'end_date', 'nasabah_id', 'petugas_id', 'jenis_perubahan']))
                                            Coba ubah filter pencarian
                                        @else
                                            Belum ada perubahan kolektibilitas yang tercatat
                                        @endif
                                    </p>
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

<!-- Pagination -->
@if($histories->hasPages())
<div class="row mt-3">
    <div class="col-12">
        <div class="d-flex justify-content-center">
            {{ $histories->links('pagination::bootstrap-5') }}
        </div>
    </div>
</div>
@endif
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Set default dates if not set
    const startDate = document.querySelector('input[name="start_date"]');
    const endDate = document.querySelector('input[name="end_date"]');
    
    if (!startDate.value) {
        startDate.value = '{{ now()->subDays(30)->format('Y-m-d') }}';
    }
    if (!endDate.value) {
        endDate.value = '{{ now()->format('Y-m-d') }}';
    }
});
</script>
@endpush