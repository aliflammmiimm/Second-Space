@extends('layouts.app')

@section('title', 'Daftar Seller - SecondSpace')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-8 col-lg-6">

            <div class="card custom-card border-0 shadow-lg p-4">
                <div class="text-center mb-4">
                    <div class="d-inline-flex p-3 rounded-circle bg-dark text-white mb-3">
                        <i class="bi bi-shop fs-2"></i>
                    </div>
                    <h3 class="fw-bold mb-1">Daftar Seller</h3>
                    <p class="text-muted mb-0" style="font-size: 0.9rem;">Buat akun dan ajukan diri untuk menjual barang di SecondSpace</p>
                </div>

                <form action="{{ route('register.seller') }}" method="POST">
                    @csrf

                    <div class="mb-3">
                        <label class="form-label fw-semibold" style="font-size: 0.9rem;">Nama Lengkap</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light border-end-0"><i class="bi bi-person text-muted"></i></span>
                            <input type="text" name="nama" class="form-control border-start-0" placeholder="Nama Anda" value="{{ old('nama') }}" required autofocus>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold" style="font-size: 0.9rem;">No HP / Email</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light border-end-0"><i class="bi bi-envelope text-muted"></i></span>
                            <input type="text" name="email" class="form-control border-start-0" placeholder="081234... atau email@domain.com" value="{{ old('email') }}" required>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold" style="font-size: 0.9rem;">Password</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light border-end-0"><i class="bi bi-lock text-muted"></i></span>
                            <input type="password" name="password" class="form-control border-start-0" placeholder="Minimal 8 karakter" required>
                        </div>
                    </div>

                    <hr class="my-4">
                    <h6 class="fw-bold mb-3"><i class="bi bi-card-checklist text-danger me-1"></i> Data Seller</h6>

                    <div class="mb-3">
                        <label class="form-label fw-semibold" style="font-size: 0.9rem;">Nomor WhatsApp Aktif</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light border-end-0"><i class="bi bi-whatsapp text-muted"></i></span>
                            <input type="tel" name="NoHP" class="form-control border-start-0" placeholder="08xxxxxxxxxx" value="{{ old('NoHP') }}" required maxlength="20">
                        </div>
                        <div class="text-muted mt-1" style="font-size: 0.8rem;">Masukkan nomor yang dapat dihubungi jika diperlukan.</div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold" style="font-size: 0.9rem;">Alamat Domisili</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light border-end-0"><i class="bi bi-geo-alt text-muted"></i></span>
                            <input type="text" name="Alamat" class="form-control border-start-0" placeholder="Jalan, nomor, RT/RW" value="{{ old('Alamat') }}" required maxlength="150">
                        </div>
                        <div class="text-muted mt-1" style="font-size: 0.8rem;">Masukkan alamat tempat tinggalmu.</div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-semibold" style="font-size: 0.9rem;">Kota/Kabupaten</label>
                            <input type="text" name="Kota" class="form-control" value="{{ old('Kota') }}" required maxlength="60">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-semibold" style="font-size: 0.9rem;">Provinsi</label>
                            <input type="text" name="Provinsi" class="form-control" value="{{ old('Provinsi') }}" required maxlength="60">
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold" style="font-size: 0.9rem;">Alasan Ingin Menjadi Seller</label>
                        <textarea name="Alasan" rows="3" class="form-control" required minlength="10" maxlength="1000">{{ old('Alasan') }}</textarea>
                        <div class="text-muted mt-1" style="font-size: 0.8rem;">Ceritakan secara singkat mengapa kamu ingin menjual barang di SecondSpace.</div>
                    </div>

                    <div class="form-check mb-4 p-3 rounded-3 bg-light border" style="padding-left: 2.4rem !important;">
                        <input class="form-check-input" type="checkbox" name="setuju" id="setuju" value="1" {{ old('setuju') ? 'checked' : '' }}>
                        <label class="form-check-label" for="setuju" style="font-size: 0.88rem;">
                            Saya menyatakan bahwa informasi yang diberikan benar dan barang yang akan saya jual
                            merupakan barang yang boleh diperjualbelikan sesuai ketentuan SecondSpace.
                        </label>
                    </div>

                    <button type="submit" class="btn btn-danger w-100 py-2 fw-bold rounded-3 shadow-sm">
                        <i class="bi bi-send me-1"></i> Daftar Seller
                    </button>
                </form>

                <div class="text-center mt-4 pt-3 border-top" style="font-size: 0.9rem;">
                    <span class="text-muted">Hanya ingin berbelanja?</span>
                    <a href="{{ route('register') }}" class="text-danger fw-bold text-decoration-none ms-1">Daftar Pelanggan</a>
                    <div class="mt-2">
                        <span class="text-muted">Sudah punya akun?</span>
                        <a href="{{ route('login') }}" class="text-danger fw-bold text-decoration-none ms-1">Login disini</a>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>
@endsection