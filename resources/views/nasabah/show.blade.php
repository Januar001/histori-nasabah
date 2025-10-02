@extends('layouts.app')

@section('content')
<div class="row">
    <div class="col-12 mb-3">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h4 class="mb-0"><i class="fas fa-user me-2 text-primary"></i>Detail Nasabah</h4>
                <small class="text-muted">Informasi lengkap nasabah</small>
            </div>
            <a href="{{ route('nasabah.index') }}" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left me-1"></i> Kembali
            </a>
        </div>
    </div>
</div>

<!-- Tab Navigation -->
<div class="row">
    <div class="col-12 mb-4">
        <ul class="nav nav-tabs" id="nasabahTabs" role="tablist">
            <li class="nav-item" role="presentation">
                <button class="nav-link active" id="info-tab" data-bs-toggle="tab" data-bs-target="#info" type="button" role="tab">
                    <i class="fas fa-info-circle me-1"></i>Informasi Utama
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="financial-tab" data-bs-toggle="tab" data-bs-target="#financial" type="button" role="tab">
                    <i class="fas fa-chart-line me-1"></i>Data Finansial
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="collateral-tab" data-bs-toggle="tab" data-bs-target="#collateral" type="button" role="tab">
                    <i class="fas fa-home me-1"></i>Jaminan & Klasifikasi
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="janji-tab" data-bs-toggle="tab" data-bs-target="#janji" type="button" role="tab">
                    <i class="fas fa-handshake me-1"></i>Janji Bayar
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="history-tab" data-bs-toggle="tab" data-bs-target="#history" type="button" role="tab">
                    <i class="fas fa-history me-1"></i>History Kolektibilitas
                </button>
            </li>
        </ul>
        
        <div class="tab-content" id="nasabahTabsContent">
            <!-- Tab 1: Informasi Utama -->
            <div class="tab-pane fade show active" id="info" role="tabpanel">
                <div class="row mt-3">
                    <!-- Data Identitas -->
                    <div class="col-12 col-md-6 mb-4">
                        <div class="card border-0 h-100">
                            <div class="card-header bg-white py-3">
                                <h6 class="mb-0"><i class="fas fa-id-card me-2 text-primary"></i>Data Identitas</h6>
                            </div>
                            <div class="card-body">
                                <table class="table table-sm table-borderless">
                                    <tr>
                                        <th width="45%" class="text-muted small">No. CIF</th>
                                        <td>{{ $nasabah->nocif }}</td>
                                    </tr>
                                    <tr>
                                        <th class="text-muted small">No. Rekening</th>
                                        <td>{{ $nasabah->rekening }}</td>
                                    </tr>
                                    <tr>
                                        <th class="text-muted small">Nama Lengkap</th>
                                        <td><strong>{{ $nasabah->namadb }}</strong></td>
                                    </tr>
                                    <tr>
                                        <th class="text-muted small">Kantor</th>
                                        <td>
                                            @if($nasabah->kantor == '001')
                                                Kantor Pusat
                                            @else
                                                {{ $nasabah->kantor ?: '-' }}
                                            @endif
                                        </td>
                                    </tr>
                                    <tr>
                                        <th class="text-muted small">No. PK</th>
                                        <td>{{ $nasabah->nopk ?: '-' }}</td>
                                    </tr>
                                    <tr>
                                        <th class="text-muted small">CIF Lama</th>
                                        <td>{{ $nasabah->ciflama ?: '-' }}</td>
                                    </tr>
                                    <tr>
                                        <th class="text-muted small">Rekening Lama</th>
                                        <td>{{ $nasabah->rekeninglama ?: '-' }}</td>
                                    </tr>
                                </table>
                            </div>
                        </div>
                    </div>

                    <!-- Status & Kualitas -->
                    <div class="col-12 col-md-6 mb-4">
                        <div class="card border-0 h-100">
                            <div class="card-header bg-white py-3">
                                <h6 class="mb-0"><i class="fas fa-chart-bar me-2 text-success"></i>Status & Kualitas</h6>
                            </div>
                            <div class="card-body">
                                <table class="table table-sm table-borderless">
                                    <tr>
                                        <th width="45%" class="text-muted small">Kualitas</th>
                                        <td>
                                            <span class="badge {{ \App\Helpers\QualityHelper::getQualityBadge($nasabah->kualitas) }} fs-6">
                                                {{ \App\Helpers\QualityHelper::getQualityLabel($nasabah->kualitas) }}
                                            </span>
                                        </td>
                                    </tr>
                                    <tr>
                                        <th class="text-muted small">Tanggal Macet</th>
                                        <td>{{ $nasabah->tgl_macet ? \Carbon\Carbon::parse($nasabah->tgl_macet)->format('d M Y') : '-' }}</td>
                                    </tr>
                                    <tr>
                                        <th class="text-muted small">Tanggal Lunas</th>
                                        <td>{{ $nasabah->tglunas ? \Carbon\Carbon::parse($nasabah->tglunas)->format('d M Y') : '-' }}</td>
                                    </tr>
                                    <tr>
                                        <th class="text-muted small">Petugas</th>
                                        <td>
                                            @if($nasabah->petugas)
                                                <span class="badge bg-info">{{ $nasabah->petugas->nama_petugas }}</span>
                                            @else
                                                <span class="badge bg-secondary">Belum diassign</span>
                                            @endif
                                        </td>
                                    </tr>
                                    <tr>
                                        <th class="text-muted small">Nama AO</th>
                                        <td><span class="badge bg-success">{{ $nasabah->namaao ?: '-' }} - {{ $nasabah->kdao ?: '-' }}</span></td>
                                    </tr>
                                    <tr>
                                        <th class="text-muted small">Tanggal Ditangani</th>
                                        <td>{{ $nasabah->tanggal_ditangani ? $nasabah->tanggal_ditangani->format('d M Y') : 'Belum ditangani' }}</td>
                                    </tr>
                                </table>
                            </div>
                        </div>
                    </div>

                    <!-- Alamat Lengkap -->
                    <div class="col-12 mb-4">
                        <div class="card border-0">
                            <div class="card-header bg-white py-3">
                                <h6 class="mb-0"><i class="fas fa-map-marker-alt me-2 text-warning"></i>Alamat Lengkap</h6>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-8">
                                        <p class="mb-2"><strong>Alamat:</strong> {{ $nasabah->alamat ?: '-' }}</p>
                                        <div class="row">
                                            <div class="col-md-4">
                                                <small class="text-muted d-block">Desa</small>
                                                <span>{{ $nasabah->desa ?: '-' }}</span>
                                            </div>
                                            <div class="col-md-4">
                                                <small class="text-muted d-block">Kecamatan</small>
                                                <span>{{ $nasabah->kecamatan ?: '-' }}</span>
                                            </div>
                                            <div class="col-md-4">
                                                <small class="text-muted d-block">Kabupaten/Kota</small>
                                                <span>{{ $nasabah->dati2 ?: '-' }}</span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        @if($nasabah->catatan_penanganan)
                                        <div class="bg-light p-3 rounded">
                                            <small class="text-muted d-block">Catatan Penanganan:</small>
                                            <p class="mb-0 small">{{ $nasabah->catatan_penanganan }}</p>
                                        </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Tab 2: Data Finansial -->
            <div class="tab-pane fade" id="financial" role="tabpanel">
                <div class="row mt-3">
                    <!-- Informasi Pinjaman -->
                    <div class="col-12 col-md-6 mb-4">
                        <div class="card border-0 h-100">
                            <div class="card-header bg-white py-3">
                                <h6 class="mb-0"><i class="fas fa-money-bill-wave me-2 text-success"></i>Informasi Pinjaman</h6>
                            </div>
                            <div class="card-body">
                                <table class="table table-sm table-borderless">
                                    <tr>
                                        <th width="50%" class="text-muted small">Plafon</th>
                                        <td class="text-end"><strong class="text-primary">Rp {{ number_format($nasabah->plafon, 0, ',', '.') }}</strong></td>
                                    </tr>
                                    <tr>
                                        <th class="text-muted small">Baki Debet</th>
                                        <td class="text-end"><strong class="text-danger">Rp {{ number_format($nasabah->bakidebet, 0, ',', '.') }}</strong></td>
                                    </tr>
                                    <tr>
                                        <th class="text-muted small">Baki DB</th>
                                        <td class="text-end">Rp {{ number_format($nasabah->bakidb, 0, ',', '.') }}</td>
                                    </tr>
                                    <tr>
                                        <th class="text-muted small">Rate Bunga</th>
                                        <td class="text-end">{{ number_format($nasabah->rate, 2) }}%</td>
                                    </tr>
                                    <tr>
                                        <th class="text-muted small">Tanggal Pinjam</th>
                                        <td class="text-end">{{ $nasabah->tglpinjam ? \Carbon\Carbon::parse($nasabah->tglpinjam)->format('d M Y') : '-' }}</td>
                                    </tr>
                                    <tr>
                                        <th class="text-muted small">Tanggal Jatuh Tempo</th>
                                        <td class="text-end">{{ $nasabah->tgltempo ? \Carbon\Carbon::parse($nasabah->tgltempo)->format('d M Y') : '-' }}</td>
                                    </tr>
                                </table>
                            </div>
                        </div>
                    </div>

                    <!-- Detail Pokok & Bunga -->
                    <div class="col-12 col-md-6 mb-4">
                        <div class="card border-0 h-100">
                            <div class="card-header bg-white py-3">
                                <h6 class="mb-0"><i class="fas fa-calculator me-2 text-info"></i>Detail Pokok & Bunga</h6>
                            </div>
                            <div class="card-body">
                                <table class="table table-sm table-borderless">
                                    <tr>
                                        <th width="50%" class="text-muted small">Nominal Pokok</th>
                                        <td class="text-end">Rp {{ number_format($nasabah->nompokok, 0, ',', '.') }}</td>
                                    </tr>
                                    <tr>
                                        <th class="text-muted small">Hari Pokok</th>
                                        <td class="text-end">{{ number_format($nasabah->hrpokok, 0, ',', '.') }} hari</td>
                                    </tr>
                                    <tr>
                                        <th class="text-muted small">Tunggakan Pokok</th>
                                        <td class="text-end">
                                            <span class="badge {{ \App\Helpers\QualityHelper::getTunggakanColor($nasabah->xtungpok) }}">
                                                {{ number_format($nasabah->xtungpok, 0, ',', '.') }}
                                            </span>
                                            Tunggakan
                                        </td>
                                    </tr>
                                    <tr>
                                        <th class="text-muted small">Nominal Bunga</th>
                                        <td class="text-end">Rp {{ number_format($nasabah->nombunga, 0, ',', '.') }}</td>
                                    </tr>
                                    <tr>
                                        <th class="text-muted small">Hari Bunga</th>
                                        <td class="text-end">{{ number_format($nasabah->hrbunga, 0, ',', '.') }} hari</td>
                                    </tr>
                                    <tr>
                                        <th class="text-muted small">Tunggakan Bunga</th>
                                        <td class="text-end">{{ number_format($nasabah->xtungbu, 0, ',', '.') }} Tunggakan</td>
                                    </tr>
                                </table>
                            </div>
                        </div>
                    </div>

                    <!-- PPAP & Nilai -->
                    <div class="col-12 col-md-6 mb-4">
                        <div class="card border-0 h-100">
                            <div class="card-header bg-white py-3">
                                <h6 class="mb-0"><i class="fas fa-shield-alt me-2 text-warning"></i>PPAP & Nilai</h6>
                            </div>
                            <div class="card-body">
                                <table class="table table-sm table-borderless">
                                    <tr>
                                        <th width="50%" class="text-muted small">Nilai CKPN</th>
                                        <td class="text-end">Rp {{ number_format($nasabah->nilckpn, 0, ',', '.') }}</td>
                                    </tr>
                                    <tr>
                                        <th class="text-muted small">Nilai Liquid</th>
                                        <td class="text-end">Rp {{ number_format($nasabah->nilliquid, 0, ',', '.') }}</td>
                                    </tr>
                                    <tr>
                                        <th class="text-muted small">Nilai Non-Liquid</th>
                                        <td class="text-end">Rp {{ number_format($nasabah->nilnliquid, 0, ',', '.') }}</td>
                                    </tr>
                                    <tr>
                                        <th class="text-muted small">Minimum PPAP</th>
                                        <td class="text-end">Rp {{ number_format($nasabah->min_ppap, 0, ',', '.') }}</td>
                                    </tr>
                                    <tr>
                                        <th class="text-muted small">PPAP WD</th>
                                        <td class="text-end">Rp {{ number_format($nasabah->ppapwd, 0, ',', '.') }}</td>
                                    </tr>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Tab 3: Jaminan & Klasifikasi -->
            <div class="tab-pane fade" id="collateral" role="tabpanel">
                <div class="row mt-3">
                    <!-- Klasifikasi Debitur -->
                    <div class="col-12 col-md-6 mb-4">
                        <div class="card border-0 h-100">
                            <div class="card-header bg-white py-3">
                                <h6 class="mb-0"><i class="fas fa-tags me-2 text-primary"></i>Klasifikasi Debitur</h6>
                            </div>
                            <div class="card-body">
                                <table class="table table-sm table-borderless">
                                    <tr>
                                        <th width="45%" class="text-muted small">Sifat</th>
                                        <td>{{ $nasabah->sifat ?: '-' }}</td>
                                    </tr>
                                    <tr>
                                        <th class="text-muted small">Jenis</th>
                                        <td>{{ $nasabah->jenis ?: '-' }}</td>
                                    </tr>
                                    <tr>
                                        <th class="text-muted small">Kategori Debitur</th>
                                        <td>{{ $nasabah->kategori_deb ?: '-' }}</td>
                                    </tr>
                                    <tr>
                                        <th class="text-muted small">Sektor</th>
                                        <td>{{ $nasabah->sektor ?: '-' }}</td>
                                    </tr>
                                    <tr>
                                        <th class="text-muted small">Jenis Penggunaan</th>
                                        <td>{{ $nasabah->jnsguna ?: '-' }}</td>
                                    </tr>
                                    <tr>
                                        <th class="text-muted small">Golongan Debitur</th>
                                        <td>{{ $nasabah->goldeb ?: '-' }}</td>
                                    </tr>
                                    <tr>
                                        <th class="text-muted small">Jenis Kredit</th>
                                        <td>{{ $nasabah->jnskre ?: '-' }}</td>
                                    </tr>
                                </table>
                            </div>
                        </div>
                    </div>

                    <!-- Jaminan & AO -->
                    <div class="col-12 col-md-6 mb-4">
                        <div class="card border-0 h-100">
                            <div class="card-header bg-white py-3">
                                <h6 class="mb-0"><i class="fas fa-home me-2 text-success"></i>Jaminan</h6>
                            </div>
                            <div class="card-body">
                                <table class="table table-sm table-borderless">
                                    <tr>
                                        <th width="45%" class="text-muted small">Keterangan Produk</th>
                                        <td>{{ $nasabah->ketproduk ?: '-' }}</td>
                                    </tr>
                                    <tr>
                                        <th class="text-muted small">Jaminan BPKB</th>
                                        <td>{{ $nasabah->jbpkb ?: '-' }}</td>
                                    </tr>
                                    <tr>
                                        <th class="text-muted small">Jaminan Sertifikat</th>
                                        <td>{{ $nasabah->jsertifikat ?: '-' }}</td>
                                    </tr>
                                    <tr>
                                        <th class="text-muted small">Jaminan Lainnya</th>
                                        <td>{{ $nasabah->jlain2 ?: '-' }}</td>
                                    </tr>
                                </table>
                            </div>
                        </div>
                    </div>

                    <!-- Catatan -->
                    @if($nasabah->catatan)
                    <div class="col-12 mb-4">
                        <div class="card border-0">
                            <div class="card-header bg-white py-3">
                                <h6 class="mb-0"><i class="fas fa-sticky-note me-2 text-warning"></i>Catatan</h6>
                            </div>
                            <div class="card-body">
                                <p class="mb-0">{{ $nasabah->catatan }}</p>
                            </div>
                        </div>
                    </div>
                    @endif
                </div>
            </div>

            <!-- Tab 4: Janji Bayar -->
            <div class="tab-pane fade" id="janji" role="tabpanel">
                <div class="row mt-3">
                    <!-- Form Janji Bayar Baru -->
                    <div class="col-12 col-md-4 mb-4">
                        <div class="card border-0">
                            <div class="card-header bg-white py-3">
                                <h6 class="mb-0"><i class="fas fa-plus-circle me-2 text-success"></i>Janji Bayar Baru</h6>
                            </div>
                            <div class="card-body">
                                <form action="{{ route('janji-bayar.store') }}" method="POST">
                                    @csrf
                                    <input type="hidden" name="nasabah_id" value="{{ $nasabah->id }}">
                                    
                                    <div class="mb-3">
                                        <label for="tanggal_janji" class="form-label small">Tanggal Janji</label>
                                        <input type="date" class="form-control form-control-sm" id="tanggal_janji" name="tanggal_janji" required>
                                    </div>
                                    
                                    <div class="mb-3">
                                        <label for="nominal_janji" class="form-label small">Nominal Janji</label>
                                        <div class="input-group input-group-sm">
                                            <span class="input-group-text">Rp</span>
                                            <input type="number" class="form-control" id="nominal_janji" name="nominal_janji" required>
                                        </div>
                                    </div>
                                    
                                    <div class="mb-3">
                                        <label for="keterangan" class="form-label small">Keterangan</label>
                                        <textarea class="form-control form-control-sm" id="keterangan" name="keterangan" rows="3" placeholder="Detail janji bayar..." required></textarea>
                                    </div>
                                    
                                    <button type="submit" class="btn btn-success btn-sm w-100">
                                        <i class="fas fa-save me-1"></i>Simpan Janji Bayar
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>

                    <!-- Daftar Janji Bayar -->
                    <div class="col-12 col-md-8">
                        <div class="card border-0">
                            <div class="card-header bg-white py-3">
                                <h6 class="mb-0"><i class="fas fa-list me-2 text-primary"></i>History Janji Bayar</h6>
                            </div>
                            <div class="card-body">
                                @if($nasabah->janjiBayar->count() > 0)
                                    @foreach($nasabah->janjiBayar->sortByDesc('created_at') as $janji)
                                    <div class="border-start 
                                        @if($janji->status == 'sukses') border-success
                                        @elseif($janji->status == 'gagal') border-danger
                                        @else border-warning @endif ps-3 mb-3">
                                        
                                        <div class="d-flex justify-content-between align-items-start">
                                            <div class="flex-grow-1">
                                                <div class="d-flex justify-content-between align-items-center mb-1">
                                                    <h6 class="mb-0 text-primary">Rp {{ number_format($janji->nominal_janji, 0, ',', '.') }}</h6>
                                                    <span class="badge 
                                                        @if($janji->status == 'sukses') bg-success
                                                        @elseif($janji->status == 'gagal') bg-danger
                                                        @else bg-warning @endif">
                                                        {{ strtoupper($janji->status) }}
                                                    </span>
                                                </div>
                                                <small class="text-muted d-block">
                                                    <i class="fas fa-calendar me-1"></i>
                                                    {{ \Carbon\Carbon::parse($janji->tanggal_janji)->format('d M Y') }}
                                                </small>
                                                <p class="mb-1 small mt-2">{{ $janji->keterangan }}</p>
                                                <small class="text-muted">
                                                    <i class="fas fa-user me-1"></i>Oleh: {{ $janji->created_by }}
                                                </small>
                                            </div>
                                        </div>
                                        
                                        @if(auth()->user()->role == 'admin')
                                        <div class="mt-2">
                                            <form action="{{ route('janji-bayar.update-status', $janji->id) }}" method="POST" class="d-inline">
                                                @csrf
                                                <select name="status" class="form-select form-select-sm" onchange="this.form.submit()">
                                                    <option value="pending" {{ $janji->status == 'pending' ? 'selected' : '' }}>Pending</option>
                                                    <option value="sukses" {{ $janji->status == 'sukses' ? 'selected' : '' }}>Sukses</option>
                                                    <option value="gagal" {{ $janji->status == 'gagal' ? 'selected' : '' }}>Gagal</option>
                                                </select>
                                            </form>
                                        </div>
                                        @endif
                                    </div>
                                    @endforeach
                                @else
                                    <div class="text-center py-4">
                                        <i class="fas fa-handshake fa-2x text-muted mb-3"></i>
                                        <p class="text-muted mb-0">Belum ada janji bayar</p>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Tab 5: History Kolektibilitas -->
            <div class="tab-pane fade" id="history" role="tabpanel">
                <div class="card border-0 mt-3">
                    <div class="card-header bg-white py-3">
                        <h5 class="mb-0"><i class="fas fa-exchange-alt me-2 text-primary"></i>Riwayat Perubahan Kolektibilitas</h5>
                    </div>
                    <div class="card-body">
                        @if($nasabah->historyKolektibilitas->count() > 0)
                            @foreach($nasabah->historyKolektibilitas->sortByDesc('tanggal_perubahan') as $history)
                            <div class="timeline-item mb-4">
                                <div class="d-flex">
                                    <div class="timeline-badge 
                                        @if($history->kolektibilitas_sesudah < $history->kolektibilitas_sebelum) bg-success
                                        @elseif($history->kolektibilitas_sesudah > $history->kolektibilitas_sebelum) bg-danger
                                        @else bg-secondary @endif">
                                        <i class="fas fa-exchange-alt"></i>
                                    </div>
                                    <div class="timeline-content ms-3 flex-grow-1">
                                        <div class="d-flex justify-content-between align-items-center mb-2">
                                            <strong class="text-primary">{{ \Carbon\Carbon::parse($history->tanggal_perubahan)->format('d M Y H:i') }}</strong>
                                            <span class="badge bg-light text-dark small">{{ $history->petugas }}</span>
                                        </div>
                                        
                                        <div class="d-flex align-items-center justify-content-between mb-2">
                                            <span class="badge {{ \App\Helpers\QualityHelper::getQualityBadge($history->kolektibilitas_sebelum) }} fs-6">
                                                {{ \App\Helpers\QualityHelper::getQualityLabel($history->kolektibilitas_sebelum) }}
                                            </span>
                                            <i class="fas fa-arrow-right mx-3 text-muted"></i>
                                            <span class="badge {{ \App\Helpers\QualityHelper::getQualityBadge($history->kolektibilitas_sesudah) }} fs-6">
                                                {{ \App\Helpers\QualityHelper::getQualityLabel($history->kolektibilitas_sesudah) }}
                                            </span>
                                        </div>
                                        
                                        @if($history->keterangan)
                                        <div class="bg-light p-3 rounded small">
                                            <strong>Keterangan:</strong> {{ $history->keterangan }}
                                        </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                            @endforeach
                        @else
                            <div class="text-center py-4">
                                <i class="fas fa-history fa-2x text-muted mb-3"></i>
                                <p class="text-muted mb-0">Belum ada history perubahan kolektibilitas</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.timeline-item {
    position: relative;
}

.timeline-badge {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    flex-shrink: 0;
}

.timeline-content {
    flex-grow: 1;
}

.nav-tabs .nav-link {
    color: #6c757d;
    font-weight: 500;
}

.nav-tabs .nav-link.active {
    color: #0d6efd;
    border-bottom: 2px solid #0d6efd;
}

.table-borderless th {
    font-weight: 500;
}
</style>

<script>
// Aktifkan tab functionality
document.addEventListener('DOMContentLoaded', function() {
    var triggerTabList = [].slice.call(document.querySelectorAll('#nasabahTabs button'))
    triggerTabList.forEach(function (triggerEl) {
        var tabTrigger = new bootstrap.Tab(triggerEl)
        
        triggerEl.addEventListener('click', function (event) {
            event.preventDefault()
            tabTrigger.show()
        })
    })
});
</script>
@endsection