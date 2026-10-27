@extends('layouts.app')
@section('title', 'Daftar Seller - Admin')

@section('content')
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
        <h2 class="fw-bold mb-0"><i class="bi bi-shop text-danger me-2"></i> Daftar Seller</h2>
        <div class="d-flex gap-2">
            <a href="{{ route('admin.seller.index') }}" class="btn btn-outline-dark rounded-pill btn-sm">Pengajuan Seller</a>
            <a href="{{ route('admin.dashboard') }}" class="btn btn-outline-secondary rounded-pill btn-sm">Dashboard</a>
        </div>
    </div>

    <form method="GET" class="mb-3 d-flex gap-2">
        <input type="text" name="search" value="{{ request('search') }}" class="form-control rounded-pill" placeholder="Cari nama / kontak seller...">
        <button class="btn btn-dark rounded-pill px-4">Cari</button>
    </form>

    @forelse($sellers as $s)
        <div class="card custom-card border-0 p-3 mb-3">
            <div class="d-flex justify-content-between flex-wrap gap-3 align-items-center">
                <div>
                    <div class="fw-bold">{{ $s->Nama }} <small class="text-muted">(ID #{{ $s->UserID }})</small></div>
                    <div class="small text-muted mb-2">{{ $s->NoHP_Email }}</div>
                    <span class="badge bg-secondary">{{ $s->produk_count }} produk</span>
                    <span class="badge bg-success">{{ $s->produk_tersedia_count }} tersedia</span>
                    <span class="badge bg-dark">{{ $s->produk_terjual_count }} terjual</span>
                    <span class="badge bg-{{ $s->pesanan_aktif_count > 0 ? 'warning text-dark' : 'light text-muted border' }}">
                        {{ $s->pesanan_aktif_count }} pesanan aktif
                    </span>
                </div>
                <form action="{{ route('admin.seller_aktif.destroy', $s->UserID) }}" method="POST"
                      onsubmit="return confirm('Hapus akun seller {{ e($s->Nama) }}?\n\n- Produk yang belum pernah dipesan akan dihapus.\n- Produk yang punya riwayat pesanan akan disembunyikan.\n- Akun tidak bisa dikembalikan.');">
                    @csrf @method('DELETE')
                    <button class="btn btn-outline-danger btn-sm rounded-pill px-3">
                        <i class="bi bi-trash me-1"></i> Hapus Akun
                    </button>
                </form>
            </div>
            @if($s->pesanan_aktif_count > 0)
                <div class="small text-warning-emphasis mt-2">
                    Seller ini masih punya pesanan aktif, penghapusan akan ditolak sampai pesanan selesai atau dibatalkan.
                </div>
            @endif
        </div>
    @empty
        <div class="text-center text-muted p-5 bg-white rounded-4 border">Belum ada seller.</div>
    @endforelse

    {{ $sellers->links() }}
</div>
@endsection