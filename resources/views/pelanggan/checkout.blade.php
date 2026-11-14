@extends('layouts.app')

@section('title', 'Checkout Pesanan - SecondSpace')

@push('styles')
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin=""/>
<style>
    #mapCheckout {
        height: 290px;
        width: 100%;
        border-radius: 12px;
        border: 1.5px solid rgba(0, 92, 102, 0.2);
        z-index: 1;
    }
</style>
@endpush

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
                        @if(!empty($tawaran))
                            <input type="hidden" name="tawaran_id" value="{{ $tawaran->TawaranID }}">
                        @endif
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
                            @php
                                $bisaAmbil = $seller ? $seller->bisaAmbil() : true;
                                $bisaAntar = $seller ? $seller->bisaAntar() : false;
                                if (!$bisaAmbil && !$bisaAntar) {
                                    $bisaAmbil = true;
                                }
                                $defaultCara = old('cara_terima', $bisaAmbil ? 'Ambil Sendiri' : 'Diantar');
                            @endphp

                            @if($bisaAmbil)
                                <label class="p-3 border rounded-3 d-flex align-items-center gap-3 cursor-pointer" style="cursor: pointer; border-color: rgba(0, 92, 102, 0.15) !important;">
                                    <input type="radio" name="cara_terima" value="Ambil Sendiri" class="form-check-input mt-0"
                                           {{ $defaultCara == 'Ambil Sendiri' ? 'checked' : '' }} required>
                                    <div>
                                        <div class="fw-bold"><i class="bi bi-shop text-primary me-1"></i> Ambil sendiri di toko seller</div>
                                        <small class="text-muted">
                                            Gratis ongkir. Anda datang langsung ke toko/alamat seller untuk mengambil barang.
                                            @if($seller && $seller->AlamatToko)
                                                <br><span class="text-dark fw-semibold"><i class="bi bi-geo-alt-fill text-danger me-1"></i> Lokasi Toko: {{ $seller->AlamatToko }} ({{ $seller->Kota ?? 'Makassar' }})</span>
                                            @endif
                                            @if($seller)
                                                @php($stSeller = $seller->statusOperasionalToko())
                                                <br><span class="text-dark small"><i class="bi bi-clock-history me-1" style="color: #00B8BE;"></i> Jam Buka: {{ $stSeller['jam'] }} ({{ $seller->hariOperasionalLabel() }})</span>
                                            @endif
                                        </small>
                                    </div>
                                </label>
                            @endif
                            @if($bisaAntar)
                                <label class="p-3 border rounded-3 d-flex align-items-center gap-3 cursor-pointer" style="cursor: pointer; border-color: rgba(0, 92, 102, 0.15) !important;">
                                    <input type="radio" name="cara_terima" value="Diantar" class="form-check-input mt-0"
                                           {{ $defaultCara == 'Diantar' ? 'checked' : '' }} required>
                                    <div>
                                        <div class="fw-bold"><i class="bi bi-truck text-danger me-1"></i> Diantar seller (khusus Kota Makassar)</div>
                                        <small class="text-muted">Ongkir dihitung otomatis berdasarkan jarak tempuh (KM) dari toko seller ke alamat Anda.</small>
                                    </div>
                                </label>
                            @endif
                        </div>
                    </div>

                    {{-- BLOK PENGANTARAN (PETA & JARAK OTOMATIS) --}}
                    <div id="blokAntar">
                        <div class="mb-3">
                            <label class="form-label fw-bold">Alamat Lengkap di Kota Makassar <span style="color: #00B8BE;">*</span></label>
                            <textarea name="alamat" id="inputAlamat" rows="2" class="form-control" placeholder="Contoh: Jl. AP Pettarani No. 45, Kelurahan ..., Kecamatan ..., Makassar">{{ old('alamat') }}</textarea>
                            <div class="form-text">Tulis patokan rumah/gedung agar pengantaran tepat sasaran.</div>
                        </div>

                        {{-- PETA INTERAKTIF JARAK & ONGKIR --}}
                        <div class="mb-3 p-3 rounded-3 bg-light border" style="border-color: rgba(0, 92, 102, 0.2) !important;">
                            <div class="d-flex justify-content-between align-items-center mb-2 flex-wrap gap-2">
                                <div>
                                    <label class="form-label fw-bold mb-0 text-dark">
                                        <i class="bi bi-geo-alt-fill text-danger me-1"></i> Tentukan Titik Lokasi Penerima di Peta
                                    </label>
                                    <small class="text-muted d-block" style="font-size: 0.78rem;">
                                        Geser pin merah ke lokasi Anda. Sistem otomatis menghitung jarak &amp; ongkir.
                                    </small>
                                </div>
                                <button type="button" class="btn btn-sm btn-outline-primary rounded-pill px-3 fw-semibold" onclick="deteksiLokasiPembeli()">
                                    <i class="bi bi-crosshair me-1"></i> Lokasi GPS Saya
                                </button>
                            </div>

                            <div id="mapCheckout" class="mb-2"></div>

                            {{-- BADGE INFO JARAK & ONGKIR LIVE --}}
                            <div class="d-flex align-items-center justify-content-between p-2 px-3 rounded-3 bg-white border flex-wrap gap-2" style="border-color: rgba(0, 92, 102, 0.25) !important;">
                                <div class="d-flex align-items-center gap-2">
                                    <span class="badge rounded-pill" style="background: rgba(0, 184, 190, 0.15); color: #005C66; border: 1px solid rgba(0, 184, 190, 0.35);">
                                        <i class="bi bi-signpost-2 me-1"></i> <span id="textJarakKm">0 KM</span>
                                    </span>
                                    <small class="text-muted" style="font-size: 0.78rem;">dari Toko Seller</small>
                                </div>
                                <div>
                                    <span class="small text-muted me-1">Ongkir Otomatis:</span>
                                    <span class="fw-bolder" style="color: #005C66; font-size: 0.95rem;" id="textOngkirLive">Rp 0</span>
                                </div>
                            </div>

                            {{-- Hidden Coordinates --}}
                            <input type="hidden" name="latitude" id="buyerLat" value="{{ old('latitude', $sellerLat - 0.012) }}">
                            <input type="hidden" name="longitude" id="buyerLon" value="{{ old('longitude', $sellerLon + 0.015) }}">
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold">Nomor HP Penerima <span style="color: #00B8BE;">*</span></label>
                            @php
                                $kontakUser = Auth::user()->NoHP_Email ?? '';
                                $hpDefault = preg_match('/^[0-9+\-\s]{8,20}$/', $kontakUser) ? $kontakUser : '';
                            @endphp
                            <input type="text" name="no_hp" id="inputHp" class="form-control" value="{{ old('no_hp', $hpDefault) }}" placeholder="Contoh: 081234567890">
                            <div class="form-text">Nomor WhatsApp/telepon penerima yang aktif saat kurir mengantar.</div>
                        </div>

                        <div class="form-check mb-4">
                            <input class="form-check-input" type="checkbox" name="di_makassar" value="1" id="diMakassar" {{ old('di_makassar', '1') ? 'checked' : '' }}>
                            <label class="form-check-label" for="diMakassar">Saya menyatakan alamat ini berada di wilayah Kota Makassar.</label>
                        </div>
                    </div>

                    <div class="mb-4">
                        <label class="form-label fw-bold">Pilih Metode Pembayaran <span style="color: #00B8BE;">*</span></label>
                        
                        <div class="d-flex flex-column gap-2">
                            <label class="p-3 border rounded-3 d-flex align-items-center gap-3 cursor-pointer" style="cursor: pointer; border-color: rgba(0, 92, 102, 0.15) !important;">
                                <input type="radio" name="metode" value="QRIS" class="form-check-input mt-0" {{ old('metode', 'QRIS') == 'QRIS' ? 'checked' : '' }} required>
                                <div class="flex-grow-1">
                                    <div class="fw-bold d-flex align-items-center gap-2" style="color: #005C66;">
                                        <i class="bi bi-qr-code-scan fs-5" style="color: #00B8BE;"></i>
                                        <span>QRIS (E-Wallet &amp; Mobile Banking)</span>
                                        <span class="badge rounded-pill" style="background: rgba(0, 184, 190, 0.15); color: #005C66; font-size: 0.7rem; border: 1px solid rgba(0, 184, 190, 0.35);">Scan &amp; Bayar</span>
                                    </div>
                                    <small class="text-muted d-block mt-1">Scan kode QRIS langsung dari HP menggunakan GoPay, OVO, DANA, ShopeePay, BCA, Livin Mandiri, atau m-Banking apa pun.</small>
                                </div>
                            </label>

                            <label class="p-3 border rounded-3 d-flex align-items-center gap-3 cursor-pointer" style="cursor: pointer; border-color: rgba(0, 92, 102, 0.15) !important;">
                                <input type="radio" name="metode" value="COD" class="form-check-input mt-0" {{ old('metode') == 'COD' ? 'checked' : '' }}>
                                <div class="flex-grow-1">
                                    <div class="fw-bold"><i class="bi bi-cash-stack text-success me-1"></i> Cash on Delivery (COD)</div>
                                    <small class="text-muted d-block mt-1">Bayar tunai ke seller saat serah-terima barang (harga barang + ongkir). Anda akan menerima PIN COD verifikasi.</small>
                                </div>
                            </label>
                        </div>
                    </div>

                    <button type="submit" class="btn btn-danger btn-lg w-100 py-3 fw-bold rounded-pill shadow">
                        <i class="bi bi-lock-fill me-1"></i> Konfirmasi &amp; Buat Pesanan
                    </button>
                </form>
            </div>
        </div>

        <!-- RINGKASAN PESANAN -->
        <div class="col-lg-5">
            <div class="card custom-card border-0 p-4 sticky-top" style="top: 100px;">
                <h5 class="fw-bold mb-3" style="color: #001B27;"><i class="bi bi-receipt text-muted me-2"></i> Ringkasan Pesanan</h5>

                @if(!empty($tawaran))
                    <div class="alert alert-success py-2 px-3 rounded-3 mb-3 d-flex align-items-center gap-2 small border" style="background: rgba(16, 185, 129, 0.12); border-color: rgba(16, 185, 129, 0.35) !important; color: #065f46;">
                        <i class="bi bi-tag-fill fs-5 text-success"></i>
                        <div>
                            <strong>Harga Kesepakatan Tawar Chat!</strong><br>
                            Harga dipotong menjadi <strong>Rp {{ number_format($tawaran->HargaTawaran, 0, ',', '.') }}</strong> (Hemat Rp {{ number_format($items->first()->Harga - $tawaran->HargaTawaran, 0, ',', '.') }}).
                        </div>
                    </div>
                @endif

                @foreach($items as $item)
                    <div class="d-flex gap-3 align-items-center p-3 rounded-3 mb-3" style="background: #f4f9fa; border: 1px solid rgba(0, 92, 102, 0.12);">
                        <img src="{{ $item->gambar_url }}" class="rounded-3" style="width: 75px; height: 75px; object-fit: cover;" onerror="this.onerror=null; this.src='{{ asset('upload/default.jpg') }}';">
                        <div class="flex-grow-1">
                            <div class="fw-bold text-truncate" style="max-width: 220px; color: #001B27;">{{ $item->NamaProduk }}</div>
                            <div class="small text-muted">
                                Seller: {{ $item->seller?->Nama ?? '-' }} &bull; Brand: {{ $item->Brand ?? '-' }}
                            </div>
                            @if(!empty($tawaran) && (int) $item->ProdukID === (int) $tawaran->ProdukID)
                                <div>
                                    <span class="text-muted text-decoration-line-through small me-1">Rp {{ number_format($item->Harga, 0, ',', '.') }}</span>
                                    <span class="fw-bold text-success fs-6">Rp {{ number_format($tawaran->HargaTawaran, 0, ',', '.') }}</span>
                                </div>
                            @else
                                <div class="fw-bold" style="color: #005C66;">Rp {{ number_format($item->Harga, 0, ',', '.') }}</div>
                            @endif
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

                <div id="infoOngkirAntar" class="p-2.5 rounded-3 mb-2 border" style="display: none; background: rgba(0, 184, 190, 0.08); border-color: rgba(0, 184, 190, 0.35) !important;">
                    <div class="fw-bold text-dark mb-1" style="font-size: 0.8rem;">
                        <i class="bi bi-truck me-1" style="color: #00B8BE;"></i> Ongkir Dihitung Otomatis Sesuai Jarak
                    </div>
                    <div class="text-muted" style="font-size: 0.75rem; line-height: 1.35;" id="infoOngkirText">
                        Jarak ke alamat Anda dihitung otomatis dari toko seller.
                    </div>
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

@push('scripts')
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>
<script>
(function () {
    const subtotal = {{ (int) $total }};
    const sellerLat = {{ (float) $sellerLat }};
    const sellerLon = {{ (float) $sellerLon }};

    let buyerLat = parseFloat(document.getElementById('buyerLat').value) || (sellerLat - 0.012);
    let buyerLon = parseFloat(document.getElementById('buyerLon').value) || (sellerLon + 0.015);
    let currentOngkir = 0;
    let currentJarakKm = 0;

    const rp = function (n) { return 'Rp ' + n.toLocaleString('id-ID'); };

    // Rumus Haversine dengan faktor kelokan rute perkotaan Makassar (1.25x)
    function hitungJarakKM(lat1, lon1, lat2, lon2) {
        const R = 6371.0;
        const dLat = (lat2 - lat1) * Math.PI / 180;
        const dLon = (lon2 - lon1) * Math.PI / 180;
        const a = Math.sin(dLat / 2) * Math.sin(dLat / 2) +
                  Math.cos(lat1 * Math.PI / 180) * Math.cos(lat2 * Math.PI / 180) *
                  Math.sin(dLon / 2) * Math.sin(dLon / 2);
        const c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1 - a));
        const rute = R * c * 1.25;
        return Math.max(0.5, Math.round(rute * 10) / 10);
    }

    // Formula tarif: 0-3 KM = Rp 5.000, > 3 KM = +Rp 2.500/KM, bulatkan ke 500
    function hitungOngkir(km) {
        if (km <= 0) return 0;
        if (km <= 3.0) return 5000;
        const sisa = km - 3.0;
        const kotor = 5000 + (sisa * 2500);
        return Math.ceil(kotor / 500) * 500;
    }

    let map = null;
    let markerSeller = null;
    let markerBuyer = null;
    let polyline = null;

    function initMap() {
        if (map) return;
        const el = document.getElementById('mapCheckout');
        if (!el) return;

        map = L.map('mapCheckout').setView([sellerLat, sellerLon], 13);

        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            maxZoom: 19,
            attribution: '&copy; OpenStreetMap'
        }).addTo(map);

        // Marker Toko Seller (Biru)
        markerSeller = L.marker([sellerLat, sellerLon]).addTo(map);
        markerSeller.bindPopup('<b>Toko Seller</b><br>{{ $seller->AlamatToko ?? "Makassar" }}');

        // Marker Lokasi Pembeli (Draggable)
        markerBuyer = L.marker([buyerLat, buyerLon], { draggable: true }).addTo(map);
        markerBuyer.bindPopup('<b>Lokasi Pengantaran Anda</b><br>Geser pin ke titik alamat Anda.').openPopup();

        // Garis penghubung rute
        polyline = L.polyline([[sellerLat, sellerLon], [buyerLat, buyerLon]], {
            color: '#005C66',
            dashArray: '6, 8',
            weight: 3,
            opacity: 0.8
        }).addTo(map);

        function updateBuyerLocation(lat, lon) {
            buyerLat = lat;
            buyerLon = lon;
            document.getElementById('buyerLat').value = lat.toFixed(7);
            document.getElementById('buyerLon').value = lon.toFixed(7);

            polyline.setLatLngs([[sellerLat, sellerLon], [buyerLat, buyerLon]]);
            kalkulasiUlang();
        }

        markerBuyer.on('dragend', function (e) {
            const pos = e.target.getLatLng();
            updateBuyerLocation(pos.lat, pos.lng);
        });

        map.on('click', function (e) {
            markerBuyer.setLatLng(e.latlng);
            updateBuyerLocation(e.latlng.lat, e.latlng.lng);
        });

        // Sesuaikan zoom agar kedua marker terlihat
        try {
            map.fitBounds([[sellerLat, sellerLon], [buyerLat, buyerLon]], { padding: [35, 35] });
        } catch (e) {}

        setTimeout(() => { map.invalidateSize(); }, 350);
    }

    window.deteksiLokasiPembeli = function () {
        if (!navigator.geolocation) {
            alert('Browser Anda tidak mendukung deteksi lokasi otomatis.');
            return;
        }
        navigator.geolocation.getCurrentPosition(function (pos) {
            const lat = pos.coords.latitude;
            const lon = pos.coords.longitude;
            buyerLat = lat;
            buyerLon = lon;
            document.getElementById('buyerLat').value = lat.toFixed(7);
            document.getElementById('buyerLon').value = lon.toFixed(7);

            if (markerBuyer && map) {
                markerBuyer.setLatLng([lat, lon]);
                markerBuyer.bindPopup('<b>Lokasi Anda (Terdeteksi GPS)</b>').openPopup();
                polyline.setLatLngs([[sellerLat, sellerLon], [lat, lon]]);
                map.fitBounds([[sellerLat, sellerLon], [lat, lon]], { padding: [35, 35] });
            }
            kalkulasiUlang();
        }, function (err) {
            alert('Gagal mendeteksi lokasi GPS: ' + err.message + '. Anda tetap bisa menggeser pin manual pada peta.');
        }, { enableHighAccuracy: true });
    };

    function kalkulasiUlang() {
        const pilih = document.querySelector('input[name="cara_terima"]:checked');
        const antar = pilih && pilih.value === 'Diantar';

        if (antar) {
            currentJarakKm = hitungJarakKM(sellerLat, sellerLon, buyerLat, buyerLon);
            currentOngkir = hitungOngkir(currentJarakKm);

            document.getElementById('textJarakKm').textContent = currentJarakKm + ' KM';
            document.getElementById('textOngkirLive').textContent = rp(currentOngkir);

            document.getElementById('labelOngkir').textContent = rp(currentOngkir);
            document.getElementById('labelTotal').textContent = rp(subtotal + currentOngkir);

            const infoText = document.getElementById('infoOngkirText');
            if (infoText) {
                infoText.innerHTML = 'Jarak: <strong>' + currentJarakKm + ' KM</strong> &bull; Ongkir: <strong>' + rp(currentOngkir) + '</strong> (0-3 KM: Rp 5.000, berikutnya Rp 2.500/KM).';
            }
        } else {
            currentOngkir = 0;
            currentJarakKm = 0;
            document.getElementById('labelOngkir').textContent = 'GRATIS';
            document.getElementById('labelTotal').textContent = rp(subtotal);
        }
    }

    function perbaruiTampilan() {
        const pilih = document.querySelector('input[name="cara_terima"]:checked');
        const antar = pilih && pilih.value === 'Diantar';

        const blok = document.getElementById('blokAntar');
        if (blok) blok.style.display = antar ? '' : 'none';

        const inputAlamat = document.getElementById('inputAlamat');
        if (inputAlamat) inputAlamat.required = antar;
        const inputHp = document.getElementById('inputHp');
        if (inputHp) inputHp.required = antar;
        const diMakassar = document.getElementById('diMakassar');
        if (diMakassar) diMakassar.required = antar;

        const infoOngkir = document.getElementById('infoOngkirAntar');
        if (infoOngkir) infoOngkir.style.display = antar ? 'block' : 'none';

        if (antar) {
            initMap();
            setTimeout(() => { if (map) map.invalidateSize(); }, 200);
        }

        kalkulasiUlang();
    }

    document.querySelectorAll('input[name="cara_terima"]').forEach(function (el) {
        el.addEventListener('change', perbaruiTampilan);
    });

    perbaruiTampilan();
})();
</script>
@endpush
@endsection
