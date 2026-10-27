@extends('layouts.app')

@section('title', 'Pengguna - Admin SecondSpace')

@section('content')
<div class="container py-4">
    <div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-3">
        <h2 class="fw-bold mb-0"><i class="bi bi-people text-danger me-2"></i> Pengguna</h2>
        <a href="{{ route('admin.dashboard') }}" class="btn btn-outline-secondary rounded-pill">
            <i class="bi bi-arrow-left me-1"></i> Kembali ke Dashboard
        </a>
    </div>

    <form method="GET" action="{{ route('admin.pengguna.index') }}" class="row g-2 mb-4">
        <div class="col-md-4">
            <select name="tipe" class="form-select">
                <option value="">Semua pengguna</option>
                <option value="buyer" {{ $tipe === 'buyer' ? 'selected' : '' }}>Buyer</option>
                <option value="seller" {{ $tipe === 'seller' ? 'selected' : '' }}>Seller</option>
                <option value="admin" {{ $tipe === 'admin' ? 'selected' : '' }}>Admin</option>
            </select>
        </div>
        <div class="col-md-5">
            <input type="text" name="search" value="{{ request('search') }}" class="form-control" placeholder="Cari nama / nomor HP / email">
        </div>
        <div class="col-md-3">
            <button class="btn btn-danger w-100 rounded-pill">Filter</button>
        </div>
    </form>

    <div class="card custom-card border-0 p-3">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>ID</th>
                        <th>Nama</th>
                        <th>Kontak</th>
                        <th>Peran</th>
                        <th class="text-center">Produk</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($users as $u)
                        <tr>
                            <td class="fw-bold">#{{ $u->UserID }}</td>
                            <td>{{ $u->Nama }}</td>
                            <td>{{ $u->NoHP_Email }}</td>
                            <td>
                                @if(strtolower($u->Role) === 'admin')
                                    <span class="badge bg-dark">Admin</span>
                                @elseif($u->is_seller)
                                    <span class="badge bg-success">Seller</span>
                                @else
                                    <span class="badge bg-primary">Buyer</span>
                                @endif
                            </td>
                            <td class="text-center">{{ $u->produk_count }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center text-muted py-4">Tidak ada pengguna yang sesuai.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="mt-3">{{ $users->links() }}</div>
    </div>
</div>
@endsection