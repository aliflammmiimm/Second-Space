@extends('layouts.app')

@section('title', 'SecondSpace - Pre-Loved & Thrift Marketplace')

@push('styles')
<style>
    /* INTRO ANIMATION */
    .intro {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100vh;
        background: #000;
        display: flex;
        align-items: center;
        justify-content: center;
        z-index: 99999;
        animation: introFade 2s forwards;
        pointer-events: none;
    }

    .intro h1 {
        font-size: clamp(38px, 8vw, 84px);
        letter-spacing: 10px;
        font-weight: 900;
        color: #e10600;
        opacity: 0;
        animation: titleReveal 1.4s forwards;
    }

    @keyframes titleReveal {
        0% { opacity: 0; transform: scale(0.85); }
        60% { opacity: 1; transform: scale(1.08); }
        100% { opacity: 1; transform: scale(1); }
    }

    @keyframes introFade {
        0% { opacity: 1; }
        65% { opacity: 1; }
        100% { opacity: 0; visibility: hidden; }
    }

    /* GLOW BACKGROUND HERO */
    .hero-wrapper {
        position: relative;
        overflow: hidden;
        background: linear-gradient(135deg, #050505 0%, #1a0000 35%, #050505 70%, #001f12 100%);
        background-size: 300% 300%;
        animation: glowMove 14s ease infinite;
        color: white;
        padding: 90px 0 110px;
    }

    @keyframes glowMove {
        0% { background-position: 0% 50%; }
        50% { background-position: 100% 50%; }
        100% { background-position: 0% 50%; }
    }

    .hero-title {
        font-size: clamp(38px, 5.5vw, 68px);
        font-weight: 900;
        letter-spacing: 2px;
        line-height: 1.15;
        margin-bottom: 20px;
    }

    .hero-title span {
        color: #ff2a2a;
        background: linear-gradient(90deg, #ff1a1a, #ff7b7b);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
    }

    .hero-text {
        font-size: 1.1rem;
        color: #cbd5e1;
        max-width: 660px;
        line-height: 1.8;
    }

    .btn-shop-now {
        padding: 14px 32px;
        background: #e10600;
        color: white;
        font-weight: 700;
        border-radius: 50px;
        transition: all 0.3s ease;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        border: none;
        text-decoration: none;
        box-shadow: 0 10px 25px rgba(225, 6, 0, 0.4);
    }

    .btn-shop-now:hover {
        transform: translateY(-3px);
        box-shadow: 0 15px 35px rgba(225, 6, 0, 0.6);
        color: white;
    }

    /* CATEGORIES GRID PREVIEW */
    .cat-card {
        background: rgba(255, 255, 255, 0.05);
        border: 1px solid rgba(255, 255, 255, 0.1);
        border-radius: 16px;
        padding: 20px;
        text-align: center;
        color: white;
        text-decoration: none;
        transition: all 0.3s ease;
        display: block;
    }

    .cat-card:hover {
        background: rgba(225, 6, 0, 0.15);
        border-color: rgba(225, 6, 0, 0.4);
        transform: translateY(-4px);
        color: white;
    }

    .cat-icon {
        font-size: 2rem;
        margin-bottom: 8px;
        color: #ff4d4d;
    }

    /* ABOUT SECTION */
    .about-section {
        background: #0d0d11;
        color: white;
        padding: 85px 0;
        border-top: 1px solid rgba(255, 255, 255, 0.05);
        border-bottom: 1px solid rgba(255, 255, 255, 0.05);
    }

    .about-box {
        max-width: 820px;
        margin: auto;
        text-align: center;
    }

    .about-box h2 {
        font-size: clamp(30px, 4vw, 42px);
        font-weight: 800;
        margin-bottom: 22px;
    }

    .about-box p {
        font-size: 1.05rem;
        line-height: 1.85;
        color: #94a3b8;
        margin-bottom: 18px;
    }

    /* PRODUCT SHOWCASE SLIDER */
    .product-showcase {
        background: #ffffff;
        color: #0f172a;
        padding: 90px 0 110px;
        overflow: hidden;
    }

    .section-title {
        text-align: center;
        font-size: clamp(30px, 3.8vw, 42px);
        font-weight: 800;
        margin-bottom: 20px;
    }

    .swiper {
        width: 100%;
        padding: 50px 0 80px;
    }

    .swiper-slide {
        width: 300px;
        transition: all 0.5s cubic-bezier(.22,.61,.36,1);
        filter: blur(4px);
        opacity: 0.5;
        transform: scale(0.8);
    }

    .swiper-slide-active {
        filter: blur(0);
        opacity: 1;
        transform: scale(1.15);
        z-index: 10;
    }

    .product-slider-card {
        background: white;
        border-radius: 20px;
        overflow: hidden;
        box-shadow: 0 20px 50px rgba(0,0,0,0.12);
        border: 1px solid rgba(0,0,0,0.06);
    }

    .product-slider-card img {
        width: 100%;
        height: 320px;
        object-fit: cover;
        object-position: center;
        background: #f8fafc;
    }

    .product-slider-info {
        padding: 20px;
        text-align: center;
    }

    .product-slider-info h5 {
        font-weight: 700;
        margin-bottom: 4px;
        font-size: 1.1rem;
        display: -webkit-box;
        -webkit-line-clamp: 1;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }

    .product-slider-info .category {
        color: #64748b;
        font-size: 0.85rem;
        margin-bottom: 8px;
    }

    .product-slider-info .price {
        font-weight: 800;
        font-size: 1.25rem;
        color: #e10600;
    }

    .swiper-button-next, .swiper-button-prev {
        color: #0f172a;
        width: 48px;
        height: 48px;
        border-radius: 50%;
        background: white;
        box-shadow: 0 8px 24px rgba(0,0,0,0.15);
    }

    .swiper-button-next::after, .swiper-button-prev::after {
        font-size: 1rem;
        font-weight: bold;
    }
</style>
@endpush

@section('content')

    <!-- INTRO REVEAL -->
    <div class="intro">
        <h1>SECONDSPACE</h1>
    </div>

    <!-- HERO SECTION -->
    <section class="hero-wrapper">
        <div class="container">
            <div class="row align-items-center mb-5">
                <div class="col-lg-8">
                    <h1 class="hero-title">
                        SECOND<span>SPACE</span>
                    </h1>
                    <p class="hero-text mb-4">
                        SecondSpace adalah marketplace barang pre-loved dan thrift terkurasi yang menghadirkan 
                        pilihan berkualitas dari <strong>Elektronik, Fashion, Peralatan Rumah Tangga, hingga ATK</strong> 
                        dengan harga ramah kantong.
                    </p>
                    <div class="d-flex gap-3 flex-wrap">
                        <a href="{{ route('pelanggan.katalog') }}" class="btn-shop-now">
                            <i class="bi bi-bag-heart-fill"></i> Jelajahi Katalog
                        </a>
                        @auth
                            <a href="{{ route('pelanggan.pesanan_aktif') }}" class="glass-btn" style="padding: 14px 26px; font-size: 0.95rem;">
                                <i class="bi bi-clock-history text-warning"></i> Cek Pesanan Aktif
                            </a>
                        @else
                            <a href="{{ route('login') }}" class="glass-btn" style="padding: 14px 26px; font-size: 0.95rem;">
                                <i class="bi bi-person-fill"></i> Masuk Akun
                            </a>
                        @endauth
                    </div>
                </div>
            </div>

            <!-- 4 KATEGORI UTAMA PREVIEW -->
            <div class="row g-3">
                <div class="col-md-3 col-6">
                    <a href="{{ route('pelanggan.katalog', ['jenis' => 'Elektronik']) }}" class="cat-card">
                        <div class="cat-icon"><i class="bi bi-laptop"></i></div>
                        <h6 class="fw-bold mb-1">Elektronik</h6>
                        <small class="text-white-50">Gadget & Audio</small>
                    </a>
                </div>
                <div class="col-md-3 col-6">
                    <a href="{{ route('pelanggan.katalog', ['jenis' => 'Fashion']) }}" class="cat-card">
                        <div class="cat-icon"><i class="bi bi-bag-heart"></i></div>
                        <h6 class="fw-bold mb-1">Fashion</h6>
                        <small class="text-white-50">Vintage & Streetwear</small>
                    </a>
                </div>
                <div class="col-md-3 col-6">
                    <a href="{{ route('pelanggan.katalog', ['jenis' => 'Peralatan Rumah Tangga']) }}" class="cat-card">
                        <div class="cat-icon"><i class="bi bi-house-door"></i></div>
                        <h6 class="fw-bold mb-1">Peralatan Rumah Tangga</h6>
                        <small class="text-white-50">Estetik & Fungsional</small>
                    </a>
                </div>
                <div class="col-md-3 col-6">
                    <a href="{{ route('pelanggan.katalog', ['jenis' => 'ATK']) }}" class="cat-card">
                        <div class="cat-icon"><i class="bi bi-pencil-square"></i></div>
                        <h6 class="fw-bold mb-1">ATK</h6>
                        <small class="text-white-50">Alat Tulis & Kantor</small>
                    </a>
                </div>
            </div>
        </div>
    </section>

    <!-- ABOUT SECTION -->
    <section class="about-section">
        <div class="container">
            <div class="about-box">
                <h2>Tentang SecondSpace</h2>
                <p>
                    Kami percaya bahwa barang pre-loved memiliki nilai dan cerita berharga.
                    Di SecondSpace, setiap barang telah melalui kurasi ketat untuk memastikan fungsi,
                    kebersihan, dan kelayakan pemakaian tetap terjaga secara optimal.
                </p>
                <p>
                    Mulai dari perangkat elektronik, pakaian thrift stylish, dekorasi dan peralatan dapur,
                    hingga perlengkapan meja kerja dan ATK — SecondSpace adalah ruang kedua bagi barang-barang 
                    berkualitas untuk menemukan pemilik barunya.
                </p>
                <p>
                    Nikmati kemudahan berbelanja pre-loved yang modern, aman, dan transparan.
                </p>
            </div>
        </div>
    </section>

    <!-- PRODUCT SHOWCASE 3D SLIDER -->
    <section class="product-showcase">
        <div class="container">
            <h2 class="section-title">
                Koleksi Pilihan SecondSpace
            </h2>
            <p class="text-center text-muted mb-4" style="max-width: 600px; margin: auto;">
                Temukan item favorit dari berbagai kategori yang siap diorder sekarang
            </p>

            <div class="swiper productSwiper">
                <div class="swiper-wrapper">
                    @forelse($featuredProducts as $item)
                        <div class="swiper-slide">
                            <div class="product-slider-card">
                                <img src="{{ $item->gambar_url }}" alt="{{ $item->NamaProduk }}"
                                     loading="lazy" onerror="this.onerror=null; this.src='{{ asset('upload/default.jpg') }}';">
                                <div class="product-slider-info">
                                    <h5 title="{{ $item->NamaProduk }}">{{ $item->NamaProduk }}</h5>
                                    <p class="category">{{ $item->jenisProduk ? $item->jenisProduk->NamaJenis : ($item->Brand ?? 'SecondSpace') }}</p>
                                    <div class="price">Rp {{ number_format($item->Harga, 0, ',', '.') }}</div>
                                    <a href="{{ route('pelanggan.produk.detail', $item->ProdukID) }}" class="btn btn-dark btn-sm rounded-pill mt-3 px-4 fw-semibold">
                                        Lihat Detail
                                    </a>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="swiper-slide">
                            <div class="product-slider-card text-center p-5">
                                <p class="text-muted">Belum ada produk untuk ditampilkan.</p>
                            </div>
                        </div>
                    @endforelse
                </div>

                <div class="swiper-button-prev"></div>
                <div class="swiper-button-next"></div>
            </div>

            <div class="text-center mt-3">
                <a href="{{ route('pelanggan.katalog') }}" class="btn btn-outline-dark rounded-pill px-5 py-3 fw-bold">
                    Lihat Semua Katalog Produk <i class="bi bi-arrow-right ms-2"></i>
                </a>
            </div>
        </div>
    </section>

@endsection

@push('scripts')
<script>
    setTimeout(() => {
        const intro = document.querySelector(".intro");
        if (intro) intro.style.display = "none";
    }, 2200);

    var swiper = new Swiper(".productSwiper", {
        loop: true,
        centeredSlides: true,
        slidesPerView: "auto",
        spaceBetween: -50,
        grabCursor: true,
        speed: 800,
        autoplay: {
            delay: 3500,
            disableOnInteraction: false,
        },
        navigation: {
            nextEl: ".swiper-button-next",
            prevEl: ".swiper-button-prev"
        },
        breakpoints: {
            0: { slidesPerView: 1.2, spaceBetween: 15 },
            768: { slidesPerView: 2.2, spaceBetween: -25 },
            1200: { slidesPerView: 3, spaceBetween: -50 }
        }
    });
</script>
@endpush