@extends('layouts.app')

@section('content')
    <div class="row">
        <div class="col-12 mb-4">
            <div class="d-flex justify-content-between align-items-center">
                <h1><i class="fas fa-users me-2"></i>Data Nasabah</h1>
                <a href="{{ route('dashboard') }}" class="btn btn-outline-primary">
                    <i class="fas fa-arrow-left me-2"></i>Kembali
                </a>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12 mb-4">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <form action="{{ route('nasabah.index') }}" method="GET">
                        <div class="input-group">
                            <input type="text" name="search" class="form-control"
                                placeholder="Cari berdasarkan nama, nocif, atau rekening..."
                                value="{{ request('search') }}">
                            <button class="btn btn-primary" type="submit">
                                <i class="fas fa-search"></i> Cari
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead class="table-light">
                                <tr>
                                    <th>NOCIF</th>
                                    <th>Rekening</th>
                                    <th>Nama</th>
                                    <th>Kualitas</th>
                                    <th>Plafon</th>
                                    <th>Baki Debet</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($nasabahs as $nasabah)
                                    <tr>
                                        <td>{{ $nasabah->nocif }}</td>
                                        <td>{{ $nasabah->rekening }}</td>
                                        <td>{{ $nasabah->namadb }}</td>
                                        <td>
                                            <span class="badge 
                                                @if($nasabah->kualitas == '1') bg-success
                                                @elseif($nasabah->kualitas == '2') bg-info
                                                @elseif($nasabah->kualitas == '3') bg-warning
                                                @elseif($nasabah->kualitas == '4') bg-danger
                                                @elseif($nasabah->kualitas == '5') bg-dark
                                                @else bg-secondary @endif">
                                                {{ $nasabah->kualitas }} - 
                                                @if($nasabah->kualitas == '1') Lancar
                                                @elseif($nasabah->kualitas == '2') Dalam Perhatian Khusus
                                                @elseif($nasabah->kualitas == '3') Kurang Lancar
                                                @elseif($nasabah->kualitas == '4') Diragukan
                                                @elseif($nasabah->kualitas == '5') Macet
                                                @else Tidak Diketahui
                                                @endif
                                            </span>
                                        </td>
                                        <td>Rp {{ number_format($nasabah->plafon, 0, ',', '.') }}</td>
                                        <td>Rp {{ number_format($nasabah->bakidebet, 0, ',', '.') }}</td>
                                        <td>
                                            <a href="{{ route('nasabah.show', $nasabah->id) }}"
                                                class="btn btn-sm btn-outline-primary">
                                                <i class="fas fa-eye"></i> Detail
                                            </a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <div class="d-flex justify-content-center mt-4">
                        {{ $nasabahs->links('pagination::bootstrap-5') }}
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
