@extends('layouts.app')

@section('title', 'Detail Pesanan #' . $pesanan->PesananID . ' - Admin SecondSpace')

@section('content')
<div class="container py-4">

    <nav class="mb-3">
        <a href="{{ route('admin.pesanan.index') }}" class="btn btn-outline-secondary btn-sm rounded-pill">
            <i class="bi bi-arrow-left me-1"></i> Kembali ke Daftar Pesanan
        </a>
    </nav>

    <div class="row g-4">
        <!-- DETAIL ITEMS -->
        <div class="col-lg-8">
            <div class="card custom-card border-0 p-4 mb-4">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h4 class="fw-bold mb-0">Rincian Item Pesanan #{{ $pesanan->PesananID }}</h4>
                    <span class="badge bg-dark fs-6">{{ $pesanan->StatusPesanan }}</span>
                </div>

                <div class="table-responsive">
                    <table class="table table-bordered align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>Produk</th>
                                <th class="text-center">Jumlah</th>
                                <th class="text-end">Harga Satuan</th>
                                <th class="text-end">Subtotal</th>
                                <th class="text-center" style="width: 60px;">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($pesanan->detail as $item)
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center gap-2">
                                            <img src="{{ $item->produk ? $item->produk->gambar_url : asset('upload/default.jpg') }}" class="rounded-2" style="width: 45px; height: 45px; object-fit: cover;">
                                            <div>
                                                <div class="fw-bold">{{ $item->produk ? $item->produk->NamaProduk : 'Produk' }}</div>
                                                <small class="text-muted">Size: {{ $item->produk ? $item->produk->Ukuran : '-' }}</small>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="text-center">{{ $item->Jumlah }}</td>
                                    <td class="text-end">Rp {{ number_format($item->HargaSatuan, 0, ',', '.') }}</td>
                                    <td class="text-end fw-bold">
                                        Rp {{ number_format($item->SubTotal, 0, ',', '.') }}
                                        <div class="small fw-normal text-muted">
                                            Komisi: Rp {{ number_format($item->Komisi, 0, ',', '.') }} ·
                                            Seller: Rp {{ number_format($item->PendapatanSeller, 0, ',', '.') }}
                                        </div>
                                    </td>
                                    <td class="text-center">
                                        <span class="text-muted">&mdash;</span>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                        <tfoot>
                            <tr>
                                <th colspan="3" class="text-end">Total Pembayaran:</th>
                                <th class="text-end text-danger fs-5" colspan="2">Rp {{ number_format($pesanan->TotalHarga, 0, ',', '.') }}</th>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>

            <!-- UPDATE STATUS FORM -->
            <div class="card custom-card border-0 p-4">
                <h5 class="fw-bold mb-3"><i class="bi bi-arrow-repeat text-primary me-2"></i> Update Status Pesanan & Pengiriman</h5>

                <form action="{{ route('admin.pesanan.update_status', $pesanan->PesananID) }}" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Status Pesanan</label>
                            <select name="StatusPesanan" class="form-select" required>
                                @foreach(['Diproses', 'Dikemas', 'Dikirim', 'Sampai Tujuan', 'Selesai'] as $st)
                                    <option value="{{ $st }}" {{ $pesanan->StatusPesanan == $st ? 'selected' : '' }}>
                                        {{ $st }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-bold">Nomor Resi Pengiriman</label>
                            <input type="text" name="NoResi" class="form-control" placeholder="Contoh: JNE123456789" value="{{ $pesanan->pengiriman ? $pesanan->pengiriman->NoResi : '' }}">
                        </div>
                    </div>

                    <div class="mt-4">
                        <button type="submit" class="btn btn-primary px-4 py-2 rounded-pill fw-semibold">
                            <i class="bi bi-save me-1"></i> Perbarui Status
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- INFO PEMBELI & PENGIRIMAN -->
        <div class="col-lg-4">
            <div class="card custom-card border-0 p-4 mb-4">
                <h5 class="fw-bold mb-3"><i class="bi bi-person me-2"></i> Data Pembeli</h5>
                <div class="mb-2">
                    <span class="text-muted small d-block">Nama Lengkap</span>
                    <strong>{{ $pesanan->user ? $pesanan->user->Nama : 'Tamu' }}</strong>
                </div>
                <div class="mb-2">
                    <span class="text-muted small d-block">Nomor HP / Email</span>
                    <strong>{{ $pesanan->user ? $pesanan->user->NoHP_Email : '-' }}</strong>
                </div>
                <div>
                    <span class="text-muted small d-block">Tanggal Pemesanan</span>
                    <span>{{ $pesanan->TanggalPesanan ? $pesanan->TanggalPesanan->format('d F Y, H:i:s') : '-' }}</span>
                </div>
            </div>

            <div class="card custom-card border-0 p-4">
                <h5 class="fw-bold mb-3"><i class="bi bi-truck me-2"></i> Logistik & Pembayaran</h5>
                <div class="mb-2">
                    <span class="text-muted small d-block">Alamat Pengiriman:</span>
                    <p class="mb-0">{{ $pesanan->pengiriman ? $pesanan->pengiriman->AlamatPengiriman : '-' }}</p>
                </div>
                <div class="mb-2">
                    <span class="text-muted small d-block">Status Pengiriman:</span>
                    <span class="badge bg-info text-dark">{{ $pesanan->pengiriman ? $pesanan->pengiriman->StatusPengiriman : '-' }}</span>
                </div>
                <div class="mb-2">
                    <span class="text-muted small d-block">Metode Pembayaran:</span>
                    <strong>{{ $pesanan->MetodePembayaran }}</strong>
                </div>
                <div>
                    <span class="text-muted small d-block">Status Pembayaran:</span>
                    <span class="badge {{ $pesanan->pembayaran && $pesanan->pembayaran->StatusPembayaran == 'Valid' ? 'bg-success' : 'bg-warning text-dark' }}">
                        {{ $pesanan->pembayaran ? $pesanan->pembayaran->StatusPembayaran : 'Menunggu' }}
                    </span>
                </div>
            </div>
        </div>
    </div>

</div>
@endsection