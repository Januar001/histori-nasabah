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

                <!-- ATURAN NAMA FILE -->
                <div class="alert alert-danger">
                    <h5 class="alert-heading"><i class="fas fa-exclamation-triangle me-2"></i>Aturan Wajib!</h5>
                    <p class="mb-0">Nama file yang di-upload **harus** mengandung tanggal dengan format **DD_MM_YYYY**. <br>Contoh: <strong>laporan_harian_01_05_2025.xlsx</strong></p>
                    <hr>
                    <p class="mb-0">Tanggal dari nama file akan digunakan sebagai tanggal perubahan data.</p>
                </div>

                @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <h5><i class="fas fa-check-circle me-2"></i>Import Selesai!</h5>
                    <p class="mb-2">{!! session('success') !!}</p>
                    @if(session('summary'))
                        @php $summary = session('summary'); @endphp
                        <ul class="list-group list-group-flush">
                            <li class="list-group-item bg-transparent d-flex justify-content-between align-items-center border-success">Data Baru Dibuat <span class="badge bg-primary rounded-pill">{{ $summary['imported'] }}</span></li>
                            <li class="list-group-item bg-transparent d-flex justify-content-between align-items-center border-success">Data Berhasil Diupdate <span class="badge bg-info rounded-pill">{{ $summary['updated'] }}</span></li>
                            <li class="list-group-item bg-transparent d-flex justify-content-between align-items-center border-success">Perubahan Kolektibilitas <span class="badge bg-warning rounded-pill">{{ $summary['changesDetected'] }}</span></li>
                        </ul>
                    @endif
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
                @endif

                @if(session('warnings') && count(session('warnings')) > 0)
                <div class="alert alert-warning alert-dismissible fade show" role="alert">
                    <h5><i class="fas fa-exclamation-circle me-2"></i>Peringatan (Data Dilewati)</h5>
                    <p>Beberapa data dilewati karena nama nasabah tidak cocok:</p>
                    <ul class="small">
                        @foreach(session('warnings') as $warning)
                            <li>{{ $warning }}</li>
                        @endforeach
                    </ul>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
                @endif

                @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <h5><i class="fas fa-exclamation-triangle me-2"></i>Import Gagal!</h5>
                    <p class="mb-0">{!! session('error') !!}</p>
                    @if(session('errors') && count(session('errors')) > 0)
                        <hr>
                        <p class="mb-1">Detail Error:</p>
                        <ul class="small">
                            @foreach(session('errors') as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    @endif
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
                            Pastikan nama file sesuai aturan di atas. Maksimal 10MB.
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

                <div class="mt-5 p-3 bg-light rounded">
                    <h6><i class="fas fa-info-circle me-2"></i>Alur Proses Impor Cerdas:</h6>
                    <ul class="text-muted small">
                        <li><strong>Validasi Nama File:</strong> Tanggal (DD_MM_YYYY) wajib ada di nama file.</li>
                        <li><strong>Pencocokan Data:</strong> Data diupdate berdasarkan <strong>Nomor Rekening</strong>.</li>
                        <li><strong>Validasi Keamanan:</strong> Update data akan dilewati jika <strong>Nama Nasabah</strong> di file tidak cocok dengan yang ada di database untuk rekening yang sama.</li>
                        <li><strong>Data Baru:</strong> Jika rekening tidak ditemukan, data baru akan dibuat. Riwayat kolektibilitas otomatis tercatat dari 'Lancar' (1).</li>
                        <li><strong>Pencatatan Riwayat:</strong> Riwayat perubahan hanya dicatat jika ada perubahan <strong>Kualitas/Kolektibilitas</strong>.</li>
                    </ul>
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
    submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Memproses, mohon tunggu...';
});
</script>
@endpush
