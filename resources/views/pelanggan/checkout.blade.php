@extends('layouts.app')

@section('title', 'Checkout Pesanan - SecondSpace')

@section('content')
<div class="container py-5">
    
    <nav class="mb-4">
        @if($langsung)
            <a href="{{ route('pelanggan.produk.detail', $items->first()->ProdukID) }}" class="btn btn-outline-secondary btn-sm rounded-pill">
                <i class="bi bi-arrow-left me-1"></i> Kembali ke Detail Produk
            </a>
        @else
            <a href="{{ route('pelanggan.keranjang') }}" class="btn btn-outline-secondary btn-sm rounded-pill">
                <i class="bi bi-arrow-left me-1"></i> Kembali ke Keranjang
            </a>
        @endif
    </nav>

    <div class="row g-4">
        
        <!-- FORM PENGIRIMAN & PEMBAYARAN -->
        <div class="col-lg-7">
            <div class="card custom-card border-0 p-4 p-md-5">
                <h4 class="fw-bold mb-4"><i class="bi bi-geo-alt text-danger me-2"></i> Informasi Pengiriman & Pembayaran</h4>

                <form action="{{ route('pelanggan.checkout.process') }}" method="POST" id="checkoutForm">
                    @csrf
                    @if($langsung)
                        <input type="hidden" name="produk_id" value="{{ $items->first()->ProdukID }}">
                    @endif

                    <div class="mb-3">
                        <label class="form-label fw-bold">Nama Penerima</label>
                        <input type="text" class="form-control bg-light" value="{{ Auth::user()->Nama }}" readonly>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold">Kontak HP / Email</label>
                        <input type="text" class="form-control bg-light" value="{{ Auth::user()->NoHP_Email }}" readonly>
                    </div>

                    <div class="mb-4">
                        <label class="form-label fw-bold">Alamat Pengiriman Lengkap <span class="text-danger">*</span></label>
                        <textarea name="alamat" rows="3" class="form-control" placeholder="Contoh: Jl. Merdeka No. 45, RT 02/RW 04, Kelurahan ..., Kecamatan ..., Kota ..., Kode Pos ..." required>{{ old('alamat') }}</textarea>
                        <div class="form-text">Pastikan alamat jelas agar kurir dapat mengantar pesanan tepat waktu.</div>
                    </div>

                    <div class="mb-4">
                        <label class="form-label fw-bold">Pilih Metode Pembayaran <span class="text-danger">*</span></label>
                        
                        <div class="d-flex flex-column gap-2">
                            <label class="p-3 border rounded-3 d-flex align-items-center gap-3 cursor-pointer" style="cursor: pointer;">
                                <input type="radio" name="metode" value="COD" class="form-check-input mt-0" {{ old('metode', 'COD') == 'COD' ? 'checked' : '' }} required>
                                <div>
                                    <div class="fw-bold"><i class="bi bi-cash-stack text-success me-1"></i> Cash on Delivery (COD)</div>
                                    <small class="text-muted">Bayar tunai langsung saat kurir mengantarkan barang ke tempat Anda.</small>
                                </div>
                            </label>

                            <label class="p-3 border rounded-3 d-flex align-items-center gap-3 cursor-pointer" style="cursor: pointer;">
                                <input type="radio" name="metode" value="Transfer" class="form-check-input mt-0" {{ old('metode') == 'Transfer' ? 'checked' : '' }}>
                                <div>
                                    <div class="fw-bold"><i class="bi bi-bank text-primary me-1"></i> Transfer Bank / E-Wallet</div>
                                    <small class="text-muted">Transfer manual ke rekening toko (BCA / Mandiri / BRI / DANA / QRIS).</small>
                                </div>
                            </label>
                        </div>
                    </div>

                    <button type="submit" class="btn btn-danger btn-lg w-100 py-3 fw-bold rounded-pill shadow">
                        <i class="bi bi-lock-fill me-1"></i> Konfirmasi & Buat Pesanan
                    </button>
                </form>
            </div>
        </div>

        <!-- RINGKASAN PESANAN -->
        <div class="col-lg-5">
            <div class="card custom-card border-0 p-4 sticky-top" style="top: 100px;">
                <h5 class="fw-bold mb-3"><i class="bi bi-receipt text-muted me-2"></i> Ringkasan Pesanan</h5>

                @foreach($items as $produk)
                    <div class="d-flex gap-3 align-items-center p-3 rounded-3 bg-light mb-3">
                        <img src="{{ $produk->gambar_url }}" class="rounded-3" style="width: 75px; height: 75px; object-fit: cover;" onerror="this.onerror=null; this.src='{{ asset('upload/default.jpg') }}';">
                        <div class="flex-grow-1">
                            <div class="fw-bold text-truncate" style="max-width: 220px;">{{ $produk->NamaProduk }}</div>
                            <div class="small text-muted">
                                Seller: {{ $produk->seller?->Nama ?? '-' }} &bull; 1 unit
                            </div>
                            <div class="fw-bold text-danger">Rp {{ number_format($produk->Harga, 0, ',', '.') }}</div>
                        </div>
                    </div>
                @endforeach

                <hr>

                <div class="d-flex justify-content-between mb-2">
                    <span class="text-muted">Jumlah Barang</span>
                    <span>{{ $items->count() }} item</span>
                </div>
                <div class="d-flex justify-content-between mb-2">
                    <span class="text-muted">Subtotal</span>
                    <span>Rp {{ number_format($total, 0, ',', '.') }}</span>
                </div>
                <div class="d-flex justify-content-between mb-2">
                    <span class="text-muted">Ongkos Kirim</span>
                    <span class="text-muted">Belum dihitung sistem</span>
                </div>

                <hr>

                <div class="d-flex justify-content-between align-items-center mb-3">
                    <span class="fs-5 fw-bold">Total Tagihan</span>
                    <span class="fs-4 fw-bolder text-danger" id="labelTotal">
                        Rp {{ number_format($total, 0, ',', '.') }}
                    </span>
                </div>
            </div>
        </div>

    </div>
</div>
@endsection