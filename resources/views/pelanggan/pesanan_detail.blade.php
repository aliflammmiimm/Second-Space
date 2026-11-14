@extends('layouts.app')

@section('title', 'Rincian Pesanan #' . $pesanan->PesananID . ' - SECONDSPACE')

@push('styles')
<style>
@media print {
    .navbar, .navbar-custom, footer, .footer, .d-print-none, .alert, .btn, .mb-4.d-flex {
        display: none !important;
    }
    body {
        background: #ffffff !important;
        color: #000000 !important;
        padding: 0 !important;
    }
    .container {
        max-width: 100% !important;
        width: 100% !important;
        padding: 0 !important;
        margin: 0 !important;
    }
    .card {
        border: 1px solid #e5e7eb !important;
        box-shadow: none !important;
        padding: 24px !important;
    }
}
</style>
@endpush

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
            <div class="d-flex align-items-center gap-3">
                <img src="{{ asset('assets/images/logo.jpeg') }}" alt="SecondSpace" class="rounded-3 shadow-sm" style="width: 48px; height: 48px; object-fit: cover; border: 1.5px solid rgba(0, 184, 190, 0.35);">
                <div>
                    <h3 class="fw-bold mb-0" style="color: #001B27;">SECOND<span style="color: #00B8BE;">SPACE</span></h3>
                    <p class="text-muted small mb-0">Invoice Pemesanan &bull; Pre-Loved & Thrift Marketplace</p>
                </div>
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
                <h6 class="fw-bold text-muted small text-uppercase mb-2">Alamat &amp; Cara Terima:</h6>
                <p class="mb-0 text-dark">{{ $pesanan->pengiriman ? $pesanan->pengiriman->AlamatPengiriman : 'Belum diisi' }}</p>
                <div class="mt-2">
                    <span class="badge bg-light text-dark border">{{ $pesanan->CaraTerima ?? 'Diantar' }}</span>
                    @if($pesanan->pengiriman && $pesanan->pengiriman->JarakKM)
                        <span class="badge bg-light text-dark border"><i class="bi bi-signpost-2 me-1" style="color: #00B8BE;"></i> {{ $pesanan->pengiriman->JarakKM }} KM</span>
                    @endif
                    <span class="badge bg-light text-dark border">Ongkir: {{ ($pesanan->Ongkir ?? 0) > 0 ? 'Rp ' . number_format($pesanan->Ongkir, 0, ',', '.') : 'Gratis' }}</span>
                </div>
                @if(($pesanan->CaraTerima ?? '') === 'Ambil Sendiri')
                    <div class="mt-2 p-2 rounded-3 small" style="background: #f4f9fa; border: 1px solid rgba(0, 92, 102, 0.15);">
                        <i class="bi bi-geo-alt-fill me-1" style="color: #00B8BE;"></i>
                        <strong>Ambil di:</strong> {{ $pesanan->detail->first()?->seller?->AlamatToko ?? 'Alamat belum diisi seller, silakan tanyakan via chat.' }}
                        <div class="text-muted">Seller: {{ $pesanan->detail->first()?->seller?->Nama }}</div>
                    </div>
                @endif
                @if($pesanan->pengiriman && $pesanan->pengiriman->NoResi)
                    <div class="mt-2"><span class="badge bg-light text-dark border">No Resi: {{ $pesanan->pengiriman->NoResi }}</span></div>
                @endif
                @php($firstProduk = $pesanan->detail->first()?->produk)
                @if($firstProduk)
                    <div class="mt-2">
                        <form action="{{ route('pelanggan.chat.mulai', $firstProduk->ProdukID) }}" method="POST" class="d-inline-block">
                            @csrf
                            <input type="hidden" name="ref" value="{{ url()->full() }}">
                            <button type="submit" class="btn btn-sm rounded-pill px-3 py-1 fw-semibold shadow-sm" style="background: #001B27; color: #ffffff; border: 1px solid #005C66;">
                                <i class="bi bi-chat-dots-fill me-1" style="color: #00B8BE;"></i> Tanya / Chat Penjual
                            </button>
                        </form>
                    </div>
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
                        @php($review = \App\Models\UlasanProduk::where('PesananID', $pesanan->PesananID)->where('ProdukID', $item->ProdukID)->first())
                        <tr>
                            <td class="align-middle">{{ $idx + 1 }}</td>
                            <td class="align-middle">
                                <div class="d-flex align-items-center gap-2.5">
                                    @if($item->produk)
                                        <img src="{{ $item->produk->gambar_url }}" class="rounded-2 flex-shrink-0" style="width: 50px; height: 50px; object-fit: cover; border: 1px solid #edf2f7;" onerror="this.onerror=null; this.src='{{ asset('upload/default.jpg') }}';">
                                    @endif
                                    <div>
                                        <div class="fw-bold" style="color: #001B27;">{{ $item->produk ? $item->produk->NamaProduk : 'Item Produk' }}</div>
                                        @if($pesanan->StatusPesanan === 'Selesai')
                                            @if($review)
                                                <div class="mt-1 d-flex align-items-center gap-1.5 flex-wrap">
                                                    <span class="badge bg-warning text-dark py-0.5 px-2 rounded-pill" style="font-size: 0.72rem;">
                                                        @for($s = 1; $s <= 5; $s++)
                                                            <i class="bi bi-star{{ $s <= $review->Bintang ? '-fill' : '' }}"></i>
                                                        @endfor
                                                        <span class="ms-1">{{ $review->Bintang }}/5</span>
                                                    </span>
                                                    <span class="text-success small fw-semibold" style="font-size: 0.74rem;"><i class="bi bi-check-circle-fill me-0.5"></i> Sudah Diulas</span>
                                                    @if($review->Komentar)
                                                        <small class="text-muted d-block w-100 fst-italic mt-0.5" style="font-size: 0.74rem;">"{{ \Illuminate\Support\Str::limit($review->Komentar, 80) }}"</small>
                                                    @endif
                                                </div>
                                            @else
                                                <div class="mt-1">
                                                    <button type="button" class="btn btn-sm btn-warning text-dark fw-bold rounded-pill px-2.5 py-0.5 shadow-xs d-inline-flex align-items-center gap-1" style="font-size: 0.74rem;" 
                                                            onclick="bukaModalUlasan({{ $item->ProdukID }}, '{{ addslashes($item->produk->NamaProduk ?? 'Produk') }}', '{{ $item->produk ? $item->produk->gambar_url : '' }}')">
                                                        <i class="bi bi-star-fill text-dark"></i> Beri Ulasan &amp; Rating
                                                    </button>
                                                </div>
                                            @endif
                                        @endif
                                    </div>
                                </div>
                            </td>
                            <td class="align-middle">
                                {{ $item->produk && $item->produk->jenisProduk ? $item->produk->jenisProduk->NamaJenis : '-' }} &bull;
                                {{ $item->produk ? $item->produk->Brand : '-' }}
                            </td>
                            <td class="text-center align-middle">{{ $item->Jumlah }}</td>
                            <td class="text-end align-middle">Rp {{ number_format($item->HargaSatuan ?? 0, 0, ',', '.') }}</td>
                            <td class="text-end fw-bold align-middle" style="color: #005C66;">Rp {{ number_format($item->SubTotal ?? $item->Subtotal ?? (($item->HargaSatuan ?? 0) * ($item->Jumlah ?? 1)), 0, ',', '.') }}</td>
                        </tr>
                    @endforeach
                </tbody>
                <tfoot>
                    <tr>
                        <th colspan="5" class="text-end">Biaya Pengiriman ({{ $pesanan->CaraTerima ?? 'Diantar' }}):</th>
                        <th class="text-end {{ ($pesanan->Ongkir ?? 0) > 0 ? '' : 'text-success' }}">
                            @if($pesanan->StatusPesanan === 'Menunggu Konfirmasi Ongkir')
                                <span class="text-warning fw-semibold"><i class="bi bi-hourglass-top me-1"></i> Menunggu konfirmasi seller</span>
                            @elseif(($pesanan->Ongkir ?? 0) > 0)
                                Rp {{ number_format($pesanan->Ongkir, 0, ',', '.') }}
                            @else
                                GRATIS
                            @endif
                        </th>
                    </tr>
                    <tr class="table-light">
                        <th colspan="5" class="text-end fs-5" style="color: #001B27;">Total Pembayaran:</th>
                        <th class="text-end fs-5 fw-bolder" style="color: #005C66;">
                            Rp {{ number_format($pesanan->TotalHarga, 0, ',', '.') }}
                            @if($pesanan->StatusPesanan === 'Menunggu Konfirmasi Ongkir')
                                <small class="text-muted fw-normal d-block" style="font-size: 0.78rem;">(+ Ongkir dihitung seller)</small>
                            @endif
                        </th>
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
                    @elseif(in_array($pesanan->MetodePembayaran, ['QRIS', 'Transfer']))
                        <i class="bi bi-qr-code-scan me-1" style="color: #00B8BE;"></i> QRIS (E-Wallet &amp; m-Banking)
                    @else
                        {{ $pesanan->MetodePembayaran }}
                    @endif
                </div>
                <div class="small text-muted mt-1">
                    Status: <span class="badge {{ $pesanan->pembayaran && $pesanan->pembayaran->StatusPembayaran == 'Valid' ? 'bg-success' : 'bg-warning text-dark' }}">
                        {{ $pesanan->pembayaran ? $pesanan->pembayaran->StatusPembayaran : 'Menunggu Pembayaran' }}
                    </span>
                </div>
            </div>

            <div class="col-md-6 text-md-end">
                <h6 class="fw-bold text-muted small text-uppercase mb-2">Status Pengiriman:</h6>
                <div class="fw-bold" style="color: #001B27;">
                    @if($pesanan->StatusPesanan === 'Menunggu Konfirmasi Ongkir')
                        <span class="text-warning"><i class="bi bi-hourglass-top me-1"></i> Menunggu Konfirmasi Ongkir</span>
                    @elseif(in_array($pesanan->MetodePembayaran, ['QRIS', 'Transfer']) && $pesanan->StatusPesanan === 'Menunggu Pembayaran')
                        <span class="text-warning"><i class="bi bi-clock-history me-1"></i> Menunggu Pembayaran</span>
                    @else
                        {{ $pesanan->pengiriman && $pesanan->pengiriman->StatusPengiriman ? $pesanan->pengiriman->StatusPengiriman : 'Dalam Proses' }}
                    @endif
                </div>
                @if($pesanan->pengiriman && $pesanan->pengiriman->TanggalKirim)
                    <div class="small text-muted">Dikirim pada: {{ $pesanan->pengiriman->TanggalKirim->format('d M Y') }}</div>
                @endif
            </div>
        </div>

        <!-- KARTU STATUS: MENUNGGU KONFIRMASI ONGKIR DARI SELLER -->
        @if($pesanan->StatusPesanan === 'Menunggu Konfirmasi Ongkir')
            <div class="card border-0 p-4 rounded-4 mt-4 shadow-sm" style="background: linear-gradient(135deg, rgba(245, 158, 11, 0.08), rgba(0, 27, 39, 0.03)); border: 1.5px solid rgba(245, 158, 11, 0.35) !important;">
                <div class="d-flex align-items-start gap-3">
                    <div class="rounded-circle p-3 d-flex align-items-center justify-content-center" style="background: rgba(245, 158, 11, 0.15); color: #d97706; font-size: 1.8rem; width: 56px; height: 56px; flex-shrink: 0;">
                        <i class="bi bi-hourglass-top"></i>
                    </div>
                    <div class="flex-grow-1">
                        <div class="d-flex align-items-center gap-2 mb-1 flex-wrap">
                            <span class="badge bg-warning text-dark rounded-pill fw-bold" style="font-size: 0.75rem;">
                                <i class="bi bi-truck me-1"></i> Menunggu Konfirmasi Ongkir
                            </span>
                            <span class="small text-muted">Pengantaran Khusus Kota Makassar</span>
                        </div>
                        <h5 class="fw-bold mb-1" style="color: #001B27;">Seller Sedang Menentukan Ongkos Kirim</h5>
                        <p class="text-muted small mb-3">
                            Seller sedang mengecek jarak ke alamat pengantaran Anda (<strong>{{ $pesanan->pengiriman->AlamatPengiriman ?? 'Kota Makassar' }}</strong>). Setelah seller menentukan tarif ongkir yang pas, tagihan pembayaran akan otomatis diperbarui dan aktif di halaman ini.
                        </p>
                        <div class="d-flex gap-2 flex-wrap">
                            @php($chatProd = $pesanan->detail->first()?->produk)
                            @if($chatProd)
                                <a href="{{ route('pelanggan.chat.mulai', ['produk' => $chatProd->ProdukID, 'ref' => url()->full()]) }}" class="btn btn-sm btn-dark rounded-pill px-3 py-1 fw-semibold shadow-sm" style="background: #001B27; border: 1px solid #00B8BE; color: #00B8BE;">
                                    <i class="bi bi-chat-dots-fill me-1"></i> Tanya / Chat Seller
                                </a>
                            @endif
                            <button type="button" class="btn btn-sm btn-outline-secondary rounded-pill px-3 py-1" onclick="window.location.reload();">
                                <i class="bi bi-arrow-clockwise me-1"></i> Muat Ulang Status
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        @endif

        <!-- KARTU PIN COD PEMBELI (SERAH TERIMA TUNAI) -->
        @if($pesanan->MetodePembayaran === 'COD' && $pesanan->StatusPesanan !== 'Selesai' && $pesanan->StatusPesanan !== 'Dibatalkan')
            <div class="card border-0 p-4 rounded-4 mt-4 shadow-sm" style="background: linear-gradient(135deg, rgba(0, 184, 190, 0.08), rgba(0, 27, 39, 0.04)); border: 2px dashed #00B8BE !important;">
                <div class="row align-items-center g-3">
                    <div class="col-md-8">
                        <div class="d-flex align-items-center gap-2 mb-1">
                            <span class="badge bg-success"><i class="bi bi-cash-stack me-1"></i> Cash on Delivery</span>
                            <span class="small text-muted">Serah-Terima Barang &amp; Uang Tunai</span>
                        </div>
                        <h5 class="fw-bold mb-1" style="color: #001B27;">Kode PIN Serah-Terima COD</h5>
                        <p class="text-muted small mb-0">
                            Tunjukkan 6 digit PIN ini kepada penjual <strong>hanya setelah</strong> Anda menerima barang dan menyerahkan uang tunai. Penjual akan memasukkan PIN ini untuk mengonfirmasi transaksi selesai.
                        </p>
                    </div>
                    <div class="col-md-4 text-md-end text-center">
                        <div class="d-inline-block px-4 py-2 rounded-3 bg-white border shadow-sm">
                            <div class="small text-muted fw-bold">KODE PIN COD</div>
                            <div class="fs-2 fw-bolder" style="color: #005C66; letter-spacing: 4px;">
                                {{ $pesanan->KodePinCod ?? '------' }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endif

        <!-- KARTU PEMBAYARAN QRIS -->
        @if(in_array($pesanan->MetodePembayaran, ['QRIS', 'Transfer']) && $pesanan->StatusPesanan == 'Menunggu Pembayaran')
            <div class="card border-0 p-4 rounded-4 mt-4 shadow-sm" style="background: linear-gradient(135deg, rgba(0, 184, 190, 0.08), rgba(0, 27, 39, 0.03)); border: 1.5px solid rgba(0, 184, 190, 0.35) !important;">
                <div class="row g-4 align-items-center">
                    <!-- Kolom Gambar QRIS -->
                    <div class="col-md-5 text-center">
                        <div class="p-3 bg-white rounded-4 shadow-sm d-inline-block border" style="border-color: rgba(0, 92, 102, 0.15) !important; max-width: 290px;">
                            <div class="small fw-bold text-uppercase mb-2" style="color: #005C66; letter-spacing: 1px;">
                                <i class="bi bi-qr-code me-1" style="color: #00B8BE;"></i> Scan QRIS
                            </div>
                            <img src="{{ asset('assets/images/qris.png') }}" alt="QRIS SecondSpace" class="img-fluid rounded-3 mb-2" style="max-height: 260px; object-fit: contain; width: 100%;">
                            <div class="d-flex justify-content-center gap-1 mt-2">
                                <a href="{{ asset('assets/images/qris.png') }}" download="QRIS-SecondSpace-Pesanan-{{ $pesanan->PesananID }}.jpeg" class="btn btn-outline-dark btn-sm rounded-pill px-3 py-1 fw-semibold" style="font-size: 0.78rem;">
                                    <i class="bi bi-download me-1"></i> Simpan Gambar QRIS
                                </a>
                            </div>
                        </div>
                    </div>

                    <!-- Kolom Info Pembayaran & Upload Bukti -->
                    <div class="col-md-7">
                        <div class="d-flex align-items-center gap-2 mb-2 flex-wrap">
                            <span class="badge rounded-pill" style="background: rgba(0, 184, 190, 0.2); color: #005C66; font-size: 0.75rem; font-weight: 700;">
                                <i class="bi bi-shield-check me-1"></i> Pembayaran QRIS Resmi
                            </span>
                            <span class="badge bg-warning text-dark rounded-pill" style="font-size: 0.72rem;">Menunggu Pembayaran</span>
                        </div>

                        <h4 class="fw-bold mb-1" style="color: #001B27;">Total Tagihan Pembayaran:</h4>
                        <div class="d-flex align-items-baseline gap-2 mb-2">
                            <span class="fs-2 fw-bolder" style="color: #005C66;">Rp {{ number_format($pesanan->TotalHarga, 0, ',', '.') }}</span>
                            <button type="button" class="btn btn-sm btn-link p-0 text-decoration-none" onclick="navigator.clipboard.writeText('{{ $pesanan->TotalHarga }}'); alert('Nominal berhasil disalin: Rp {{ number_format($pesanan->TotalHarga, 0, ',', '.') }}');" style="color: #00B8BE; font-size: 0.82rem;">
                                <i class="bi bi-copy me-1"></i> Salin Nominal
                            </button>
                        </div>

                        @if($pesanan->BatasBayar)
                            <p class="text-muted small mb-3">
                                <i class="bi bi-clock-history me-1 text-warning"></i> Selesaikan pembayaran sebelum <strong>{{ $pesanan->BatasBayar->format('d M Y, H:i') }}</strong>
                            </p>
                        @endif

                        <div class="p-3 rounded-3 mb-3" style="background: #ffffff; border: 1px solid rgba(0, 92, 102, 0.12);">
                            <div class="small fw-bold mb-1" style="color: #001B27;"><i class="bi bi-info-circle me-1" style="color: #00B8BE;"></i> Langkah Pembayaran:</div>
                            <ol class="small text-muted ps-3 mb-0" style="line-height: 1.6;">
                                <li>Buka e-Wallet (GoPay, OVO, DANA, ShopeePay) atau m-Banking Anda.</li>
                                <li>Scan kode QR di samping (atau buka gambar QRIS yang diunduh).</li>
                                <li>Pastikan nama merchant <strong>SecondSpace</strong> dan masukkan nominal tepat <strong>Rp {{ number_format($pesanan->TotalHarga, 0, ',', '.') }}</strong>.</li>
                                <li>Setelah transfer berhasil, unggah screenshot/foto bukti bayar di bawah.</li>
                            </ol>
                        </div>

                        <!-- FORM UPLOAD BUKTI BAYAR -->
                        <div class="p-3 rounded-3 bg-white border shadow-sm" style="border-color: rgba(0, 184, 190, 0.3) !important;">
                            <div class="fw-bold small mb-2" style="color: #001B27;">
                                <i class="bi bi-receipt me-1" style="color: #00B8BE;"></i> Unggah Bukti Pembayaran:
                            </div>

                            @if($pesanan->pembayaran && $pesanan->pembayaran->BuktiBayar)
                                <div class="alert alert-success py-2 px-3 small d-flex align-items-center justify-content-between mb-2">
                                    <div>
                                        <i class="bi bi-check-circle-fill me-1"></i> Bukti bayar telah dikirim
                                        <span class="text-muted">({{ $pesanan->pembayaran->DiunggahPada ? \Carbon\Carbon::parse($pesanan->pembayaran->DiunggahPada)->diffForHumans() : 'Sedang diverifikasi' }})</span>
                                    </div>
                                    <a href="{{ asset('upload/pembayaran/' . $pesanan->pembayaran->BuktiBayar) }}" target="_blank" class="fw-bold text-success text-decoration-underline">
                                        Lihat Bukti
                                    </a>
                                </div>
                            @endif

                            <form action="{{ route('pelanggan.pesanan.upload_bukti', $pesanan->PesananID) }}" method="POST" enctype="multipart/form-data" class="m-0">
                                @csrf
                                <div class="input-group">
                                    <input type="file" name="bukti_bayar" class="form-control form-control-sm" accept="image/jpeg,image/png,image/jpg,image/webp" required>
                                    <button type="submit" class="btn btn-sm fw-bold px-3" style="background: #005C66; color: #fff;">
                                        <i class="bi bi-cloud-arrow-up-fill me-1"></i> {{ $pesanan->pembayaran && $pesanan->pembayaran->BuktiBayar ? 'Unggah Ulang' : 'Kirim Bukti' }}
                                    </button>
                                </div>
                                <div class="form-text" style="font-size: 0.72rem;">Format: JPG, PNG, WEBP (Maksimal 3 MB). Admin akan memvalidasi pembayaran Anda secepatnya.</div>
                            </form>
                        </div>
                    </div>
                </div>
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
                <div class="small fw-normal text-muted mt-1">Jangan lupa bagikan ulasan & rating untuk produk thrift yang Anda beli pada tabel di atas!</div>
            </div>
        @elseif(in_array($pesanan->StatusPesanan, ['Diproses', 'Dikemas']))
            <div class="alert alert-info mt-4 p-3 rounded-3 text-center" style="background: #f4f9fa; border: 1px solid rgba(0, 92, 102, 0.15); color: #53707c;">
                <i class="bi bi-hourglass-split me-1" style="color: #005C66;"></i> Pesanan sedang diproses / dikemas oleh seller. Tombol konfirmasi penerimaan akan aktif setelah barang dikirim dan memiliki nomor resi.
            </div>
        @endif

    </div>
</div>

{{-- MODAL BERI ULASAN & RATING --}}
<div class="modal fade" id="modalBeriUlasan" tabindex="-1" aria-labelledby="modalBeriUlasanLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" style="max-width: 480px;">
        <div class="modal-content rounded-4 border-0 shadow">
            <div class="modal-header border-bottom pb-2">
                <h5 class="modal-title fw-bold fs-6 d-flex align-items-center gap-2" id="modalBeriUlasanLabel" style="color: #001B27;">
                    <i class="bi bi-star-fill text-warning"></i> Beri Rating &amp; Ulasan Produk
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('pelanggan.ulasan.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <input type="hidden" name="pesanan_id" value="{{ $pesanan->PesananID }}">
                <input type="hidden" name="produk_id" id="ulasanProdukId" value="">
                
                <div class="modal-body p-3">
                    {{-- Ringkasan Produk --}}
                    <div class="d-flex align-items-center gap-3 p-2.5 rounded-3 bg-light border mb-3">
                        <img id="ulasanImg" src="" class="rounded-2 flex-shrink-0" style="width: 52px; height: 52px; object-fit: cover;" alt="Produk">
                        <div class="overflow-hidden">
                            <div id="ulasanNamaProduk" class="fw-bold text-truncate small" style="color: #001B27;"></div>
                            <small class="text-muted">Item Thrift Terkurasi SecondSpace</small>
                        </div>
                    </div>

                    {{-- Rating Bintang Interaktif --}}
                    <div class="text-center mb-3 p-3 rounded-3" style="background: #fdfbf7; border: 1px dashed rgba(245, 158, 11, 0.4);">
                        <label class="form-label fw-bold small text-dark d-block mb-1">Kualitas Barang &amp; Kepuasan Belanja</label>
                        <div class="star-rating d-inline-flex gap-2 my-1" style="font-size: 1.85rem; cursor: pointer;">
                            @for($i = 1; $i <= 5; $i++)
                                <i class="bi bi-star-fill star-item text-warning" data-value="{{ $i }}" onclick="setRating({{ $i }})" style="transition: transform 0.15s;"></i>
                            @endfor
                        </div>
                        <input type="hidden" name="bintang" id="inputRating" value="5">
                        <div id="ratingText" class="fw-bold text-warning small mt-1" style="color: #b45309 !important;">5 - Sangat Puas / Luar Biasa!</div>
                    </div>

                    {{-- Komentar Ulasan --}}
                    <div class="mb-3">
                        <label class="form-label fw-semibold small text-dark">Ulasan &amp; Pengalaman Belanja</label>
                        <textarea name="komentar" rows="3" class="form-control" placeholder="Ceritakan kondisi barang thrift, kesesuaian deskripsi, dan kecepatan pengantaran seller..." maxlength="1000" style="font-size: 0.9rem;"></textarea>
                    </div>

                    {{-- Unggah Foto Ulasan --}}
                    <div class="mb-2">
                        <label class="form-label fw-semibold small text-dark"><i class="bi bi-camera text-primary me-1"></i> Foto Produk Saat Diterima (Opsional, Maks 2 MB)</label>
                        <input type="file" name="foto" accept="image/jpeg,image/png,image/jpg,image/webp" class="form-control form-control-sm">
                        <small class="text-muted" style="font-size: 0.74rem;">Foto asli dari pembeli sangat membantu calon pembeli lain melihat kondisi nyata barang.</small>
                    </div>
                </div>

                <div class="modal-footer border-top pt-2">
                    <button type="button" class="btn btn-sm btn-light rounded-pill px-3" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-sm rounded-pill px-4 fw-bold text-dark shadow-sm" style="background: #ffc107;">
                        <i class="bi bi-send-fill me-1"></i> Kirim Ulasan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
window.bukaModalUlasan = function (prodId, nama, img) {
    document.getElementById('ulasanProdukId').value = prodId;
    document.getElementById('ulasanNamaProduk').textContent = nama;
    document.getElementById('ulasanImg').src = img || '{{ asset("upload/default.jpg") }}';

    // Reset rating ke 5
    window.setRating(5);

    const modalEl = document.getElementById('modalBeriUlasan');
    if (modalEl && window.bootstrap) {
        const modal = bootstrap.Modal.getOrCreateInstance(modalEl);
        modal.show();
    }
};

const ratingLabels = {
    1: '1 - Sangat Buruk',
    2: '2 - Kurang Memuaskan',
    3: '3 - Cukup Baik',
    4: '4 - Bagus / Sesuai Deskripsi',
    5: '5 - Sangat Puas / Luar Biasa!'
};

window.setRating = function (val) {
    document.getElementById('inputRating').value = val;
    document.getElementById('ratingText').textContent = ratingLabels[val] || (val + ' Bintang');

    const stars = document.querySelectorAll('.star-item');
    stars.forEach(st => {
        const itemVal = parseInt(st.getAttribute('data-value'));
        if (itemVal <= val) {
            st.classList.remove('bi-star');
            st.classList.add('bi-star-fill', 'text-warning');
        } else {
            st.classList.remove('bi-star-fill', 'text-warning');
            st.classList.add('bi-star', 'text-secondary');
        }
    });
};
</script>
@endpush
@endsection
