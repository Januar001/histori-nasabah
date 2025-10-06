@extends('layouts.app')

@section('title', 'Analisis Kolektibilitas Murni')

@section('content')
<div class="row">
    <div class="col-12 mb-3">
        <h4 class="mb-0"><i class="fas fa-vial me-2 text-primary"></i>Analisis Kolektibilitas Murni</h4>
        <small class="text-muted">Memantau nasabah yang bertahan, masuk, dan berpotensi masuk ke setiap status kolektibilitas.</small>
    </div>
</div>

<div class="card border-0 mb-4">
    <div class="card-body">
        <form method="GET" action="{{ route('analisis.kolektibilitas-murni') }}">
            <div class="row align-items-end">
                <div class="col-md-4">
                    <label for="tanggal" class="form-label">Pilih Tanggal Laporan</label>
                    <select name="tanggal" id="tanggal" class="form-select">
                        @foreach($availableDates as $date)
                            <option value="{{ $date }}" {{ $selectedDate == $date ? 'selected' : '' }}>{{ \Carbon\Carbon::parse($date)->translatedFormat('d F Y') }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-4">
                    <button type="submit" class="btn btn-primary w-100"><i class="fas fa-search me-1"></i> Tampilkan Analisis</button>
                </div>
            </div>
        </form>
    </div>
</div>

@if($selectedDate)
    @foreach($results as $kualitas => $data)
    <div class="card border-0 mb-4">
        <div class="card-header bg-white d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Analisis <span class="badge {{ \App\Helpers\QualityHelper::getQualityBadge($kualitas) }}">{{ \App\Helpers\QualityHelper::getQualityLabel($kualitas) }}</span></h5>
            <span class="badge bg-primary rounded-pill">Total: {{ $data['total_nasabah'] }} Nasabah</span>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-3 text-center border-end">
                    <h3 class="mb-0">{{ $data['kolek_murni_count'] }}</h3>
                    <small class="text-muted">Kolektibilitas Murni</small>
                    <p class="small">(Bertahan dari bulan lalu)</p>
                </div>
                <div class="col-md-3 text-center border-end">
                    <h3 class="mb-0">{{ $data['masuk_count'] }}</h3>
                    <small class="text-muted">Nasabah Masuk</small>
                    <p class="small">(Baru masuk di bulan ini)</p>
                </div>
                <div class="col-md-3 text-center">
                     <h3 class="mb-0">{{ $data['potensi_masuk_count'] }}</h3>
                    <small class="text-muted">Potensi Masuk</small>
                    <p class="small">(Dari KOL {{ $kualitas - 1 }})</p>
                </div>
                <div class="col-md-3">
                    <button class="btn btn-outline-secondary btn-sm w-100 mb-1" type="button" data-bs-toggle="collapse" data-bs-target="#detailMasuk{{$kualitas}}">Lihat Detail Masuk</button>
                    <button class="btn btn-outline-warning btn-sm w-100" type="button" data-bs-toggle="collapse" data-bs-target="#detailPotensi{{$kualitas}}">Lihat Detail Potensi</button>
                </div>
            </div>
            <div class="collapse mt-3" id="detailMasuk{{$kualitas}}">
                <h6>Detail Nasabah Masuk ke KOL {{ $kualitas }}</h6>
                <ul class="list-group">
                    @forelse($data['nasabah_masuk'] as $nasabah)
                    <li class="list-group-item">{{ $nasabah->namadb }} ({{ $nasabah->nocif }})</li>
                    @empty
                    <li class="list-group-item">Tidak ada.</li>
                    @endforelse
                </ul>
            </div>
            <div class="collapse mt-3" id="detailPotensi{{$kualitas}}">
                <h6>Detail Potensi Masuk ke KOL {{ $kualitas }}</h6>
                 <ul class="list-group">
                    @forelse($data['nasabah_potensi_masuk'] as $nasabah)
                    <li class="list-group-item">{{ $nasabah->namadb }} ({{ $nasabah->nocif }})</li>
                    @empty
                    <li class="list-group-item">Tidak ada.</li>
                    @endforelse
                </ul>
            </div>
        </div>
    </div>
    @endforeach
@else
    <div class="alert alert-info">Pilih tanggal laporan untuk memulai analisis.</div>
@endif

@endsection