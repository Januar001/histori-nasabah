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

<!-- Section Assign Petugas -->
<div class="row">
    <div class="col-12 mb-4">
        <div class="card border-0">
            <div class="card-header bg-white py-2">
                <h6 class="mb-0"><i class="fas fa-user-tie me-2 text-primary"></i>Penanganan Nasabah</h6>
            </div>
            <div class="card-body">
                @if($nasabah->petugas)
                <div class="row g-3 mb-3">
                    <div class="col-12 col-md-4">
                        <small class="text-muted d-block">Petugas Penanganan</small>
                        <div class="d-flex align-items-center">
                            <i class="fas fa-user-circle me-2 text-primary"></i>
                            <div>
                                <strong>{{ $nasabah->petugas->nama_petugas }}</strong>
                                <br>
                                <small class="text-muted">{{ $nasabah->petugas->kode_petugas }}</small>
                            </div>
                        </div>
                    </div>
                    <div class="col-12 col-md-3">
                        <small class="text-muted d-block">Divisi</small>
                        <span class="badge 
                            @if($nasabah->petugas->divisi == 'AO') bg-success
                            @elseif($nasabah->petugas->divisi == 'Remedial') bg-warning
                            @else bg-info @endif">
                            {{ $nasabah->petugas->divisi }}
                        </span>
                    </div>
                    <div class="col-12 col-md-5">
                        <small class="text-muted d-block">Tanggal Ditangani</small>
                        <div class="d-flex align-items-center">
                            <i class="fas fa-calendar me-2 text-secondary"></i>
                            <strong>{{ $nasabah->tanggal_ditangani ? $nasabah->tanggal_ditangani->format('d M Y') : 'Belum ditangani' }}</strong>
                        </div>
                    </div>
                </div>
                @endif

                @if($nasabah->catatan_penanganan)
                <div class="mb-3">
                    <small class="text-muted d-block">Catatan Penanganan</small>
                    <div class="bg-light p-3 rounded">
                        {{ $nasabah->catatan_penanganan }}
                    </div>
                </div>
                @endif

                <!-- Form Assign/Update Penanganan -->
                <form action="{{ route('penanganan.assign', $nasabah->id) }}" method="POST">
                    @csrf
                    <div class="row g-2">
                        <div class="col-12 col-md-6">
                            <label class="form-label small">Pilih Petugas</label>
                            <select name="petugas_id" class="form-select form-select-sm" required>
                                <option value="">Pilih Petugas...</option>
                                @foreach($petugasList as $petugas)
                                <option value="{{ $petugas->id }}" 
                                    {{ $nasabah->petugas_id == $petugas->id ? 'selected' : '' }}>
                                    [{{ $petugas->divisi }}] {{ $petugas->nama_petugas }} - {{ $petugas->kode_petugas }}
                                </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-12 col-md-6">
                            <label class="form-label small">Catatan Penanganan</label>
                            <textarea name="catatan_penanganan" class="form-control form-control-sm" 
                                      rows="2" placeholder="Catatan progres penanganan...">{{ $nasabah->catatan_penanganan }}</textarea>
                        </div>
                        <div class="col-12">
                            <button type="submit" class="btn btn-sm btn-primary w-100">
                                <i class="fas fa-save me-1"></i>
                                {{ $nasabah->petugas_id ? 'Update' : 'Assign' }} Penanganan
                            </button>
                        </div>
                    </div>
                </form>

                @if($nasabah->petugas)
                <div class="mt-3 pt-3 border-top">
                    <small class="text-muted d-block mb-2">Info Petugas:</small>
                    <div class="row g-2">
                        <div class="col-6">
                            <small class="text-muted">Email:</small>
                            <div>{{ $nasabah->petugas->email ?: '-' }}</div>
                        </div>
                        <div class="col-6">
                            <small class="text-muted">Telepon:</small>
                            <div>{{ $nasabah->petugas->telepon ?: '-' }}</div>
                        </div>
                    </div>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Informasi Nasabah -->
<div class="row">
    <div class="col-12 col-md-6 mb-3">
        <div class="card border-0">
            <div class="card-header bg-white py-2">
                <h6 class="mb-0"><i class="fas fa-info-circle me-2 text-primary"></i>Informasi Nasabah</h6>
            </div>
            <div class="card-body">
                <table class="table table-sm">
                    <tr>
                        <th width="40%">Nama</th>
                        <td>{{ $nasabah->namadb }}</td>
                    </tr>
                    <tr>
                        <th>NOCIF</th>
                        <td>{{ $nasabah->nocif }}</td>
                    </tr>
                    <tr>
                        <th>Rekening</th>
                        <td>{{ $nasabah->rekening }}</td>
                    </tr>
                    <tr>
                        <th>Kualitas</th>
                        <td>
                            <span class="badge {{ \App\Helpers\QualityHelper::getQualityBadge($nasabah->kualitas) }}">
                                {{ \App\Helpers\QualityHelper::getQualityLabel($nasabah->kualitas) }}
                            </span>
                        </td>
                    </tr>
                    <tr>
                        <th>Plafon</th>
                        <td>Rp {{ number_format($nasabah->plafon, 0, ',', '.') }}</td>
                    </tr>
                    <tr>
                        <th>Baki Debet</th>
                        <td>Rp {{ number_format($nasabah->bakidebet, 0, ',', '.') }}</td>
                    </tr>
                    <tr>
                        <th>Alamat</th>
                        <td>{{ $nasabah->alamat }}</td>
                    </tr>
                </table>
            </div>
        </div>
    </div>

    <!-- Input Janji Bayar -->
    <div class="col-12 col-md-6 mb-3">
        <div class="card border-0">
            <div class="card-header bg-white py-2">
                <h6 class="mb-0"><i class="fas fa-handshake me-2 text-success"></i>Janji Bayar Baru</h6>
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
                        <input type="number" class="form-control form-control-sm" id="nominal_janji" name="nominal_janji" required>
                    </div>
                    
                    <div class="mb-3">
                        <label for="keterangan" class="form-label small">Keterangan</label>
                        <textarea class="form-control form-control-sm" id="keterangan" name="keterangan" rows="2" required></textarea>
                    </div>
                    
                    <button type="submit" class="btn btn-sm btn-success w-100">
                        <i class="fas fa-save me-1"></i>Simpan Janji Bayar
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- History Kolektibilitas -->
<div class="row">
    <div class="col-12 col-md-6 mb-3">
        <div class="card border-0">
            <div class="card-header bg-white py-2">
                <h6 class="mb-0"><i class="fas fa-history me-2 text-primary"></i>History Kolektibilitas</h6>
            </div>
            <div class="card-body">
                @foreach($nasabah->historyKolektibilitas as $history)
                <div class="border-start border-primary ps-3 mb-3">
                    <div class="d-flex justify-content-between">
                        <small class="text-muted">{{ \Carbon\Carbon::parse($history->tanggal_perubahan)->format('d M Y') }}</small>
                        <span class="badge bg-light text-dark">{{ $history->petugas }}</span>
                    </div>
                    <p class="mb-1">
                        <span class="badge {{ \App\Helpers\QualityHelper::getQualityBadge($history->kolektibilitas_sebelum) }}">
                            {{ \App\Helpers\QualityHelper::getQualityLabel($history->kolektibilitas_sebelum) }}
                        </span> 
                        <i class="fas fa-arrow-right mx-2 text-muted"></i>
                        <span class="badge {{ \App\Helpers\QualityHelper::getQualityBadge($history->kolektibilitas_sesudah) }}">
                            {{ \App\Helpers\QualityHelper::getQualityLabel($history->kolektibilitas_sesudah) }}
                        </span>
                    </p>
                    @if($history->keterangan)
                    <small class="text-muted">{{ $history->keterangan }}</small>
                    @endif
                </div>
                @endforeach

                @if($nasabah->historyKolektibilitas->isEmpty())
                <p class="text-muted text-center">Belum ada history perubahan</p>
                @endif
            </div>
        </div>
    </div>

    <!-- History Janji Bayar -->
    <div class="col-12 col-md-6 mb-3">
        <div class="card border-0">
            <div class="card-header bg-white py-2">
                <h6 class="mb-0"><i class="fas fa-handshake me-2 text-success"></i>History Janji Bayar</h6>
            </div>
            <div class="card-body">
                @foreach($nasabah->janjiBayar as $janji)
                <div class="border-start 
                    @if($janji->status == 'sukses') border-success
                    @elseif($janji->status == 'gagal') border-danger
                    @else border-warning @endif ps-3 mb-3">
                    
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <h6 class="mb-1">Rp {{ number_format($janji->nominal_janji, 0, ',', '.') }}</h6>
                            <small class="text-muted">{{ \Carbon\Carbon::parse($janji->tanggal_janji)->format('d M Y') }}</small>
                        </div>
                        <span class="badge 
                            @if($janji->status == 'sukses') bg-success
                            @elseif($janji->status == 'gagal') bg-danger
                            @else bg-warning @endif">
                            {{ strtoupper($janji->status) }}
                        </span>
                    </div>
                    
                    <p class="mb-1 small">{{ $janji->keterangan }}</p>
                    <small class="text-muted">Oleh: {{ $janji->created_by }}</small>
                    
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

                @if($nasabah->janjiBayar->isEmpty())
                <p class="text-muted text-center">Belum ada janji bayar</p>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection