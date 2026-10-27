@extends('layouts.app')

@section('title', $produk->NamaProduk . ' - SecondSpace')

@section('content')
<div class="container py-5">
    
    <nav class="mb-4">
        <a href="{{ route('pelanggan.katalog') }}" class="btn btn-outline-secondary btn-sm rounded-pill">
            <i class="bi bi-arrow-left me-1"></i> Kembali ke Katalog
        </a>
    </nav>

    <div class="card custom-card border-0 shadow-sm p-4 p-md-5 mb-5">
        <div class="row g-4 align-items-center">
            
            <!-- GAMBAR PRODUK -->
            <div class="col-md-5">
                <div class="position-relative rounded-4 overflow-hidden shadow-sm" style="background: #f8fafc;">
                    <img src="{{ $produk->gambar_url }}" alt="{{ $produk->NamaProduk }}" class="w-100" 
                         style="max-height: 480px; object-fit: cover; object-position: center;"
                         onerror="this.onerror=null; this.src='{{ asset('upload/default.jpg') }}';">
                    
                    @if($produk->Status !== 'Tersedia')
                        <div class="position-absolute top-0 start-0 w-100 h-100 bg-dark bg-opacity-75 d-flex align-items-center justify-content-center">
                            <span class="badge bg-danger fs-5 px-3 py-2">
                                {{ $produk->Status === 'Dipesan' ? 'SEDANG DIPESAN' : 'SUDAH TERJUAL' }}
                            </span>
                        </div>
                    @endif
                </div>
            </div>

            <!-- DETAIL INFO -->
            <div class="col-md-7">
                <div class="ps-md-3">
                    <div class="d-flex gap-2 mb-2">
                        <span class="badge bg-dark">{{ $produk->jenisProduk ? $produk->jenisProduk->NamaJenis : 'SecondSpace' }}</span>
                        <span class="badge bg-secondary">Kondisi: {{ \App\Models\Produk::KONDISI[$produk->Kondisi] ?? $produk->Kondisi }}</span>
                        @if($produk->Status == 'Tersedia')
                            <span class="badge bg-success bg-opacity-10 text-success border border-success">
                                <i class="bi bi-check-circle me-1"></i> Tersedia
                            </span>
                        @endif
                    </div>

                    <h2 class="fw-bold mb-2">{{ $produk->NamaProduk }}</h2>
                    <p class="text-muted mb-3">Seller: <strong>{{ $produk->seller?->Nama ?? '-' }}</strong></p>

                    <div class="d-flex align-items-baseline gap-3 mb-4">
                        <span class="fs-2 fw-bolder text-danger">Rp {{ number_format($produk->Harga, 0, ',', '.') }}</span>
                        <span class="badge bg-light text-muted border px-3 py-2 rounded-pill small">
                            <i class="bi bi-gem me-1 text-danger"></i> Item Thrift Eksklusif (1 Unit)
                        </span>
                    </div>

                    <!-- DESKRIPSI PRODUK -->
                    <div class="p-3 rounded-3 bg-light mb-4 border">
                        <h6 class="fw-bold mb-2 text-dark"><i class="bi bi-card-text text-danger me-1"></i> Deskripsi & Kondisi Barang:</h6>
                        <p class="text-muted mb-0" style="line-height: 1.7; white-space: pre-line;">{{ $produk->Deskripsi ?? 'Kondisi barang thrift / pre-loved pilihan terkurasi, bersih dan siap pakai.' }}</p>
                    </div>

                    <!-- JAMINAN KUALITAS -->
                    <div class="p-3 rounded-3 bg-white border mb-4 shadow-sm">
                        <h6 class="fw-bold mb-2 small text-uppercase text-muted"><i class="bi bi-shield-check text-success me-1"></i> Jaminan Belanja di SecondSpace:</h6>
                        <ul class="mb-0 text-muted small ps-3">
                            <li>Barang dijual oleh seller yang telah disetujui Admin SecondSpace.</li>
                            <li>Tersedia pembayaran aman via Transfer Bank & Cash on Delivery (COD).</li>
                            <li>Barang thrift/pre-loved bersifat eksklusif (hanya tersedia 1 unit untuk setiap produk).</li>
                        </ul>
                    </div>

                    @if($produk->Status == 'Tersedia')
                        @auth
                            @if((int) $produk->SellerID === (int) Auth::id())
                                <div class="alert alert-info mb-0">Ini adalah barang milik Anda sendiri.</div>
                            @elseif(Auth::user()->Role === 'Pelanggan')
                                <div class="d-flex gap-3 flex-wrap">
                                    <a href="{{ route('pelanggan.checkout.show', $produk->ProdukID) }}" class="btn btn-danger btn-lg px-4 py-3 fw-bold rounded-pill shadow">
                                        <i class="bi bi-bag-check-fill me-1"></i> Beli Sekarang
                                    </a>

                                    <form action="{{ route('pelanggan.keranjang.tambah', $produk->ProdukID) }}" method="POST">
                                        @csrf
                                        <button type="submit" class="btn btn-outline-danger btn-lg px-4 py-3 fw-bold rounded-pill">
                                            <i class="bi bi-cart-plus me-1"></i> Tambah ke Keranjang
                                        </button>
                                    </form>

                                    @if($waUrl)
                                        <a href="{{ $waUrl }}" target="_blank" rel="noopener" class="btn btn-success btn-lg px-4 py-3 fw-bold rounded-pill shadow">
                                            <i class="bi bi-whatsapp me-1"></i> Tanya via WhatsApp
                                        </a>
                                    @endif
                                </div>
                            @endif
                        @else
                            <a href="{{ route('login') }}" class="btn btn-danger btn-lg px-4 py-3 fw-bold rounded-pill shadow">
                                <i class="bi bi-box-arrow-in-right me-1"></i> Login untuk Membeli
                            </a>
                        @endauth
                    @else
                        <button class="btn btn-secondary btn-lg px-4 py-3 rounded-pill disabled" disabled>
                            {{ $produk->Status === 'Dipesan' ? 'Barang Sedang Dipesan' : 'Barang Telah Terjual' }}
                        </button>
                    @endif
                </div>
            </div>

        </div>
    </div>

    <!-- PRODUK SERUPA -->
    @if($relatedProducts->isNotEmpty())
        <div class="mt-5">
            <h4 class="fw-bold mb-3">Produk Lain di Kategori yang Sama</h4>
            <div class="row g-3">
                @foreach($relatedProducts as $rel)
                    <div class="col-md-3 col-6">
                        <div class="card custom-card border-0 p-2 h-100 shadow-sm">
                            <img src="{{ $rel->gambar_url }}" class="rounded-3" style="height: 180px; object-fit: cover;" onerror="this.onerror=null; this.src='{{ asset('upload/default.jpg') }}';">
                            <div class="p-2">
                                <div class="small fw-bold text-truncate">{{ $rel->NamaProduk }}</div>
                                <div class="text-danger fw-bold">Rp {{ number_format($rel->Harga, 0, ',', '.') }}</div>
                                <a href="{{ route('pelanggan.produk.detail', $rel->ProdukID) }}" class="btn btn-outline-dark btn-sm rounded-pill w-100 mt-2">
                                    Lihat
                                </a>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    @endif

</div>
@endsection