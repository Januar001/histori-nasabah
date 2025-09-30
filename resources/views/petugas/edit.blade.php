@extends('layouts.app')

@section('content')
<div class="row justify-content-center">
    <div class="col-12 col-md-8 col-lg-6">
        <div class="card border-0">
            <div class="card-header bg-white py-3">
                <div class="d-flex justify-content-between align-items-center">
                    <h5 class="mb-0"><i class="fas fa-edit me-2 text-warning"></i>Edit Data Petugas</h5>
                    <a href="{{ route('petugas.index') }}" class="btn btn-sm btn-outline-secondary">
                        <i class="fas fa-arrow-left me-1"></i> Kembali
                    </a>
                </div>
            </div>
            <div class="card-body">
                <form action="{{ route('petugas.update', $petugas->id) }}" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="row g-3">
                        <div class="col-12 col-md-6">
                            <label for="kode_petugas" class="form-label">Kode Petugas <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('kode_petugas') is-invalid @enderror" 
                                   id="kode_petugas" name="kode_petugas" value="{{ old('kode_petugas', $petugas->kode_petugas) }}" 
                                   placeholder="Contoh: AO001, REM001" required>
                            @error('kode_petugas')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-12 col-md-6">
                            <label for="nama_petugas" class="form-label">Nama Lengkap <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('nama_petugas') is-invalid @enderror" 
                                   id="nama_petugas" name="nama_petugas" value="{{ old('nama_petugas', $petugas->nama_petugas) }}" 
                                   placeholder="Nama lengkap petugas" required>
                            @error('nama_petugas')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-12 col-md-6">
                            <label for="divisi" class="form-label">Divisi <span class="text-danger">*</span></label>
                            <select class="form-select @error('divisi') is-invalid @enderror" id="divisi" name="divisi" required>
                                <option value="">Pilih Divisi...</option>
                                <option value="AO" {{ old('divisi', $petugas->divisi) == 'AO' ? 'selected' : '' }}>AO - Account Officer</option>
                                <option value="Remedial" {{ old('divisi', $petugas->divisi) == 'Remedial' ? 'selected' : '' }}>Remedial - Penanganan Khusus</option>
                                <option value="Special" {{ old('divisi', $petugas->divisi) == 'Special' ? 'selected' : '' }}>Special - Kasus Khusus</option>
                            </select>
                            @error('divisi')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-12 col-md-6">
                            <label for="email" class="form-label">Email</label>
                            <input type="email" class="form-control @error('email') is-invalid @enderror" 
                                   id="email" name="email" value="{{ old('email', $petugas->email) }}" 
                                   placeholder="email@bank.com">
                            @error('email')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-12 col-md-6">
                            <label for="telepon" class="form-label">Nomor Telepon</label>
                            <input type="text" class="form-control @error('telepon') is-invalid @enderror" 
                                   id="telepon" name="telepon" value="{{ old('telepon', $petugas->telepon) }}" 
                                   placeholder="081234567890">
                            @error('telepon')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-12 col-md-6">
                            <label class="form-label">Status</label>
                            <div class="form-check form-switch mt-2">
                                <input class="form-check-input" type="checkbox" id="status_aktif" name="status_aktif" value="1" 
                                    {{ old('status_aktif', $petugas->status_aktif) ? 'checked' : '' }}>
                                <label class="form-check-label" for="status_aktif">Aktif</label>
                            </div>
                            <small class="text-muted">Non-aktifkan jika petugas sudah tidak bertugas</small>
                        </div>

                        <div class="col-12">
                            <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                                <a href="{{ route('petugas.show', $petugas->id) }}" class="btn btn-secondary me-md-2">
                                    <i class="fas fa-times me-1"></i> Batal
                                </a>
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save me-1"></i> Update Data
                                </button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <!-- Stats Card -->
        <div class="card border-0 mt-3">
            <div class="card-body">
                <h6><i class="fas fa-chart-bar me-2 text-primary"></i>Statistik Petugas</h6>
                <div class="row text-center">
                    <div class="col-4">
                        <div class="border-end">
                            <h5 class="text-primary mb-0">{{ $petugas->nasabahs_count }}</h5>
                            <small class="text-muted">Total Nasabah</small>
                        </div>
                    </div>
                    <div class="col-4">
                        <div class="border-end">
                            <h5 class="text-warning mb-0">{{ $petugas->history_kolektibilitas_count }}</h5>
                            <small class="text-muted">Total Perubahan</small>
                        </div>
                    </div>
                    <div class="col-4">
                        <div>
                            <h5 class="text-success mb-0">{{ $petugas->created_at->format('d M Y') }}</h5>
                            <small class="text-muted">Bergabung</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection