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
<div class="mb-3"><label class="form-label fw-semibold" for="Harga">Harga (Rp)</label>
    <input id="Harga" type="number" min="1" name="Harga" class="form-control" value="{{ old('Harga', $p?->Harga) }}" required></div>
<div class="mb-3"><label class="form-label fw-semibold" for="Deskripsi">Deskripsi</label>
    <textarea id="Deskripsi" name="Deskripsi" rows="4" class="form-control" required maxlength="3000">{{ old('Deskripsi', $p?->Deskripsi) }}</textarea></div>
<div class="mb-3"><label class="form-label fw-semibold" for="foto">Foto barang (JPG/PNG/WEBP, maks. 2 MB)</label>
    <input id="foto" type="file" name="foto" accept=".jpg,.jpeg,.png,.webp" class="form-control" {{ $p ? '' : 'required' }}>
    @if($p)<img src="{{ $p->gambar_url }}" class="rounded-3 mt-2" style="height:90px" alt="Foto saat ini">@endif</div>