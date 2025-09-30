@extends('layouts.app')

@section('content')
<div class="row">
    <div class="col-12 mb-3">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h4 class="mb-0"><i class="fas fa-user-tie me-2 text-primary"></i>Data Petugas</h4>
                <small class="text-muted">Management petugas AO, Remedial, dan Special</small>
            </div>
            <a href="{{ route('petugas.create') }}" class="btn btn-primary">
                <i class="fas fa-plus me-1"></i> Tambah Petugas
            </a>
        </div>
    </div>
</div>

<!-- Stats Cards -->
<div class="row g-2 mb-4">
    <div class="col-6 col-md-3">
        <div class="card border-0 h-100">
            <div class="card-body p-3 text-center">
                <div class="text-primary mb-2">
                    <i class="fas fa-users fa-2x"></i>
                </div>
                <h4 class="mb-1">{{ $stats['total'] }}</h4>
                <small class="text-muted">Total Petugas</small>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="card border-0 h-100">
            <div class="card-body p-3 text-center">
                <div class="text-success mb-2">
                    <i class="fas fa-user-check fa-2x"></i>
                </div>
                <h4 class="mb-1">{{ $stats['ao'] }}</h4>
                <small class="text-muted">Petugas AO</small>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="card border-0 h-100">
            <div class="card-body p-3 text-center">
                <div class="text-warning mb-2">
                    <i class="fas fa-user-shield fa-2x"></i>
                </div>
                <h4 class="mb-1">{{ $stats['remedial'] }}</h4>
                <small class="text-muted">Petugas Remedial</small>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="card border-0 h-100">
            <div class="card-body p-3 text-center">
                <div class="text-info mb-2">
                    <i class="fas fa-user-cog fa-2x"></i>
                </div>
                <h4 class="mb-1">{{ $stats['special'] }}</h4>
                <small class="text-muted">Petugas Special</small>
            </div>
        </div>
    </div>
</div>

<!-- Petugas List -->
<div class="row">
    <div class="col-12">
        <div class="card border-0">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Kode</th>
                                <th>Nama Petugas</th>
                                <th>Divisi</th>
                                <th>Kontak</th>
                                <th>Total Nasabah</th>
                                <th>Status</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($petugas as $p)
                            <tr>
                                <td>
                                    <strong>{{ $p->kode_petugas }}</strong>
                                </td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="flex-shrink-0">
                                            <div class="bg-primary rounded-circle d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                                                <i class="fas fa-user text-white"></i>
                                            </div>
                                        </div>
                                        <div class="flex-grow-1 ms-3">
                                            <h6 class="mb-0">{{ $p->nama_petugas }}</h6>
                                            @if($p->email)
                                            <small class="text-muted">{{ $p->email }}</small>
                                            @endif
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <span class="badge 
                                        @if($p->divisi == 'AO') bg-success
                                        @elseif($p->divisi == 'Remedial') bg-warning
                                        @else bg-info @endif">
                                        {{ $p->divisi }}
                                    </span>
                                </td>
                                <td>
                                    @if($p->telepon)
                                    <small class="text-muted d-block">
                                        <i class="fas fa-phone me-1"></i> {{ $p->telepon }}
                                    </small>
                                    @endif
                                    @if($p->email)
                                    <small class="text-muted d-block">
                                        <i class="fas fa-envelope me-1"></i> {{ $p->email }}
                                    </small>
                                    @endif
                                </td>
                                <td>
                                    <div class="text-center">
                                        <h5 class="mb-0 text-primary">{{ $p->nasabahs_count }}</h5>
                                        <small class="text-muted">Nasabah</small>
                                    </div>
                                </td>
                                <td>
                                    @if($p->status_aktif)
                                    <span class="badge bg-success">
                                        <i class="fas fa-check me-1"></i> Aktif
                                    </span>
                                    @else
                                    <span class="badge bg-secondary">
                                        <i class="fas fa-times me-1"></i> Non-Aktif
                                    </span>
                                    @endif
                                </td>
                                <td>
                                    <div class="d-flex gap-1">
                                        <a href="{{ route('petugas.show', $p->id) }}" class="btn btn-sm btn-outline-primary">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="{{ route('petugas.edit', $p->id) }}" class="btn btn-sm btn-outline-warning">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        @if($p->nasabahs_count == 0)
                                        <form action="{{ route('petugas.destroy', $p->id) }}" method="POST" class="d-inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-outline-danger" onclick="return confirm('Hapus petugas ini?')">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                        @else
                                        <button class="btn btn-sm btn-outline-secondary" disabled title="Tidak dapat dihapus karena masih menangani nasabah">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="7" class="text-center py-4">
                                    <i class="fas fa-user-tie fa-3x text-muted mb-3"></i>
                                    <h5 class="text-muted">Belum ada data petugas</h5>
                                    <p class="text-muted">Tambahkan petugas untuk mulai menangani nasabah</p>
                                    <a href="{{ route('petugas.create') }}" class="btn btn-primary">
                                        <i class="fas fa-plus me-1"></i> Tambah Petugas Pertama
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

<!-- Quick Actions -->
<div class="row mt-4">
    <div class="col-12">
        <div class="card border-0">
            <div class="card-header bg-white py-2">
                <h6 class="mb-0"><i class="fas fa-bolt me-2 text-warning"></i>Quick Actions</h6>
            </div>
            <div class="card-body">
                <div class="row g-2">
                    <div class="col-md-4">
                        <a href="{{ route('nasabah.index') }}?petugas_id=" class="btn btn-outline-primary w-100 text-start">
                            <i class="fas fa-users me-2"></i>
                            <div>
                                <strong>Lihat Semua Nasabah</strong>
                                <small class="d-block text-muted">Kelola data nasabah</small>
                            </div>
                        </a>
                    </div>
                    <div class="col-md-4">
                        <a href="{{ route('nasabah.index') }}?petugas_id=&kualitas=5" class="btn btn-outline-danger w-100 text-start">
                            <i class="fas fa-exclamation-triangle me-2"></i>
                            <div>
                                <strong>Nasabah Macet</strong>
                                <small class="d-block text-muted">Butuh penanganan khusus</small>
                            </div>
                        </a>
                    </div>
                    <div class="col-md-4">
                        <a href="{{ route('petugas.create') }}" class="btn btn-outline-success w-100 text-start">
                            <i class="fas fa-user-plus me-2"></i>
                            <div>
                                <strong>Tambah Petugas</strong>
                                <small class="d-block text-muted">Expand tim penanganan</small>
                            </div>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection