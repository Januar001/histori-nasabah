@extends('layouts.app')

@section('content')
<div class="row">
    <div class="col-12 mb-3">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h4 class="mb-0"><i class="fas fa-history me-2 text-primary"></i>History Import</h4>
                <small class="text-muted">Riwayat import data Excel</small>
            </div>
            <div class="d-flex gap-2">
                <a href="{{ route('import.show') }}" class="btn btn-outline-primary">
                    <i class="fas fa-upload me-1"></i> Import Baru
                </a>
                <a href="{{ route('dashboard') }}" class="btn btn-outline-secondary">
                    <i class="fas fa-arrow-left me-1"></i> Kembali
                </a>
            </div>
        </div>
    </div>
</div>

<!-- Summary Stats -->
@if($summary)
<div class="row g-2 mb-4">
    <div class="col-6 col-md-3">
        <div class="card border-0 h-100">
            <div class="card-body p-3 text-center">
                <div class="text-primary mb-2">
                    <i class="fas fa-database fa-2x"></i>
                </div>
                <h4 class="mb-1">{{ $summary->total_imports }}</h4>
                <small class="text-muted">Total Import</small>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="card border-0 h-100">
            <div class="card-body p-3 text-center">
                <div class="text-success mb-2">
                    <i class="fas fa-calendar-day fa-2x"></i>
                </div>
                <h4 class="mb-1">{{ $summary->total_days }}</h4>
                <small class="text-muted">Hari Aktif</small>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="card border-0 h-100">
            <div class="card-body p-3 text-center">
                <div class="text-warning mb-2">
                    <i class="fas fa-clock fa-2x"></i>
                </div>
                <h4 class="mb-1">
                    @if($summary->last_import)
                        {{ \Carbon\Carbon::parse($summary->last_import)->diffForHumans() }}
                    @else
                        -
                    @endif
                </h4>
                <small class="text-muted">Import Terakhir</small>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="card border-0 h-100">
            <div class="card-body p-3 text-center">
                <div class="text-info mb-2">
                    <i class="fas fa-sync-alt fa-2x"></i>
                </div>
                <h4 class="mb-1">{{ $imports->total() }}</h4>
                <small class="text-muted">Records</small>
            </div>
        </div>
    </div>
</div>
@endif

<!-- Import History Table -->
<div class="row">
    <div class="col-12">
        <div class="card border-0">
            <div class="card-header bg-white py-2 d-flex justify-content-between align-items-center">
                <h6 class="mb-0"><i class="fas fa-list me-2 text-primary"></i>Riwayat Perubahan dari Import</h6>
                <div class="d-flex gap-2">
                    <a href="{{ route('import.stats') }}" class="btn btn-sm btn-outline-info">
                        <i class="fas fa-chart-bar me-1"></i> Stats
                    </a>
                    <form action="{{ route('import.clear-history') }}" method="POST" class="d-inline">
                        @csrf
                        <input type="hidden" name="confirm" value="1">
                        <button type="submit" class="btn btn-sm btn-outline-danger" 
                                onclick="return confirm('Hapus history import yang lebih dari 3 bulan?')">
                            <i class="fas fa-trash me-1"></i> Clear Old
                        </button>
                    </form>
                </div>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Tanggal</th>
                                <th>Nasabah</th>
                                <th>Perubahan</th>
                                <th>Keterangan</th>
                                <th>Petugas</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($imports as $import)
                            <tr>
                                <td>
                                    <small class="text-muted d-block">{{ $import->created_at->format('d M Y') }}</small>
                                    <small class="text-muted">{{ $import->created_at->format('H:i') }}</small>
                                </td>
                                <td>
                                    @if($import->nasabah)
                                    <a href="{{ route('nasabah.show', $import->nasabah->id) }}" class="text-decoration-none">
                                        <strong>{{ $import->nasabah->namadb }}</strong>
                                    </a>
                                    <br>
                                    <small class="text-muted">{{ $import->nasabah->nocif }}</small>
                                    @else
                                    <span class="text-muted">-</span>
                                    @endif
                                </td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <span class="badge bg-light text-dark me-2">
                                            {{ \App\Helpers\QualityHelper::getQualityLabel($import->kolektibilitas_sebelum) }}
                                        </span>
                                        <i class="fas fa-arrow-right text-muted me-2"></i>
                                        <span class="badge {{ \App\Helpers\QualityHelper::getQualityBadge($import->kolektibilitas_sesudah) }}">
                                            {{ \App\Helpers\QualityHelper::getQualityLabel($import->kolektibilitas_sesudah) }}
                                        </span>
                                    </div>
                                </td>
                                <td>
                                    <small class="text-muted">{{ $import->keterangan ?: 'Auto update dari import Excel' }}</small>
                                </td>
                                <td>
                                    @if($import->petugas_id)
                                    <span class="badge bg-primary">{{ $import->petugasRelasi->nama_petugas ?? '-' }}</span>
                                    @else
                                    <span class="badge bg-secondary">System Import</span>
                                    @endif
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="5" class="text-center py-4">
                                    <i class="fas fa-history fa-3x text-muted mb-3"></i>
                                    <h5 class="text-muted">Belum ada history import</h5>
                                    <p class="text-muted">Data import akan tercatat di sini setelah melakukan import Excel</p>
                                    <a href="{{ route('import.show') }}" class="btn btn-primary">
                                        <i class="fas fa-upload me-1"></i> Import Data Pertama
                                    </a>
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
@if($imports->hasPages())
<div class="row mt-3">
    <div class="col-12">
        <div class="d-flex justify-content-center">
            {{ $imports->links() }}
        </div>
    </div>
</div>
@endif

<!-- Info Card -->
<div class="row mt-4">
    <div class="col-12">
        <div class="card border-0">
            <div class="card-body">
                <h6><i class="fas fa-info-circle me-2 text-info"></i>Informasi</h6>
                <div class="row">
                    <div class="col-md-6">
                        <ul class="text-muted">
                            <li>History import mencatat semua perubahan kolektibilitas dari file Excel</li>
                            <li>Data yang tidak berubah tidak akan tercatat di history</li>
                            <li>System Import menunjukkan perubahan otomatis dari file Excel</li>
                        </ul>
                    </div>
                    <div class="col-md-6">
                        <ul class="text-muted">
                            <li>Petugas yang sudah diassign akan tercatat bersama perubahan</li>
                            <li>History lebih dari 3 bulan dapat dihapus untuk optimasi database</li>
                            <li>Gunakan filter dan search untuk menemukan data tertentu</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection