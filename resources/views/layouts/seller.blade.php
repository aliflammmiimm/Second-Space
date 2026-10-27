@extends('layouts.app')

@push('styles')
<style>
    .seller-side { position: sticky; top: 90px; }
    .seller-avatar { width: 46px; height: 46px; border-radius: 14px; background: #e10600; color: #fff;
        display: flex; align-items: center; justify-content: center; font-weight: 800; font-size: 1.2rem; flex-shrink: 0; }
    .seller-menu a { display: flex; align-items: center; gap: .65rem; padding: .65rem .9rem; border-radius: 12px;
        color: #334155; text-decoration: none; font-weight: 600; font-size: .95rem; }
    .seller-menu a:hover { background: #f1f5f9; }
    .seller-menu a.active { background: #e10600; color: #fff; }
    .seller-stat { border-radius: 16px; border: 1px solid #e5e7eb; background: #fff; padding: 16px 18px; height: 100%; }
    .seller-stat .angka { font-size: 1.6rem; font-weight: 800; line-height: 1.1; }
    .seller-stat .label { font-size: .8rem; color: #64748b; font-weight: 600; text-transform: uppercase; letter-spacing: .4px; }
</style>
@endpush

@section('content')
@php($u = Auth::user())
<div class="container py-4">
    <div class="row g-4">
        <aside class="col-lg-3">
            <div class="card custom-card border-0 p-3 seller-side">
                <div class="d-flex align-items-center gap-3 pb-3 mb-3 border-bottom">
                    <div class="seller-avatar">{{ strtoupper(mb_substr($u->Nama, 0, 1)) }}</div>
                    <div class="overflow-hidden">
                        <div class="fw-bold text-truncate">{{ $u->Nama }}</div>
                        <span class="badge bg-danger">Seller</span>
                    </div>
                </div>

                <nav class="seller-menu d-grid gap-1">
                    <a href="{{ route('seller.produk.index') }}"
                       class="{{ request()->routeIs('seller.produk.index', 'seller.produk.edit') ? 'active' : '' }}">
                        <i class="bi bi-speedometer2"></i> Dashboard
                    </a>
                    <a href="{{ route('seller.produk.create') }}"
                       class="{{ request()->routeIs('seller.produk.create') ? 'active' : '' }}">
                        <i class="bi bi-plus-circle"></i> Tambah Produk
                    </a>
                    <a href="{{ route('seller.pesanan.index') }}"
                       class="{{ request()->routeIs('seller.pesanan.*') ? 'active' : '' }}">
                        <i class="bi bi-inbox"></i> Pesanan Masuk
                    </a>
                    <a href="{{ route('seller.pencairan.index') }}"
                       class="{{ request()->routeIs('seller.pencairan.*') ? 'active' : '' }}">
                        <i class="bi bi-wallet2"></i> Saldo &amp; Pencairan
                    </a>

                    <hr class="my-2">

                    <a href="{{ route('pelanggan.katalog') }}"><i class="bi bi-grid"></i> Lihat Katalog</a>
                    <a href="{{ route('pelanggan.pesanan_aktif') }}"><i class="bi bi-bag"></i> Belanja Saya</a>
                </nav>
            </div>
        </aside>

        <section class="col-lg-9">
            @yield('seller_content')
        </section>
    </div>
</div>
@endsection