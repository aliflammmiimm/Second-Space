@extends('layouts.app')

@section('title', 'Login - SecondSpace')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-5 col-lg-4">
            
            <div class="card custom-card border-0 shadow-lg p-4">
                <div class="text-center mb-4">
                    <div class="d-inline-flex p-3 rounded-circle bg-danger bg-opacity-10 text-danger mb-3">
                        <i class="bi bi-person-fill-lock fs-2"></i>
                    </div>
                    <h3 class="fw-bold mb-1">Masuk Akun</h3>
                    <p class="text-muted" style="font-size: 0.9rem;">Gunakan Nomor HP / Email untuk login</p>
                </div>

                <form action="{{ route('login') }}" method="POST">
                    @csrf
                    
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

                    <button type="submit" class="btn btn-danger w-100 py-2 fw-bold rounded-3 shadow-sm">
                        <i class="bi bi-box-arrow-in-right me-1"></i> Masuk Sekarang
                    </button>
                </form>

                <div class="text-center mt-4 pt-3 border-top" style="font-size: 0.9rem;">
                    <span class="text-muted">Belum punya akun?</span>
                    <a href="{{ route('register') }}" class="text-danger fw-bold text-decoration-none ms-1">
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