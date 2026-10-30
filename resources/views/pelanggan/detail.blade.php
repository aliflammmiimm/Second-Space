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
                <div class="position-relative rounded-4 overflow-hidden shadow-sm" style="background: #f4f9fa; border: 1px solid rgba(0, 92, 102, 0.12);">
                    <img src="{{ $produk->gambar_url }}" alt="{{ $produk->NamaProduk }}" class="w-100" 
                         style="max-height: 480px; object-fit: cover; object-position: center;"
                         onerror="this.onerror=null; this.src='{{ asset('upload/default.jpg') }}';">
                    
                    @if($produk->Status !== 'Tersedia')
                        <div class="position-absolute top-0 start-0 w-100 h-100 d-flex align-items-center justify-content-center" style="background: rgba(0, 27, 39, 0.85);">
                            <span class="badge fs-5 px-3 py-2" style="background: #001B27; color: #00B8BE; border: 1px solid #00B8BE;">
                                {{ $produk->Status === 'Dipesan' ? 'SEDANG DIPESAN' : 'SUDAH TERJUAL' }}
                            </span>
                        </div>
                    @endif
                </div>
            </div>

            <!-- DETAIL INFO -->
            <div class="col-md-7">
                <div class="ps-md-3">
                    <div class="d-flex gap-2 mb-2 flex-wrap">
                        <span class="badge" style="background: #001B27; color: #fff;">{{ $produk->jenisProduk ? $produk->jenisProduk->NamaJenis : 'SecondSpace' }}</span>
                        <span class="badge bg-light text-secondary border">Kondisi: {{ \App\Models\Produk::KONDISI[$produk->Kondisi] ?? $produk->Kondisi }}</span>
                        @if($produk->Status == 'Tersedia')
                            <span class="badge" style="background: rgba(0, 184, 190, 0.15); color: #005C66; border: 1px solid rgba(0, 184, 190, 0.4);">
                                <i class="bi bi-check-circle me-1"></i> Tersedia (1 Pcs)
                            </span>
                        @endif
                    </div>

                    <h2 class="fw-bold mb-2" style="color: #001B27;">{{ $produk->NamaProduk }}</h2>
                    <p class="text-muted mb-3">Seller: <strong>{{ $produk->seller?->Nama ?? '-' }}</strong></p>

                    <div class="d-flex align-items-baseline gap-3 mb-4 flex-wrap">
                        <span class="fs-2 fw-bolder" style="color: #005C66;">Rp {{ number_format($produk->Harga, 0, ',', '.') }}</span>
                        <span class="badge px-3 py-2 rounded-pill small" style="background: rgba(0, 184, 190, 0.12); color: #005C66; border: 1px solid rgba(0, 184, 190, 0.35);">
                            <i class="bi bi-gem me-1" style="color: #00B8BE;"></i> Item Thrift Eksklusif (1 Unit)
                        </span>
                    </div>

                    <!-- DESKRIPSI PRODUK -->
                    <div class="p-3 rounded-3 mb-4" style="background: #f4f9fa; border: 1px solid rgba(0, 92, 102, 0.15);">
                        <h6 class="fw-bold mb-2" style="color: #001B27;"><i class="bi bi-card-text me-1" style="color: #00B8BE;"></i> Deskripsi & Kondisi Barang:</h6>
                        <p class="text-muted mb-0" style="line-height: 1.7; white-space: pre-line;">{{ $produk->Deskripsi ?? 'Kondisi barang thrift / pre-loved pilihan terkurasi, bersih dan siap pakai.' }}</p>
                    </div>

                    <!-- JAMINAN KUALITAS -->
                    <div class="p-3 rounded-3 bg-white border mb-4 shadow-sm" style="border-color: rgba(0, 92, 102, 0.15) !important;">
                        <h6 class="fw-bold mb-2 small text-uppercase" style="color: #005C66;"><i class="bi bi-shield-check text-success me-1"></i> Jaminan Belanja di SecondSpace:</h6>
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
            <h4 class="fw-bold mb-3" style="color: #001B27;">Produk Lain di Kategori yang Sama</h4>
            <div class="row g-3">
                @foreach($relatedProducts as $rel)
                    <div class="col-md-3 col-6">
                        <div class="card custom-card border-0 p-2 h-100 shadow-sm">
                            <img src="{{ $rel->gambar_url }}" class="rounded-3" style="height: 180px; object-fit: cover;" onerror="this.onerror=null; this.src='{{ asset('upload/default.jpg') }}';">
                            <div class="p-2">
                                <div class="small fw-bold text-truncate" style="color: #001B27;">{{ $rel->NamaProduk }}</div>
                                <div class="fw-bold mt-1" style="color: #005C66;">Rp {{ number_format($rel->Harga, 0, ',', '.') }}</div>
                                <a href="{{ route('pelanggan.produk.detail', $rel->ProdukID) }}" class="btn btn-dark btn-sm rounded-pill w-100 mt-2">
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