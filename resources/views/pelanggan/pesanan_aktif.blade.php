@extends('layouts.app')

@section('title', 'Status Pesanan Aktif - SecondSpace')

@push('styles')
<style>
    .order-step-timeline {
        display: flex;
        justify-content: space-between;
        position: relative;
        margin: 25px 0 15px;
    }

    .order-step-timeline::before {
        content: "";
        position: absolute;
        top: 18px;
        left: 20px;
        right: 20px;
        height: 3px;
        background: rgba(0, 92, 102, 0.2);
        z-index: 1;
    }

    .step-item {
        position: relative;
        z-index: 2;
        text-align: center;
        background: #fff;
        padding: 0 10px;
    }

    .step-icon {
        width: 38px;
        height: 38px;
        border-radius: 50%;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        background: #f4f9fa;
        color: #53707c;
        font-size: 0.95rem;
        margin-bottom: 6px;
        border: 2px solid rgba(0, 92, 102, 0.2);
        transition: all 0.3s ease;
    }

    .step-item.active .step-icon {
        background: #00B8BE;
        color: #001B27;
        border-color: #00B8BE;
        box-shadow: 0 0 0 4px rgba(0, 184, 190, 0.25);
    }

    .step-item.completed .step-icon {
        background: #005C66;
        color: #fff;
        border-color: #005C66;
    }

    .step-label {
        font-size: 0.78rem;
        font-weight: 600;
        color: #53707c;
        display: block;
    }

    .step-item.active .step-label {
        color: #005C66;
        font-weight: 700;
    }
</style>
@endpush

@section('content')
<div class="container py-4">

    <!-- HEADER & TABS -->
    <div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-3">
        <div>
            <h2 class="fw-bold mb-1" style="color: #001B27;"><i class="bi bi-clock-history me-2" style="color: #00B8BE;"></i> Status Pesanan Aktif Saya</h2>
            <p class="text-muted mb-0">Pantau proses pengemasan dan pelacakan pengiriman paket Anda secara real-time</p>
        </div>

        <div class="d-flex gap-2">
            <a href="{{ route('pelanggan.pesanan_aktif') }}" class="btn btn-dark rounded-pill px-3 shadow-sm">
                <i class="bi bi-hourglass-split me-1"></i> Pesanan Aktif
            </a>
            <a href="{{ route('pelanggan.pesanan') }}" class="btn btn-outline-secondary rounded-pill px-3">
                <i class="bi bi-archive me-1"></i> Riwayat Semua Pesanan
            </a>
            <a href="{{ route('pelanggan.katalog') }}" class="btn btn-outline-danger rounded-pill px-3">
                <i class="bi bi-bag-plus me-1"></i> Belanja
            </a>
        </div>
    </div>

    @forelse($pesananAktif as $ps)
        @php
            $status = $ps->StatusPesanan;
            $stepIndex = 1;
            if ($status == 'Diproses') $stepIndex = 1;
            elseif ($status == 'Dikemas') $stepIndex = 2;
            elseif ($status == 'Dikirim') $stepIndex = 3;
            elseif ($status == 'Sampai Tujuan') $stepIndex = 4;
        @endphp

        <div class="card custom-card border-0 mb-4 p-4 shadow-sm">
            <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 pb-3 border-bottom">
                <div>
                    <span class="fs-5 fw-bold text-dark me-2">ID Pesanan #{{ $ps->PesananID }}</span>
                    <span class="text-muted small">&bull; {{ $ps->TanggalPesanan ? $ps->TanggalPesanan->format('d M Y, H:i') : '-' }}</span>
                </div>
                <div>
                    @if($status == 'Diproses')
                        <span class="badge bg-warning text-dark px-3 py-2 rounded-pill"><i class="bi bi-hourglass-split me-1"></i> Sedang Diproses</span>
                    @elseif($status == 'Dikemas')
                        <span class="badge px-3 py-2 rounded-pill" style="background: #005C66; color: #fff;"><i class="bi bi-box-seam me-1"></i> Sedang Dikemas</span>
                    @elseif($status == 'Dikirim')
                        <span class="badge px-3 py-2 rounded-pill" style="background: #00B8BE; color: #001B27;"><i class="bi bi-truck me-1"></i> Dalam Pengiriman</span>
                    @elseif($status == 'Sampai Tujuan')
                        <span class="badge bg-success px-3 py-2 rounded-pill"><i class="bi bi-geo-alt me-1"></i> Telah Sampai Tujuan</span>
                    @endif
                </div>
            </div>

            <!-- VISUAL STEP TIMELINE -->
            <div class="order-step-timeline">
                <div class="step-item {{ $stepIndex >= 1 ? ($stepIndex == 1 ? 'active' : 'completed') : '' }}">
                    <div class="step-icon"><i class="bi bi-receipt"></i></div>
                    <span class="step-label">Pesanan Dibuat</span>
                </div>
                <div class="step-item {{ $stepIndex >= 2 ? ($stepIndex == 2 ? 'active' : 'completed') : '' }}">
                    <div class="step-icon"><i class="bi bi-box-seam"></i></div>
                    <span class="step-label">Dikemas</span>
                </div>
                <div class="step-item {{ $stepIndex >= 3 ? ($stepIndex == 3 ? 'active' : 'completed') : '' }}">
                    <div class="step-icon"><i class="bi bi-truck"></i></div>
                    <span class="step-label">Dikirim</span>
                </div>
                <div class="step-item {{ $stepIndex >= 4 ? 'active' : '' }}">
                    <div class="step-icon"><i class="bi bi-house-door"></i></div>
                    <span class="step-label">Sampai Tujuan</span>
                </div>
            </div>

            <hr class="my-3">

            <!-- RINCIAN BARANG & PENGIRIMAN -->
            <div class="row g-4 align-items-center">
                <div class="col-lg-7">
                    <h6 class="fw-bold mb-3 text-muted small text-uppercase">Daftar Produk yang Dipesan:</h6>
                    @foreach($ps->detail as $item)
                        <div class="d-flex gap-3 align-items-center mb-3 p-2 rounded-3" style="background: #f4f9fa; border: 1px solid rgba(0, 92, 102, 0.1);">
                            <img src="{{ $item->produk ? $item->produk->gambar_url : asset('upload/default.jpg') }}" 
                                 class="rounded-3" style="width: 65px; height: 65px; object-fit: cover;"
                                 onerror="this.onerror=null; this.src='{{ asset('upload/default.jpg') }}';">
                            <div class="flex-grow-1">
                                <h6 class="mb-1 fw-bold" style="color: #001B27;">{{ $item->produk ? $item->produk->NamaProduk : 'Produk' }}</h6>
                                <div class="small text-muted">
                                    Kategori: {{ $item->produk && $item->produk->jenisProduk ? $item->produk->jenisProduk->NamaJenis : '-' }} &bull; 
                                    Size: {{ $item->produk ? $item->produk->Ukuran : '-' }}
                                </div>
                                <div class="fw-bold" style="color: #005C66;">{{ $item->Jumlah }} &times; Rp {{ number_format($item->HargaSatuan, 0, ',', '.') }}</div>
                            </div>
                        </div>
                    @endforeach
                </div>

                <div class="col-lg-5 border-start-lg">
                    <div class="ps-lg-3">
                        <div class="mb-3">
                            <span class="text-muted small d-block">Alamat Pengantaran:</span>
                            <div class="fw-semibold text-dark">{{ $ps->pengiriman ? $ps->pengiriman->AlamatPengiriman : '-' }}</div>
                        </div>

                        <div class="mb-3">
                            <span class="text-muted small d-block">Metode Pembayaran:</span>
                            <span class="badge bg-secondary me-1">{{ $ps->MetodePembayaran }}</span>
                            <span class="badge {{ $ps->pembayaran && $ps->pembayaran->StatusPembayaran == 'Valid' ? 'bg-success' : 'bg-warning text-dark' }}">
                                Status: {{ $ps->pembayaran ? $ps->pembayaran->StatusPembayaran : 'Menunggu' }}
                            </span>
                        </div>

                        @if($ps->pengiriman && $ps->pengiriman->NoResi)
                            <div class="p-3 rounded-3 mb-3 border" style="background: #f4f9fa; border-color: rgba(0, 92, 102, 0.15) !important;">
                                <span class="small text-muted d-block">Nomor Resi Pengiriman:</span>
                                <strong class="fs-6 font-monospace" style="color: #001B27;">{{ $ps->pengiriman->NoResi }}</strong>
                            </div>
                        @endif

                                                <div class="d-flex justify-content-between align-items-center pt-3 border-top gap-2 flex-wrap">
                            <div>
                                <small class="text-muted d-block">Total Tagihan</small>
                                <span class="fs-4 fw-bolder" style="color: #005C66;">Rp {{ number_format($ps->TotalHarga, 0, ',', '.') }}</span>
                            </div>
                            <div class="d-flex gap-2 align-items-center">
                                                                @if($ps->StatusPesanan === 'Menunggu Pembayaran')
                                    <a href="{{ route('pelanggan.pesanan.detail', $ps->PesananID) }}" class="btn btn-danger rounded-pill px-3 py-2 fw-bold shadow-sm">
                                        <i class="bi bi-wallet2 me-1"></i> Bayar Sekarang
                                    </a>
                                @endif
                                @if(in_array($ps->StatusPesanan, ['Dikirim', 'Sampai Tujuan']))
                                    <form action="{{ route('pelanggan.pesanan.terima', $ps->PesananID) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin barang sudah diterima dengan baik? Dana akan diteruskan ke penjual.')" class="m-0">
                                        @csrf
                                        @method('PUT')
                                        <button type="submit" class="btn btn-success rounded-pill px-3 py-2 fw-bold shadow-sm">
                                            <i class="bi bi-check-circle me-1"></i> Terima Barang
                                        </button>
                                    </form>
                                @endif
                                <a href="{{ route('pelanggan.pesanan.detail', $ps->PesananID) }}" class="btn btn-dark rounded-pill px-4">
                                    Detail Invoice &rarr;
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @empty
        <div class="card custom-card border-0 text-center py-5 shadow-sm">
            <div class="py-4">
                <i class="bi bi-clipboard-check fs-1 text-muted"></i>
                <h4 class="mt-3 fw-bold" style="color: #001B27;">Tidak Ada Pesanan Aktif</h4>
                <p class="text-muted" style="max-width: 480px; margin: auto;">
                    Saat ini Anda tidak memiliki transaksi yang sedang berjalan.
                </p>
                <a href="{{ route('pelanggan.katalog') }}" class="btn btn-danger rounded-pill px-4 mt-3">
                    <i class="bi bi-bag-plus me-1"></i> Mulai Belanja Sekarang
                </a>
            </div>
        </div>
    @endforelse

</div>
@endsection