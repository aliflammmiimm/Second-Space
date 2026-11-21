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
        height: 100dvh;
        background: #001B27;
        display: flex;
        align-items: center;
        justify-content: center;
        z-index: 99999;
        animation: introFade 2s forwards;
        pointer-events: none;
        padding: 20px;
        box-sizing: border-box;
        overflow: hidden;
    }

    .intro .intro-content {
        max-width: 90vw;
        text-align: center;
    }

    .intro-logo {
        width: clamp(70px, 18vw, 95px);
        height: clamp(70px, 18vw, 95px);
        object-fit: cover;
        border-radius: 22px;
        border: 2px solid rgba(0, 184, 190, 0.6);
        box-shadow: 0 0 35px rgba(0, 184, 190, 0.5);
        animation: titleReveal 1.4s forwards;
    }

    .intro h1 {
        font-size: clamp(24px, 6.5vw, 64px);
        letter-spacing: clamp(3px, 1.2vw, 8px);
        font-weight: 900;
        color: #00B8BE;
        opacity: 0;
        animation: titleReveal 1.4s forwards;
        text-shadow: 0 0 30px rgba(0, 184, 190, 0.5);
        white-space: nowrap;
        margin: 0;
    }

    @keyframes titleReveal {
        0% { opacity: 0; transform: scale(0.85); }
        60% { opacity: 1; transform: scale(1.03); }
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
        background: linear-gradient(135deg, #001B27 0%, #003642 35%, #005C66 70%, #001B27 100%);
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
        font-size: clamp(32px, 5.5vw, 68px);
        font-weight: 900;
        letter-spacing: clamp(1px, 0.5vw, 2px);
        line-height: 1.15;
        margin-bottom: 20px;
        word-break: break-word;
    }

    .hero-title span {
        color: #00B8BE;
        background: linear-gradient(90deg, #00B8BE, #67f2f7);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
    }

    .hero-text {
        font-size: 1.1rem;
        color: #d1e7ec;
        max-width: 660px;
        line-height: 1.8;
    }

    .btn-shop-now {
        padding: 14px 32px;
        background: #00B8BE;
        color: #001B27;
        font-weight: 800;
        border-radius: 50px;
        transition: all 0.3s ease;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        border: none;
        text-decoration: none;
        box-shadow: 0 10px 25px rgba(0, 184, 190, 0.45);
        min-height: 50px;
    }

    .btn-shop-now:hover {
        transform: translateY(-3px);
        background: #00d2da;
        box-shadow: 0 15px 35px rgba(0, 184, 190, 0.6);
        color: #001B27;
    }

    .hero-sec-btn {
        padding: 14px 28px !important;
        font-size: 0.95rem;
        min-height: 50px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        border-radius: 50px;
    }

    /* HERO LOGO FLOATING */
    .hero-logo-box {
        position: relative;
    }

    .hero-logo-box::before {
        content: "";
        position: absolute;
        inset: -15px;
        background: radial-gradient(circle, rgba(0, 184, 190, 0.45) 0%, rgba(0, 27, 39, 0) 70%);
        border-radius: 40px;
        z-index: 1;
        filter: blur(20px);
        animation: pulseGlow 4s ease-in-out infinite alternate;
    }

    @keyframes pulseGlow {
        from { opacity: 0.5; transform: scale(0.95); }
        to { opacity: 0.9; transform: scale(1.05); }
    }

    .hero-logo-img {
        position: relative;
        z-index: 2;
        width: 320px;
        max-width: 100%;
        border-radius: 36px;
        box-shadow: 0 25px 60px rgba(0, 0, 0, 0.55), 0 0 40px rgba(0, 184, 190, 0.4);
        border: 2px solid rgba(0, 184, 190, 0.4);
        transition: transform 0.4s ease, box-shadow 0.4s ease;
        animation: heroFloat 5s ease-in-out infinite;
    }

    .hero-logo-img:hover {
        transform: translateY(-8px) scale(1.03);
        box-shadow: 0 30px 70px rgba(0, 0, 0, 0.65), 0 0 55px rgba(0, 184, 190, 0.65);
    }

    @keyframes heroFloat {
        0%, 100% { transform: translateY(0); }
        50% { transform: translateY(-12px); }
    }

    /* CATEGORIES GRID PREVIEW */
    .cat-card {
        background: rgba(0, 92, 102, 0.35);
        border: 1px solid rgba(0, 184, 190, 0.25);
        border-radius: 20px;
        padding: 22px 14px;
        text-align: center;
        color: white;
        text-decoration: none;
        transition: all 0.3s ease;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        height: 100%;
        min-height: 160px;
        backdrop-filter: blur(10px);
        box-shadow: 0 8px 20px rgba(0, 27, 39, 0.2);
    }

    .cat-card:hover {
        background: rgba(0, 184, 190, 0.22);
        border-color: #00B8BE;
        transform: translateY(-4px);
        color: white;
        box-shadow: 0 12px 30px rgba(0, 184, 190, 0.35);
    }

    .cat-icon {
        font-size: 2.3rem;
        line-height: 1;
        margin-bottom: 12px;
        color: #00B8BE;
        height: 44px;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: transform 0.3s ease;
    }

    .cat-card:hover .cat-icon {
        transform: scale(1.12);
    }

    .cat-card h6 {
        font-size: 0.95rem;
        font-weight: 700;
        line-height: 1.3;
        margin-bottom: 4px;
        min-height: 2.6em;
        display: flex;
        align-items: center;
        justify-content: center;
        text-align: center;
        width: 100%;
        color: #ffffff;
        word-break: break-word;
    }

    .cat-card small {
        font-size: 0.78rem;
        line-height: 1.3;
        color: rgba(209, 231, 236, 0.72);
        min-height: 2.6em;
        display: flex;
        align-items: center;
        justify-content: center;
        text-align: center;
        width: 100%;
    }

    /* ABOUT SECTION */
    .about-section {
        background: #001B27;
        color: white;
        padding: 85px 0;
        border-top: 1px solid rgba(0, 184, 190, 0.15);
        border-bottom: 1px solid rgba(0, 184, 190, 0.15);
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
        color: #ffffff;
    }

    .about-box p {
        font-size: 1.05rem;
        line-height: 1.85;
        color: #8faeb8;
        margin-bottom: 18px;
    }

    /* PRODUCT SHOWCASE SLIDER */
    .product-showcase {
        background: #f4f9fa;
        color: #001B27;
        padding: 70px 0 90px;
        overflow: hidden;
    }

    .section-title {
        text-align: center;
        font-size: clamp(26px, 3.8vw, 38px);
        font-weight: 800;
        margin-bottom: 12px;
        color: #001B27;
    }

    .swiper {
        width: 100%;
        padding: 24px 0 45px;
    }

    .swiper-slide {
        width: 280px;
        max-width: 82vw;
        transition: transform 0.3s ease, opacity 0.3s ease;
        opacity: 0.82;
        transform: scale(0.96);
    }

    .swiper-slide-active,
    .swiper-slide-duplicate-active {
        opacity: 1;
        transform: scale(1);
        z-index: 5;
    }

    .product-slider-card {
        background: white;
        border-radius: 18px;
        overflow: hidden;
        box-shadow: 0 10px 30px rgba(0, 27, 39, 0.08);
        border: 1px solid rgba(0, 92, 102, 0.12);
        transition: transform 0.25s ease, box-shadow 0.25s ease;
    }

    .product-slider-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 14px 35px rgba(0, 27, 39, 0.12);
        border-color: rgba(0, 184, 190, 0.4);
    }

    .product-slider-card img {
        width: 100%;
        height: 240px;
        object-fit: cover;
        object-position: center;
        background: #f8fafc;
    }

    .product-slider-info {
        padding: 16px 18px 20px;
        text-align: center;
    }

    .product-slider-info h5 {
        font-weight: 700;
        margin-bottom: 4px;
        font-size: 1.05rem;
        color: #001B27;
        display: -webkit-box;
        -webkit-line-clamp: 1;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }

    .product-slider-info .category {
        color: #53707c;
        font-size: 0.82rem;
        margin-bottom: 6px;
    }

    .product-slider-info .price {
        font-weight: 800;
        font-size: 1.2rem;
        color: #005C66;
    }

    .swiper-button-next, .swiper-button-prev {
        color: #001B27;
        width: 44px;
        height: 44px;
        border-radius: 50%;
        background: white;
        border: 1px solid rgba(0, 184, 190, 0.25);
        box-shadow: 0 6px 20px rgba(0, 27, 39, 0.12);
        transition: all 0.25s ease;
    }

    .swiper-button-next:hover, .swiper-button-prev:hover {
        background: #00B8BE;
        color: #001B27;
        border-color: #00B8BE;
    }

    .swiper-button-next::after, .swiper-button-prev::after {
        font-size: 0.9rem;
        font-weight: bold;
    }

    @media (max-width: 576px) {
        .hero-title {
            font-size: clamp(28px, 8vw, 38px) !important;
            margin-bottom: 14px;
        }
        .hero-text {
            font-size: 0.94rem;
            line-height: 1.65;
            margin-bottom: 22px;
        }
        .hero-cta-group {
            display: flex;
            flex-direction: column;
            width: 100%;
            gap: 12px !important;
        }
        .hero-cta-group .btn-shop-now,
        .hero-cta-group .hero-sec-btn,
        .hero-cta-group .glass-btn {
            width: 100% !important;
            display: flex !important;
            align-items: center !important;
            justify-content: center !important;
            padding: 13px 20px !important;
            font-size: 0.95rem !important;
            border-radius: 50px !important;
            box-sizing: border-box !important;
            min-height: 50px;
            text-align: center;
        }
        .cat-card {
            padding: 16px 10px !important;
            min-height: 145px !important;
            border-radius: 16px !important;
        }
        .cat-icon {
            font-size: 1.95rem !important;
            height: 38px !important;
            margin-bottom: 8px !important;
        }
        .cat-card h6 {
            font-size: 0.88rem !important;
            min-height: 2.5em !important;
        }
        .cat-card small {
            font-size: 0.72rem !important;
            min-height: 2.5em !important;
        }
        .hero-wrapper {
            padding: 45px 0 55px;
        }
        .about-section {
            padding: 48px 0;
        }
        .about-box h2 {
            font-size: 1.65rem !important;
            margin-bottom: 16px;
        }
        .about-box p {
            font-size: 0.9rem !important;
            line-height: 1.65 !important;
        }
        .product-showcase {
            padding: 40px 0 50px;
        }
        .product-showcase .section-title {
            font-size: 1.5rem !important;
        }
        .swiper {
            padding: 10px 0 25px;
        }
        .swiper-slide {
            width: 250px !important;
            max-width: 78vw !important;
            opacity: 1 !important;
            transform: none !important;
        }
        .product-slider-card img {
            height: 190px !important;
        }
        .product-slider-info {
            padding: 12px 14px 16px !important;
        }
        .product-slider-info h5 {
            font-size: 0.92rem !important;
        }
        .product-slider-info .price {
            font-size: 1.05rem !important;
        }
        .product-slider-info .btn {
            font-size: 0.82rem !important;
            padding: 6px 18px !important;
            margin-top: 8px !important;
        }
        .swiper-button-next, .swiper-button-prev {
            display: none !important;
        }
    }
</style>
@endpush

@section('content')

    <!-- INTRO REVEAL -->
    <div class="intro">
        <div class="text-center intro-content">
            <img src="{{ asset('assets/images/logo.jpeg') }}" alt="SecondSpace" class="mb-3 rounded-4 shadow-lg intro-logo">
            <h1>SECONDSPACE</h1>
        </div>
    </div>

    <!-- HERO SECTION -->
    <section class="hero-wrapper">
        <div class="container">
            <div class="row align-items-center mb-5 g-4">
                <div class="col-lg-7">
                    <h1 class="hero-title">
                        SECOND<span>SPACE</span>
                    </h1>
                    <p class="hero-text mb-4">
                        SecondSpace adalah marketplace barang pre-loved dan thrift terkurasi yang menghadirkan 
                        pilihan berkualitas dari <strong>Elektronik, Fashion, Peralatan Rumah Tangga, hingga ATK</strong> 
                        dengan harga ramah kantong.
                    </p>
                    <div class="d-flex gap-3 flex-wrap hero-cta-group">
                        <a href="{{ route('pelanggan.katalog') }}" class="btn-shop-now">
                            <i class="bi bi-bag-heart-fill"></i> Jelajahi Katalog
                        </a>
                        @auth
                            <a href="{{ route('pelanggan.pesanan_aktif') }}" class="glass-btn hero-sec-btn">
                                <i class="bi bi-clock-history text-warning"></i> Cek Pesanan Aktif
                            </a>
                        @else
                            <a href="{{ route('login') }}" class="glass-btn hero-sec-btn">
                                <i class="bi bi-person-fill"></i> Masuk Akun
                            </a>
                        @endauth
                    </div>
                </div>
                <div class="col-lg-5 text-center d-none d-lg-block">
                    <div class="hero-logo-box d-inline-block">
                        <img src="{{ asset('assets/images/logo.jpeg') }}" alt="SecondSpace Official Logo" class="hero-logo-img">
                    </div>
                </div>
            </div>

            <!-- 4 KATEGORI UTAMA PREVIEW -->
            <div class="category-preview-header d-flex align-items-center justify-content-between mb-3 mt-4 pt-1">
                <div class="d-flex align-items-center gap-2">
                    <span class="badge rounded-pill px-3 py-2" style="background: rgba(0, 184, 190, 0.18); color: #00B8BE; border: 1px solid rgba(0, 184, 190, 0.35); font-size: 0.75rem; font-weight: 700; letter-spacing: 0.6px;">
                        KATEGORI PILIHAN
                    </span>
                </div>
                <a href="{{ route('pelanggan.katalog') }}" class="small text-decoration-none d-flex align-items-center gap-1" style="color: #00B8BE; font-weight: 600; font-size: 0.85rem;">
                    <span>Lihat Semua</span> <i class="bi bi-chevron-right" style="font-size: 0.75rem;"></i>
                </a>
            </div>

            <div class="row g-3 g-md-4">
                <div class="col-md-3 col-6 d-flex">
                    <a href="{{ route('pelanggan.katalog', ['jenis' => 'Elektronik']) }}" class="cat-card w-100">
                        <div class="cat-icon"><i class="bi bi-laptop"></i></div>
                        <h6 class="fw-bold mb-1">Elektronik</h6>
                        <small>Gadget & Audio</small>
                    </a>
                </div>
                <div class="col-md-3 col-6 d-flex">
                    <a href="{{ route('pelanggan.katalog', ['jenis' => 'Fashion']) }}" class="cat-card w-100">
                        <div class="cat-icon"><i class="bi bi-bag-heart"></i></div>
                        <h6 class="fw-bold mb-1">Fashion</h6>
                        <small>Vintage & Streetwear</small>
                    </a>
                </div>
                <div class="col-md-3 col-6 d-flex">
                    <a href="{{ route('pelanggan.katalog', ['jenis' => 'Peralatan Rumah Tangga']) }}" class="cat-card w-100">
                        <div class="cat-icon"><i class="bi bi-house-door"></i></div>
                        <h6 class="fw-bold mb-1">Peralatan Rumah Tangga</h6>
                        <small>Estetik & Fungsional</small>
                    </a>
                </div>
                <div class="col-md-3 col-6 d-flex">
                    <a href="{{ route('pelanggan.katalog', ['jenis' => 'ATK']) }}" class="cat-card w-100">
                        <div class="cat-icon"><i class="bi bi-pencil-square"></i></div>
                        <h6 class="fw-bold mb-1">ATK</h6>
                        <small>Alat Tulis & Kantor</small>
                    </a>
                </div>
            </div>
        </div>
    </section>

    <!-- ABOUT SECTION -->
    <section class="about-section">
        <div class="container">
            <div class="about-box">
                <div class="mb-3">
                    <span class="badge rounded-pill px-3 py-2" style="background: rgba(0, 184, 190, 0.15); color: #00B8BE; border: 1px solid rgba(0, 184, 190, 0.35); font-size: 0.75rem; font-weight: 700; letter-spacing: 0.6px;">
                        TENTANG KAMI
                    </span>
                </div>
                <h2>Tentang SecondSpace</h2>
                <p class="lead" style="color: #e2f1f4; font-size: 1.05rem; line-height: 1.8;">
                    Kami percaya bahwa barang pre-loved memiliki nilai dan cerita berharga.
                    Di SecondSpace, setiap barang telah melalui kurasi ketat untuk memastikan fungsi,
                    kebersihan, dan kelayakan pemakaian tetap terjaga secara optimal.
                </p>
                <p style="color: #b2d2d8; font-size: 0.95rem; line-height: 1.8;">
                    Mulai dari perangkat elektronik, pakaian thrift stylish, dekorasi dan peralatan dapur,
                    hingga perlengkapan meja kerja dan ATK &mdash; SecondSpace adalah ruang kedua bagi barang-barang 
                    berkualitas untuk menemukan pemilik barunya.
                </p>
                <p class="mb-0" style="color: #00B8BE; font-weight: 600; font-size: 0.95rem;">
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
        loopAddBlankSlides: true,
        loopAdditionalSlides: 4,
        loopPreventsSliding: false,
        centeredSlides: true,
        slidesPerView: "auto",
        spaceBetween: 25,
        grabCursor: true,
        speed: 700,
        autoplay: {
            delay: 3200,
            disableOnInteraction: false,
        },
        navigation: {
            nextEl: ".swiper-button-next",
            prevEl: ".swiper-button-prev"
        },
        breakpoints: {
            0: { slidesPerView: "auto", spaceBetween: 16, centeredSlides: true },
            768: { slidesPerView: "auto", spaceBetween: 24, centeredSlides: true },
            1200: { slidesPerView: "auto", spaceBetween: 30, centeredSlides: true }
        },
        on: {
            reachEnd: function () {
                // Fail-safe jika mencapai slide paling akhir, otomatis putar kembali ke slide pertama
                this.slideTo(0, 800);
            }
        }
    });

    // Pengaman tombol navigasi jika mencapai batas slide
    const nextBtn = document.querySelector(".swiper-button-next");
    if (nextBtn) {
        nextBtn.addEventListener("click", function () {
            if (swiper && (swiper.isEnd || swiper.activeIndex >= swiper.slides.length - 1)) {
                swiper.slideTo(0, 800);
            }
        });
    }

    const prevBtn = document.querySelector(".swiper-button-prev");
    if (prevBtn) {
        prevBtn.addEventListener("click", function () {
            if (swiper && swiper.isBeginning && !swiper.params.loop) {
                swiper.slideTo(swiper.slides.length - 1, 800);
            }
        });
    }
</script>
@endpush