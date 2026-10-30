@extends('layouts.app')

@section('title', 'Admin Dashboard - SecondSpace')

@section('content')
<div class="container py-4">

    <!-- HERO HEADER -->
    <div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-3">
        <div>
            <h2 class="fw-bold mb-1" style="color: #001B27;"><i class="bi bi-speedometer2 me-2" style="color: #00B8BE;"></i> Panel Dashboard Admin</h2>
            <p class="text-muted mb-0">Selamat datang kembali, <strong>{{ Auth::guard('admin')->user()->Nama ?? (Auth::user()->Nama ?? 'Administrator') }}</strong>! Ringkasan operasional SecondSpace.</p>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('admin.seller.index') }}" class="btn btn-danger rounded-pill fw-semibold shadow-sm px-3">
                <i class="bi bi-person-check me-1"></i> Pengajuan Seller
            </a>
            <a href="{{ route('pelanggan.katalog') }}" class="btn btn-outline-dark rounded-pill px-3" target="_blank">
                <i class="bi bi-box-arrow-up-right me-1"></i> Buka Katalog
            </a>
        </div>
    </div>

    <!-- STATISTIC CARDS -->
    <div class="row g-3 mb-4">
        <div class="col-md-3 col-sm-6">
            <div class="card custom-card border-0 p-4 h-100 border-start border-4" style="border-left-color: #00B8BE !important; background: rgba(0, 184, 190, 0.08);">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <div class="small fw-bold text-uppercase" style="color: #53707c;">Produk Tersedia</div>
                        <h3 class="fw-bolder mb-0 mt-1" style="color: #005C66;">{{ number_format($totalProdukTersedia) }}</h3>
                    </div>
                    <div class="fs-1 opacity-75" style="color: #00B8BE;"><i class="bi bi-check-circle-fill"></i></div>
                </div>
            </div>
        </div>

        <div class="col-md-3 col-sm-6">
            <div class="card custom-card border-0 p-4 h-100 border-start border-4" style="border-left-color: #005C66 !important; background: rgba(0, 92, 102, 0.08);">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <div class="small fw-bold text-uppercase" style="color: #53707c;">Total Produk</div>
                        <h3 class="fw-bolder mb-0 mt-1" style="color: #005C66;">{{ number_format($totalProduk) }}</h3>
                    </div>
                    <div class="fs-1 opacity-75" style="color: #005C66;"><i class="bi bi-tags"></i></div>
                </div>
            </div>
        </div>

        <div class="col-md-3 col-sm-6">
            <div class="card custom-card border-0 p-4 h-100 border-start border-4" style="border-left-color: #001B27 !important; background: rgba(0, 27, 39, 0.06);">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <div class="small fw-bold text-uppercase" style="color: #53707c;">Pesanan Aktif</div>
                        <h3 class="fw-bolder mb-0 mt-1" style="color: #001B27;">{{ number_format($totalPesananAktif) }}</h3>
                    </div>
                    <div class="fs-1 opacity-75" style="color: #001B27;"><i class="bi bi-hourglass-split"></i></div>
                </div>
            </div>
        </div>

        <div class="col-md-3 col-sm-6">
            <div class="card custom-card border-0 p-4 h-100 border-start border-4" style="border-left-color: #00B8BE !important; background: rgba(0, 184, 190, 0.08);">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <div class="small fw-bold text-uppercase" style="color: #53707c;">Produk Terjual</div>
                        <h3 class="fw-bolder mb-0 mt-1" style="color: #005C66;">{{ number_format($totalTerjual) }}</h3>
                    </div>
                    <div class="fs-1 opacity-75" style="color: #00B8BE;"><i class="bi bi-bag-check"></i></div>
                </div>
            </div>
        </div>
    </div>

    <!-- SECONDARY STAT CARDS -->
    <div class="row g-3 mb-4">
        <div class="col-md-3 col-6">
            <div class="card custom-card border-0 p-3 h-100">
                <div class="text-muted small fw-bold text-uppercase"><i class="bi bi-person me-1" style="color: #00B8BE;"></i> Buyer</div>
                <div class="fs-3 fw-bold" style="color: #001B27;">{{ number_format($totalBuyer) }}</div>
            </div>
        </div>
        <div class="col-md-3 col-6">
            <div class="card custom-card border-0 p-3 h-100">
                <div class="text-muted small fw-bold text-uppercase"><i class="bi bi-shop me-1" style="color: #005C66;"></i> Seller Disetujui</div>
                <div class="fs-3 fw-bold" style="color: #001B27;">{{ number_format($totalSeller) }}</div>
            </div>
        </div>
        <div class="col-md-3 col-6">
            <div class="card custom-card border-0 p-3 h-100">
                <div class="text-muted small fw-bold text-uppercase"><i class="bi bi-hourglass-split me-1" style="color: #00B8BE;"></i> Pengajuan Pending</div>
                <div class="fs-3 fw-bold" style="color: #001B27;">{{ number_format($pengajuanPending) }}</div>
            </div>
        </div>
        <div class="col-md-3 col-6">
            <div class="card custom-card border-0 p-3 h-100">
                <div class="text-muted small fw-bold text-uppercase"><i class="bi bi-receipt me-1" style="color: #005C66;"></i> Total Pesanan</div>
                <div class="fs-3 fw-bold" style="color: #001B27;">{{ number_format($totalPesanan) }}</div>
            </div>
        </div>
    </div>

    <!-- QUICK NAV BUTTONS -->
    <div class="card custom-card border-0 p-3 mb-4">
        <div class="d-flex gap-2 flex-wrap">
            <a href="{{ route('admin.seller.index') }}" class="btn btn-outline-dark rounded-pill px-3 py-2">
                <i class="bi bi-person-check me-1"></i> Pengajuan Seller
            </a>
            <a href="{{ route('admin.seller_aktif.index') }}" class="btn btn-outline-dark rounded-pill px-3 py-2">
                Daftar Seller
            </a>
            <a href="{{ route('admin.pengguna.index') }}" class="btn btn-outline-dark rounded-pill px-3 py-2">
                <i class="bi bi-people me-1"></i> Pengguna
            </a>
            <a href="{{ route('admin.produk.index') }}" class="btn btn-outline-dark rounded-pill px-3 py-2">
                <i class="bi bi-box me-1"></i> Data Produk
            </a>
            <a href="{{ route('admin.jenis.index') }}" class="btn btn-outline-dark rounded-pill px-3 py-2">
                <i class="bi bi-collection me-1"></i> Jenis Produk
            </a>
            <a href="{{ route('admin.pesanan.index') }}" class="btn btn-outline-dark rounded-pill px-3 py-2">
                <i class="bi bi-cart me-1"></i> Pesanan
            </a>
            <a href="{{ route('admin.pembayaran.index') }}" class="btn btn-outline-dark rounded-pill px-3 py-2">
                <i class="bi bi-credit-card me-1"></i> Verifikasi Pembayaran
            </a>
            <a href="{{ route('admin.pengiriman.index') }}" class="btn btn-outline-dark rounded-pill px-3 py-2">
                <i class="bi bi-truck me-1"></i> Pengiriman
            </a>
            <a href="{{ route('admin.pencairan.index') }}" class="btn btn-outline-dark rounded-pill px-3 py-2">
                <i class="bi bi-cash-coin me-1"></i> Pencairan Seller
            </a>
        </div>
    </div>

    <div class="row g-4">
        <!-- PESANAN TERBARU -->
        <div class="col-lg-8">
            <div class="card custom-card border-0 p-4 h-100">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h5 class="fw-bold mb-0" style="color: #001B27;"><i class="bi bi-receipt me-1" style="color: #00B8BE;"></i> Pesanan Masuk Terbaru</h5>
                    <a href="{{ route('admin.pesanan.index') }}" class="small text-decoration-none fw-semibold" style="color: #005C66;">Semua Pesanan &rarr;</a>
                </div>

                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>ID</th>
                                <th>Pembeli</th>
                                <th>Tanggal</th>
                                <th>Total</th>
                                <th>Status</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($recentOrders as $ro)
                                <tr>
                                    <td class="fw-bold">#{{ $ro->PesananID }}</td>
                                    <td>{{ $ro->user ? $ro->user->Nama : 'Tamu' }}</td>
                                    <td>{{ $ro->TanggalPesanan ? $ro->TanggalPesanan->format('d/m H:i') : '-' }}</td>
                                    <td class="fw-bold" style="color: #005C66;">Rp {{ number_format($ro->TotalHarga, 0, ',', '.') }}</td>
                                    <td>
                                        <span class="badge {{ $ro->StatusPesanan == 'Selesai' ? 'bg-success' : 'bg-warning text-dark' }}">
                                            {{ $ro->StatusPesanan }}
                                        </span>
                                    </td>
                                    <td>
                                        <a href="{{ route('admin.pesanan.detail', $ro->PesananID) }}" class="btn btn-sm btn-dark rounded-pill">
                                            Detail
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center text-muted py-4">Belum ada pesanan terbaru.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                                <div class="mt-3 p-3 rounded-3 d-flex justify-content-between align-items-center flex-wrap gap-2" style="background: #f4f9fa; border: 1px solid rgba(0, 92, 102, 0.12);">
                    <div>
                        <div class="text-muted small fw-bold">Komisi Terealisasi (Pesanan Selesai)</div>
                        <div class="fw-bold fs-5" style="color: #005C66;">Rp {{ number_format($totalKomisi, 0, ',', '.') }}</div>
                    </div>
                    <div class="text-md-end border-start ps-md-3">
                        <div class="text-muted small fw-bold">Komisi Berjalan (Pesanan Aktif)</div>
                        <div class="fw-bold fs-6 text-warning">Rp {{ number_format($komisiAktif ?? 0, 0, ',', '.') }}</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- PRODUK TERBARU MASUK -->
        <div class="col-lg-4">
            <div class="card custom-card border-0 p-4 h-100">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h5 class="fw-bold mb-0" style="color: #001B27;"><i class="bi bi-box-seam me-1" style="color: #00B8BE;"></i> Produk Terbaru</h5>
                    <a href="{{ route('admin.produk.index') }}" class="small text-decoration-none fw-semibold" style="color: #005C66;">Lihat Semua &rarr;</a>
                </div>
                <p class="text-muted small">Item thrift & pre-loved yang baru diinput:</p>

                <div class="d-flex flex-column gap-3">
                    @forelse($latestProducts as $item)
                        <div class="d-flex gap-3 align-items-center p-2 rounded-3" style="background: #f4f9fa; border: 1px solid rgba(0, 92, 102, 0.08);">
                            <img src="{{ $item->gambar_url }}" class="rounded-2" style="width: 50px; height: 50px; object-fit: cover;" onerror="this.onerror=null; this.src='{{ asset('upload/default.jpg') }}';">
                            <div class="flex-grow-1">
                                <div class="fw-bold small text-truncate" style="max-width: 140px; color: #001B27;">{{ $item->NamaProduk }}</div>
                                <div class="d-flex gap-1 align-items-center mt-1">
                                    <span class="badge" style="background: {{ $item->Status == 'Tersedia' ? 'rgba(0, 184, 190, 0.2)' : 'rgba(0, 27, 39, 0.1)' }}; color: {{ $item->Status == 'Tersedia' ? '#005C66' : '#53707c' }}; font-size: 0.7rem;">
                                        {{ $item->Status }}
                                    </span>
                                    <span class="small fw-bold" style="color: #005C66;">Rp {{ number_format($item->Harga, 0, ',', '.') }}</span>
                                </div>
                            </div>
                            <a href="{{ route('admin.produk.index') }}" class="btn btn-sm btn-outline-dark">
                                Lihat
                            </a>
                        </div>
                    @empty
                        <p class="text-muted small mb-0">Belum ada data produk.</p>
                    @endforelse
                </div>
            </div>
        </div>
    </div>

</div>
@endsection