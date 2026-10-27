@extends('layouts.app')

@section('title', 'Tambah Produk - Admin SecondSpace')

@section('content')
<div class="container py-4">

    <nav class="mb-3">
        <a href="{{ route('admin.produk.index') }}" class="btn btn-outline-secondary btn-sm rounded-pill">
            <i class="bi bi-arrow-left me-1"></i> Kembali ke Daftar Produk
        </a>
    </nav>

    <div class="card custom-card border-0 p-4 p-md-5 shadow-sm" style="max-width: 800px; margin: auto;">
        <h3 class="fw-bold mb-1"><i class="bi bi-plus-circle text-danger me-2"></i> Tambah Produk Thrift Baru</h3>
        <p class="text-muted small mb-4">Setiap produk thrift bersifat unik (otomatis memiliki stok 1 item)</p>

        <form action="{{ route('admin.produk.store') }}" method="POST" enctype="multipart/form-data">
            @csrf

            <div class="mb-3">
                <label class="form-label fw-bold">Nama Produk <span class="text-danger">*</span></label>
                <input type="text" name="NamaProduk" class="form-control" placeholder="Contoh: Kamera Sony Cyber-shot / Vintage Hoodie Nike" value="{{ old('NamaProduk') }}" required autofocus>
            </div>

            <div class="row g-3 mb-3">
                <div class="col-md-6">
                    <label class="form-label fw-bold">Kategori Produk <span class="text-danger">*</span></label>
                    <select name="JenisID" class="form-select" required>
                        <option value="">Pilih Kategori...</option>
                        @foreach($categories as $cat)
                            <option value="{{ $cat->JenisID }}" {{ old('JenisID') == $cat->JenisID ? 'selected' : '' }}>
                                {{ $cat->NamaJenis }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-6">
                    <label class="form-label fw-bold">Brand / Merek</label>
                    <input type="text" name="Brand" class="form-control" placeholder="Contoh: Sony, Nike, IKEA, Casio" value="{{ old('Brand') }}">
                </div>
            </div>

            <div class="row g-3 mb-3">
                <div class="col-md-6">
                    <label class="form-label fw-bold">Ukuran / Spesifikasi Singkat</label>
                    <input type="text" name="Ukuran" class="form-control" placeholder="Contoh: L / XL / Compact / 12 Digit" value="{{ old('Ukuran') }}">
                </div>

                <div class="col-md-6">
                    <label class="form-label fw-bold">Harga Produk (Rp) <span class="text-danger">*</span></label>
                    <input type="number" name="Harga" class="form-control" min="0" placeholder="Contoh: 125000" value="{{ old('Harga') }}" required>
                </div>
            </div>

            <div class="mb-3">
                <label class="form-label fw-bold">Deskripsi Produk <span class="text-danger">*</span></label>
                <textarea name="Deskripsi" rows="4" class="form-control" placeholder="Tuliskan deskripsi lengkap kondisi barang thrift/pre-loved, kelengkapan, minus pemakaian (jika ada), dsb...">{{ old('Deskripsi') }}</textarea>
                <div class="form-text">Deskripsi yang detail dan jujur akan membantu pembeli menentukan pilihan.</div>
            </div>

            <div class="mb-4">
                <label class="form-label fw-bold">Foto Produk <span class="text-danger">*</span></label>
                <input type="file" name="gambar" class="form-control" accept="image/*" required>
                <small class="text-muted">Format: JPG, PNG, WEBP. Maksimal 5MB.</small>
            </div>

            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-danger py-2 px-4 fw-bold rounded-pill shadow-sm">
                    <i class="bi bi-save me-1"></i> Simpan Produk
                </button>
                <a href="{{ route('admin.produk.index') }}" class="btn btn-outline-secondary py-2 px-4 rounded-pill">
                    Batal
                </a>
            </div>
        </form>
    </div>

</div>
@endsection