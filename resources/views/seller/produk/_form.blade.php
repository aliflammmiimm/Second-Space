@php($p = $produk ?? null)
<div class="mb-3"><label class="form-label fw-semibold" for="NamaProduk">Nama barang</label>
    <input id="NamaProduk" name="NamaProduk" class="form-control" value="{{ old('NamaProduk', $p?->NamaProduk) }}" required maxlength="150"></div>
<div class="row">
    <div class="col-md-6 mb-3"><label class="form-label fw-semibold" for="JenisID">Kategori</label>
        <select id="JenisID" name="JenisID" class="form-select" required>
            <option value="">Pilih kategori</option>
            @foreach($categories as $c)
                <option value="{{ $c->JenisID }}" @selected(old('JenisID', $p?->JenisID) == $c->JenisID)>{{ $c->NamaJenis }}</option>
            @endforeach
        </select></div>
    <div class="col-md-6 mb-3"><label class="form-label fw-semibold" for="Kondisi">Kondisi</label>
        <select id="Kondisi" name="Kondisi" class="form-select" required>
            @foreach($kondisi as $k => $label)
                <option value="{{ $k }}" @selected(old('Kondisi', $p?->Kondisi) === $k)>{{ $label }}</option>
            @endforeach
        </select></div>
</div>
<div class="mb-3">
    <label class="form-label fw-semibold" for="HargaDisplay">Harga (Rp) <span class="text-danger">*</span></label>
    <div class="input-group">
        <span class="input-group-text bg-light fw-bold" style="border-color: rgba(0, 92, 102, 0.25); color: #005C66;">Rp</span>
        <input id="HargaDisplay" type="text" inputmode="numeric" class="form-control" 
               placeholder="Contoh: 50.000" 
               value="{{ old('Harga', $p?->Harga) ? number_format((int) old('Harga', $p?->Harga), 0, ',', '.') : '' }}" 
               required autocomplete="off" style="border-color: rgba(0, 92, 102, 0.25); font-weight: 600; color: #001B27;">
    </div>
    <input id="Harga" type="hidden" name="Harga" value="{{ old('Harga', $p?->Harga) }}">
    <div class="form-text text-muted">Format otomatis pemisah ribuan agar nominal mudah dibaca (contoh: 50.000).</div>
    @error('Harga')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
</div>
<div class="mb-3"><label class="form-label fw-semibold" for="Deskripsi">Deskripsi</label>
    <textarea id="Deskripsi" name="Deskripsi" rows="4" class="form-control" required maxlength="3000">{{ old('Deskripsi', $p?->Deskripsi) }}</textarea></div>
<div class="mb-4">
    <label class="form-label fw-semibold" for="foto">Foto Utama / Sampul (JPG/PNG/WEBP, maks. 2 MB) <span class="text-danger">*</span></label>
    <input id="foto" type="file" name="foto" accept=".jpg,.jpeg,.png,.webp" class="form-control" {{ $p ? '' : 'required' }}>
    <div class="form-text">Foto ini akan menjadi gambar utama yang tampil pada katalog pencarian.</div>
    @if($p)
        <div class="mt-2 d-flex align-items-center gap-2">
            <img src="{{ $p->gambar_url }}" class="rounded-3 border" style="width: 90px; height: 90px; object-fit: cover;" alt="Foto utama saat ini">
            <span class="small text-muted">Foto utama saat ini (unggah file baru di atas jika ingin mengganti).</span>
        </div>
    @endif
</div>

<div class="mb-4 p-3 rounded-3" style="background: #f4f9fa; border: 1px dashed rgba(0, 92, 102, 0.3);">
    <label class="form-label fw-semibold" for="foto_tambahan">
        <i class="bi bi-images me-1" style="color: #00B8BE;"></i> Foto Pendukung / Detail Thrift (Opsional, Maks. 4 Foto)
    </label>
    <input id="foto_tambahan" type="file" name="foto_tambahan[]" accept=".jpg,.jpeg,.png,.webp" class="form-control" multiple>
    <div class="form-text">
        Sangat disarankan untuk barang thrift: foto tampak belakang, label/tag merek, atau detail minus (noda/cacat kecil) agar pembeli percaya.
    </div>

    @if($p && $p->gambarTambahan && $p->gambarTambahan->isNotEmpty())
        <div class="mt-3">
            <div class="fw-semibold small mb-2 text-dark">Foto Detail Tambahan yang Sudah Ada:</div>
            <div class="d-flex flex-wrap gap-3">
                @foreach($p->gambarTambahan as $gt)
                    <div class="border rounded-3 p-2 bg-white text-center shadow-sm" style="width: 110px;">
                        <img src="{{ $gt->gambar_url }}" class="rounded-2 w-100 mb-2" style="height: 80px; object-fit: cover;" alt="Detail">
                        <div class="form-check d-flex justify-content-center align-items-center gap-1">
                            <input class="form-check-input mt-0" type="checkbox" name="hapus_foto_tambahan[]" value="{{ $gt->GambarID }}" id="hapus_foto_{{ $gt->GambarID }}">
                            <label class="form-check-label small text-danger" for="hapus_foto_{{ $gt->GambarID }}" style="font-size: 0.75rem;">Hapus</label>
                        </div>
                    </div>
                @endforeach
            </div>
            <small class="text-muted d-block mt-1">Centang "Hapus" pada foto yang ingin dibuang saat menyimpan perubahan.</small>
        </div>
    @endif
</div>

<script>
(function () {
    const displayInput = document.getElementById('HargaDisplay');
    const hiddenInput = document.getElementById('Harga');

    function formatRibuan(angka) {
        if (!angka) return '';
        const clean = angka.toString().replace(/[^0-9]/g, '');
        return clean.replace(/\B(?=(\d{3})+(?!\d))/g, '.');
    }

    if (displayInput && hiddenInput) {
        if (hiddenInput.value) {
            displayInput.value = formatRibuan(hiddenInput.value);
        }

        displayInput.addEventListener('input', function () {
            const raw = this.value.replace(/[^0-9]/g, '');
            hiddenInput.value = raw;
            this.value = formatRibuan(raw);
        });

        displayInput.closest('form')?.addEventListener('submit', function () {
            hiddenInput.value = displayInput.value.replace(/[^0-9]/g, '');
        });
    }
})();
</script>