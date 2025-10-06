@extends('layouts.app')

@section('title', 'Analisis Pergerakan Kolektibilitas')

@section('content')
<div class="row">
    <div class="col-12 mb-3">
        <h4 class="mb-0"><i class="fas fa-analytics me-2 text-primary"></i>Analisis Pergerakan Kolektibilitas</h4>
        <small class="text-muted">Melihat pergerakan nasabah antar status kolektibilitas pada periode tertentu</small>
    </div>
</div>

<div class="card border-0 mb-4">
    <div class="card-body">
        <form method="GET" action="{{ route('analisis.pergerakan-kol') }}">
            <div class="row align-items-end">
                <div class="col-md-4">
                    <label for="start_date" class="form-label">Tanggal Mulai</label>
                    <input type="date" name="start_date" id="start_date" class="form-control" value="{{ $startDate }}">
                </div>
                <div class="col-md-4">
                    <label for="end_date" class="form-label">Tanggal Akhir</label>
                    <input type="date" name="end_date" id="end_date" class="form-control" value="{{ $endDate }}">
                </div>
                <div class="col-md-4">
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="fas fa-search me-1"></i> Tampilkan Analisis
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

@for ($i = 1; $i <= 5; $i++)
<div class="card border-0 mb-4">
    <div class="card-header bg-white">
        <h5 class="mb-0">
            Analisis <span class="badge {{ \App\Helpers\QualityHelper::getQualityBadge($i) }}">{{ \App\Helpers\QualityHelper::getQualityLabel($i) }}</span>
        </h5>
    </div>
    <div class="card-body">
        <div class="row">
            <div class="col-md-4">
                <h6><i class="fas fa-arrow-up text-success me-1"></i> Masuk (Membaik) <span class="badge bg-success rounded-pill">{{ $results[$i]['masuk_membaik']->count() }}</span></h6>
                <div class="list-group">
                    @forelse($results[$i]['masuk_membaik'] as $history)
                        <a href="{{ route('nasabah.show', $history->nasabah_id) }}" class="list-group-item list-group-item-action">
                            <div class="d-flex w-100 justify-content-between">
                                <h6 class="mb-1">{{ $history->nasabah->namadb }}</h6>
                                <small>{{ \Carbon\Carbon::parse($history->tanggal_perubahan)->format('d/m/y') }}</small>
                            </div>
                            <small>
                                <span class="badge {{ \App\Helpers\QualityHelper::getQualityBadge($history->kolektibilitas_sebelum) }}">{{ $history->kolektibilitas_sebelum }}</span>
                                &rarr;
                                <span class="badge {{ \App\Helpers\QualityHelper::getQualityBadge($history->kolektibilitas_sesudah) }}">{{ $history->kolektibilitas_sesudah }}</span>
                            </small>
                        </a>
                    @empty
                        <div class="list-group-item text-muted">Tidak ada data</div>
                    @endforelse
                </div>
            </div>
            
            <div class="col-md-4">
                <h6><i class="fas fa-arrow-down text-danger me-1"></i> Masuk (Memburuk) <span class="badge bg-danger rounded-pill">{{ $results[$i]['masuk_memburuk']->count() }}</span></h6>
                <div class="list-group">
                     @forelse($results[$i]['masuk_memburuk'] as $history)
                        <a href="{{ route('nasabah.show', $history->nasabah_id) }}" class="list-group-item list-group-item-action">
                            <div class="d-flex w-100 justify-content-between">
                                <h6 class="mb-1">{{ $history->nasabah->namadb }}</h6>
                                <small>{{ \Carbon\Carbon::parse($history->tanggal_perubahan)->format('d/m/y') }}</small>
                            </div>
                            <small>
                                <span class="badge {{ \App\Helpers\QualityHelper::getQualityBadge($history->kolektibilitas_sebelum) }}">{{ $history->kolektibilitas_sebelum }}</span>
                                &rarr;
                                <span class="badge {{ \App\Helpers\QualityHelper::getQualityBadge($history->kolektibilitas_sesudah) }}">{{ $history->kolektibilitas_sesudah }}</span>
                            </small>
                        </a>
                    @empty
                        <div class="list-group-item text-muted">Tidak ada data</div>
                    @endforelse
                </div>
            </div>

            <div class="col-md-4">
                <h6><i class="fas fa-sign-out-alt text-info me-1"></i> Keluar (Membaik) <span class="badge bg-info rounded-pill">{{ $results[$i]['keluar_membaik']->count() }}</span></h6>
                 <div class="list-group">
                     @forelse($results[$i]['keluar_membaik'] as $history)
                        <a href="{{ route('nasabah.show', $history->nasabah_id) }}" class="list-group-item list-group-item-action">
                            <div class="d-flex w-100 justify-content-between">
                                <h6 class="mb-1">{{ $history->nasabah->namadb }}</h6>
                                <small>{{ \Carbon\Carbon::parse($history->tanggal_perubahan)->format('d/m/y') }}</small>
                            </div>
                            <small>
                                <span class="badge {{ \App\Helpers\QualityHelper::getQualityBadge($history->kolektibilitas_sebelum) }}">{{ $history->kolektibilitas_sebelum }}</span>
                                &rarr;
                                <span class="badge {{ \App\Helpers\QualityHelper::getQualityBadge($history->kolektibilitas_sesudah) }}">{{ $history->kolektibilitas_sesudah }}</span>
                            </small>
                        </a>
                    @empty
                        <div class="list-group-item text-muted">Tidak ada data</div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</div>
@endfor
@endsection