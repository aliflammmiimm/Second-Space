@extends('layouts.app')

@section('title', 'Rincian Pesanan #' . $pesanan->PesananID . ' - SECONDSPACE')

@section('content')
<div class="container py-5">
    
    <div class="mb-4 d-flex justify-content-between align-items-center">
        <a href="{{ route('pelanggan.pesanan') }}" class="btn btn-outline-secondary btn-sm rounded-pill">
            <i class="bi bi-arrow-left me-1"></i> Kembali ke Pesanan Saya
        </a>
        <button onclick="window.print()" class="btn btn-outline-dark btn-sm rounded-pill d-print-none">
            <i class="bi bi-printer me-1"></i> Cetak Invoice
        </button>
    </div>

    <div class="card custom-card border-0 p-4 p-md-5">
        <div class="d-flex justify-content-between align-items-start border-bottom pb-4 mb-4">
            <div>
                <h3 class="fw-bold text-danger mb-1"><i class="bi bi-tag-fill me-1"></i> SECONDSPACE</h3>
                <p class="text-muted small mb-0">Invoice Pemesanan &bull; Pre-Loved & Thrift Marketplace</p>
            </div>
            <div class="text-end">
                <h4 class="fw-bold mb-1">#{{ $pesanan->PesananID }}</h4>
                <div class="text-muted small">{{ $pesanan->TanggalPesanan ? $pesanan->TanggalPesanan->format('d F Y, H:i') : '-' }}</div>
                <div class="mt-2">
                    <span class="badge bg-danger">{{ $pesanan->StatusPesanan }}</span>
                </div>
            </div>
        </div>

        <div class="row g-4 mb-4">
            <div class="col-md-6">
                <h6 class="fw-bold text-muted small text-uppercase mb-2">Informasi Pembeli:</h6>
                <div class="fw-bold fs-6">{{ Auth::user()->Nama }}</div>
                <div class="text-muted">{{ Auth::user()->NoHP_Email }}</div>
            </div>
            <div class="col-md-6">
                <h6 class="fw-bold text-muted small text-uppercase mb-2">Alamat Pengiriman:</h6>
                <p class="mb-0 text-dark">{{ $pesanan->pengiriman ? $pesanan->pengiriman->AlamatPengiriman : 'Belum diisi' }}</p>
                @if($pesanan->pengiriman && $pesanan->pengiriman->NoResi)
                    <div class="mt-2"><span class="badge bg-light text-dark border">No Resi: {{ $pesanan->pengiriman->NoResi }}</span></div>
                @endif
            </div>
        </div>

        <!-- TABEL PRODUK -->
        <div class="table-responsive mb-4">
            <table class="table table-bordered">
                <thead class="table-dark">
                    <tr>
                        <th>No</th>
                        <th>Item Produk</th>
                        <th>Kategori / Brand</th>
                        <th class="text-center">Jumlah</th>
                        <th class="text-end">Harga Satuan</th>
                        <th class="text-end">Subtotal</th>
                    </tr>
                </thead>
                <tbody>
                    @php $no = 1; @endphp
                    @foreach($pesanan->detail as $item)
                        <tr>
                            <td>{{ $no++ }}</td>
                            <td class="fw-bold">{{ $item->produk ? $item->produk->NamaProduk : 'Produk' }}</td>
                            <td>{{ $item->produk && $item->produk->jenisProduk ? $item->produk->jenisProduk->NamaJenis : '-' }}</td>
                            <td class="text-center">{{ $item->Jumlah }}</td>
                            <td class="text-end">Rp {{ number_format($item->HargaSatuan, 0, ',', '.') }}</td>
                            <td class="text-end fw-bold">Rp {{ number_format($item->SubTotal, 0, ',', '.') }}</td>
                        </tr>
                    @endforeach
                </tbody>
                <tfoot>
                    <tr>
                        <th colspan="5" class="text-end">Total Pembayaran:</th>
                        <th class="text-end text-danger fs-5">Rp {{ number_format($pesanan->TotalHarga, 0, ',', '.') }}</th>
                    </tr>
                </tfoot>
            </table>
        </div>

        <div class="row g-3">
            <div class="col-md-6">
                <div class="p-3 bg-light rounded-3">
                    <h6 class="fw-bold mb-1">Metode Pembayaran: {{ $pesanan->MetodePembayaran }}</h6>
                    <div class="small text-muted">
                        Status Pembayaran: <span class="badge {{ $pesanan->pembayaran && $pesanan->pembayaran->StatusPembayaran == 'Valid' ? 'bg-success' : 'bg-warning text-dark' }}">{{ $pesanan->pembayaran ? $pesanan->pembayaran->StatusPembayaran : 'Menunggu' }}</span>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="p-3 bg-light rounded-3">
                    <h6 class="fw-bold mb-1">Status Pengiriman: {{ $pesanan->pengiriman ? $pesanan->pengiriman->StatusPengiriman : 'Diproses' }}</h6>
                    <div class="small text-muted">Terakhir diperbarui: {{ $pesanan->pengiriman && $pesanan->pengiriman->TanggalUpdate ? $pesanan->pengiriman->TanggalUpdate->format('d M Y H:i') : '-' }}</div>
                </div>
            </div>
        </div>

        @if(in_array($pesanan->StatusPesanan, ['Dikirim', 'Sampai Tujuan'], true))
            <form action="{{ route('pelanggan.pesanan.terima', $pesanan->PesananID) }}" method="POST"
                  onsubmit="return confirm('Pastikan barang sudah Anda terima. Lanjutkan?')" class="mt-4">
                @csrf
                @method('PUT')
                <button type="submit" class="btn btn-success rounded-pill px-4 fw-semibold">
                    <i class="bi bi-check-circle me-1"></i> Pesanan Diterima
                </button>
            </form>
        @endif

    </div>
</div>
@endsection