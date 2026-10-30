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
                @foreach($item as $k)
                    <div class="card custom-card border-0 p-3 mb-3 {{ $k->tersedia ? '' : 'opacity-75' }}">
                        <div class="d-flex gap-3 align-items-center">
                            <img src="{{ $k->produk?->gambar_url }}" class="rounded-3" style="width: 90px; height: 90px; object-fit: cover;" alt="">
                            <div class="flex-grow-1">
                                <div class="fw-bold" style="color: #001B27;">{{ $k->produk?->NamaProduk ?? 'Produk sudah dihapus' }}</div>
                                <div class="small text-muted">Seller: {{ $k->produk?->seller?->Nama ?? '-' }} &bull; 1 unit</div>
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
                    </div>
                @endforeach
            </div>
            <div class="col-lg-4">
                <div class="card custom-card border-0 p-4">
                    <div class="d-flex justify-content-between mb-2"><span>Subtotal</span><strong style="color: #005C66;">Rp {{ number_format($total, 0, ',', '.') }}</strong></div>
                    <div class="small text-muted mb-3">Harga diambil dari data terbaru. Ongkos kirim belum dihitung oleh sistem.</div>
                    @if($total > 0)
                        <a href="{{ route('pelanggan.checkout.show') }}" class="btn btn-danger rounded-pill fw-semibold py-2">Lanjut Checkout</a>
                    @endif
                </div>
            </div>
        </div>
    @endif
</div>
@endsection