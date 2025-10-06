@extends('layouts.app')

@section('content')
<div class="row">
    <div class="col-12 mb-3">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h4 class="mb-0"><i class="fas fa-chart-line me-2 text-primary"></i>Analisis Kinerja AO</h4>
                <small class="text-muted">Menganalisis perbaikan dan penurunan kolektibilitas oleh Account Officer</small>
            </div>
        </div>
    </div>
</div>

<!-- Filter Section -->
<div class="row">
    <div class="col-12 mb-4">
        <div class="card border-0">
            <div class="card-body">
                <form action="{{ route('analisis.ao') }}" method="GET">
                    <div class="row align-items-end">
                        <div class="col-md-4 mb-3 mb-md-0">
                            <label for="bulan" class="form-label">Pilih Bulan</label>
                            <select name="bulan" id="bulan" class="form-select">
                                @for ($m = 1; $m <= 12; $m++)
                                    <option value="{{ $m }}" {{ $bulan == $m ? 'selected' : '' }}>
                                        {{ \Carbon\Carbon::create(null, $m, 1)->translatedFormat('F') }}
                                    </option>
                                @endfor
                            </select>
                        </div>
                        <div class="col-md-4 mb-3 mb-md-0">
                            <label for="tahun" class="form-label">Pilih Tahun</label>
                            <select name="tahun" id="tahun" class="form-select">
                                @for ($y = date('Y'); $y >= date('Y') - 5; $y--)
                                    <option value="{{ $y }}" {{ $tahun == $y ? 'selected' : '' }}>
                                        {{ $y }}
                                    </option>
                                @endfor
                            </select>
                        </div>
                        <div class="col-md-4">
                            <button class="btn btn-primary w-100" type="submit">
                                <i class="fas fa-filter me-1"></i> Terapkan Filter
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Results Table -->
<div class="row">
    <div class="col-12">
        <div class="card border-0">
            <div class="card-header bg-white py-3">
                <h5 class="mb-0">
                    Hasil Analisis untuk 
                    <span class="text-primary">{{ \Carbon\Carbon::create(null, $bulan, 1)->translatedFormat('F') }} {{ $tahun }}</span>
                </h5>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover table-bordered mb-0">
                        <thead class="table-light text-center">
                            <tr>
                                <th rowspan="2" class="align-middle">Nama AO</th>
                                <th rowspan="2" class="align-middle"><i class="fas fa-users"></i> Total Nasabah</th>
                                <th colspan="3">Kinerja</th>
                                <th colspan="5">Distribusi Kolektibilitas (Sesudah)</th>
                            </tr>
                            <tr>
                                <th><i class="fas fa-arrow-up text-success"></i> Perbaikan</th>
                                <th><i class="fas fa-arrow-down text-danger"></i> Penurunan</th>
                                <th><i class="fas fa-balance-scale"></i> Bersih</th>
                                <th>Kol 1</th>
                                <th>Kol 2</th>
                                <th>Kol 3</th>
                                <th>Kol 4</th>
                                <th>Kol 5</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php
                                $totalNasabah = 0;
                                $totalPerbaikan = 0;
                                $totalPenurunan = 0;
                                $totalKol1 = 0;
                                $totalKol2 = 0;
                                $totalKol3 = 0;
                                $totalKol4 = 0;
                                $totalKol5 = 0;
                            @endphp
                            @forelse($kinerjaAo as $item)
                                @php
                                    $kinerjaBersih = $item->perbaikan - $item->penurunan;
                                    $totalNasabah += $item->total_nasabah_ditangani;
                                    $totalPerbaikan += $item->perbaikan;
                                    $totalPenurunan += $item->penurunan;
                                    $totalKol1 += $item->kol_1;
                                    $totalKol2 += $item->kol_2;
                                    $totalKol3 += $item->kol_3;
                                    $totalKol4 += $item->kol_4;
                                    $totalKol5 += $item->kol_5;
                                @endphp
                                <tr>
                                    <td>
                                        <strong>{{ $item->namaao }}</strong>
                                    </td>
                                    <td class="text-center">
                                        <span class="badge bg-secondary">{{ $item->total_nasabah_ditangani }}</span>
                                    </td>
                                    <td class="text-center">
                                        <span class="badge bg-success">{{ $item->perbaikan }}</span>
                                    </td>
                                    <td class="text-center">
                                        <span class="badge bg-danger">{{ $item->penurunan }}</span>
                                    </td>
                                    <td class="text-center">
                                        @if ($kinerjaBersih > 0)
                                            <span class="badge bg-success fw-bold">+{{ $kinerjaBersih }}</span>
                                        @elseif ($kinerjaBersih < 0)
                                            <span class="badge bg-danger fw-bold">{{ $kinerjaBersih }}</span>
                                        @else
                                            <span class="badge bg-warning fw-bold">{{ $kinerjaBersih }}</span>
                                        @endif
                                    </td>
                                    <td class="text-center">{{ $item->kol_1 > 0 ? $item->kol_1 : '-' }}</td>
                                    <td class="text-center">{{ $item->kol_2 > 0 ? $item->kol_2 : '-' }}</td>
                                    <td class="text-center">{{ $item->kol_3 > 0 ? $item->kol_3 : '-' }}</td>
                                    <td class="text-center">{{ $item->kol_4 > 0 ? $item->kol_4 : '-' }}</td>
                                    <td class="text-center">{{ $item->kol_5 > 0 ? $item->kol_5 : '-' }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="10" class="text-center py-5">
                                        <i class="fas fa-search fa-2x text-muted mb-3"></i>
                                        <h5 class="text-muted">Tidak ada data untuk periode ini</h5>
                                        <p class="text-muted mb-0">Silakan pilih bulan dan tahun yang lain.</p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                        @if($kinerjaAo->isNotEmpty())
                        <tfoot>
                            <tr class="table-dark fw-bold text-center">
                                <td>TOTAL KESELURUHAN</td>
                                <td>{{ $totalNasabah }}</td>
                                <td class="text-success">{{ $totalPerbaikan }}</td>
                                <td class="text-danger">{{ $totalPenurunan }}</td>
                                <td>
                                    @php $totalBersih = $totalPerbaikan - $totalPenurunan; @endphp
                                    @if ($totalBersih > 0)
                                        <span class="text-success">+{{ $totalBersih }}</span>
                                    @elseif ($totalBersih < 0)
                                        <span class="text-danger">{{ $totalBersih }}</span>
                                    @else
                                        <span>{{ $totalBersih }}</span>
                                    @endif
                                </td>
                                <td>{{ $totalKol1 }}</td>
                                <td>{{ $totalKol2 }}</td>
                                <td>{{ $totalKol3 }}</td>
                                <td>{{ $totalKol4 }}</td>
                                <td>{{ $totalKol5 }}</td>
                            </tr>
                        </tfoot>
                        @endif
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Explanation Card -->
<div class="row mt-4">
    <div class="col-12">
        <div class="card border-0">
            <div class="card-header bg-white">
                <h6 class="mb-0"><i class="fas fa-info-circle me-2 text-info"></i>Panduan Membaca Tabel</h6>
            </div>
            <div class="card-body small">
                <dl class="row mb-0">
                    <dt class="col-sm-3 border-bottom pb-2">Total Nasabah</dt>
                    <dd class="col-sm-9 border-bottom pb-2">Jumlah nasabah unik yang statusnya diubah oleh AO pada periode terpilih.</dd>

                    <dt class="col-sm-3 border-bottom py-2">Perbaikan</dt>
                    <dd class="col-sm-9 border-bottom py-2">Jumlah nasabah yang status kolektibilitasnya membaik (misal: dari Kol 5 ke Kol 3).</dd>

                    <dt class="col-sm-3 border-bottom py-2">Penurunan</dt>
                    <dd class="col-sm-9 border-bottom py-2">Jumlah nasabah yang status kolektibilitasnya menurun (misal: dari Kol 1 ke Kol 2).</dd>

                    <dt class="col-sm-3 border-bottom py-2">Kinerja Bersih</dt>
                    <dd class="col-sm-9 border-bottom py-2">Hasil dari (Total Perbaikan - Total Penurunan). Nilai positif menunjukkan kinerja baik.</dd>

                    <dt class="col-sm-3 pt-2">Distribusi Kol (Sesudah)</dt>
                    <dd class="col-sm-9 pt-2">Menunjukkan jumlah nasabah yang status akhirnya berada di setiap level kolektibilitas (Kol 1 s/d 5) setelah adanya perubahan.</dd>
                </dl>
            </div>
        </div>
    </div>
</div>
@endsection

