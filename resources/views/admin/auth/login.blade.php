@extends('layouts.app')

@section('title', 'Login Administrator - SecondSpace')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-5 col-lg-4">
            
            <div class="card custom-card border-0 shadow-lg p-4 p-md-5">
                <div class="text-center mb-4">
                    <div class="d-inline-flex p-3 rounded-circle mb-3" style="background: rgba(0, 184, 190, 0.15);">
                        <i class="bi bi-shield-lock-fill fs-2" style="color: #005C66;"></i>
                    </div>
                    <div>
                        <span class="badge mb-2 px-3 py-1" style="background: #001B27; color: #00B8BE; border: 1px solid rgba(0, 184, 190, 0.3); font-size: 0.72rem;">
                            <i class="bi bi-shield-check me-1"></i> Admin Portal
                        </span>
                    </div>
                    <h3 class="fw-bold mb-1" style="color: #001B27;">Login Admin</h3>
                    <p class="text-muted" style="font-size: 0.88rem;">Panel kontrol dan manajemen sistem SecondSpace</p>
                </div>

                <form action="{{ route('admin.login') }}" method="POST">
                    @csrf
                    
                    <div class="mb-3">
                        <label class="form-label fw-semibold" style="font-size: 0.9rem;">Email / No HP Admin</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light border-end-0"><i class="bi bi-person-badge text-muted"></i></span>
                            <input type="text" name="email" class="form-control border-start-0" placeholder="admin@secondspace.test atau No HP" value="{{ old('email') }}" required autofocus>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold" style="font-size: 0.9rem;">Password Admin</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light border-end-0"><i class="bi bi-key text-muted"></i></span>
                            <input type="password" name="password" id="adminPasswordInput" class="form-control border-start-0 border-end-0" placeholder="Password administrator" required>
                            <button class="btn btn-outline-secondary border-start-0 bg-light" type="button" onclick="toggleAdminPass()">
                                <i class="bi bi-eye" id="adminEyeIcon"></i>
                            </button>
                        </div>
                    </div>

                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="remember" id="rememberAdmin">
                            <label class="form-check-label text-muted" for="rememberAdmin" style="font-size: 0.85rem;">
                                Ingat saya di perangkat ini
                            </label>
                        </div>
                    </div>

                    <button type="submit" class="btn btn-dark w-100 py-3 fw-bold rounded-3 shadow-sm" style="background: #001B27; border-color: #005C66; color: #fff;">
                        <i class="bi bi-box-arrow-in-right me-1" style="color: #00B8BE;"></i> Masuk ke Panel Admin
                    </button>
                </form>

                <div class="text-center mt-4 pt-3 border-top" style="font-size: 0.85rem;">
                    <a href="{{ route('home') }}" class="text-decoration-none" style="color: #53707c;">
                        <i class="bi bi-arrow-left me-1"></i> Kembali ke Beranda SecondSpace
                    </a>
                </div>
            </div>

        </div>
    </div>
</div>

<script>
    function toggleAdminPass() {
        const pass = document.getElementById("adminPasswordInput");
        const eye = document.getElementById("adminEyeIcon");
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