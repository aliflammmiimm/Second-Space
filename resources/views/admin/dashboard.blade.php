@extends('layouts.app')

@section('title', 'Admin Dashboard - SecondSpace')

@section('content')
<div class="container py-4">

    <!-- HERO HEADER -->
    <div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-3">
        <div>
            <h2 class="fw-bold mb-1"><i class="bi bi-speedometer2 text-danger me-2"></i> Panel Dashboard Admin</h2>
            <p class="text-muted mb-0">Selamat datang kembali, <strong>{{ Auth::user()->Nama }}</strong>! Ringkasan operasional SecondSpace.</p>
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
            <div class="card custom-card border-0 p-4 h-100 bg-primary bg-opacity-10 border-start border-primary border-4">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <div class="text-muted small fw-bold text-uppercase">Produk Tersedia</div>
                        <h3 class="fw-bolder mb-0 text-primary mt-1">{{ number_format($totalProdukTersedia) }}</h3>
                    </div>
                    <div class="fs-1 text-primary opacity-50"><i class="bi bi-check-circle-fill"></i></div>
                </div>
            </div>
        </div>

        <div class="col-md-3 col-sm-6">
            <div class="card custom-card border-0 p-4 h-100 bg-success bg-opacity-10 border-start border-success border-4">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <div class="text-muted small fw-bold text-uppercase">Total Produk</div>
                        <h3 class="fw-bolder mb-0 text-success mt-1">{{ number_format($totalProduk) }}</h3>
                    </div>
                    <div class="fs-1 text-success opacity-50"><i class="bi bi-tags"></i></div>
                </div>
            </div>
        </div>

        <div class="col-md-3 col-sm-6">
            <div class="card custom-card border-0 p-4 h-100 bg-warning bg-opacity-10 border-start border-warning border-4">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <div class="text-muted small fw-bold text-uppercase">Pesanan Aktif</div>
                        <h3 class="fw-bolder mb-0 text-warning mt-1">{{ number_format($totalPesananAktif) }}</h3>
                    </div>
                    <div class="fs-1 text-warning opacity-50"><i class="bi bi-hourglass-split"></i></div>
                </div>
            </div>
        </div>

        <div class="col-md-3 col-sm-6">
            <div class="card custom-card border-0 p-4 h-100 bg-dark bg-opacity-10 border-start border-dark border-4">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <div class="text-muted small fw-bold text-uppercase">Produk Terjual</div>
                        <h3 class="fw-bolder mb-0 text-dark mt-1">{{ number_format($totalTerjual) }}</h3>
                    </div>
                    <div class="fs-1 text-dark opacity-50"><i class="bi bi-bag-check"></i></div>
                </div>
            </div>
        </div>
    </div>
        <div class="row g-3 mb-4">
        <div class="col-md-3 col-6">
            <div class="card custom-card border-0 p-3 h-100">
                <div class="text-muted small fw-bold text-uppercase"><i class="bi bi-person me-1"></i> Buyer</div>
                <div class="fs-3 fw-bold">{{ number_format($totalBuyer) }}</div>
            </div>
        </div>
        <div class="col-md-3 col-6">
            <div class="card custom-card border-0 p-3 h-100">
                <div class="text-muted small fw-bold text-uppercase"><i class="bi bi-shop me-1"></i> Seller Disetujui</div>
                <div class="fs-3 fw-bold">{{ number_format($totalSeller) }}</div>
            </div>
        </div>
        <div class="col-md-3 col-6">
            <div class="card custom-card border-0 p-3 h-100">
                <div class="text-muted small fw-bold text-uppercase"><i class="bi bi-hourglass-split me-1"></i> Pengajuan Pending</div>
                <div class="fs-3 fw-bold">{{ number_format($pengajuanPending) }}</div>
            </div>
        </div>
        <div class="col-md-3 col-6">
            <div class="card custom-card border-0 p-3 h-100">
                <div class="text-muted small fw-bold text-uppercase"><i class="bi bi-receipt me-1"></i> Total Pesanan</div>
                <div class="fs-3 fw-bold">{{ number_format($totalPesanan) }}</div>
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
                    <h5 class="fw-bold mb-0"><i class="bi bi-receipt me-1"></i> Pesanan Masuk Terbaru</h5>
                    <a href="{{ route('admin.pesanan.index') }}" class="small text-decoration-none fw-semibold">Semua Pesanan &rarr;</a>
                </div>
        <div class="col-6 col-md-3">
            <div class="card border-0 shadow-sm p-3">
                 <div class="text-muted small">Pendapatan Komisi Website ({{ config('santara.komisi_persen') }}%)</div>
                     <div class="fw-bold fs-5 text-success">Rp {{ number_format($totalKomisi, 0, ',', '.') }}</div>
                </div>
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
                                    <td class="fw-bold">Rp {{ number_format($ro->TotalHarga, 0, ',', '.') }}</td>
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
            </div>
        </div>

        <!-- PRODUK TERBARU MASUK -->
        <div class="col-lg-4">
            <div class="card custom-card border-0 p-4 h-100">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h5 class="fw-bold mb-0 text-dark"><i class="bi bi-box-seam me-1"></i> Produk Terbaru</h5>
                    <a href="{{ route('admin.produk.index') }}" class="small text-decoration-none fw-semibold">Lihat Semua &rarr;</a>
                </div>
                <p class="text-muted small">Item thrift & pre-loved yang baru diinput:</p>

                <div class="d-flex flex-column gap-3">
                    @forelse($latestProducts as $item)
                        <div class="d-flex gap-3 align-items-center p-2 rounded-3 bg-light">
                            <img src="{{ $item->gambar_url }}" class="rounded-2" style="width: 50px; height: 50px; object-fit: cover;" onerror="this.onerror=null; this.src='{{ asset('upload/default.jpg') }}';">
                            <div class="flex-grow-1">
                                <div class="fw-bold small text-truncate" style="max-width: 140px;">{{ $item->NamaProduk }}</div>
                                <div class="d-flex gap-1 align-items-center mt-1">
                                    <span class="badge {{ $item->Status == 'Tersedia' ? 'bg-success' : 'bg-secondary' }}" style="font-size: 0.7rem;">
                                        {{ $item->Status }}
                                    </span>
                                    <span class="small text-muted fw-bold">Rp {{ number_format($item->Harga, 0, ',', '.') }}</span>
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