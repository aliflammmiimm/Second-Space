@extends('layouts.app')

@section('title', 'Checkout Pesanan - SecondSpace')

@section('content')
<div class="container py-5">
    
    <nav class="mb-4">
        @if($langsung && $items->isNotEmpty())
            <a href="{{ route('pelanggan.produk.detail', $items->first()->ProdukID) }}" class="btn btn-outline-secondary btn-sm rounded-pill">
                <i class="bi bi-arrow-left me-1"></i> Kembali ke Detail Produk
            </a>
        @else
            <a href="{{ route('pelanggan.keranjang') }}" class="btn btn-outline-secondary btn-sm rounded-pill">
                <i class="bi bi-arrow-left me-1"></i> Kembali ke Keranjang
            </a>
        @endif
    </nav>

    <div class="row g-4">
        
        <!-- FORM PENGIRIMAN & PEMBAYARAN -->
        <div class="col-lg-7">
            <div class="card custom-card border-0 p-4 p-md-5">
                <h4 class="fw-bold mb-4" style="color: #001B27;"><i class="bi bi-geo-alt me-2" style="color: #00B8BE;"></i> Informasi Pengiriman & Pembayaran</h4>

                <form action="{{ route('pelanggan.checkout.process') }}" method="POST" id="checkoutForm">
                    @csrf
                    @if($langsung && $items->isNotEmpty())
                        <input type="hidden" name="produk_id" value="{{ $items->first()->ProdukID }}">
                    @elseif(!empty($sellerId))
                        <input type="hidden" name="seller_id" value="{{ $sellerId }}">
                    @endif

                    <div class="mb-3">
                        <label class="form-label fw-bold">Nama Penerima</label>
                        <input type="text" class="form-control bg-light" value="{{ Auth::user()->Nama }}" readonly>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold">Kontak HP / Email</label>
                        <input type="text" class="form-control bg-light" value="{{ Auth::user()->NoHP_Email }}" readonly>
                    </div>

                    <div class="mb-4">
                        <label class="form-label fw-bold">Cara Terima Barang <span style="color: #00B8BE;">*</span></label>
                        <div class="d-flex flex-column gap-2">
                            @if($seller->bisaAmbil())
                                <label class="p-3 border rounded-3 d-flex align-items-center gap-3 cursor-pointer" style="cursor: pointer; border-color: rgba(0, 92, 102, 0.15) !important;">
                                    <input type="radio" name="cara_terima" value="Ambil Sendiri" class="form-check-input mt-0"
                                           {{ old('cara_terima', $seller->bisaAmbil() ? 'Ambil Sendiri' : 'Diantar') == 'Ambil Sendiri' ? 'checked' : '' }}>
                                    <div>
                                        <div class="fw-bold"><i class="bi bi-shop text-primary me-1"></i> Ambil sendiri di toko seller</div>
                                        <small class="text-muted">Gratis ongkir. Anda datang langsung ke alamat seller untuk mengambil barang.</small>
                                    </div>
                                </label>
                            @endif
                            @if($seller->bisaAntar())
                                <label class="p-3 border rounded-3 d-flex align-items-center gap-3 cursor-pointer" style="cursor: pointer; border-color: rgba(0, 92, 102, 0.15) !important;">
                                    <input type="radio" name="cara_terima" value="Diantar" class="form-check-input mt-0"
                                           {{ old('cara_terima', $seller->bisaAmbil() ? 'Ambil Sendiri' : 'Diantar') == 'Diantar' ? 'checked' : '' }}>
                                    <div>
                                        <div class="fw-bold"><i class="bi bi-truck text-danger me-1"></i> Diantar seller (khusus Kota Makassar)</div>
                                        <small class="text-muted">Ongkir flat Rp {{ number_format($seller->ongkirAntar(), 0, ',', '.') }}. Diantar langsung ke tempat Anda.</small>
                                    </div>
                                </label>
                            @endif
                        </div>
                    </div>

                    <div id="blokAntar">
                        <div class="mb-3">
                            <label class="form-label fw-bold">Alamat Lengkap di Kota Makassar <span style="color: #00B8BE;">*</span></label>
                            <textarea name="alamat" id="inputAlamat" rows="3" class="form-control" placeholder="Contoh: Jl. Pettarani No. 45, Kelurahan ..., Kecamatan ..., Makassar">{{ old('alamat') }}</textarea>
                            <div class="form-text">Pastikan alamat jelas agar seller dapat mengantar pesanan tepat waktu.</div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold">Nomor HP Penerima <span style="color: #00B8BE;">*</span></label>
                            <input type="text" name="no_hp" id="inputHp" class="form-control" value="{{ old('no_hp', Auth::user()->NoHP_Email) }}" placeholder="08xxxxxxxxxx">
                        </div>
                        <div class="form-check mb-4">
                            <input class="form-check-input" type="checkbox" name="di_makassar" value="1" id="diMakassar" {{ old('di_makassar') ? 'checked' : '' }}>
                            <label class="form-check-label" for="diMakassar">Saya menyatakan alamat ini berada di Kota Makassar.</label>
                        </div>
                    </div>

                    <div class="mb-4">
                        <label class="form-label fw-bold">Pilih Metode Pembayaran <span style="color: #00B8BE;">*</span></label>
                        
                        <div class="d-flex flex-column gap-2">
                            <label class="p-3 border rounded-3 d-flex align-items-center gap-3 cursor-pointer" style="cursor: pointer; border-color: rgba(0, 92, 102, 0.15) !important;">
                                <input type="radio" name="metode" value="COD" class="form-check-input mt-0" {{ old('metode', 'COD') == 'COD' ? 'checked' : '' }} required>
                                <div>
                                    <div class="fw-bold"><i class="bi bi-cash-stack text-success me-1"></i> Cash on Delivery (COD)</div>
                                    <small class="text-muted">Bayar tunai ke seller saat serah-terima barang (harga + ongkir bila diantar). Anda akan menerima PIN COD.</small>
                                </div>
                            </label>

                            <label class="p-3 border rounded-3 d-flex align-items-center gap-3 cursor-pointer" style="cursor: pointer; border-color: rgba(0, 92, 102, 0.15) !important;">
                                <input type="radio" name="metode" value="Transfer" class="form-check-input mt-0" {{ old('metode') == 'Transfer' ? 'checked' : '' }}>
                                <div>
                                    <div class="fw-bold" style="color: #005C66;">
                                        <i class="bi bi-bank me-1" style="color: #00B8BE;"></i> Transfer Bank / E-Wallet (Midtrans)
                                        <span class="badge rounded-pill ms-1" style="background: rgba(0, 184, 190, 0.15); color: #005C66; font-size: 0.7rem;">Otomatis</span>
                                    </div>
                                    <small class="text-muted">Bayar instan via Virtual Account (BCA, Mandiri, BNI, BRI, Permata) &amp; QRIS / GoPay.</small>
                                </div>
                            </label>
                        </div>
                    </div>

                    <button type="submit" class="btn btn-danger btn-lg w-100 py-3 fw-bold rounded-pill shadow">
                        <i class="bi bi-lock-fill me-1"></i> Konfirmasi & Buat Pesanan
                    </button>
                </form>
            </div>
        </div>

        <!-- RINGKASAN PESANAN -->
        <div class="col-lg-5">
            <div class="card custom-card border-0 p-4 sticky-top" style="top: 100px;">
                <h5 class="fw-bold mb-3" style="color: #001B27;"><i class="bi bi-receipt text-muted me-2"></i> Ringkasan Pesanan</h5>

                @foreach($items as $item)
                    <div class="d-flex gap-3 align-items-center p-3 rounded-3 mb-3" style="background: #f4f9fa; border: 1px solid rgba(0, 92, 102, 0.12);">
                        <img src="{{ $item->gambar_url }}" class="rounded-3" style="width: 75px; height: 75px; object-fit: cover;" onerror="this.onerror=null; this.src='{{ asset('upload/default.jpg') }}';">
                        <div class="flex-grow-1">
                            <div class="fw-bold text-truncate" style="max-width: 220px; color: #001B27;">{{ $item->NamaProduk }}</div>
                            <div class="small text-muted">
                                Seller: {{ $item->seller?->Nama ?? '-' }} &bull; Brand: {{ $item->Brand ?? '-' }}
                            </div>
                            <div class="fw-bold" style="color: #005C66;">Rp {{ number_format($item->Harga, 0, ',', '.') }}</div>
                        </div>
                    </div>
                @endforeach

                <div class="mb-3 p-3 rounded-3 d-flex justify-content-between align-items-center" style="background: #f4f9fa; border: 1px solid rgba(0, 92, 102, 0.15);">
                    <div>
                        <div class="fw-bold text-dark"><i class="bi bi-tag-fill me-1" style="color: #00B8BE;"></i> Total Item</div>
                        <small class="text-muted">Item thrift eksklusif (1 unit per produk)</small>
                    </div>
                    <span class="badge rounded-pill px-3 py-2 fs-6" style="background: #001B27; color: #00B8BE; border: 1px solid #005C66;">{{ $items->count() }} Pcs</span>
                </div>

                <hr>

                <div class="d-flex justify-content-between mb-2">
                    <span class="text-muted">Subtotal Barang</span>
                    <span>Rp {{ number_format($total, 0, ',', '.') }}</span>
                </div>
                <div class="d-flex justify-content-between mb-2">
                    <span class="text-muted">Jumlah Barang</span>
                    <span>{{ $items->count() }} Unit</span>
                </div>
                <div class="d-flex justify-content-between mb-2">
                    <span class="text-muted">Ongkir</span>
                    <span class="fw-bold" id="labelOngkir">GRATIS</span>
                </div>

                <hr>

                <div class="d-flex justify-content-between align-items-center mb-3">
                    <span class="fs-5 fw-bold" style="color: #001B27;">Total Tagihan</span>
                    <span class="fs-4 fw-bolder" style="color: #005C66;" id="labelTotal">
                        Rp {{ number_format($total, 0, ',', '.') }}
                    </span>
                </div>
            </div>
        </div>

    </div>
</div>

<script>
(function () {
    var subtotal = {{ (int) $total }};
    var ongkirAntar = {{ (int) $seller->ongkirAntar() }};
    var rp = function (n) { return 'Rp ' + n.toLocaleString('id-ID'); };

    function perbarui() {
        var pilih = document.querySelector('input[name="cara_terima"]:checked');
        var antar = pilih && pilih.value === 'Diantar';
        var blok = document.getElementById('blokAntar');
        if (blok) blok.style.display = antar ? '' : 'none';
        var inputAlamat = document.getElementById('inputAlamat');
        if (inputAlamat) inputAlamat.required = antar;
        var inputHp = document.getElementById('inputHp');
        if (inputHp) inputHp.required = antar;
        var diMakassar = document.getElementById('diMakassar');
        if (diMakassar) diMakassar.required = antar;

        var ongkir = antar ? ongkirAntar : 0;
        var labelOngkir = document.getElementById('labelOngkir');
        if (labelOngkir) labelOngkir.textContent = antar ? rp(ongkir) : 'GRATIS';
        var labelTotal = document.getElementById('labelTotal');
        if (labelTotal) labelTotal.textContent = rp(subtotal + ongkir);
    }

    document.querySelectorAll('input[name="cara_terima"]').forEach(function (el) {
        el.addEventListener('change', perbarui);
    });
    perbarui();
})();
</script>
@endsection