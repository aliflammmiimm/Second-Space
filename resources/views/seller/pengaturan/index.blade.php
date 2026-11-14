@extends('layouts.seller')
@section('title', 'Pengaturan Toko - Seller')

@push('styles')
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin=""/>
<style>
    #mapSellerToko {
        height: 280px;
        width: 100%;
        border-radius: 12px;
        border: 1.5px solid rgba(0, 92, 102, 0.2);
        z-index: 1;
    }
</style>
@endpush

@section('seller_content')
<div>
    <h4 class="fw-bold mb-3" style="color: #001B27;"><i class="bi bi-shop me-2" style="color: #00B8BE;"></i> Pengaturan Toko & Lokasi Pengantaran</h4>
    
    <div class="card custom-card border-0 p-4" style="max-width: 720px;">
        <form method="POST" action="{{ route('seller.pengaturan.update') }}">
            @csrf
            @method('PUT')

            <div class="mb-3">
                <label class="form-label fw-bold">Kota Toko</label>
                <input type="text" name="Kota" class="form-control" value="{{ old('Kota', $user->Kota ?? 'Makassar') }}" required>
                <div class="form-text">Layanan pengantaran saat ini dioptimalkan khusus untuk wilayah Kota Makassar.</div>
            </div>

            <div class="mb-3">
                <label class="form-label fw-bold">Alamat Pengambilan / Alamat Toko</label>
                <textarea name="AlamatToko" rows="2" class="form-control"
                          placeholder="Contoh: Jl. AP Pettarani No. 45, Panakkukang, Makassar (patokan: samping minimarket)">{{ old('AlamatToko', $user->AlamatToko) }}</textarea>
                <div class="form-text">Tulis alamat lengkap sebagai panduan pembeli saat mengambil barang atau kurir saat menjemput.</div>
            </div>

            {{-- PIN POINT LOKASI TOKO DI PETA --}}
            <div class="mb-4 p-3 rounded-3 bg-light border" style="border-color: rgba(0, 92, 102, 0.2) !important;">
                <div class="d-flex justify-content-between align-items-center mb-2 flex-wrap gap-2">
                    <div>
                        <label class="form-label fw-bold mb-0 text-dark">
                            <i class="bi bi-geo-alt-fill text-danger me-1"></i> Titik Koordinat Peta Toko (Untuk Hitung KM)
                        </label>
                        <small class="text-muted d-block" style="font-size: 0.78rem;">
                            Titik ini digunakan sistem untuk menghitung jarak pengantaran (KM) dan ongkir otomatis ke pembeli.
                        </small>
                    </div>
                    <button type="button" class="btn btn-sm btn-outline-primary rounded-pill px-3 fw-semibold" onclick="deteksiLokasiSaya()">
                        <i class="bi bi-crosshair me-1"></i> Lokasi GPS Saya
                    </button>
                </div>

                <div id="mapSellerToko" class="mb-2"></div>

                <div class="d-flex align-items-center justify-content-between flex-wrap gap-2 small text-muted">
                    <div>
                        <i class="bi bi-info-circle me-1 text-primary"></i> 
                        Geser pin atau klik di peta untuk memindahkan titik lokasi toko Anda.
                    </div>
                    <div id="koordinatDisplay" class="fw-semibold text-dark font-monospace" style="font-size: 0.78rem;">
                        {{ $user->Latitude ?? \App\Services\OngkirService::DEFAULT_LAT }}, {{ $user->Longitude ?? \App\Services\OngkirService::DEFAULT_LON }}
                    </div>
                </div>

                {{-- Hidden Inputs Latitude & Longitude --}}
                <input type="hidden" name="latitude" id="inputLat" value="{{ old('latitude', $user->Latitude ?? \App\Services\OngkirService::DEFAULT_LAT) }}">
                <input type="hidden" name="longitude" id="inputLon" value="{{ old('longitude', $user->Longitude ?? \App\Services\OngkirService::DEFAULT_LON) }}">

                {{-- Preset Lokasi Cepat di Makassar --}}
                <div class="mt-2.5 pt-2 border-top">
                    <small class="fw-semibold text-muted d-block mb-1" style="font-size: 0.75rem;">Pilih Wilayah Cepat (Makassar):</small>
                    <div class="d-flex gap-1 flex-wrap">
                        <button type="button" class="btn btn-outline-secondary btn-sm py-0 px-2 rounded-pill" style="font-size: 0.72rem;" onclick="setTitik(-5.147665, 119.432731, 'Pusat Kota / Losari')">Pusat / Losari</button>
                        <button type="button" class="btn btn-outline-secondary btn-sm py-0 px-2 rounded-pill" style="font-size: 0.72rem;" onclick="setTitik(-5.152864, 119.444985, 'Panakkukang')">Panakkukang</button>
                        <button type="button" class="btn btn-outline-secondary btn-sm py-0 px-2 rounded-pill" style="font-size: 0.72rem;" onclick="setTitik(-5.132800, 119.489300, 'Tamalanrea / Unhas')">Tamalanrea (Unhas)</button>
                        <button type="button" class="btn btn-outline-secondary btn-sm py-0 px-2 rounded-pill" style="font-size: 0.72rem;" onclick="setTitik(-5.166600, 119.436000, 'Rappocini')">Rappocini</button>
                        <button type="button" class="btn btn-outline-secondary btn-sm py-0 px-2 rounded-pill" style="font-size: 0.72rem;" onclick="setTitik(-5.170500, 119.487000, 'Manggala / Antang')">Manggala</button>
                    </div>
                </div>
            </div>

            <div class="form-check mb-3">
                <input class="form-check-input" type="checkbox" name="melayani_ambil" value="1" id="ambil"
                       {{ old('melayani_ambil', $user->melayani_ambil) ? 'checked' : '' }}>
                <label class="form-check-label fw-bold" for="ambil">Melayani ambil sendiri di toko (gratis)</label>
            </div>

            <div class="form-check mb-4">
                <input class="form-check-input" type="checkbox" name="melayani_antar" value="1" id="antar"
                       {{ old('melayani_antar', $user->melayani_antar) ? 'checked' : '' }}>
                <label class="form-check-label fw-bold" for="antar">
                    <i class="bi bi-truck text-danger me-1"></i> Melayani pengantaran (Ongkir otomatis dihitung berdasarkan jarak KM)
                </label>
            </div>

            {{-- JAM OPERASIONAL & HARI BUKA TOKO --}}
            <div class="mb-4 p-3.5 p-3 rounded-4 bg-white border shadow-sm" style="border-color: rgba(0, 92, 102, 0.2) !important;">
                <div class="d-flex align-items-center justify-content-between mb-3 flex-wrap gap-2">
                    <h6 class="fw-bold mb-0 text-dark d-flex align-items-center">
                        <i class="bi bi-clock-history me-2" style="color: #00B8BE; font-size: 1.15rem;"></i>
                        <span>Jam Buka & Hari Operasional Toko</span>
                    </h6>
                    @php($statusToko = $user->statusOperasionalToko())
                    <span class="badge {{ $statusToko['badge_class'] }} px-3 py-1.5 rounded-pill" style="font-size: 0.78rem;">
                        <i class="bi {{ $statusToko['buka'] ? 'bi-check-circle-fill' : 'bi-moon-stars-fill' }} me-1"></i>
                        Status Saat Ini: {{ $statusToko['label'] }}
                    </span>
                </div>

                {{-- Status Buka / Tutup Sementara --}}
                <div class="form-check form-switch mb-3 p-2 px-4 rounded-3 bg-light border" style="border-color: rgba(0, 92, 102, 0.12) !important;">
                    <input class="form-check-input ms-0 me-3" type="checkbox" role="switch" name="is_toko_buka" value="1" id="switchBukaToko"
                           {{ old('is_toko_buka', $user->is_toko_buka ?? true) ? 'checked' : '' }} onchange="toggleCatatanLibur()">
                    <label class="form-check-label fw-bold text-dark" for="switchBukaToko">
                        Toko Aktif / Siap Menerima Pesanan
                    </label>
                    <small class="text-muted d-block mt-0.5" style="font-size: 0.78rem;">
                        Nonaktifkan tombol ini jika toko Anda sedang libur sementara atau sedang tidak bisa melayani pembeli.
                    </small>
                </div>

                <div id="boxCatatanLibur" class="mb-3 {{ old('is_toko_buka', $user->is_toko_buka ?? true) ? 'd-none' : '' }}">
                    <label class="form-label fw-semibold small text-danger">Catatan Libur Sementara (Tampil ke Pembeli):</label>
                    <input type="text" name="CatatanLibur" class="form-control form-control-sm" 
                           placeholder="Contoh: Sedang mudik / libur sampai hari Minggu." 
                           value="{{ old('CatatanLibur', $user->CatatanLibur) }}">
                </div>

                {{-- Pilihan Jam Buka & Jam Tutup --}}
                <div class="row g-3 mb-3">
                    <div class="col-sm-6">
                        <label class="form-label fw-semibold small text-dark"><i class="bi bi-sun text-warning me-1"></i> Jam Buka (WITA)</label>
                        <input type="time" name="JamBuka" class="form-control" value="{{ old('JamBuka', $user->JamBuka ?? '08:00') }}" required>
                        <div class="form-text" style="font-size: 0.75rem;">Contoh: 08:00 WITA</div>
                    </div>
                    <div class="col-sm-6">
                        <label class="form-label fw-semibold small text-dark"><i class="bi bi-moon-stars text-primary me-1"></i> Jam Tutup (WITA)</label>
                        <input type="time" name="JamTutup" class="form-control" value="{{ old('JamTutup', $user->JamTutup ?? '21:00') }}" required>
                        <div class="form-text" style="font-size: 0.75rem;">Contoh: 21:00 WITA</div>
                    </div>
                </div>

                {{-- Hari Operasional --}}
                <div class="mb-1">
                    <label class="form-label fw-semibold small text-dark d-block mb-2">
                        <i class="bi bi-calendar-check me-1" style="color: #00B8BE;"></i> Hari Buka Toko:
                    </label>
                    @php($hariAktifUser = $user->hariOperasionalArray())
                    <div class="d-flex flex-wrap gap-2">
                        @foreach(\App\Models\User::DAFTAR_HARI as $hari)
                            <label class="btn btn-sm btn-outline-secondary rounded-pill px-3 py-1 d-inline-flex align-items-center gap-1.5 cursor-pointer" style="font-size: 0.8rem;">
                                <input type="checkbox" name="hari_operasional[]" value="{{ $hari }}" class="form-check-input mt-0 me-1"
                                       {{ in_array($hari, old('hari_operasional', $hariAktifUser), true) ? 'checked' : '' }}>
                                {{ $hari }}
                            </label>
                        @endforeach
                    </div>
                    <small class="text-muted d-block mt-2" style="font-size: 0.76rem;">
                        Pilih hari apa saja toko Anda siap melayani pengantaran, pengambilan, atau merespons chat pembeli.
                    </small>
                </div>
            </div>

            <button type="submit" class="btn btn-danger rounded-pill px-4 py-2 fw-semibold shadow-sm mt-1">
                <i class="bi bi-save me-1"></i> Simpan Pengaturan
            </button>
        </form>
    </div>
</div>

@push('scripts')
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    const defaultLat = parseFloat(document.getElementById('inputLat').value) || -5.147665;
    const defaultLon = parseFloat(document.getElementById('inputLon').value) || 119.432731;

    const map = L.map('mapSellerToko').setView([defaultLat, defaultLon], 14);

    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        maxZoom: 19,
        attribution: '&copy; OpenStreetMap'
    }).addTo(map);

    // Marker toko yang dapat digeser
    const marker = L.marker([defaultLat, defaultLon], { draggable: true }).addTo(map);
    marker.bindPopup('<b>Lokasi Toko Anda</b><br>Geser ke lokasi yang tepat.').openPopup();

    function updatePos(lat, lon) {
        document.getElementById('inputLat').value = lat.toFixed(7);
        document.getElementById('inputLon').value = lon.toFixed(7);
        document.getElementById('koordinatDisplay').textContent = lat.toFixed(6) + ', ' + lon.toFixed(6);
    }

    marker.on('dragend', function (e) {
        const pos = e.target.getLatLng();
        updatePos(pos.lat, pos.lng);
    });

    map.on('click', function (e) {
        marker.setLatLng(e.latlng);
        updatePos(e.latlng.lat, e.latlng.lng);
    });

    window.setTitik = function (lat, lon, label) {
        map.setView([lat, lon], 15);
        marker.setLatLng([lat, lon]);
        marker.bindPopup('<b>' + label + '</b>').openPopup();
        updatePos(lat, lon);
    };

    window.deteksiLokasiSaya = function () {
        if (!navigator.geolocation) {
            alert('Browser Anda tidak mendukung deteksi lokasi otomatis.');
            return;
        }
        navigator.geolocation.getCurrentPosition(function (pos) {
            const lat = pos.coords.latitude;
            const lon = pos.coords.longitude;
            map.setView([lat, lon], 16);
            marker.setLatLng([lat, lon]);
            marker.bindPopup('<b>Lokasi Terdeteksi GPS</b>').openPopup();
            updatePos(lat, lon);
        }, function (err) {
            alert('Gagal mendeteksi lokasi GPS: ' + err.message + '. Silakan geser pin manual pada peta.');
        }, { enableHighAccuracy: true });
    };

    window.toggleCatatanLibur = function () {
        const sw = document.getElementById('switchBukaToko');
        const box = document.getElementById('boxCatatanLibur');
        if (sw && box) {
            if (sw.checked) {
                box.classList.add('d-none');
            } else {
                box.classList.remove('d-none');
            }
        }
    };

    setTimeout(() => { map.invalidateSize(); }, 300);
});
</script>
@endpush
@endsection
