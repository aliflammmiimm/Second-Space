@extends('layouts.app')

@section('title', 'Kelola Produk - Admin SecondSpace')

@section('content')
<div class="container py-4">

    <!-- HEADER & ACTION BUTTONS -->
    <div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-3">
        <div>
            <h2 class="fw-bold mb-1" style="color: #001B27;"><i class="bi bi-box-seam me-2" style="color: #00B8BE;"></i> Kelola Data Produk</h2>
            <p class="text-muted mb-0">Daftar semua inventaris produk thrift & pre-loved SecondSpace</p>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('admin.dashboard') }}" class="btn btn-outline-secondary rounded-pill px-3">
                <i class="bi bi-arrow-left me-1"></i> Dashboard
            </a>
        </div>
    </div>

    <!-- FILTER & SEARCH -->
    <div class="card custom-card border-0 p-3 mb-4 shadow-sm">
        <form action="{{ route('admin.produk.index') }}" method="GET" class="row g-2 align-items-center">
            <div class="col-md-5">
                <input type="text" name="search" class="form-control" placeholder="Cari nama produk / brand..." value="{{ request('search') }}">
            </div>
            <div class="col-md-3">
                <select name="jenis_id" class="form-select">
                    <option value="">Semua Kategori</option>
                    @foreach($categories as $cat)
                        <option value="{{ $cat->JenisID }}" {{ request('jenis_id') == $cat->JenisID ? 'selected' : '' }}>
                            {{ $cat->NamaJenis }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <select name="status" class="form-select">
                    <option value="">Semua Status</option>
                    <option value="Tersedia" {{ request('status') == 'Tersedia' ? 'selected' : '' }}>Tersedia</option>
                    <option value="Terjual" {{ request('status') == 'Terjual' ? 'selected' : '' }}>Terjual</option>
                </select>
            </div>
            <div class="col-md-2 d-flex gap-2">
                <button type="submit" class="btn btn-dark w-100">Filter</button>
                <a href="{{ route('admin.produk.index') }}" class="btn btn-outline-secondary">Reset</a>
            </div>
        </form>
    </div>

    <!-- TABEL PRODUK -->
    <div class="card custom-card border-0 p-4 shadow-sm">
        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead class="table-light">
                    <tr>
                        <th style="width: 70px;">Foto</th>
                        <th>Nama Produk</th>
                        <th>Kategori</th>
                        <th>Brand & Ukuran</th>
                        <th style="max-width: 280px;">Deskripsi</th>
                        <th>Harga</th>
                        <th>Status</th>
                        <th class="text-center" style="width: 120px;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($produk as $p)
                        <tr>
                            <td>
                                <img src="{{ $p->gambar_url }}" alt="{{ $p->NamaProduk }}" class="rounded-3" 
                                     style="width: 55px; height: 55px; object-fit: cover;" 
                                     onerror="this.onerror=null; this.src='{{ asset('upload/default.jpg') }}';">
                            </td>
                            <td>
                                <div class="fw-bold" style="color: #001B27;">{{ $p->NamaProduk }}</div>
                                <small class="text-muted">ID: #{{ $p->ProdukID }}</small>
                            </td>
                            <td>
                                <span class="badge bg-light text-dark border">{{ $p->jenisProduk ? $p->jenisProduk->NamaJenis : '-' }}</span>
                            </td>
                            <td>
                                <div class="fw-semibold">{{ $p->Brand ?? '-' }}</div>
                                <small class="text-muted">{{ $p->Ukuran ? 'Ukuran: ' . $p->Ukuran : '-' }}</small>
                            </td>
                            <td>
                                <div class="text-muted small text-truncate" style="max-width: 280px;" title="{{ $p->Deskripsi }}">
                                    {{ $p->Deskripsi ?? 'Tidak ada deskripsi' }}
                                </div>
                            </td>
                            <td class="fw-bold" style="color: #005C66;">Rp {{ number_format($p->Harga, 0, ',', '.') }}</td>
                            <td>
                                @if($p->Status == 'Tersedia')
                                    <span class="badge px-2 py-1" style="background: rgba(0, 184, 190, 0.15); color: #005C66; border: 1px solid rgba(0, 184, 190, 0.4);">
                                        <i class="bi bi-check-circle me-1"></i> Tersedia
                                    </span>
                                @else
                                    <span class="badge px-2 py-1" style="background: rgba(0, 27, 39, 0.08); color: #53707c; border: 1px solid rgba(0, 27, 39, 0.15);">
                                        <i class="bi bi-x-circle me-1"></i> Terjual
                                    </span>
                                @endif
                            </td>
                            <td>
                                <div class="d-flex gap-1 justify-content-center">
                                    @if($p->Status === 'Tersedia')
                                        <form action="{{ route('admin.produk.sembunyikan', $p->ProdukID) }}" method="POST" onsubmit="return confirm('Sembunyikan produk ini dari katalog?')">
                                            @csrf
                                            @method('PUT')
                                            <button class="btn btn-sm btn-outline-secondary" title="Sembunyikan"><i class="bi bi-eye-slash"></i></button>
                                        </form>
                                    @elseif($p->Status === 'Disembunyikan')
                                        <form action="{{ route('admin.produk.tampilkan', $p->ProdukID) }}" method="POST">
                                            @csrf
                                            @method('PUT')
                                            <button class="btn btn-sm btn-outline-success" title="Tampilkan"><i class="bi bi-eye"></i></button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center text-muted py-5">
                                Belum ada data produk yang terdaftar.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="d-flex justify-content-center mt-4">
            {{ $produk->links() }}
        </div>
    </div>

</div>
@endsection