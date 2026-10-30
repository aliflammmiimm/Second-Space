@extends('layouts.app')

@section('title', 'Login - SecondSpace')

@push('styles')
<style>
    .role-card {
        background: #ffffff;
        border: 1px solid rgba(0, 92, 102, 0.15) !important;
        transition: all 0.2s ease;
    }
    .role-card:hover {
        border-color: #00B8BE !important;
        background: #f4f9fa;
    }
    .role-card:has(input:checked) {
        border-color: #00B8BE !important;
        background: rgba(0, 184, 190, 0.08) !important;
        box-shadow: 0 4px 12px rgba(0, 184, 190, 0.15);
    }
</style>
@endpush

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-5 col-lg-4">
            
            <div class="card custom-card border-0 shadow-lg p-4 p-md-5">
                <div class="text-center mb-4">
                    <div class="d-inline-flex p-3 rounded-circle mb-3" style="background: rgba(0, 184, 190, 0.15);">
                        <i class="bi bi-person-fill-lock fs-2" style="color: #005C66;"></i>
                    </div>
                    <h3 class="fw-bold mb-1" style="color: #001B27;">Masuk Akun</h3>
                    <p class="text-muted" style="font-size: 0.88rem;">Pilih peran dan gunakan Nomor HP / Email untuk login</p>
                </div>

                <form action="{{ route('login') }}" method="POST">
                    @csrf

                    <!-- RADIO BUTTON PILIHAN PERAN (PELANGGAN / SELLER) -->
                    <div class="mb-4">
                        <label class="form-label fw-bold small text-muted text-uppercase mb-2 d-block">
                            <i class="bi bi-person-badge me-1" style="color: #00B8BE;"></i> Masuk Sebagai:
                        </label>
                        <div class="row g-2">
                            <div class="col-6">
                                <label class="role-card p-3 rounded-3 d-flex align-items-center gap-2 cursor-pointer w-100" style="cursor: pointer;">
                                    <input type="radio" name="role" value="pelanggan" class="form-check-input mt-0" {{ old('role', 'pelanggan') === 'pelanggan' ? 'checked' : '' }} required>
                                    <div>
                                        <div class="fw-bold small" style="color: #001B27;"><i class="bi bi-bag-heart me-1" style="color: #00B8BE;"></i> Pelanggan</div>
                                        <small class="text-muted" style="font-size: 0.72rem;">Belanja thrift</small>
                                    </div>
                                </label>
                            </div>
                            <div class="col-6">
                                <label class="role-card p-3 rounded-3 d-flex align-items-center gap-2 cursor-pointer w-100" style="cursor: pointer;">
                                    <input type="radio" name="role" value="seller" class="form-check-input mt-0" {{ old('role') === 'seller' ? 'checked' : '' }}>
                                    <div>
                                        <div class="fw-bold small" style="color: #001B27;"><i class="bi bi-shop me-1" style="color: #005C66;"></i> Seller</div>
                                        <small class="text-muted" style="font-size: 0.72rem;">Kelola toko</small>
                                    </div>
                                </label>
                            </div>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label fw-semibold" style="font-size: 0.9rem;">No HP / Email</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light border-end-0"><i class="bi bi-person text-muted"></i></span>
                            <input type="text" name="email" class="form-control border-start-0" placeholder="contoh: 08123... atau email" value="{{ old('email') }}" required autofocus>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold" style="font-size: 0.9rem;">Password</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light border-end-0"><i class="bi bi-key text-muted"></i></span>
                            <input type="password" name="password" id="passwordInput" class="form-control border-start-0 border-end-0" placeholder="Password akun" required>
                            <button class="btn btn-outline-secondary border-start-0 bg-light" type="button" onclick="togglePass()">
                                <i class="bi bi-eye" id="eyeIcon"></i>
                            </button>
                        </div>
                    </div>

                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="remember" id="rememberMe">
                            <label class="form-check-label text-muted" for="rememberMe" style="font-size: 0.85rem;">
                                Ingat saya
                            </label>
                        </div>
                    </div>

                    <button type="submit" class="btn btn-danger w-100 py-3 fw-bold rounded-3 shadow-sm">
                        <i class="bi bi-box-arrow-in-right me-1"></i> Masuk Sekarang
                    </button>
                </form>

                <div class="text-center mt-4 pt-3 border-top" style="font-size: 0.88rem;">
                    <span class="text-muted">Belum punya akun?</span>
                    <a href="{{ route('register') }}" class="fw-bold text-decoration-none ms-1" style="color: #005C66;">
                        Daftar disini
                    </a>
                </div>
            </div>

        </div>
    </div>
</div>

<script>
    function togglePass() {
        const pass = document.getElementById("passwordInput");
        const eye = document.getElementById("eyeIcon");
        if (pass.type === "password") {
            pass.type = "text";
            eye.classList.replace("bi-eye", "bi-eye-slash");
        } else {
            pass.type = "password";
            eye.classList.replace("bi-eye-slash", "bi-eye");
        }
    }
</script>
@endsection