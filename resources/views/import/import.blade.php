@extends('layouts.app')

@section('content')
<div class="row">
    <div class="col-12 mb-3">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h4 class="mb-0"><i class="fas fa-upload me-2 text-primary"></i>Import Data Excel</h4>
                <small class="text-muted">Upload file Excel untuk update data nasabah</small>
            </div>
            <div class="d-flex gap-2">
                <a href="{{ route('import.history') }}" class="btn btn-outline-info">
                    <i class="fas fa-history me-1"></i>History Import
                </a>
                <a href="{{ route('dashboard') }}" class="btn btn-outline-secondary">
                    <i class="fas fa-arrow-left me-1"></i>Kembali
                </a>
            </div>
        </div>
    </div>
</div>

<div class="row justify-content-center">
    <div class="col-12 col-md-8">
        <div class="card border-0">
            <div class="card-body p-5">
                <div class="text-center mb-4">
                    <i class="fas fa-file-excel fa-4x text-success mb-3"></i>
                    <h3>Import Data Nasabah</h3>
                    <p class="text-muted">Upload file Excel untuk update data nasabah</p>
                </div>

                @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <h5><i class="fas fa-check-circle me-2"></i>Import Berhasil!</h5>
                    <p class="mb-0">{!! session('success') !!}</p>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
                @endif

                @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <h5><i class="fas fa-exclamation-triangle me-2"></i>Import Gagal!</h5>
                    <p class="mb-0">{{ session('error') }}</p>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
                @endif

                <form action="{{ route('import.excel') }}" method="POST" enctype="multipart/form-data" id="importForm">
                    @csrf
                    <div class="mb-4">
                        <label for="file" class="form-label">Pilih File Excel</label>
                        <input type="file" class="form-control" id="file" name="file"
                               accept=".xlsx,.xls" required>
                        <div class="form-text">
                            Format file harus .xlsx atau .xls. Maksimal 10MB.
                        </div>
                    </div>

                    <div class="d-grid gap-2">
                        <button type="submit" class="btn btn-success btn-lg" id="submitBtn">
                            <i class="fas fa-upload me-2"></i>Upload & Proses
                        </button>
                        <a href="{{ route('import.download-template') }}" class="btn btn-outline-secondary">
                            <i class="fas fa-download me-2"></i>Download Template
                        </a>
                    </div>
                </form>

                <div class="mt-5">
                    <h5><i class="fas fa-info-circle me-2"></i>Informasi Import:</h5>
                    <div class="row">
                        <div class="col-md-6">
                            <ul class="text-muted">
                                <li>Data akan diupdate berdasarkan <strong>Nomor Rekening</strong></li>
                                <li>Semua <strong>perubahan data</strong> dan <strong>nasabah baru</strong> akan tercatat di history</li>
                                <li>Format <strong>kualitas menggunakan angka</strong> (1,2,3,4,5)</li>
                            </ul>
                        </div>
                        <div class="col-md-6">
                            <ul class="text-muted">
                                <li>Tanggal otomatis dikonversi ke format YYYY-MM-DD</li>
                                <li>Nilai numerik dibersihkan dari format currency</li>
                                <li>Jika tidak ada data yang berubah, tidak akan dicatat</li>
                            </ul>
                        </div>
                    </div>
                </div>

                <div class="mt-4 p-3 bg-light rounded">
                    <h6><i class="fas fa-table me-2"></i>Mapping Kualitas (Kolektibilitas):</h6>
                    <div class="table-responsive">
                        <table class="table table-sm table-bordered">
                            <thead class="table-light">
                                <tr>
                                    <th>Kode</th>
                                    <th>Keterangan</th>
                                    <th>Badge</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td><strong>1</strong></td>
                                    <td>Lancar</td>
                                    <td><span class="badge bg-success">Lancar</span></td>
                                </tr>
                                <tr>
                                    <td><strong>2</strong></td>
                                    <td>Dalam Perhatian Khusus</td>
                                    <td><span class="badge bg-info">Dalam Perhatian</span></td>
                                </tr>
                                <tr>
                                    <td><strong>3</strong></td>
                                    <td>Kurang Lancar</td>
                                    <td><span class="badge bg-warning">Kurang Lancar</span></td>
                                </tr>
                                <tr>
                                    <td><strong>4</strong></td>
                                    <td>Diragukan</td>
                                    <td><span class="badge bg-danger">Diragukan</span></td>
                                </tr>
                                <tr>
                                    <td><strong>5</strong></td>
                                    <td>Macet</td>
                                    <td><span class="badge bg-dark">Macet</span></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.getElementById('importForm').addEventListener('submit', function() {
    const submitBtn = document.getElementById('submitBtn');
    submitBtn.disabled = true;
    submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Memproses...';
});
</script>
@endpush