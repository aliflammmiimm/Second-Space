@extends('layouts.app')

@section('title', 'Kelola Jenis Produk - Admin SecondSpace')

@section('content')
<div class="container py-4">

    <div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-3">
        <div>
            <h2 class="fw-bold mb-1"><i class="bi bi-collection text-danger me-2"></i> Jenis & Kategori Produk</h2>
            <p class="text-muted mb-0">Kelola master data kategori pakaian thrift</p>
        </div>
        <a href="{{ route('admin.dashboard') }}" class="btn btn-outline-secondary rounded-pill">
            <i class="bi bi-arrow-left me-1"></i> Kembali ke Dashboard
        </a>
    </div>

    <div class="row g-4">
        <!-- FORM TAMBAH -->
        <div class="col-md-5">
            <div class="card custom-card border-0 p-4">
                <h5 class="fw-bold mb-3"><i class="bi bi-info-circle me-1"></i> Kategori Utama</h5>
                <p class="text-muted mb-0">
                    SecondSpace memakai 4 kategori utama (maksimal 4 aktif). Kategori tidak dihapus,
                    hanya diaktifkan atau dinonaktifkan agar riwayat produk tetap aman.
                </p>
            </div>
        </div>

        <!-- DAFTAR KATEGORI -->
        <div class="col-md-7">
            <div class="card custom-card border-0 p-4">
                <h5 class="fw-bold mb-3">Daftar Kategori Saat Ini</h5>
                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>ID</th>
                                <th>Nama Kategori</th>
                                <th class="text-center">Jumlah Produk</th>
                                <th class="text-end">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($categories as $cat)
                                <tr>
                                    <td class="fw-bold">#{{ $cat->JenisID }}</td>
                                    <td>{{ $cat->NamaJenis }}</td>
                                    <td class="text-center">
                                        <span class="badge bg-secondary rounded-pill">{{ $cat->produk_count }} produk</span>
                                    </td>
                                    <td class="text-end">
                                        <form action="{{ route('admin.jenis.update', $cat->JenisID) }}" method="POST" class="d-inline">
                                            @csrf
                                            @method('PUT')
                                            <input type="hidden" name="NamaJenis" value="{{ $cat->NamaJenis }}">
                                            <input type="hidden" name="Aktif" value="{{ $cat->Aktif ? 0 : 1 }}">
                                            <button type="submit" class="btn btn-sm {{ $cat->Aktif ? 'btn-outline-secondary' : 'btn-outline-success' }}">
                                                {{ $cat->Aktif ? 'Nonaktifkan' : 'Aktifkan' }}
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="text-center text-muted py-4">Belum ada kategori yang dibuat.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

</div>
@endsection