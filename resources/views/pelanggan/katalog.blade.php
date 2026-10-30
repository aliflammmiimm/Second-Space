@extends('layouts.app')

@section('title', 'Katalog Produk - SecondSpace')

@push('styles')
<style>
    .filter-wrapper {
        background: #ffffff;
        border-radius: 16px;
        padding: 18px 24px;
        box-shadow: 0 4px 20px rgba(0, 27, 39, 0.03);
        border: 1px solid rgba(0, 92, 102, 0.12);
    }

    .category-pill {
        display: inline-block;
        padding: 8px 18px;
        border-radius: 50px;
        background: #eaf6f7;
        color: #005C66;
        text-decoration: none;
        font-size: 0.9rem;
        font-weight: 600;
        transition: all 0.2s ease;
        border: 1px solid rgba(0, 92, 102, 0.15);
    }

    .category-pill:hover, .category-pill.active {
        background: #001B27;
        color: #00B8BE;
        border-color: #005C66;
        box-shadow: 0 4px 12px rgba(0, 27, 39, 0.25);
    }

    .product-box {
        background: #ffffff;
        border-radius: 16px;
        border: 1px solid rgba(0, 92, 102, 0.12);
        overflow: hidden;
        transition: all 0.3s ease;
        display: flex;
        flex-direction: column;
        height: 100%;
        box-shadow: 0 4px 15px rgba(0, 27, 39, 0.03);
    }

    .product-box:hover {
        transform: translateY(-6px);
        box-shadow: 0 16px 35px rgba(0, 27, 39, 0.08);
        border-color: rgba(0, 184, 190, 0.5);
    }

    .product-thumb-wrapper {
        position: relative;
        width: 100%;
        height: 250px;
        background: #f4f9fa;
        overflow: hidden;
    }

    .product-thumb {
        width: 100%;
        height: 100%;
        object-fit: cover;
        object-position: center;
        transition: transform 0.4s ease;
    }

    .product-box:hover .product-thumb {
        transform: scale(1.05);
    }

    .product-title {
        font-weight: 700;
        font-size: 1.05rem;
        color: #001B27;
        margin-bottom: 6px;
        line-height: 1.4;
        min-height: 44px;
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }

    .product-desc {
        color: #53707c;
        font-size: 0.85rem;
        line-height: 1.4;
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
        min-height: 38px;
    }

    .product-price {
        font-weight: 800;
        font-size: 1.25rem;
        color: #005C66;
    }

    .badge-stock {
        font-size: 0.75rem;
        padding: 5px 10px;
        border-radius: 20px;
        font-weight: 600;
    }
</style>
@endpush

@section('content')
<div class="container py-4">

    <!-- HEADER, SEARCH BAR & QUICK LINK -->
    <div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-3">
        <div>
            <h2 class="fw-bold mb-1"><i class="bi bi-grid text-danger me-2"></i> Katalog Produk SecondSpace</h2>
            <p class="text-muted mb-0">Temukan barang pre-loved & thrift berkualitas terkurasi (1 Pcs Per Produk)</p>
        </div>

        <div class="d-flex gap-2 flex-wrap align-items-center">
            @auth
                <a href="{{ route('pelanggan.pesanan_aktif') }}" class="btn btn-outline-dark rounded-pill px-3 shadow-sm">
                    <i class="bi bi-clock-history me-1 text-danger"></i> Status Pesanan Aktif Saya
                </a>
            @endauth

            <form action="{{ route('pelanggan.katalog') }}" method="GET" class="d-flex" style="max-width: 380px; width: 100%;">
                @if(request('jenis'))
                    <input type="hidden" name="jenis" value="{{ request('jenis') }}">
                @endif
                <div class="input-group shadow-sm rounded-pill overflow-hidden border">
                    <input type="text" name="search" class="form-control border-0 ps-3" placeholder="Cari nama produk / brand..." value="{{ request('search') }}">
                    <button class="btn btn-dark px-4" type="submit">
                        <i class="bi bi-search"></i>
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- FILTER KATEGORI -->
    <div class="filter-wrapper mb-4">
        <div class="d-flex align-items-center gap-2 flex-wrap">
            <span class="fw-bold text-muted me-2" style="font-size: 0.9rem;">
                <i class="bi bi-funnel me-1"></i> Kategori:
            </span>
            
            <a href="{{ route('pelanggan.katalog', array_merge(request()->except('jenis', 'page'))) }}" 
               class="category-pill {{ !request('jenis') ? 'active' : '' }}">
                Semua Produk
            </a>

            @foreach($categories as $cat)
                <a href="{{ route('pelanggan.katalog', array_merge(request()->except('page'), ['jenis' => $cat->NamaJenis])) }}" 
                   class="category-pill {{ request('jenis') == $cat->NamaJenis ? 'active' : '' }}">
                    @if($cat->NamaJenis == 'Elektronik')
                        <i class="bi bi-laptop me-1"></i>
                    @elseif($cat->NamaJenis == 'Fashion')
                        <i class="bi bi-bag-heart me-1"></i>
                    @elseif($cat->NamaJenis == 'Peralatan Rumah Tangga')
                        <i class="bi bi-house-door me-1"></i>
                    @elseif($cat->NamaJenis == 'ATK')
                        <i class="bi bi-pencil-square me-1"></i>
                    @endif
                    {{ $cat->NamaJenis }}
                </a>
            @endforeach
        </div>
    </div>

    <!-- GRID PRODUK -->
    <div class="row g-4 mb-5">
        @forelse($produk as $p)
            <div class="col-xl-3 col-lg-4 col-md-6 col-sm-6">
                <div class="product-box">
                    <div class="product-thumb-wrapper">
                        <img src="{{ $p->gambar_url }}" alt="{{ $p->NamaProduk }}" class="product-thumb" 
                             loading="lazy" onerror="this.onerror=null; this.src='{{ asset('upload/default.jpg') }}';">
                        
                        <div class="position-absolute top-0 start-0 m-2 d-flex gap-1 flex-column">
                            @if($p->jenisProduk)
                                <span class="badge shadow-sm" style="background: rgba(0, 27, 39, 0.88); color: #fff;">{{ $p->jenisProduk->NamaJenis }}</span>
                            @endif
                        </div>
                    </div>

                    <div class="p-3 d-flex flex-column flex-grow-1 justify-content-between">
                        <div>
                            <div class="d-flex justify-content-between align-items-center text-muted small mb-1">
                                <span class="fw-semibold">{{ $p->Brand ?? ($p->jenisProduk ? $p->jenisProduk->NamaJenis : 'SecondSpace') }}</span>
                                @if($p->Ukuran)
                                    <span class="badge bg-light text-secondary border">{{ $p->Ukuran }}</span>
                                @endif
                            </div>
                            
                            <h5 class="product-title" title="{{ $p->NamaProduk }}">{{ $p->NamaProduk }}</h5>
                            
                            <p class="product-desc mb-2" title="{{ $p->Deskripsi }}">
                                {{ $p->Deskripsi ?? 'Kondisi barang thrift terawat dan siap pakai.' }}
                            </p>

                            <div class="d-flex justify-content-between align-items-center mt-2 mb-3">
                                <span class="product-price">Rp {{ number_format($p->Harga, 0, ',', '.') }}</span>
                                @if($p->Status == 'Tersedia' && $p->Stok > 0)
                                    <span class="badge badge-stock" style="background: rgba(0, 184, 190, 0.15); color: #005C66; border: 1px solid rgba(0, 184, 190, 0.4);">
                                        <i class="bi bi-check2-circle me-1"></i> Tersedia
                                    </span>
                                @else
                                    <span class="badge badge-stock" style="background: rgba(0, 27, 39, 0.08); color: #53707c; border: 1px solid rgba(0, 27, 39, 0.15);">
                                        <i class="bi bi-slash-circle me-1"></i> Terjual
                                    </span>
                                @endif
                            </div>
                        </div>

                        <div>
                            <a href="{{ route('pelanggan.produk.detail', $p->ProdukID) }}" class="btn btn-dark btn-sm w-100 rounded-pill py-2 fw-semibold">
                                <i class="bi bi-eye me-1"></i> Lihat Detail
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-12 text-center py-5">
                <div class="p-5 bg-white rounded-4 shadow-sm border">
                    <i class="bi bi-inbox fs-1 text-muted"></i>
                    <h5 class="mt-3 text-muted fw-bold">Tidak ada produk yang cocok dengan pencarian Anda.</h5>
                    <p class="text-muted small">Coba ubah kata kunci atau pilih kategori lain.</p>
                    <a href="{{ route('pelanggan.katalog') }}" class="btn btn-dark rounded-pill px-4 mt-2">Reset Filter</a>
                </div>
            </div>
        @endforelse
    </div>

    <!-- PAGINATION -->
    <div class="d-flex justify-content-center mb-5">
        {{ $produk->withQueryString()->links() }}
    </div>

</div>
@endsection