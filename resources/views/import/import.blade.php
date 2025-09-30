@extends('layouts.app')

@section('content')
<div class="row">
    <div class="col-12 mb-4">
        <div class="d-flex justify-content-between align-items-center">
            <h1><i class="fas fa-upload me-2"></i>Import Data Excel</h1>
            <a href="{{ route('dashboard') }}" class="btn btn-outline-primary">
                <i class="fas fa-arrow-left me-2"></i>Kembali
            </a>
        </div>
    </div>
</div>

<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card border-0 shadow-sm">
            <div class="card-body p-5">
                <div class="text-center mb-4">
                    <i class="fas fa-file-excel fa-4x text-success mb-3"></i>
                    <h3>Import Data Nasabah</h3>
                    <p class="text-muted">Upload file Excel untuk update data nasabah</p>
                </div>

                <form action="{{ route('import.excel') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="mb-4">
                        <label for="file" class="form-label">Pilih File Excel</label>
                        <input type="file" class="form-control" id="file" name="file" 
                               accept=".xlsx,.xls" required>
                        <div class="form-text">
                            Format file harus .xlsx atau .xls. Kolom harus sesuai dengan template.
                        </div>
                    </div>

                    <button type="submit" class="btn btn-success btn-lg w-100">
                        <i class="fas fa-upload me-2"></i>Upload & Proses
                    </button>
                </form>

                <div class="mt-4">
                    <h5>Catatan:</h5>
                    <ul class="text-muted">
                        <li>Data yang sudah ada akan diupdate berdasarkan nomor rekening</li>
                        <li>Perubahan kolektibilitas akan tercatat otomatis</li>
                        <li>Pastikan format kolom sesuai dengan template</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection