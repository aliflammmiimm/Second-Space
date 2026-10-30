@extends('layouts.app')
@section('title', 'Daftar Jadi Seller - SecondSpace')

@push('styles')
<style>
    .seller-hero { 
        background: linear-gradient(135deg, #001B27 0%, #003642 45%, #005C66 100%); 
        color: #fff; 
        border-radius: 22px; 
        padding: 38px 34px; 
        border-bottom: 4px solid #00B8BE; 
        box-shadow: 0 10px 30px rgba(0, 27, 39, 0.15);
    }
    .seller-hero h1 { font-weight: 800; }
    .seller-hero .accent { color: #00B8BE; }
    .form-hint { font-size: .82rem; color: #53707c; }
    .step-box { border-left: 4px solid; border-radius: 12px; background: #fff; padding: 12px 16px; margin-bottom: 10px; border: 1px solid rgba(0, 92, 102, 0.12); border-left-width: 4px; }
</style>
@endpush

@section('content')
<div class="container py-4" style="max-width: 860px;">

    {{-- HERO --}}
    <div class="seller-hero mb-4 shadow-sm">
        <div class="small fw-bold text-uppercase mb-2" style="letter-spacing:1px; color:#00B8BE;">
            <i class="bi bi-shop me-1"></i> Daftar Jadi Seller
        </div>
        <h1 class="h2 mb-2">Ubah Barang Tak Terpakai Menjadi Sesuatu yang <span class="accent">Berarti</span></h1>
        <p class="mb-2 opacity-75">
            Punya barang yang masih bagus tetapi sudah jarang digunakan? Jual barangmu di SecondSpace dan
            berikan kesempatan kedua kepada barang tersebut untuk menemukan pemilik baru.
        </p>
        <p class="mb-0 fw-semibold">Jual barang milikmu dengan mudah, tanpa perlu membuat toko sendiri.</p>
    </div>

    @if($user->isSeller())
        <div class="alert alert-success d-flex align-items-center gap-2">
            <i class="bi bi-patch-check-fill fs-4 text-success"></i>
            <div>
                Pengajuanmu sudah <strong>disetujui</strong>. Kamu sudah dapat mulai mengunggah barang untuk dijual.
                <a href="{{ route('seller.produk.index') }}" class="fw-semibold ms-1" style="color: #005C66;">Buka Dashboard Seller &rarr;</a>
            </div>
        </div>
    @else
        {{-- STATUS PENGAJUAN TERAKHIR --}}
        @if($pengajuan)
            @php($w = ['pending' => 'warning', 'approved' => 'success', 'rejected' => 'danger'][$pengajuan->Status])
            @php($label = ['pending' => 'Menunggu Persetujuan', 'approved' => 'Disetujui', 'rejected' => 'Ditolak'][$pengajuan->Status])
            <div class="card custom-card border-0 p-3 mb-4">
                <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
                    <div class="fw-semibold">Status pengajuan terakhir
                        <span class="text-muted small">({{ $pengajuan->created_at->format('d M Y H:i') }})</span>
                    </div>
                    <span class="badge bg-{{ $w }} px-3 py-2">{{ $label }}</span>
                </div>
                @if($pengajuan->Status === 'pending')
                    <p class="text-muted small mb-0 mt-2">Pengajuan sedang diperiksa Admin. Kamu tetap bisa berbelanja seperti biasa.</p>
                @elseif($pengajuan->Status === 'rejected')
                    <div class="mt-2 p-3 rounded-3" style="background:#fff1f2; border:1px solid #fecdd3;">
                        <div class="fw-semibold small text-uppercase text-danger mb-1" style="color: #e11d48 !important;">Alasan penolakan dari Admin</div>
                        <div style="color: #9f1239;">{{ $pengajuan->AlasanPenolakan ?? '-' }}</div>
                    </div>
                    <p class="text-muted small mb-0 mt-2">Kamu dapat memperbaiki data lalu mengajukan kembali melalui formulir di bawah.</p>
                @endif
            </div>
        @endif

        {{-- FORMULIR --}}
        @if(!$pengajuan || $pengajuan->Status === 'rejected')
            <form action="{{ route('pelanggan.seller.store') }}" method="POST" class="card custom-card border-0 p-4 mb-4" novalidate>
                @csrf
                <h2 class="h5 fw-bold mb-3" style="color: #001B27;"><i class="bi bi-card-checklist me-2" style="color: #00B8BE;"></i> Formulir Pengajuan Seller</h2>

                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label fw-semibold" for="nama">Nama Lengkap</label>
                        <input id="nama" class="form-control bg-light" value="{{ $user->Nama }}" readonly>
                        <div class="form-hint mt-1">Diambil otomatis dari akun pengguna.</div>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold" for="kontak">Email atau Nomor HP</label>
                        <input id="kontak" class="form-control bg-light" value="{{ $user->NoHP_Email }}" readonly>
                        <div class="form-hint mt-1">Diambil otomatis dari akun pengguna.</div>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fw-semibold" for="NoHP">Nomor WhatsApp Aktif</label>
                        <input id="NoHP" name="NoHP" type="tel" inputmode="tel"
                               class="form-control @error('NoHP') is-invalid @enderror"
                               value="{{ old('NoHP') }}" placeholder="08xxxxxxxxxx" required maxlength="20">
                        <div class="form-hint mt-1">Masukkan nomor yang dapat dihubungi jika diperlukan.</div>
                        @error('NoHP')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fw-semibold" for="Alamat">Alamat Domisili</label>
                        <input id="Alamat" name="Alamat" class="form-control @error('Alamat') is-invalid @enderror"
                               value="{{ old('Alamat') }}" required maxlength="150">
                        <div class="form-hint mt-1">Masukkan alamat tempat tinggalmu.</div>
                        @error('Alamat')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fw-semibold" for="Kota">Kota/Kabupaten</label>
                        <input id="Kota" name="Kota" class="form-control @error('Kota') is-invalid @enderror"
                               value="{{ old('Kota') }}" required maxlength="60">
                        <div class="form-hint mt-1">Masukkan kota atau kabupaten tempat tinggalmu.</div>
                        @error('Kota')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fw-semibold" for="Provinsi">Provinsi</label>
                        <input id="Provinsi" name="Provinsi" class="form-control @error('Provinsi') is-invalid @enderror"
                               value="{{ old('Provinsi') }}" required maxlength="60">
                        <div class="form-hint mt-1">Masukkan provinsi tempat tinggalmu.</div>
                        @error('Provinsi')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                    </div>

                    <div class="col-12">
                        <label class="form-label fw-semibold" for="Alasan">Alasan Ingin Menjadi Seller</label>
                        <textarea id="Alasan" name="Alasan" rows="4" class="form-control @error('Alasan') is-invalid @enderror"
                                  required minlength="10" maxlength="1000">{{ old('Alasan') }}</textarea>
                        <div class="form-hint mt-1">Ceritakan secara singkat mengapa kamu ingin menjual barang di SecondSpace.</div>
                        @error('Alasan')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                    </div>

                    <div class="col-12">
                        <div class="form-check p-3 rounded-3" style="background:#f4f9fa; border:1px solid rgba(0, 92, 102, 0.15); padding-left:2.4rem !important;">
                            <input class="form-check-input @error('setuju') is-invalid @enderror" type="checkbox" name="setuju" id="setuju" value="1" {{ old('setuju') ? 'checked' : '' }}>
                            <label class="form-check-label" for="setuju">
                                Saya menyatakan bahwa informasi yang diberikan benar dan barang yang akan saya jual
                                merupakan barang yang boleh diperjualbelikan sesuai ketentuan SecondSpace.
                            </label>
                            @error('setuju')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                        </div>
                    </div>
                </div>

                <button type="submit" class="btn btn-danger btn-lg rounded-pill fw-semibold mt-4 py-2">
                    <i class="bi bi-send me-1"></i> Kirim Pengajuan
                </button>
            </form>
        @endif
    @endif

    {{-- SETELAH MENGAJUKAN --}}
    <div class="card custom-card border-0 p-4 mb-3">
        <h2 class="h5 fw-bold mb-2" style="color: #001B27;">Setelah Mengajukan</h2>
        <p class="text-muted">Pengajuanmu akan diperiksa oleh Admin SecondSpace.</p>

        <div class="step-box" style="border-left-color: #f59e0b !important;">
            <strong><i class="bi bi-hourglass-split me-1 text-warning"></i> Menunggu Persetujuan:</strong>
            Pengajuan sedang diperiksa.
        </div>
        <div class="step-box" style="border-left-color: #005C66 !important;">
            <strong><i class="bi bi-check-circle-fill me-1" style="color: #005C66;"></i> Disetujui:</strong>
            Kamu sudah dapat mulai mengunggah barang untuk dijual.
        </div>
        <div class="step-box mb-0" style="border-left-color: #e11d48 !important;">
            <strong><i class="bi bi-x-circle-fill me-1" style="color: #e11d48;"></i> Ditolak:</strong>
            Pengajuan belum disetujui. Periksa alasan penolakan yang diberikan Admin.
        </div>
    </div>

    <p class="text-center text-muted small mb-0">
        Terima kasih telah menjadi bagian dari SecondSpace dan ikut memberikan kesempatan kedua bagi
        barang-barang yang masih layak digunakan.
    </p>
</div>
@endsection