@extends('layouts.app')

@section('title', 'Penugasan Nasabah')

@section('content')
<div class="row">
    <div class="col-12 mb-3">
        <h4 class="mb-0"><i class="fas fa-user-tag me-2 text-primary"></i>Penugasan Nasabah ke Petugas</h4>
        <small class="text-muted">Menugaskan nasabah secara massal atau individual.</small>
    </div>
</div>

<div class="card border-0 mb-4">
    <div class="card-body">
        <form method="GET" action="{{ route('assignment.index') }}">
            <div class="row g-3 align-items-end">
                <div class="col-md-3">
                    <label for="nama_nasabah" class="form-label">Nama Nasabah</label>
                    <input type="text" name="nama_nasabah" id="nama_nasabah" class="form-control" value="{{ request('nama_nasabah') }}">
                </div>
                <div class="col-md-3">
                    <label for="kualitas" class="form-label">Kolektibilitas</label>
                    <select name="kualitas" id="kualitas" class="form-select">
                        <option value="">Semua</option>
                        @for ($i=1; $i<=5; $i++)
                        <option value="{{ $i }}" {{ request('kualitas') == $i ? 'selected' : '' }}>KOL {{ $i }}</option>
                        @endfor
                    </select>
                </div>
                <div class="col-md-3">
                    <label for="petugas_id" class="form-label">Status Penugasan</label>
                    <select name="petugas_id" id="petugas_id" class="form-select">
                        <option value="">Semua</option>
                        <option value="belum_ditugaskan" {{ request('petugas_id') == 'belum_ditugaskan' ? 'selected' : '' }}>Belum Ditugaskan</option>
                        @foreach($petugasList as $petugas)
                        <option value="{{ $petugas->id }}" {{ request('petugas_id') == $petugas->id ? 'selected' : '' }}>{{ $petugas->nama_petugas }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <button type="submit" class="btn btn-primary w-100"><i class="fas fa-filter"></i> Filter</button>
                </div>
            </div>
        </form>
    </div>
</div>

<form method="POST" action="{{ route('assignment.bulk') }}">
    @csrf
    <div class="card border-0">
        <div class="card-header bg-white d-flex justify-content-between align-items-center">
            <h6 class="mb-0">Daftar Nasabah</h6>
            <div class="d-flex">
                 <select name="petugas_id_bulk" class="form-select form-select-sm me-2" required>
                    <option value="">-- Pilih Petugas --</option>
                    @foreach($petugasList as $petugas)
                    <option value="{{ $petugas->id }}">{{ $petugas->nama_petugas }}</option>
                    @endforeach
                 </select>
                 <button type="submit" class="btn btn-success btn-sm"><i class="fas fa-check-double"></i> Assign Terpilih</button>
            </div>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead>
                        <tr class="table-light">
                            <th><input type="checkbox" id="checkAll"></th>
                            <th>Nama Nasabah</th>
                            <th>Kolektibilitas</th>
                            <th>Petugas Saat Ini</th>
                            <th class="text-center">Aksi Individual</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($nasabahs as $nasabah)
                        <tr>
                            <td><input type="checkbox" name="nasabah_ids[]" value="{{ $nasabah->id }}" class="nasabah-checkbox"></td>
                            <td>
                                <div class="fw-bold">{{ $nasabah->namadb }}</div>
                                <small class="text-muted">{{ $nasabah->nocif }}</small>
                            </td>
                            <td><span class="badge {{ \App\Helpers\QualityHelper::getQualityBadge($nasabah->kualitas) }}">{{ \App\Helpers\QualityHelper::getQualityLabel($nasabah->kualitas) }}</span></td>
                            <td>{{ $nasabah->petugas->nama_petugas ?? 'Belum ada' }}</td>
                            <td class="text-center">
                                <button type="button" class="btn btn-xs btn-outline-primary" data-bs-toggle="modal" data-bs-target="#individualAssignModal{{ $nasabah->id }}">Assign</button>
                            </td>
                        </tr>
                        @empty
                        <tr><td colspan="5" class="text-center py-5">Tidak ada data ditemukan.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        <div class="card-footer bg-white">
            {{ $nasabahs->appends(request()->query())->links() }}
        </div>
    </div>
</form>

@foreach($nasabahs as $nasabah)
<div class="modal fade" id="individualAssignModal{{ $nasabah->id }}" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Assign Petugas Individual</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('assignment.individual', $nasabah->id) }}" method="POST">
                @csrf
                <div class="modal-body">
                    <p>Nasabah: <strong>{{ $nasabah->namadb }}</strong></p>
                    <div class="mb-3">
                        <label for="petugas_id_individual" class="form-label">Pilih Petugas</label>
                        <select name="petugas_id_individual" class="form-select" required>
                            <option value="">-- Pilih Petugas --</option>
                            @foreach($petugasList as $petugas)
                            <option value="{{ $petugas->id }}" {{ $nasabah->petugas_id == $petugas->id ? 'selected' : '' }}>{{ $petugas->nama_petugas }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endforeach
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const checkAll = document.getElementById('checkAll');
    const checkboxes = document.querySelectorAll('.nasabah-checkbox');

    if (checkAll) {
        checkAll.addEventListener('change', function() {
            checkboxes.forEach(checkbox => {
                checkbox.checked = this.checked;
            });
        });
    }
});
</script>
@endpush