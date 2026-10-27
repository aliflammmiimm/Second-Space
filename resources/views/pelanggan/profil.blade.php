@extends('layouts.app')

@section('title', 'Profil Saya - SecondSpace')

@section('content')
<div class="container py-4" style="max-width: 640px;">
    <h2 class="fw-bold mb-4"><i class="bi bi-person-circle text-danger me-2"></i> Profil Saya</h2>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <form action="{{ route('pelanggan.profil.update') }}" method="POST" class="card custom-card border-0 p-4">
        @csrf
        @method('PUT')

        <div class="mb-3">
            <label class="form-label fw-bold">Nama</label>
            <input type="text" name="Nama" value="{{ old('Nama', $user->Nama) }}" class="form-control @error('Nama') is-invalid @enderror" required>
            @error('Nama')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>

        <div class="mb-3">
            <label class="form-label fw-bold">Nomor HP / Email</label>
            <input type="text" name="NoHP_Email" value="{{ old('NoHP_Email', $user->NoHP_Email) }}" class="form-control @error('NoHP_Email') is-invalid @enderror" required>
            @error('NoHP_Email')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>

        <hr>
        <p class="text-muted small">Isi bagian ini hanya jika ingin mengganti password.</p>

        <div class="mb-3">
            <label class="form-label fw-bold">Password saat ini</label>
            <input type="password" name="current_password" class="form-control @error('current_password') is-invalid @enderror" autocomplete="current-password">
            @error('current_password')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>

        <div class="mb-3">
            <label class="form-label fw-bold">Password baru (min. 8 karakter)</label>
            <input type="password" name="password" class="form-control @error('password') is-invalid @enderror" autocomplete="new-password">
            @error('password')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>

        <div class="mb-4">
            <label class="form-label fw-bold">Ulangi password baru</label>
            <input type="password" name="password_confirmation" class="form-control" autocomplete="new-password">
        </div>

        <button type="submit" class="btn btn-danger rounded-pill px-4 fw-semibold">Simpan Perubahan</button>
    </form>
</div>
@endsection