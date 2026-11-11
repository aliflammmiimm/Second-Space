@extends('layouts.app')

@push('styles')
<style>
    .seller-side { position: sticky; top: 90px; }
    .seller-avatar { 
        width: 46px; 
        height: 46px; 
        border-radius: 14px; 
        background: linear-gradient(135deg, #00B8BE, #005C66); 
        color: #001B27;
        display: flex; 
        align-items: center; 
        justify-content: center; 
        font-weight: 800; 
        font-size: 1.2rem; 
        flex-shrink: 0; 
        box-shadow: 0 4px 14px rgba(0, 184, 190, 0.35);
    }
    .seller-menu a { 
        display: flex; 
        align-items: center; 
        gap: .65rem; 
        padding: .7rem .95rem; 
        border-radius: 12px;
        color: #001B27; 
        text-decoration: none; 
        font-weight: 600; 
        font-size: .95rem; 
        transition: all 0.2s ease;
    }
    .seller-menu a:hover { 
        background: #eaf6f7; 
        color: #005C66; 
    }
    .seller-menu a.active { 
        background: #001B27; 
        color: #00B8BE; 
        border-left: 4px solid #00B8BE;
        box-shadow: 0 4px 14px rgba(0, 27, 39, 0.15);
    }
    .seller-stat { 
        border-radius: 16px; 
        border: 1px solid rgba(0, 92, 102, 0.15); 
        background: #fff; 
        padding: 16px 18px; 
        height: 100%; 
        box-shadow: 0 4px 16px rgba(0, 27, 39, 0.03);
    }
    .seller-stat .angka { 
        font-size: 1.6rem; 
        font-weight: 800; 
        line-height: 1.1; 
        color: #005C66;
    }
    .seller-stat .label { 
        font-size: .8rem; 
        color: #53707c; 
        font-weight: 600; 
        text-transform: uppercase; 
        letter-spacing: .4px; 
    }
</style>
@endpush

@section('content')
@php($u = Auth::guard('seller')->user() ?? Auth::user())
<div class="container py-4">
    <div class="row g-4">
        <aside class="col-lg-3">
            <div class="card custom-card border-0 p-3 seller-side">
                <div class="d-flex align-items-center gap-3 pb-3 mb-3 border-bottom">
                    <div class="seller-avatar">{{ strtoupper(mb_substr($u->Nama ?? 'S', 0, 1)) }}</div>
                    <div class="overflow-hidden">
                        <div class="fw-bold text-truncate">{{ $u->Nama ?? 'Seller' }}</div>
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
                    <a href="{{ route('seller.komisi.index') }}"
                       class="{{ request()->routeIs('seller.komisi.*') ? 'active' : '' }}">
                        <i class="bi bi-receipt-cutoff"></i> Komisi COD
                    </a>
                    <a href="{{ route('seller.chat.index') }}"
                       class="{{ request()->routeIs('seller.chat.*') ? 'active' : '' }}">
                        <i class="bi bi-chat-dots"></i> Pesan Pembeli
                    </a>
                    <a href="{{ route('seller.pengaturan.index') }}"
                       class="{{ request()->routeIs('seller.pengaturan.*') ? 'active' : '' }}">
                        <i class="bi bi-gear"></i> Pengaturan Toko
                    </a>

                    <hr class="my-2">

                    <a href="{{ route('pelanggan.katalog') }}"><i class="bi bi-grid"></i> Lihat Katalog</a>
                    <a href="{{ route('pelanggan.pesanan_aktif') }}"><i class="bi bi-bag"></i> Belanja Saya</a>

                    <form action="{{ route('seller.logout') }}" method="POST" class="mt-2">
                        @csrf
                        <button type="submit" class="btn btn-outline-danger btn-sm w-100 rounded-pill py-2">
                            <i class="bi bi-box-arrow-right me-1"></i> Logout Seller
                        </button>
                    </form>
                </nav>
            </div>
        </aside>

        <section class="col-lg-9">
            @if(!empty($u->is_blocked))
                <div class="alert alert-danger border-0 shadow-sm rounded-4 p-4 mb-4" style="background: rgba(225, 29, 72, 0.1); border-left: 5px solid #e11d48 !important;">
                    <div class="d-flex align-items-start gap-3">
                        <i class="bi bi-shield-slash-fill fs-2 text-danger"></i>
                        <div>
                            <h5 class="fw-bold mb-1 text-danger">Toko Anda Sedang Diblokir</h5>
                            <p class="mb-2 text-dark small">{{ $u->AlasanBlokir ?? 'Toko Anda ditangguhkan karena tunggakan komisi COD melewati batas waktu 4 hari atau tindakan penertiban admin.' }}</p>
                            <div class="small text-muted">
                                <strong>Dampak pemblokiran:</strong> Produk Anda disembunyikan dari katalog publik dan Anda tidak dapat menambah/mengubah produk.
                            </div>
                            <div class="mt-3">
                                <a href="{{ route('seller.komisi.index') }}" class="btn btn-danger btn-sm rounded-pill px-3 fw-semibold">
                                    <i class="bi bi-receipt-cutoff me-1"></i> Buka Menu Komisi COD &amp; Selesaikan Tagihan
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            @endif

            @yield('seller_content')
        </section>
    </div>
</div>
@endsection