@extends('layouts.app')

@section('title', 'Riwayat Pesanan - SecondSpace')

@section('content')
<div class="container py-4">
    
    <div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-3">
        <div>
            <h2 class="fw-bold mb-1" style="color: #001B27;"><i class="bi bi-archive me-2" style="color: #00B8BE;"></i> Riwayat Semua Pesanan</h2>
            <p class="text-muted mb-0">Arsip seluruh transaksi dan histori belanja Anda di SecondSpace</p>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('pelanggan.pesanan_aktif') }}" class="btn btn-outline-dark rounded-pill px-3 shadow-sm">
                <i class="bi bi-clock-history me-1" style="color: #00B8BE;"></i> Lihat Pesanan Aktif
            </a>
            <a href="{{ route('pelanggan.katalog') }}" class="btn btn-outline-secondary rounded-pill px-3">
                <i class="bi bi-cart-plus me-1"></i> Belanja Lagi
            </a>
        </div>
    </div>

    @forelse($pesanan as $p)
        <div class="card custom-card border-0 mb-4 p-4 shadow-sm">
            <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 pb-3 mb-3 border-bottom">
                <div class="d-flex align-items-center gap-2">
                    <span class="fw-bold fs-5" style="color: #001B27;">Pesanan #{{ $p->PesananID }}</span>
                    <span class="text-muted small">&bull; {{ $p->TanggalPesanan ? $p->TanggalPesanan->format('d M Y, H:i') : '-' }}</span>
                </div>
                <div>
                    @if($p->StatusPesanan == 'Diproses')
                        <span class="badge bg-warning text-dark px-3 py-2 rounded-pill"><i class="bi bi-hourglass-split me-1"></i> Diproses</span>
                    @elseif($p->StatusPesanan == 'Dikemas')
                        <span class="badge px-3 py-2 rounded-pill" style="background: #005C66; color: #fff;"><i class="bi bi-box-seam me-1"></i> Dikemas</span>
                    @elseif($p->StatusPesanan == 'Dikirim')
                        <span class="badge px-3 py-2 rounded-pill" style="background: #00B8BE; color: #001B27;"><i class="bi bi-truck me-1"></i> Sedang Dikirim</span>
                    @elseif($p->StatusPesanan == 'Sampai Tujuan')
                        <span class="badge bg-success px-3 py-2 rounded-pill"><i class="bi bi-geo-alt me-1"></i> Sampai Tujuan</span>
                    @elseif($p->StatusPesanan == 'Selesai')
                        <span class="badge px-3 py-2 rounded-pill" style="background: #001B27; color: #00B8BE;"><i class="bi bi-check2-all me-1"></i> Selesai</span>
                    @endif
                </div>
            </div>

            <!-- DETAIL ITEM -->
            <div class="row g-3 align-items-center">
                <div class="col-md-7">
                    @foreach($p->detail as $item)
                        <div class="d-flex gap-3 align-items-center mb-2">
                            <img src="{{ $item->produk ? $item->produk->gambar_url : asset('upload/default.jpg') }}" 
                                 class="rounded-3" style="width: 60px; height: 60px; object-fit: cover;"
                                 onerror="this.onerror=null; this.src='{{ asset('upload/default.jpg') }}';">
                            <div>
                                <h6 class="mb-0 fw-bold" style="color: #001B27;">{{ $item->produk ? $item->produk->NamaProduk : 'Produk' }}</h6>
                                <div class="small text-muted">{{ $item->Jumlah }} item &times; Rp {{ number_format($item->HargaSatuan, 0, ',', '.') }}</div>
                            </div>
                        </div>
                    @endforeach
                </div>

                <div class="col-md-5 border-start-md">
                    <div class="ps-md-3">
                        <div class="small text-muted mb-1">
                            Metode Pembayaran: <strong class="text-dark">{{ $p->MetodePembayaran }}</strong> 
                            ({{ $p->pembayaran ? $p->pembayaran->StatusPembayaran : 'Menunggu' }})
                        </div>
                        <div class="small text-muted mb-2">
                            Alamat: <span class="text-dark">{{ $p->pengiriman ? $p->pengiriman->AlamatPengiriman : '-' }}</span>
                        </div>
                        @if($p->pengiriman && $p->pengiriman->NoResi)
                            <div class="small mb-2">
                                <span class="badge bg-light text-dark border"><i class="bi bi-upc-scan me-1"></i> No. Resi: {{ $p->pengiriman->NoResi }}</span>
                            </div>
                        @endif

                        <div class="d-flex justify-content-between align-items-center mt-3 pt-2 border-top">
                            <div>
                                <small class="text-muted d-block">Total Pembayaran</small>
                                <span class="fs-5 fw-bold" style="color: #005C66;">Rp {{ number_format($p->TotalHarga, 0, ',', '.') }}</span>
                            </div>
                            <a href="{{ route('pelanggan.pesanan.detail', $p->PesananID) }}" class="btn btn-outline-dark btn-sm rounded-pill px-3">
                                Rincian <i class="bi bi-chevron-right ms-1"></i>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @empty
        <div class="card custom-card border-0 text-center py-5 shadow-sm">
            <i class="bi bi-bag-x fs-1 text-muted"></i>
            <h5 class="mt-3 text-muted">Anda belum memiliki riwayat pesanan.</h5>
            <div class="mt-2">
                <a href="{{ route('pelanggan.katalog') }}" class="btn btn-danger rounded-pill px-4">
                    Mulai Belanja di SecondSpace
                </a>
            </div>
        </div>
    @endforelse

</div>
@endsection