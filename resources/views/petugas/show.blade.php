@extends('layouts.app')

@section('content')
<div class="row">
    <div class="col-12 mb-3">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h4 class="mb-0"><i class="fas fa-user-tie me-2 text-primary"></i>Detail Petugas</h4>
                <small class="text-muted">Informasi lengkap dan performance petugas</small>
            </div>
            <div class="d-flex gap-2">
                <a href="{{ route('petugas.index') }}" class="btn btn-outline-secondary">
                    <i class="fas fa-arrow-left me-1"></i> Kembali
                </a>
                <a href="{{ route('petugas.edit', $petugas->id) }}" class="btn btn-warning">
                    <i class="fas fa-edit me-1"></i> Edit
                </a>
            </div>
        </div>
    </div>
</div>

<div class="row g-3">
    <!-- Profile Info -->
    <div class="col-12 col-md-4">
        <div class="card border-0">
            <div class="card-body text-center">
                <div class="mb-3">
                    <div class="bg-primary rounded-circle d-flex align-items-center justify-content-center mx-auto" style="width: 80px; height: 80px;">
                        <i class="fas fa-user text-white fa-2x"></i>
                    </div>
                </div>
                <h4 class="mb-1">{{ $petugas->nama_petugas }}</h4>
                <p class="text-muted mb-2">{{ $petugas->kode_petugas }}</p>
                <span class="badge 
                    @if($petugas->divisi == 'AO') bg-success
                    @elseif($petugas->divisi == 'Remedial') bg-warning
                    @else bg-info @endif fs-6">
                    {{ $petugas->divisi }}
                </span>
                
                <div class="mt-4">
                    @if($petugas->status_aktif)
                    <span class="badge bg-success fs-6">
                        <i class="fas fa-check me-1"></i> Aktif
                    </span>
                    @else
                    <span class="badge bg-secondary fs-6">
                        <i class="fas fa-times me-1"></i> Non-Aktif
                    </span>
                    @endif
                </div>

                <div class="mt-4 pt-3 border-top">
                    <div class="row text-center">
                        <div class="col-6">
                            <h5 class="text-primary mb-0">{{ $petugas->nasabahs_count }}</h5>
                            <small class="text-muted">Nasabah</small>
                        </div>
                        <div class="col-6">
                            <h5 class="text-warning mb-0">{{ $petugas->history_kolektibilitas_count }}</h5>
                            <small class="text-muted">Perubahan</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Contact Info -->
        <div class="card border-0 mt-3">
            <div class="card-header bg-white py-2">
                <h6 class="mb-0"><i class="fas fa-address-card me-2 text-info"></i>Kontak</h6>
            </div>
            <div class="card-body">
                @if($petugas->email)
                <div class="d-flex align-items-center mb-3">
                    <div class="flex-shrink-0">
                        <i class="fas fa-envelope text-muted"></i>
                    </div>
                    <div class="flex-grow-1 ms-3">
                        <small class="text-muted d-block">Email</small>
                        <strong>{{ $petugas->email }}</strong>
                    </div>
                </div>
                @endif

                @if($petugas->telepon)
                <div class="d-flex align-items-center mb-3">
                    <div class="flex-shrink-0">
                        <i class="fas fa-phone text-muted"></i>
                    </div>
                    <div class="flex-grow-1 ms-3">
                        <small class="text-muted d-block">Telepon</small>
                        <strong>{{ $petugas->telepon }}</strong>
                    </div>
                </div>
                @endif

                <div class="d-flex align-items-center">
                    <div class="flex-shrink-0">
                        <i class="fas fa-calendar text-muted"></i>
                    </div>
                    <div class="flex-grow-1 ms-3">
                        <small class="text-muted d-block">Bergabung</small>
                        <strong>{{ $petugas->created_at->format('d M Y') }}</strong>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Performance & Activity -->
    <div class="col-12 col-md-8">
        <!-- Performance Stats -->
        <div class="card border-0">
            <div class="card-header bg-white py-2">
                <h6 class="mb-0"><i class="fas fa-chart-line me-2 text-success"></i>Performance Bulan Ini</h6>
            </div>
            <div class="card-body">
                <div class="row text-center">
                    <div class="col-4">
                        <div class="border-end">
                            <h3 class="text-primary mb-0">{{ $performance['total_perubahan'] }}</h3>
                            <small class="text-muted">Total Perubahan</small>
                        </div>
                    </div>
                    <div class="col-4">
                        <div class="border-end">
                            <h3 class="text-success mb-0">{{ $performance['berhasil_memperbaiki'] }}</h3>
                            <small class="text-muted">Berhasil Diperbaiki</small>
                        </div>
                    </div>
                    <div class="col-4">
                        <div>
                            <h3 class="text-warning mb-0">{{ $performance['success_rate'] }}%</h3>
                            <small class="text-muted">Success Rate</small>
                        </div>
                    </div>
                </div>

                @if($performance['total_perubahan'] > 0)
                <div class="mt-3">
                    <div class="d-flex justify-content-between mb-1">
                        <small class="text-muted">Progress Improvement</small>
                        <small class="text-muted">{{ $performance['berhasil_memperbaiki'] }}/{{ $performance['total_perubahan'] }}</small>
                    </div>
                    <div class="progress" style="height: 10px;">
                        <div class="progress-bar bg-success" style="width: {{ $performance['success_rate'] }}%"></div>
                    </div>
                </div>
                @else
                <div class="text-center text-muted py-3">
                    <i class="fas fa-chart-bar fa-2x mb-2"></i>
                    <p class="mb-0">Belum ada data performance bulan ini</p>
                </div>
                @endif
            </div>
        </div>

        <!-- Recent Activity -->
        <div class="card border-0 mt-3">
            <div class="card-header bg-white py-2 d-flex justify-content-between align-items-center">
                <h6 class="mb-0"><i class="fas fa-history me-2 text-primary"></i>Aktivitas Terbaru</h6>
                <a href="{{ route('nasabah.index') }}?petugas_id={{ $petugas->id }}" class="btn btn-sm btn-outline-primary">
                    Lihat Semua Nasabah
                </a>
            </div>
            <div class="card-body p-0">
                <div class="list-group list-group-flush">
                    @forelse($petugas->historyKolektibilitas->take(5) as $activity)
                    <div class="list-group-item">
                        <div class="d-flex justify-content-between align-items-start">
                            <div class="flex-grow-1">
                                <div class="d-flex justify-content-between align-items-center mb-1">
                                    <strong class="text-truncate me-2" style="max-width: 200px;">
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
                                @if($activity->keterangan)
                                <small class="text-muted">{{ $activity->keterangan }}</small>
                                @endif
                            </div>
                            <a href="{{ route('nasabah.show', $activity->nasabah->id) }}" 
                               class="btn btn-sm btn-outline-primary ms-2">
                                <i class="fas fa-eye"></i>
                            </a>
                        </div>
                    </div>
                    @empty
                    <div class="list-group-item text-center py-4">
                        <i class="fas fa-history fa-2x text-muted mb-3"></i>
                        <h6 class="text-muted">Belum ada aktivitas</h6>
                        <small class="text-muted">Petugas ini belum menangani perubahan kolektibilitas</small>
                    </div>
                    @endforelse
                </div>
            </div>
        </div>

        <!-- Assigned Nasabahs -->
        <div class="card border-0 mt-3">
            <div class="card-header bg-white py-2">
                <h6 class="mb-0"><i class="fas fa-users me-2 text-info"></i>Nasabah yang Ditangani</h6>
            </div>
            <div class="card-body">
                @if($petugas->nasabahs_count > 0)
                <div class="table-responsive">
                    <table class="table table-sm table-hover">
                        <thead>
                            <tr>
                                <th>Nama Nasabah</th>
                                <th>Kolektibilitas</th>
                                <th>Plafon</th>
                                <th>Ditangani</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($petugas->nasabahs->take(5) as $nasabah)
                            <tr>
                                <td>
                                    <a href="{{ route('nasabah.show', $nasabah->id) }}" class="text-decoration-none">
                                        <strong>{{ $nasabah->namadb }}</strong>
                                    </a>
                                    <br>
                                    <small class="text-muted">{{ $nasabah->nocif }}</small>
                                </td>
                                <td>
                                    <span class="badge {{ \App\Helpers\QualityHelper::getQualityBadge($nasabah->kualitas) }}">
                                        {{ \App\Helpers\QualityHelper::getQualityLabel($nasabah->kualitas) }}
                                    </span>
                                </td>
                                <td>
                                    <strong>Rp {{ number_format($nasabah->plafon, 0, ',', '.') }}</strong>
                                </td>
                                <td>
                                    <small class="text-muted">
                                        {{ $nasabah->tanggal_ditangani ? $nasabah->tanggal_ditangani->format('d M Y') : '-' }}
                                    </small>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                
                @if($petugas->nasabahs_count > 5)
                <div class="text-center mt-2">
                    <a href="{{ route('nasabah.index') }}?petugas_id={{ $petugas->id }}" class="btn btn-sm btn-outline-primary">
                        Lihat Semua {{ $petugas->nasabahs_count }} Nasabah
                    </a>
                </div>
                @endif
                @else
                <div class="text-center text-muted py-3">
                    <i class="fas fa-users fa-2x mb-2"></i>
                    <h6 class="text-muted">Belum ada nasabah yang ditangani</h6>
                    <small class="text-muted">Assign nasabah melalui menu Data Nasabah</small>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection