@extends('layouts.app')

@section('title', 'Edit Produk - Admin SecondSpace')

@section('content')
<div class="container py-4">

    <nav class="mb-3">
        <a href="{{ route('admin.produk.index') }}" class="btn btn-outline-secondary btn-sm rounded-pill">
            <i class="bi bi-arrow-left me-1"></i> Kembali ke Daftar Produk
        </a>
    </nav>

    <div class="card custom-card border-0 p-4 p-md-5 shadow-sm" style="max-width: 800px; margin: auto;">
        <h3 class="fw-bold mb-4"><i class="bi bi-pencil-square text-primary me-2"></i> Edit Produk #{{ $produk->ProdukID }}</h3>

        <form action="{{ route('admin.produk.update', $produk->ProdukID) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            <div class="mb-3">
                <label class="form-label fw-bold">Nama Produk <span class="text-danger">*</span></label>
                <input type="text" name="NamaProduk" class="form-control" value="{{ old('NamaProduk', $produk->NamaProduk) }}" required>
            </div>

            <div class="row g-3 mb-3">
                <div class="col-md-6">
                    <label class="form-label fw-bold">Kategori Produk <span class="text-danger">*</span></label>
                    <select name="JenisID" class="form-select" required>
                        @foreach($categories as $cat)
                            <option value="{{ $cat->JenisID }}" {{ old('JenisID', $produk->JenisID) == $cat->JenisID ? 'selected' : '' }}>
                                {{ $cat->NamaJenis }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-6">
                    <label class="form-label fw-bold">Brand / Merek</label>
                    <input type="text" name="Brand" class="form-control" value="{{ old('Brand', $produk->Brand) }}">
                </div>
            </div>

            <div class="row g-3 mb-3">
                <div class="col-md-4">
                    <label class="form-label fw-bold">Ukuran / Spesifikasi</label>
                    <input type="text" name="Ukuran" class="form-control" value="{{ old('Ukuran', $produk->Ukuran) }}">
                </div>

                <div class="col-md-4">
                    <label class="form-label fw-bold">Harga Produk (Rp) <span class="text-danger">*</span></label>
                    <input type="number" name="Harga" class="form-control" min="0" value="{{ old('Harga', $produk->Harga) }}" required>
                </div>

                <div class="col-md-4">
                    <label class="form-label fw-bold">Status Produk <span class="text-danger">*</span></label>
                    <select name="Status" class="form-select" required>
                        <option value="Tersedia" {{ old('Status', $produk->Status) == 'Tersedia' ? 'selected' : '' }}>Tersedia</option>
                        <option value="Terjual" {{ old('Status', $produk->Status) == 'Terjual' ? 'selected' : '' }}>Terjual</option>
                    </select>
                </div>
            </div>

            <div class="mb-3">
                <label class="form-label fw-bold">Deskripsi Produk</label>
                <textarea name="Deskripsi" rows="4" class="form-control" placeholder="Tuliskan deskripsi kondisi barang, kelengkapan, dll...">{{ old('Deskripsi', $produk->Deskripsi) }}</textarea>
            </div>

            <div class="mb-4">
                <label class="form-label fw-bold">Ganti Foto Produk</label>
                <div class="d-flex gap-3 align-items-center mb-2">
                    <img src="{{ $produk->gambar_url }}" class="rounded-3 shadow-sm" style="width: 75px; height: 75px; object-fit: cover;" onerror="this.onerror=null; this.src='{{ asset('upload/default.jpg') }}';">
                    <div class="small text-muted">
                        Foto saat ini: <code>{{ $produk->Gambar ?? 'default.jpg' }}</code>
                    </div>
                </div>
                <input type="file" name="gambar" class="form-control" accept="image/*">
                <small class="text-muted">Biarkan kosong jika tidak ingin mengubah foto produk saat ini.</small>
            </div>

            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-primary py-2 px-4 fw-bold rounded-pill shadow-sm">
                    <i class="bi bi-save me-1"></i> Perbarui Produk
                </button>
                <a href="{{ route('admin.produk.index') }}" class="btn btn-outline-secondary py-2 px-4 rounded-pill">
                    Batal
                </a>
            </div>
        </form>
    </div>

</div>
@endsection