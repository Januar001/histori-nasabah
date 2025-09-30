@extends('layouts.app')

@section('content')
<div class="row">
    <div class="col-12 mb-3">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h4 class="mb-0"><i class="fas fa-users me-2 text-primary"></i>Data Nasabah</h4>
                <small class="text-muted">
                    @if(request('kualitas'))
                        Filter: {{ \App\Helpers\QualityHelper::getQualityLabel(request('kualitas')) }}
                    @elseif(request('petugas_id'))
                        Filter: Petugas {{ $petugas->where('id', request('petugas_id'))->first()->nama_petugas ?? '' }}
                    @else
                        Semua nasabah
                    @endif
                </small>
            </div>
            <div class="d-flex gap-2">
                @if(request('search') || request('kualitas') || request('petugas_id'))
                <a href="{{ route('nasabah.index') }}" class="btn btn-sm btn-outline-secondary">
                    <i class="fas fa-refresh"></i>
                </a>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Search & Filter Section -->
<div class="row">
    <div class="col-12 mb-4">
        <div class="card border-0">
            <div class="card-body">
                <form action="{{ route('nasabah.index') }}" method="GET">
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label for="search" class="form-label">Cari Nasabah</label>
                            <div class="input-group">
                                <input type="text" name="search" class="form-control" 
                                       placeholder="Cari berdasarkan nama, nocif, atau rekening..." 
                                       value="{{ request('search') }}">
                                <button class="btn btn-primary" type="submit">
                                    <i class="fas fa-search"></i>
                                </button>
                            </div>
                        </div>
                        <div class="col-md-3 mb-3">
                            <label for="kualitas" class="form-label">Filter Kolektibilitas</label>
                            <select name="kualitas" class="form-select" onchange="this.form.submit()">
                                <option value="">Semua Kolektibilitas</option>
                                @foreach($qualityOptions as $value => $label)
                                <option value="{{ $value }}" {{ request('kualitas') == $value ? 'selected' : '' }}>
                                    {{ $label }}
                                </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-3 mb-3">
                            <label for="petugas_id" class="form-label">Filter Petugas</label>
                            <select name="petugas_id" class="form-select" onchange="this.form.submit()">
                                <option value="">Semua Petugas</option>
                                @foreach($petugas as $p)
                                <option value="{{ $p->id }}" {{ request('petugas_id') == $p->id ? 'selected' : '' }}>
                                    {{ $p->nama_petugas }} ({{ $p->divisi }})
                                </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-2 mb-3">
                            <label class="form-label">&nbsp;</label>
                            <div class="d-grid">
                                <a href="{{ route('nasabah.index') }}" class="btn btn-outline-secondary">
                                    Reset
                                </a>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Quick Filter Buttons -->
<div class="row mb-4">
    <div class="col-12">
        <div class="card border-0">
            <div class="card-body py-3">
                <div class="d-flex flex-wrap gap-2">
                    <small class="text-muted me-2 align-self-center">Filter Cepat:</small>
                    <a href="{{ route('nasabah.index') }}?kualitas=5" class="btn btn-sm btn-danger">
                        <i class="fas fa-exclamation-triangle me-1"></i>Nasabah Macet
                    </a>
                    <a href="{{ route('nasabah.index') }}?kualitas=4" class="btn btn-sm btn-warning">
                        <i class="fas fa-exclamation-circle me-1"></i>Nasabah Diragukan
                    </a>
                    <a href="{{ route('nasabah.index') }}?kualitas=1" class="btn btn-sm btn-success">
                        <i class="fas fa-check-circle me-1"></i>Nasabah Lancar
                    </a>
                    <a href="{{ route('nasabah.index') }}?petugas_id=" class="btn btn-sm btn-info">
                        <i class="fas fa-user-tie me-1"></i>Belum Ditugaskan
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Results Count -->
@if(request('search') || request('kualitas') || request('petugas_id'))
<div class="row mb-3">
    <div class="col-12">
        <div class="alert alert-info py-2">
            <i class="fas fa-info-circle me-2"></i>
            Menampilkan <strong>{{ $nasabahs->total() }}</strong> nasabah
            @if(request('search'))
                dengan kata kunci "<strong>{{ request('search') }}</strong>"
            @endif
            @if(request('kualitas'))
                dengan status <strong>{{ \App\Helpers\QualityHelper::getQualityLabel(request('kualitas')) }}</strong>
            @endif
            @if(request('petugas_id'))
                dengan petugas <strong>{{ $petugas->where('id', request('petugas_id'))->first()->nama_petugas ?? '' }}</strong>
            @endif
        </div>
    </div>
</div>
@endif

<!-- Nasabah List -->
<div class="row">
    <div class="col-12">
        <div class="card border-0">
            <div class="card-body p-0">
                <div class="list-group list-group-flush">
                    @forelse($nasabahs as $nasabah)
                    <div class="list-group-item">
                        <div class="d-flex justify-content-between align-items-start">
                            <div class="flex-grow-1">
                                <div class="d-flex justify-content-between align-items-center mb-1">
                                    <h6 class="mb-0 text-truncate me-2" style="max-width: 200px;">
                                        {{ $nasabah->namadb }}
                                    </h6>
                                    <span class="badge {{ \App\Helpers\QualityHelper::getQualityBadge($nasabah->kualitas) }}">
                                        {{ \App\Helpers\QualityHelper::getQualityLabel($nasabah->kualitas) }}
                                    </span>
                                </div>
                                
                                <div class="row g-2 mb-2">
                                    <div class="col-6">
                                        <small class="text-muted d-block">NOCIF</small>
                                        <strong>{{ $nasabah->nocif }}</strong>
                                    </div>
                                    <div class="col-6">
                                        <small class="text-muted d-block">Rekening</small>
                                        <strong>{{ $nasabah->rekening }}</strong>
                                    </div>
                                </div>
                                
                                <div class="row g-2">
                                    <div class="col-6">
                                        <small class="text-muted d-block">Plafon</small>
                                        <strong class="text-primary">Rp {{ number_format($nasabah->plafon, 0, ',', '.') }}</strong>
                                    </div>
                                    <div class="col-6">
                                        <small class="text-muted d-block">Baki Debet</small>
                                        <strong class="{{ $nasabah->bakidebet > 0 ? 'text-danger' : 'text-success' }}">
                                            Rp {{ number_format($nasabah->bakidebet, 0, ',', '.') }}
                                        </strong>
                                    </div>
                                </div>
                                
                                @if($nasabah->petugas)
                                <small class="text-muted mt-1 d-block">
                                    <i class="fas fa-user-tie me-1"></i> 
                                    {{ $nasabah->petugas->nama_petugas }} 
                                    <span class="badge bg-secondary ms-1">{{ $nasabah->petugas->divisi }}</span>
                                    @if($nasabah->tanggal_ditangani)
                                    <small class="text-muted ms-2">
                                        <i class="fas fa-calendar me-1"></i>
                                        {{ $nasabah->tanggal_ditangani->format('d M Y') }}
                                    </small>
                                    @endif
                                </small>
                                @else
                                <small class="text-warning mt-1 d-block">
                                    <i class="fas fa-exclamation-triangle me-1"></i> Belum ada petugas yang ditugaskan
                                </small>
                                @endif
                            </div>
                            
                            <div class="ms-3">
                                <a href="{{ route('nasabah.show', $nasabah->id) }}" 
                                   class="btn btn-sm btn-outline-primary">
                                    <i class="fas fa-eye"></i> Detail
                                </a>
                            </div>
                        </div>
                    </div>
                    @empty
                    <div class="list-group-item text-center py-5">
                        <i class="fas fa-search fa-2x text-muted mb-3"></i>
                        <h5 class="text-muted">Tidak ada data nasabah</h5>
                        <p class="text-muted mb-3">
                            @if(request('search') || request('kualitas') || request('petugas_id'))
                                Coba ubah kata kunci pencarian atau filter
                            @else
                                Belum ada data nasabah yang terdaftar
                            @endif
                        </p>
                        @if(request('search') || request('kualitas') || request('petugas_id'))
                        <a href="{{ route('nasabah.index') }}" class="btn btn-primary">
                            <i class="fas fa-refresh me-1"></i> Tampilkan Semua
                        </a>
                        @endif
                    </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Pagination -->
@if($nasabahs->hasPages())
<div class="row mt-3">
    <div class="col-12">
        <div class="d-flex justify-content-center">
            {{ $nasabahs->links('pagination::bootstrap-5') }}
        </div>
    </div>
</div>
@endif

<!-- Quick Stats -->
@if(!request('search') && !request('kualitas') && !request('petugas_id'))
<div class="row mt-4">
    <div class="col-12">
        <div class="card border-0">
            <div class="card-header bg-white">
                <h5 class="card-title mb-0">
                    <i class="fas fa-chart-pie me-2"></i>Statistik Kolektibilitas
                </h5>
            </div>
            <div class="card-body">
                <div class="row text-center">
                    @php
                        $stats = \App\Models\Nasabah::select('kualitas', \DB::raw('COUNT(*) as total'))
                            ->groupBy('kualitas')
                            ->get()
                            ->keyBy('kualitas');
                    @endphp
                    <div class="col">
                        <div class="border-end">
                            <h3 class="text-success">{{ $stats['1']->total ?? 0 }}</h3>
                            <small class="text-muted">Lancar</small>
                        </div>
                    </div>
                    <div class="col">
                        <div class="border-end">
                            <h3 class="text-info">{{ $stats['2']->total ?? 0 }}</h3>
                            <small class="text-muted">Dalam Perhatian</small>
                        </div>
                    </div>
                    <div class="col">
                        <div class="border-end">
                            <h3 class="text-warning">{{ $stats['3']->total ?? 0 }}</h3>
                            <small class="text-muted">Kurang Lancar</small>
                        </div>
                    </div>
                    <div class="col">
                        <div class="border-end">
                            <h3 class="text-danger">{{ $stats['4']->total ?? 0 }}</h3>
                            <small class="text-muted">Diragukan</small>
                        </div>
                    </div>
                    <div class="col">
                        <div>
                            <h3 class="text-dark">{{ $stats['5']->total ?? 0 }}</h3>
                            <small class="text-muted">Macet</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endif
@endsection