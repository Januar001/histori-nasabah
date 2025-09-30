@extends('layouts.app')

@section('content')
<div class="row">
    <div class="col-12 mb-4">
        <div class="d-flex justify-content-between align-items-center">
            <h1><i class="fas fa-user me-2"></i>Detail Nasabah</h1>
            <a href="{{ route('nasabah.index') }}" class="btn btn-outline-primary">
                <i class="fas fa-arrow-left me-2"></i>Kembali
            </a>
        </div>
    </div>
</div>

<div class="row">
    <!-- Data Nasabah -->
    <div class="col-md-6 mb-4">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white">
                <h5 class="card-title mb-0"><i class="fas fa-info-circle me-2"></i>Informasi Nasabah</h5>
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
                            <span class="badge 
                                @if($nasabah->kualitas == 'LANCAR') bg-success
                                @elseif($nasabah->kualitas == 'DIRAGUKAN') bg-warning
                                @elseif($nasabah->kualitas == 'MACET') bg-danger
                                @else bg-secondary @endif">
                                {{ $nasabah->kualitas }}
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
    <div class="col-md-6 mb-4">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white">
                <h5 class="card-title mb-0"><i class="fas fa-handshake me-2"></i>Janji Bayar Baru</h5>
            </div>
            <div class="card-body">
                <form action="{{ route('janji-bayar.store') }}" method="POST">
                    @csrf
                    <input type="hidden" name="nasabah_id" value="{{ $nasabah->id }}">
                    
                    <div class="mb-3">
                        <label for="tanggal_janji" class="form-label">Tanggal Janji</label>
                        <input type="date" class="form-control" id="tanggal_janji" name="tanggal_janji" required>
                    </div>
                    
                    <div class="mb-3">
                        <label for="nominal_janji" class="form-label">Nominal Janji</label>
                        <input type="number" class="form-control" id="nominal_janji" name="nominal_janji" required>
                    </div>
                    
                    <div class="mb-3">
                        <label for="keterangan" class="form-label">Keterangan</label>
                        <textarea class="form-control" id="keterangan" name="keterangan" rows="3" required></textarea>
                    </div>
                    
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="fas fa-save me-2"></i>Simpan Janji Bayar
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <!-- History Kolektibilitas -->
    <div class="col-md-6 mb-4">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white">
                <h5 class="card-title mb-0"><i class="fas fa-history me-2"></i>History Kolektibilitas</h5>
            </div>
            <div class="card-body">
                @foreach($nasabah->historyKolektibilitas as $history)
                <div class="border-start border-primary ps-3 mb-3">
                    <div class="d-flex justify-content-between">
                        <small class="text-muted">{{ $history->tanggal_perubahan->format('d M Y') }}</small>
                        <span class="badge bg-light text-dark">{{ $history->petugas }}</span>
                    </div>
                    <p class="mb-1">
                        <span class="text-danger">{{ $history->kolektibilitas_sebelum }}</span> 
                        <i class="fas fa-arrow-right mx-2 text-muted"></i>
                        <span class="text-success">{{ $history->kolektibilitas_sesudah }}</span>
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
    <div class="col-md-6 mb-4">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white">
                <h5 class="card-title mb-0"><i class="fas fa-handshake me-2"></i>History Janji Bayar</h5>
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
                            <small class="text-muted">{{ $janji->tanggal_janji->format('d M Y') }}</small>
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