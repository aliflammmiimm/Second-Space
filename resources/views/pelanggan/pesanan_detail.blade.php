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
                <h3 class="fw-bold mb-1" style="color: #001B27;"><i class="bi bi-tag-fill me-1" style="color: #00B8BE;"></i> SECONDSPACE</h3>
                <p class="text-muted small mb-0">Invoice Pemesanan &bull; Pre-Loved & Thrift Marketplace</p>
            </div>
            <div class="text-end">
                <h4 class="fw-bold mb-1" style="color: #001B27;">#{{ $pesanan->PesananID }}</h4>
                <div class="text-muted small">{{ $pesanan->TanggalPesanan ? $pesanan->TanggalPesanan->format('d F Y, H:i') : '-' }}</div>
                <div class="mt-2">
                    <span class="badge" style="background: #001B27; color: #00B8BE; border: 1px solid #005C66;">{{ $pesanan->StatusPesanan }}</span>
                </div>
            </div>
        </div>

        <div class="row g-4 mb-4">
            <div class="col-md-6">
                <h6 class="fw-bold text-muted small text-uppercase mb-2">Informasi Pembeli:</h6>
                <div class="fw-bold fs-6" style="color: #001B27;">{{ Auth::user()->Nama }}</div>
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
                    @foreach($pesanan->detail as $idx => $item)
                        <tr>
                            <td>{{ $idx + 1 }}</td>
                            <td>
                                <div class="fw-bold" style="color: #001B27;">{{ $item->produk ? $item->produk->NamaProduk : 'Item Produk' }}</div>
                            </td>
                            <td>
                                {{ $item->produk && $item->produk->jenisProduk ? $item->produk->jenisProduk->NamaJenis : '-' }} &bull;
                                {{ $item->produk ? $item->produk->Brand : '-' }}
                            </td>
                            <td class="text-center">{{ $item->Jumlah }}</td>
                            <td class="text-end">Rp {{ number_format($item->HargaSatuan, 0, ',', '.') }}</td>
                            <td class="text-end fw-bold" style="color: #005C66;">Rp {{ number_format($item->Subtotal, 0, ',', '.') }}</td>
                        </tr>
                    @endforeach
                </tbody>
                <tfoot>
                    <tr>
                        <th colspan="5" class="text-end">Biaya Pengiriman:</th>
                        <th class="text-end text-success">GRATIS</th>
                    </tr>
                    <tr class="table-light">
                        <th colspan="5" class="text-end fs-5" style="color: #001B27;">Total Pembayaran:</th>
                        <th class="text-end fs-5 fw-bolder" style="color: #005C66;">Rp {{ number_format($pesanan->TotalHarga, 0, ',', '.') }}</th>
                    </tr>
                </tfoot>
            </table>
        </div>

        <!-- INFORMASI PEMBAYARAN -->
        <div class="row g-4 border-top pt-4">
            <div class="col-md-6">
                <h6 class="fw-bold text-muted small text-uppercase mb-2">Metode Pembayaran:</h6>
                <div class="fw-bold" style="color: #001B27;">
                    @if($pesanan->MetodePembayaran == 'COD')
                        <i class="bi bi-cash-stack text-success me-1"></i> Cash on Delivery (COD)
                    @else
                        <i class="bi bi-bank me-1" style="color: #00B8BE;"></i> Transfer Bank / E-Wallet
                    @endif
                </div>
                <div class="small text-muted mt-1">
                    Status: <span class="badge {{ $pesanan->pembayaran && $pesanan->pembayaran->StatusPembayaran == 'Valid' ? 'bg-success' : 'bg-warning text-dark' }}">
                        {{ $pesanan->pembayaran ? $pesanan->pembayaran->StatusPembayaran : 'Menunggu Verifikasi' }}
                    </span>
                </div>
            </div>

            <div class="col-md-6 text-md-end">
                <h6 class="fw-bold text-muted small text-uppercase mb-2">Status Pengiriman:</h6>
                <div class="fw-bold" style="color: #001B27;">
                    {{ $pesanan->pengiriman ? $pesanan->pengiriman->StatusPengiriman : 'Dalam Proses' }}
                </div>
                @if($pesanan->pengiriman && $pesanan->pengiriman->TanggalKirim)
                    <div class="small text-muted">Dikirim pada: {{ $pesanan->pengiriman->TanggalKirim->format('d M Y') }}</div>
                @endif
            </div>
        </div>

        <!-- BANNER & TOMBOL PEMBAYARAN MIDTRANS JIKA BELUM BAYAR -->
        @if($pesanan->MetodePembayaran == 'Transfer' && $pesanan->StatusPesanan == 'Menunggu Pembayaran')
            <div class="card border-0 p-4 rounded-3 mt-4 text-center" style="background: linear-gradient(135deg, rgba(0, 184, 190, 0.12), rgba(0, 92, 102, 0.08)); border: 1px solid rgba(0, 184, 190, 0.3) !important;">
                <div class="d-inline-flex p-3 rounded-circle mb-2 mx-auto" style="background: rgba(0, 184, 190, 0.2);">
                    <i class="bi bi-credit-card-2-front fs-2" style="color: #005C66;"></i>
                </div>
                <h5 class="fw-bold mb-1" style="color: #001B27;">Menunggu Pembayaran</h5>
                <p class="text-muted small mb-3">
                    @if($pesanan->BatasBayar)
                        Selesaikan pembayaran sebelum <strong>{{ $pesanan->BatasBayar->format('d M Y, H:i') }}</strong>
                    @else
                        Silakan selesaikan pembayaran untuk memproses pesanan Anda.
                    @endif
                </p>
                
                @if($pesanan->pembayaran && $pesanan->pembayaran->SnapToken)
                    <div>
                        <button id="pay-button" type="button" class="btn btn-danger btn-lg rounded-pill px-5 py-3 fw-bold shadow">
                            <i class="bi bi-wallet2 me-2"></i> Bayar Sekarang (Midtrans)
                        </button>
                    </div>
                    <small class="text-muted d-block mt-2">Mendukung Virtual Account (BCA, Mandiri, BNI, BRI, Permata) &amp; QRIS / GoPay</small>
                @else
                    <div class="alert alert-warning py-2 px-3 small d-inline-block mx-auto">
                        <i class="bi bi-exclamation-triangle me-1"></i> Kunci Midtrans Server Key belum diatur di file <code>.env</code>.
                    </div>
                @endif
            </div>
        @endif

        <!-- ACTION KONFIRMASI TERIMA BARANG -->
        @if(in_array($pesanan->StatusPesanan, ['Dikirim', 'Sampai Tujuan']))
            <div class="alert alert-success d-flex justify-content-between align-items-center mt-4 p-3 rounded-3 flex-wrap gap-3" style="background: rgba(0, 184, 190, 0.12); border: 1px solid rgba(0, 184, 190, 0.35);">
                <div>
                    <h6 class="fw-bold mb-1" style="color: #001B27;"><i class="bi bi-box2-heart me-1" style="color: #00B8BE;"></i> Paket Sudah Sampai?</h6>
                    <small class="text-muted">Jika pesanan Anda telah tiba dalam kondisi baik, silakan konfirmasi penerimaan agar dana dapat diteruskan ke pihak seller.</small>
                </div>
                <form action="{{ route('pelanggan.pesanan.terima', $pesanan->PesananID) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin barang sudah diterima dengan baik? Dana akan diteruskan ke penjual.')" class="m-0">
                    @csrf
                    @method('PUT')
                    <button type="submit" class="btn btn-success rounded-pill px-4 py-2 fw-bold shadow">
                        <i class="bi bi-check2-circle me-1"></i> Konfirmasi Terima Barang
                    </button>
                </form>
            </div>
        @elseif($pesanan->StatusPesanan == 'Selesai')
            <div class="alert alert-success mt-4 p-3 rounded-3 text-center fw-bold" style="background: rgba(0, 184, 190, 0.12); border: 1px solid rgba(0, 184, 190, 0.35); color: #005C66;">
                <i class="bi bi-patch-check-fill me-1" style="color: #00B8BE;"></i> Pesanan ini telah Selesai dan dana telah berhasil diteruskan ke pihak seller.
            </div>
        @elseif(in_array($pesanan->StatusPesanan, ['Diproses', 'Dikemas']))
            <div class="alert alert-info mt-4 p-3 rounded-3 text-center" style="background: #f4f9fa; border: 1px solid rgba(0, 92, 102, 0.15); color: #53707c;">
                <i class="bi bi-hourglass-split me-1" style="color: #005C66;"></i> Pesanan sedang diproses / dikemas oleh seller. Tombol konfirmasi penerimaan akan aktif setelah barang dikirim dan memiliki nomor resi.
            </div>
        @endif

    </div>
</div>
@endsection
@if($pesanan->pembayaran && $pesanan->pembayaran->SnapToken && $pesanan->StatusPesanan === 'Menunggu Pembayaran')
@push('scripts')
<script src="{{ config('midtrans.snap_url') }}" data-client-key="{{ config('midtrans.client_key') }}"></script>
<script>
    const payButton = document.getElementById('pay-button');
    const snapToken = '{{ $pesanan->pembayaran->SnapToken }}';

    function triggerMidtransPay() {
        if (!window.snap) {
            alert('Gagal memuat sistem pembayaran Midtrans. Periksa koneksi internet Anda.');
            return;
        }

        window.snap.pay(snapToken, {
            onSuccess: function(result) {
                console.log('Midtrans Payment Success:', result);
                window.location.reload();
            },
            onPending: function(result) {
                console.log('Midtrans Payment Pending:', result);
                window.location.reload();
            },
            onError: function(result) {
                console.log('Midtrans Payment Error:', result);
                alert('Pembayaran gagal atau dibatalkan.');
            },
            onClose: function() {
                console.log('Customer closed Midtrans popup without payment');
            }
        });
    }

    if (payButton) {
        payButton.addEventListener('click', function(e) {
            e.preventDefault();
            triggerMidtransPay();
        });
    }

    @if(session('auto_pay'))
        document.addEventListener('DOMContentLoaded', function() {
            setTimeout(function() {
                triggerMidtransPay();
            }, 800);
        });
    @endif
</script>
@endpush
@endif