@extends('layouts.seller')
@section('title', 'Dashboard Seller - SecondSpace')

@section('seller_content')
@php($badge = ['Tersedia' => 'success', 'Dipesan' => 'warning text-dark', 'Terjual' => 'dark', 'Disembunyikan' => 'secondary'])
<div>
    <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
        <h4 class="fw-bold mb-0"><i class="bi bi-speedometer2 text-danger me-2"></i> Dashboard Seller</h4>
        <a href="{{ route('seller.produk.create') }}" class="btn btn-danger rounded-pill fw-semibold px-4">
            <i class="bi bi-plus-lg me-1"></i> Tambah Produk
        </a>
    </div>

    {{-- RINGKASAN --}}
    <div class="row g-3 mb-4">
        <div class="col-6 col-md-4"><div class="seller-stat"><div class="label">Tersedia</div><div class="angka text-success">{{ $ringkasan['tersedia'] }}</div></div></div>
        <div class="col-6 col-md-4"><div class="seller-stat"><div class="label">Sedang Dipesan</div><div class="angka text-warning">{{ $ringkasan['dipesan'] }}</div></div></div>
        <div class="col-6 col-md-4"><div class="seller-stat"><div class="label">Terjual</div><div class="angka">{{ $ringkasan['terjual'] }}</div></div></div>
        <div class="col-6 col-md-4"><div class="seller-stat"><div class="label">Disembunyikan</div><div class="angka text-secondary">{{ $ringkasan['disembunyikan'] }}</div></div></div>
        <div class="col-6 col-md-4">
            <a href="{{ route('seller.pesanan.index') }}" class="text-decoration-none text-reset">
                <div class="seller-stat"><div class="label">Pesanan Aktif</div><div class="angka text-danger">{{ $pesananAktif }}</div></div>
            </a>
        </div>
        <div class="col-6 col-md-4"><div class="seller-stat"><div class="label">Penjualan Kotor (Selesai)</div><div class="angka" style="font-size:1.25rem;">Rp {{ number_format($penjualanKotor, 0, ',', '.') }}</div></div></div>
        <div class="col-6 col-md-4"><div class="seller-stat"><div class="label">Komisi Website ({{ config('santara.komisi_persen') }}%)</div><div class="angka text-danger" style="font-size:1.25rem;">- Rp {{ number_format($totalKomisi, 0, ',', '.') }}</div></div></div>
        <div class="col-6 col-md-4"><div class="seller-stat"><div class="label">Pendapatan Bersih Anda</div><div class="angka text-success" style="font-size:1.25rem;">Rp {{ number_format($pendapatan, 0, ',', '.') }}</div></div></div>
    </div>

    {{-- DAFTAR PRODUK --}}
    <div class="card custom-card border-0 p-3">
        <h6 class="fw-bold mb-3">Produk Saya</h6>
        <div class="table-responsive">
            <table class="table align-middle mb-0">
                <thead class="table-light">
                    <tr><th>Produk</th><th>Kategori</th><th>Kondisi</th><th>Harga</th><th>Status</th><th class="text-end">Aksi</th></tr>
                </thead>
                <tbody>
                @forelse($produk as $p)
                    <tr>
                        <td>
                            <div class="d-flex align-items-center gap-2">
                                <img src="{{ $p->gambar_url }}" class="rounded-3" style="width:52px;height:52px;object-fit:cover;" alt="">
                                <div class="fw-semibold">{{ $p->NamaProduk }}</div>
                            </div>
                        </td>
                        <td>{{ $p->jenisProduk->NamaJenis ?? '-' }}</td>
                        <td>{{ \App\Models\Produk::KONDISI[$p->Kondisi] ?? $p->Kondisi }}</td>
                        <td>Rp {{ number_format($p->Harga, 0, ',', '.') }}</td>
                        <td><span class="badge bg-{{ $badge[$p->Status] ?? 'secondary' }}">{{ $p->Status }}</span></td>
                        <td class="text-end">
                            @if($p->Status === 'Tersedia')
                                <div class="d-inline-flex gap-1">
                                    <a href="{{ route('seller.produk.edit', $p->ProdukID) }}" class="btn btn-sm btn-outline-dark rounded-pill">
                                        <i class="bi bi-pencil"></i> Edit
                                    </a>
                                    <form action="{{ route('seller.produk.tarik', $p->ProdukID) }}" method="POST"
                                          onsubmit="return confirm('Tarik produk ini dari katalog?');">
                                        @csrf @method('DELETE')
                                        <button class="btn btn-sm btn-outline-danger rounded-pill"><i class="bi bi-box-arrow-down"></i> Tarik</button>
                                    </form>
                                </div>
                            @else
                                <span class="text-muted small">&mdash;</span>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="text-center text-muted py-5">
                            Belum ada produk.
                            <a href="{{ route('seller.produk.create') }}" class="fw-semibold">Tambah produk pertama Anda</a>
                        </td>
                    </tr>
                @endforelse
                </tbody>
            </table>
        </div>
        <div class="mt-3">{{ $produk->links() }}</div>
    </div>
</div>
@endsection