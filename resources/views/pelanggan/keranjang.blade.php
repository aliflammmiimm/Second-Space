@extends('layouts.app')
@section('title', 'Keranjang - SecondSpace')

@section('content')
<div class="container py-4">
    <h2 class="fw-bold mb-4" style="color: #001B27;"><i class="bi bi-cart3 me-2" style="color: #00B8BE;"></i> Keranjang Belanja</h2>

    @if($item->isEmpty())
        <div class="text-center p-5 bg-white rounded-4 shadow-sm border" style="border-color: rgba(0, 92, 102, 0.15) !important;">
            <i class="bi bi-cart-x fs-1 text-muted"></i>
            <h5 class="mt-3 fw-bold text-muted">Keranjang Anda masih kosong</h5>
            <a href="{{ route('pelanggan.katalog') }}" class="btn btn-danger rounded-pill px-4 mt-2">Jelajahi Katalog</a>
        </div>
    @else
        <div class="row g-4">
            <div class="col-lg-8">
                @if(isset($sellersTersedia) && $sellersTersedia->count() > 1)
                    <div class="alert alert-info border-0 shadow-sm rounded-4 p-3 mb-4 d-flex align-items-center gap-3" style="background: rgba(0, 184, 190, 0.12); color: #005C66;">
                        <i class="bi bi-info-circle-fill fs-3" style="color: #00B8BE;"></i>
                        <div>
                            <strong>Keranjang Berisi Barang dari Beberapa Toko</strong>
                            <div class="small">Karena setiap penjual mengirim barang secara mandiri, silakan klik tombol <strong>"Checkout Toko Ini"</strong> pada masing-masing toko.</div>
                        </div>
                    </div>
                @endif

                @foreach($itemBySeller as $sellerId => $sellerItems)
                    @php
                        $namaSeller = $sellerItems->first()->produk?->seller?->Nama ?? 'Toko';
                        $subtotalSeller = $sellerItems->where('tersedia', true)->sum(fn ($k) => $k->produk->Harga);
                        $adaTersedia = $sellerItems->where('tersedia', true)->isNotEmpty();
                    @endphp
                    <div class="card custom-card border-0 mb-4 overflow-hidden">
                        <div class="p-3 bg-light border-bottom d-flex justify-content-between align-items-center flex-wrap gap-2">
                            <div class="fw-bold" style="color: #001B27;">
                                <i class="bi bi-shop me-2" style="color: #00B8BE;"></i> Toko: {{ $namaSeller }}
                            </div>
                            @if($adaTersedia)
                                <a href="{{ route('pelanggan.checkout.show', ['seller_id' => $sellerId]) }}" class="btn btn-sm btn-danger rounded-pill px-3 flex-grow-1 flex-sm-grow-0 text-center">
                                    <i class="bi bi-bag-check me-1"></i> Checkout Toko Ini (Rp {{ number_format($subtotalSeller, 0, ',', '.') }})
                                </a>
                            @endif
                        </div>

                        <div class="p-3">
                            @foreach($sellerItems as $k)
                                <div class="d-flex gap-3 align-items-center py-2 {{ !$loop->last ? 'border-bottom' : '' }} {{ $k->tersedia ? '' : 'opacity-75' }}">
                                    <img src="{{ $k->produk?->gambar_url }}" class="rounded-3 flex-shrink-0" style="width: 68px; height: 68px; object-fit: cover;" alt="">
                                    <div class="flex-grow-1">
                                        <div class="fw-bold" style="color: #001B27;">{{ $k->produk?->NamaProduk ?? 'Produk sudah dihapus' }}</div>
                                        <div class="small text-muted">Kondisi: {{ \App\Models\Produk::KONDISI[$k->produk?->Kondisi] ?? ucfirst($k->produk?->Kondisi ?? 'Baik') }} &bull; 1 unit</div>
                                        @if($k->tersedia)
                                            <div class="fw-bold mt-1" style="color: #005C66;">Rp {{ number_format($k->produk->Harga, 0, ',', '.') }}</div>
                                        @else
                                            <span class="badge bg-secondary">Tidak tersedia lagi &ndash; tidak ikut checkout</span>
                                        @endif
                                    </div>
                                    <form action="{{ route('pelanggan.keranjang.hapus', $k->ProdukID) }}" method="POST">
                                        @csrf @method('DELETE')
                                        <button class="btn btn-sm btn-outline-danger" aria-label="Hapus dari keranjang"><i class="bi bi-trash"></i></button>
                                    </form>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endforeach
            </div>
            <div class="col-lg-4">
                <div class="card custom-card border-0 p-4 sticky-top" style="top: 90px;">
                    <h5 class="fw-bold mb-3" style="color: #001B27;"><i class="bi bi-receipt me-2" style="color: #00B8BE;"></i> Ringkasan Belanja</h5>
                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-muted">Total Seluruh Keranjang</span>
                        <strong style="color: #005C66;">Rp {{ number_format($total, 0, ',', '.') }}</strong>
                    </div>
                    <div class="small text-muted mb-3">Harga diambil dari data terbaru. Pengiriman diproses langsung oleh masing-masing penjual.</div>
                    
                    @if(isset($sellersTersedia) && $sellersTersedia->count() === 1)
                        <a href="{{ route('pelanggan.checkout.show', ['seller_id' => $sellersTersedia->first()]) }}" class="btn btn-danger rounded-pill fw-semibold py-2 w-100">
                            <i class="bi bi-lock-fill me-1"></i> Lanjut Checkout
                        </a>
                    @elseif(isset($sellersTersedia) && $sellersTersedia->count() > 1)
                        <div class="alert alert-secondary py-2 small mb-0 text-center">
                            Silakan klik <strong>Checkout Toko Ini</strong> pada toko di sebelah kiri untuk melanjutkan.
                        </div>
                    @endif
                </div>
            </div>
        </div>
    @endif
</div>
@endsection