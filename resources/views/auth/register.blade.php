@extends('layouts.app')

@section('title', 'Daftar Pelanggan - SecondSpace')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-5 col-lg-4">

            <div class="card custom-card border-0 shadow-lg p-4">
                <div class="text-center mb-4">
                    <div class="d-inline-flex p-3 rounded-circle mb-3" style="background: rgba(0, 184, 190, 0.15);">
                        <i class="bi bi-person-plus-fill fs-2" style="color: #005C66;"></i>
                    </div>
                    <h3 class="fw-bold mb-1" style="color: #001B27;">Daftar Pelanggan</h3>
                    <p class="text-muted" style="font-size: 0.9rem;">Buat akun untuk berbelanja koleksi thrift</p>
                </div>

                <form action="{{ route('register') }}" method="POST">
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

                    <div class="mb-4">
                        <label class="form-label fw-semibold" style="font-size: 0.9rem;">Password</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light border-end-0"><i class="bi bi-lock text-muted"></i></span>
                            <input type="password" name="password" class="form-control border-start-0" placeholder="Minimal 8 karakter" required>
                        </div>
                    </div>

                    <button type="submit" class="btn btn-danger w-100 py-2 fw-bold rounded-3 shadow-sm">
                        <i class="bi bi-check-circle me-1"></i> Buat Akun Pelanggan
                    </button>
                </form>

                <div class="text-center mt-4 pt-3 border-top" style="font-size: 0.9rem;">
                    <span class="text-muted">Sudah punya akun?</span>
                    <a href="{{ route('login') }}" class="fw-bold text-decoration-none ms-1" style="color: #005C66;">
                        Login disini
                    </a>

                    <div class="mt-3">
                        <a href="{{ route('register.seller') }}" class="btn btn-dark w-100 py-2 fw-bold rounded-3">
                            <i class="bi bi-shop me-1" style="color: #00B8BE;"></i> Daftar Seller
                        </a>
                        <div class="text-muted mt-1" style="font-size: 0.78rem;">Ingin menjual barang? Daftar sebagai Seller.</div>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>
@endsection