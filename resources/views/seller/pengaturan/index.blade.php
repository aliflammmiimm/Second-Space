@extends('layouts.seller')
@section('title', 'Pengaturan Toko - Seller')

@section('seller_content')
<div>
    <h4 class="fw-bold mb-3"><i class="bi bi-shop text-danger me-2"></i> Pengaturan Toko</h4>
    <div class="card custom-card border-0 p-4" style="max-width: 640px;">
        <form method="POST" action="{{ route('seller.pengaturan.update') }}">
            @csrf
            @method('PUT')

            <div class="mb-3">
                <label class="form-label fw-bold">Kota Toko</label>
                <input type="text" name="Kota" class="form-control" value="{{ old('Kota', $user->Kota) }}" required>
                <div class="form-text">Layanan antar saat ini khusus untuk seller di Kota Makassar.</div>
            </div>

            <div class="mb-3">
                <label class="form-label fw-bold">Alamat Pengambilan Barang</label>
                <textarea name="AlamatToko" rows="2" class="form-control"
                          placeholder="Contoh: Jl. Merdeka No. 45, Makassar (patokan: samping minimarket)">{{ old('AlamatToko', $user->AlamatToko) }}</textarea>
                <div class="form-text">Wajib diisi jika Anda melayani ambil sendiri. Alamat hanya tampil ke pembeli setelah pesanan dibuat.</div>
            </div>

            <div class="form-check mb-3">
                <input class="form-check-input" type="checkbox" name="melayani_ambil" value="1" id="ambil"
                       {{ old('melayani_ambil', $user->melayani_ambil) ? 'checked' : '' }}>
                <label class="form-check-label fw-bold" for="ambil">Melayani ambil sendiri di toko (gratis)</label>
            </div>

            <div class="form-check mb-2">
                <input class="form-check-input" type="checkbox" name="melayani_antar" value="1" id="antar"
                       {{ old('melayani_antar', $user->melayani_antar) ? 'checked' : '' }}>
                <label class="form-check-label fw-bold" for="antar">Melayani antar (khusus Kota Makassar)</label>
            </div>

            <div class="mb-4">
                <label class="form-label fw-bold">Tarif antar (Rp)</label>
                <input type="number" name="tarif_antar" class="form-control" min="0" max="{{ $maks }}"
                       value="{{ old('tarif_antar', $user->tarif_antar) }}">
                <div class="form-text">Tarif flat pengantaran, maksimal Rp{{ number_format($maks, 0, ',', '.') }}. (100% ongkir adalah hak Anda).</div>
                @error('tarif_antar')<div class="text-danger small">{{ $message }}</div>@enderror
            </div>

            <button type="submit" class="btn btn-danger rounded-pill px-4 fw-semibold shadow-sm">
                <i class="bi bi-save me-1"></i> Simpan Pengaturan
            </button>
        </form>
    </div>
</div>
@endsection

