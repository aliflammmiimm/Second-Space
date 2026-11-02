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
        <div class="card custom-card border-0 p-3 mb-3 {{ !empty($s->is_blocked) ? 'border-start border-4 border-danger' : '' }}">
            <div class="d-flex justify-content-between flex-wrap gap-3 align-items-center">
                <div>
                    <div class="d-flex align-items-center gap-2">
                        <span class="fw-bold fs-6">{{ $s->Nama }}</span>
                        <small class="text-muted">(ID #{{ $s->UserID }})</small>
                        @if(!empty($s->is_blocked))
                            <span class="badge bg-danger"><i class="bi bi-slash-circle me-1"></i> Diblokir</span>
                        @else
                            <span class="badge bg-success-subtle text-success border border-success-subtle"><i class="bi bi-check-circle me-1"></i> Aktif</span>
                        @endif
                    </div>
                    <div class="small text-muted mb-2">{{ $s->NoHP_Email }}</div>
                    <span class="badge bg-secondary">{{ $s->produk_count }} produk</span>
                    <span class="badge bg-success">{{ $s->produk_tersedia_count }} tersedia</span>
                    <span class="badge bg-dark">{{ $s->produk_terjual_count }} terjual</span>
                    <span class="badge bg-{{ $s->pesanan_aktif_count > 0 ? 'warning text-dark' : 'light text-muted border' }}">
                        {{ $s->pesanan_aktif_count }} pesanan aktif
                    </span>

                    @if(!empty($s->is_blocked))
                        <div class="small text-danger mt-2">
                            <i class="bi bi-exclamation-circle me-1"></i> <strong>Alasan Blokir:</strong> {{ $s->AlasanBlokir ?? 'Tunggakan komisi COD' }}
                            @if($s->DiblokirPada)
                                (sejak {{ $s->DiblokirPada->format('d M Y, H:i') }})
                            @endif
                        </div>
                    @endif
                </div>

                <div class="d-flex gap-2 align-items-center flex-wrap">
                    @if(!empty($s->is_blocked))
                        <form action="{{ route('admin.seller_aktif.buka_blokir', $s->UserID) }}" method="POST"
                              onsubmit="return confirm('Buka blokir toko {{ e($s->Nama) }}? Produk akan tampil kembali di katalog.');">
                            @csrf @method('PUT')
                            <button type="submit" class="btn btn-outline-success btn-sm rounded-pill px-3">
                                <i class="bi bi-unlock me-1"></i> Buka Blokir
                            </button>
                        </form>
                    @else
                        <!-- Tombol Buka Modal Blokir -->
                        <button type="button" class="btn btn-outline-warning text-dark btn-sm rounded-pill px-3" data-bs-toggle="modal" data-bs-target="#modalBlokir{{ $s->UserID }}">
                            <i class="bi bi-slash-circle me-1"></i> Blokir Toko
                        </button>

                        <!-- Modal Blokir Toko -->
                        <div class="modal fade" id="modalBlokir{{ $s->UserID }}" tabindex="-1" aria-hidden="true">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <form action="{{ route('admin.seller_aktif.blokir', $s->UserID) }}" method="POST">
                                        @csrf @method('PUT')
                                        <div class="modal-header">
                                            <h5 class="modal-title fw-bold">Blokir Toko {{ $s->Nama }}</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                        </div>
                                        <div class="modal-body">
                                            <p class="small text-muted mb-3">
                                                Toko yang diblokir tidak dapat menambah/mengubah produk dan seluruh produknya disembunyikan dari katalog publik.
                                            </p>
                                            <div class="mb-3">
                                                <label class="form-label fw-bold">Alasan Pemblokiran</label>
                                                <textarea name="alasan" class="form-control" rows="3" placeholder="Contoh: Tunggakan komisi COD, barang tidak sesuai, pelanggaran aturan, dll." required></textarea>
                                            </div>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary rounded-pill" data-bs-dismiss="modal">Batal</button>
                                            <button type="submit" class="btn btn-danger rounded-pill px-4">Blokir Toko</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    @endif

                    <form action="{{ route('admin.seller_aktif.destroy', $s->UserID) }}" method="POST"
                          onsubmit="return confirm('Hapus akun seller {{ e($s->Nama) }}?\n\n- Produk yang belum pernah dipesan akan dihapus.\n- Produk yang punya riwayat pesanan akan disembunyikan.\n- Akun tidak bisa dikembalikan.');">
                        @csrf @method('DELETE')
                        <button class="btn btn-outline-danger btn-sm rounded-pill px-3">
                            <i class="bi bi-trash me-1"></i> Hapus Akun
                        </button>
                    </form>
                </div>
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