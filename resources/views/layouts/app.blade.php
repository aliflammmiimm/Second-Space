<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'SecondSpace - Pre-Loved & Thrift Marketplace')</title>
    
    <!-- Favicon -->
    <link rel="icon" type="image/jpeg" href="{{ asset('assets/images/logo.jpeg') }}">
    <link rel="apple-touch-icon" href="{{ asset('assets/images/logo.jpeg') }}">
    
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    
    <!-- Bootstrap 5 & Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    
    <!-- Swiper.js -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css"/>

    <style>
        :root {
            /* COLOR PALETTE BEHANCE / DRIBBLE */
            --color-white: #FFFFFF;
            --color-primary: #00B8BE;        /* Vibrant Cyan / Accent */
            --color-primary-hover: #00d2da;
            --color-primary-dark: #009ea3;
            --color-deep-teal: #005C66;      /* Deep Ocean Teal */
            --color-deep-teal-hover: #00454d;
            --color-midnight: #001B27;       /* Midnight Dark Navy / Teal */
            
            --primary-accent: #00B8BE;
            --primary-hover: #00d2da;
            --dark-bg: #001B27;
            --card-dark: rgba(0, 27, 39, 0.92);
            --border-glass: rgba(0, 184, 190, 0.22);
            
            --theme-bg-light: #f4f9fa;
            --theme-text-dark: #001B27;
            --theme-text-muted: #53707c;
        }

        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background-color: var(--theme-bg-light);
            color: var(--theme-text-dark);
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }

        /* NAVBAR */
        .navbar-custom {
            background: rgba(0, 27, 39, 0.96);
            backdrop-filter: blur(16px);
            -webkit-backdrop-filter: blur(16px);
            border-bottom: 1px solid rgba(0, 184, 190, 0.2);
            padding: 14px 0;
            position: sticky;
            top: 0;
            z-index: 1030;
        }

        .navbar-brand {
            font-weight: 800;
            letter-spacing: 1.5px;
            color: #fff !important;
            font-size: 1.35rem;
            display: flex;
            align-items: center;
            gap: 10px;
            text-decoration: none;
        }

        .navbar-logo {
            height: 38px;
            width: 38px;
            border-radius: 10px;
            object-fit: cover;
            border: 1.5px solid rgba(0, 184, 190, 0.4);
            box-shadow: 0 2px 10px rgba(0, 184, 190, 0.3);
            transition: transform 0.25s ease, box-shadow 0.25s ease;
        }

        .navbar-brand:hover .navbar-logo {
            transform: scale(1.08);
            box-shadow: 0 4px 15px rgba(0, 184, 190, 0.6);
        }

        .navbar-brand i {
            color: var(--color-primary) !important;
        }

        .navbar-brand span {
            color: var(--color-primary);
        }

        /* GLASS BUTTON */
        .glass-btn {
            position: relative;
            padding: 8px 18px;
            border-radius: 50px;
            background: rgba(255, 255, 255, 0.08);
            border: 1px solid rgba(0, 184, 190, 0.25);
            color: #fff;
            backdrop-filter: blur(15px);
            overflow: hidden;
            transition: all 0.3s ease;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            font-size: 0.9rem;
            font-weight: 500;
        }

        .glass-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(0, 184, 190, 0.35);
            background: rgba(0, 184, 190, 0.18);
            border-color: var(--color-primary);
            color: #fff;
        }

        .glass-btn-primary {
            background: var(--color-primary);
            border-color: var(--color-primary);
            color: var(--color-midnight) !important;
            font-weight: 700;
        }

        .glass-btn-primary:hover {
            background: var(--color-primary-hover);
            border-color: var(--color-primary-hover);
            color: var(--color-midnight) !important;
            box-shadow: 0 8px 25px rgba(0, 184, 190, 0.5);
        }

        .glass-btn.active {
            background: rgba(0, 184, 190, 0.22);
            border-color: var(--color-primary);
            color: var(--color-primary);
        }

        /* ALERTS */
        .alert-custom {
            border-radius: 14px;
            border: none;
            box-shadow: 0 10px 30px rgba(0, 27, 39, 0.06);
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 14px 20px;
        }

        /* CARD STYLES */
        .custom-card {
            background: #ffffff;
            border: 1px solid rgba(0, 92, 102, 0.12);
            border-radius: 18px;
            box-shadow: 0 8px 24px rgba(0, 27, 39, 0.04);
            transition: all 0.3s ease;
        }

        .custom-card:hover {
            box-shadow: 0 14px 35px rgba(0, 27, 39, 0.08);
            border-color: rgba(0, 184, 190, 0.35);
        }

        /* FOOTER */
        .footer {
            margin-top: auto;
            background: var(--color-midnight);
            color: #7d9fad;
            padding: 32px 0;
            border-top: 1px solid rgba(0, 184, 190, 0.18);
            font-size: 0.9rem;
        }

        /* PAGINATION STYLING */
        .pagination {
            margin-bottom: 0;
            display: inline-flex;
            gap: 4px;
            align-items: center;
        }
        .page-item .page-link {
            border-radius: 8px !important;
            border: 1px solid rgba(0, 92, 102, 0.2);
            color: var(--color-midnight);
            font-weight: 600;
            padding: 7px 14px;
            margin: 0 2px;
            transition: all 0.2s ease;
            background: #ffffff;
            box-shadow: 0 1px 3px rgba(0, 27, 39, 0.04);
        }
        .page-item.active .page-link {
            background-color: var(--color-midnight) !important;
            border-color: var(--color-deep-teal) !important;
            color: var(--color-primary) !important;
            box-shadow: 0 4px 12px rgba(0, 27, 39, 0.2);
        }
        .page-item.disabled .page-link {
            color: #94a3b8;
            background-color: #f4f9fa;
            border-color: #e2e8f0;
        }
        .page-item .page-link:hover {
            background-color: var(--color-deep-teal);
            border-color: var(--color-deep-teal);
            color: #ffffff;
        }

        /* BUTTONS OVERRIDE TO NEW PALETTE */
        .btn-danger {
            background-color: var(--color-primary) !important;
            border-color: var(--color-primary) !important;
            color: var(--color-midnight) !important;
            font-weight: 700 !important;
            transition: all 0.3s ease;
        }
        .btn-danger:hover, .btn-danger:focus, .btn-danger:active {
            background-color: var(--color-primary-hover) !important;
            border-color: var(--color-primary-hover) !important;
            color: var(--color-midnight) !important;
            box-shadow: 0 8px 22px rgba(0, 184, 190, 0.45) !important;
            transform: translateY(-1px);
        }

        .btn-outline-danger {
            color: var(--color-deep-teal) !important;
            border-color: var(--color-primary) !important;
            font-weight: 600;
            transition: all 0.3s ease;
        }
        .btn-outline-danger:hover, .btn-outline-danger:focus, .btn-outline-danger:active {
            background-color: var(--color-primary) !important;
            border-color: var(--color-primary) !important;
            color: var(--color-midnight) !important;
            box-shadow: 0 6px 18px rgba(0, 184, 190, 0.35) !important;
        }

        /* Preserve red for actual delete/trash buttons */
        .btn-outline-danger:has(i.bi-trash), button.btn-outline-danger:has(.bi-trash) {
            color: #e11d48 !important;
            border-color: #fecdd3 !important;
        }
        .btn-outline-danger:has(i.bi-trash):hover {
            background-color: #e11d48 !important;
            border-color: #e11d48 !important;
            color: #ffffff !important;
        }

        .btn-dark {
            background-color: var(--color-midnight) !important;
            border-color: var(--color-midnight) !important;
            color: #ffffff !important;
            transition: all 0.3s ease;
        }
        .btn-dark:hover, .btn-dark:focus, .btn-dark:active {
            background-color: var(--color-deep-teal) !important;
            border-color: var(--color-deep-teal) !important;
            color: #ffffff !important;
            box-shadow: 0 6px 18px rgba(0, 27, 39, 0.25) !important;
        }

        .btn-outline-dark {
            color: var(--color-midnight) !important;
            border-color: rgba(0, 92, 102, 0.4) !important;
            transition: all 0.3s ease;
        }
        .btn-outline-dark:hover, .btn-outline-dark:focus, .btn-outline-dark:active {
            background-color: var(--color-midnight) !important;
            border-color: var(--color-midnight) !important;
            color: var(--color-primary) !important;
        }

        /* TEXT & BADGES OVERRIDE */
        .text-danger {
            color: var(--color-deep-teal) !important;
        }
        i.text-danger, .navbar-brand i.text-danger, .bi.text-danger {
            color: var(--color-primary) !important;
        }
        .bg-danger {
            background-color: var(--color-primary) !important;
            color: var(--color-midnight) !important;
        }
        .bg-danger.bg-opacity-10 {
            background-color: rgba(0, 184, 190, 0.12) !important;
            color: var(--color-deep-teal) !important;
        }
        .border-danger {
            border-color: var(--color-primary) !important;
        }
        .badge.bg-danger {
            background-color: var(--color-primary) !important;
            color: var(--color-midnight) !important;
            font-weight: 700;
        }
        .table-dark {
            --bs-table-bg: var(--color-midnight);
            --bs-table-border-color: rgba(0, 92, 102, 0.25);
            color: #fff;
        }

        /* NOTIFICATION BELL BUTTON & BADGE */
        .notif-dropdown {
            position: relative;
            overflow: visible !important;
        }

        .notif-bell-btn {
            width: 42px;
            height: 42px;
            min-width: 42px;
            padding: 0 !important;
            border-radius: 50% !important;
            display: inline-flex !important;
            align-items: center !important;
            justify-content: center !important;
            position: relative !important;
            overflow: visible !important; /* Jangan pernah clip badge notifikasi */
            background: rgba(255, 255, 255, 0.08) !important;
            border: 1px solid rgba(0, 184, 190, 0.25) !important;
            color: #ffffff !important;
            backdrop-filter: blur(15px);
            transition: all 0.25s ease !important;
            text-decoration: none;
        }

        .notif-bell-btn:hover,
        .notif-bell-btn:focus,
        .notif-bell-btn[aria-expanded="true"] {
            background: rgba(0, 184, 190, 0.2) !important;
            border-color: var(--color-primary) !important;
            color: #00B8BE !important;
            transform: translateY(-2px);
            box-shadow: 0 4px 18px rgba(0, 184, 190, 0.35) !important;
        }

        /* Badge notifikasi merah menyala, tampak timbul dan kontras tajam di atas navbar gelap */
        .notif-badge-pill {
            position: absolute !important;
            top: -4px !important;
            right: -4px !important;
            min-width: 20px !important;
            height: 20px !important;
            padding: 0 5px !important;
            border-radius: 10px !important;
            background-color: #ef4444 !important; /* Merah cerah */
            color: #ffffff !important;
            font-size: 0.68rem !important;
            font-weight: 800 !important;
            line-height: 20px !important;
            text-align: center !important;
            display: inline-flex !important;
            align-items: center !important;
            justify-content: center !important;
            border: 2px solid #001B27 !important; /* Ring pemisah gelap senada navbar */
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.5) !important;
            z-index: 1055 !important;
            pointer-events: none;
        }

        /* RESPONSIVE KHUSUS NAVBAR SELLER CENTRE */
        @media (min-width: 992px) {
            #sellerNavbarMenu,
            .seller-mobile-collapse {
                display: none !important;
            }
        }
    </style>
    @stack('styles')
</head>
<body>

    @php
        $activeGuard = null;
        $activeUser = null;

        // 1. Rute Admin (/admin*) HANYA menggunakan sesi Admin secara terisolasi
        if (request()->is('admin*')) {
            if (Auth::guard('admin')->check()) {
                $activeGuard = 'admin';
                $activeUser = Auth::guard('admin')->user();
            }
        // 2. Rute Seller (/seller*) HANYA menggunakan sesi Seller secara terisolasi
        } elseif (request()->is('seller*')) {
            if (Auth::guard('seller')->check()) {
                $activeGuard = 'seller';
                $activeUser = Auth::guard('seller')->user();
            }
        // 3. Rute Publik & Pelanggan (Beranda /, /pelanggan*, dll) HANYA membaca sesi Pelanggan (guard 'web')
        } else {
            if (Auth::guard('web')->check()) {
                $activeGuard = 'web';
                $activeUser = Auth::guard('web')->user();
            } elseif (Auth::guard('seller')->check()) {
                $activeGuard = 'seller';
                $activeUser = Auth::guard('seller')->user();
            }
        }

        $unreadChatCount = 0;
        $sellerPesananMasukCount = 0;
        $sellerKomisiPendingCount = 0;
        $buyerKeranjangCount = 0;
        $buyerPesananAktifCount = 0;
        $unreadNotifCount = 0;
        $adminPending = ['total' => 0, 'pengajuan_seller' => 0, 'pembayaran' => 0, 'pencairan' => 0, 'komisi' => 0];

        if ($activeUser) {
            if ($activeGuard === 'seller') {
                if (method_exists($activeUser, 'pesanBelumDibaca')) {
                    $unreadChatCount = $activeUser->pesanBelumDibaca(true);
                }
                if (method_exists($activeUser, 'pesananMasukPerluTindakanCount')) {
                    $sellerPesananMasukCount = $activeUser->pesananMasukPerluTindakanCount();
                }
                if (method_exists($activeUser, 'tagihanKomisiBelumDibayarCount')) {
                    $sellerKomisiPendingCount = $activeUser->tagihanKomisiBelumDibayarCount();
                }
                if (method_exists($activeUser, 'unreadNotifikasiCount')) {
                    $unreadNotifCount = $activeUser->unreadNotifikasiCount();
                }
            } elseif ($activeGuard === 'admin') {
                if (method_exists($activeUser, 'adminPendingCounts')) {
                    $adminPending = $activeUser->adminPendingCounts();
                }
            } else {
                if (method_exists($activeUser, 'pesanBelumDibaca')) {
                    $unreadChatCount = $activeUser->pesanBelumDibaca(false);
                }
                if (method_exists($activeUser, 'keranjangCount')) {
                    $buyerKeranjangCount = $activeUser->keranjangCount();
                }
                if (method_exists($activeUser, 'pesananAktifBuyerCount')) {
                    $buyerPesananAktifCount = $activeUser->pesananAktifBuyerCount();
                }
                if (method_exists($activeUser, 'unreadNotifikasiCount')) {
                    $unreadNotifCount = $activeUser->unreadNotifikasiCount();
                }
            }
        }
        $sellerTotalNotif = $unreadChatCount + $sellerPesananMasukCount + $sellerKomisiPendingCount + $unreadNotifCount;
        $buyerTotalNotif = $unreadChatCount + $buyerKeranjangCount + $buyerPesananAktifCount + $unreadNotifCount;
    @endphp

    <!-- NAVBAR -->
    <nav class="navbar navbar-dark navbar-expand-lg navbar-custom">
        <div class="container d-flex flex-wrap align-items-center justify-content-between">
            @if(request()->is('seller*') && $activeUser)
                {{-- ==================== NAVBAR KHUSUS SELLER CENTRE ==================== --}}
                <div class="d-flex align-items-center justify-content-between w-100">
                    <a class="navbar-brand d-flex align-items-center gap-2 m-0" href="{{ route('seller.produk.index') }}">
                        <img src="{{ asset('assets/images/logo.jpeg') }}" alt="SecondSpace Logo" class="navbar-logo">
                        <span>SECOND</span>SPACE
                        <span class="badge rounded-pill ms-1" style="background: rgba(0, 184, 190, 0.18); color: #00B8BE; border: 1px solid rgba(0, 184, 190, 0.4); font-size: 0.68rem; font-weight: 700; letter-spacing: 0.5px; padding: 0.35em 0.7em;">
                            SELLER
                        </span>
                    </a>

                    <!-- Desktop Action (Layar >= 992px): Ke Marketplace & Profil -->
                    <div class="d-none d-lg-flex align-items-center gap-2">
                        <a href="{{ route('pelanggan.katalog') }}" class="glass-btn">
                            <i class="bi bi-grid me-1"></i> Ke Marketplace
                        </a>

                        <!-- Notification Bell Dropdown (Seller Desktop) -->
                        <div class="dropdown notif-dropdown">
                            <button class="notif-bell-btn border-0" type="button" data-bs-toggle="dropdown" aria-expanded="false" id="notifDropdownBtnSeller" title="Pusat Notifikasi">
                                <i class="bi bi-bell fs-5"></i>
                                <span class="notif-badge-pill notif-badge {{ $unreadNotifCount > 0 ? '' : 'd-none' }}">
                                    {{ $unreadNotifCount > 99 ? '99+' : $unreadNotifCount }}
                                </span>
                            </button>
                            <div class="dropdown-menu dropdown-menu-end shadow-lg border-0 rounded-4 p-0 notif-dropdown-menu" style="width: 340px; max-width: 90vw;">
                                <div class="p-3 border-bottom d-flex align-items-center justify-content-between" style="background: #001B27; border-top-left-radius: 1rem; border-top-right-radius: 1rem;">
                                    <div class="fw-bold text-white d-flex align-items-center gap-2">
                                        <i class="bi bi-bell-fill" style="color: #00B8BE;"></i>
                                        <span>Notifikasi Toko</span>
                                        <span class="badge rounded-pill bg-danger notif-badge notif-badge-header {{ $unreadNotifCount > 0 ? '' : 'd-none' }}" style="font-size: 0.68rem;">{{ $unreadNotifCount }} Baru</span>
                                    </div>
                                    <a href="{{ route('seller.notifikasi') }}" class="small text-decoration-none" style="color: #00B8BE; font-size: 0.78rem;">Lihat Semua</a>
                                </div>
                                <div class="notif-feed-container" style="max-height: 320px; overflow-y: auto;">
                                    <div class="p-3 text-center text-muted small"><span class="spinner-border spinner-border-sm me-1 text-primary"></span> Memuat notifikasi...</div>
                                </div>
                                <div class="p-2 border-top text-center bg-light" style="border-bottom-left-radius: 1rem; border-bottom-right-radius: 1rem;">
                                    <a href="{{ route('seller.notifikasi') }}" class="small fw-semibold text-decoration-none" style="color: #005C66;">
                                        Buka Pusat Notifikasi Toko &rarr;
                                    </a>
                                </div>
                            </div>
                        </div>

                        <!-- User Profile Dropdown Seller (Desktop) -->
                        <div class="dropdown">
                            <button class="glass-btn dropdown-toggle border-0" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                <i class="bi bi-shop" style="color: var(--color-primary);"></i> {{ $activeUser->Nama }}
                                <span class="badge bg-danger ms-1" style="font-size: 0.7rem;">Seller</span>
                            </button>
                            <ul class="dropdown-menu dropdown-menu-end shadow border-0 rounded-3 mt-2">
                                <li class="px-3 py-2 text-muted border-bottom" style="font-size: 0.85rem;">
                                    Masuk sebagai <strong>Seller</strong><br>
                                    <small>{{ $activeUser->NoHP_Email }}</small>
                                </li>
                                <li>
                                    <a class="dropdown-item py-2" href="{{ route('seller.pengaturan.index') }}">
                                        <i class="bi bi-gear me-2"></i> Pengaturan Toko
                                    </a>
                                </li>
                                <li>
                                    <a class="dropdown-item py-2" href="{{ route('pelanggan.katalog') }}">
                                        <i class="bi bi-grid me-2"></i> Mode Pembeli
                                    </a>
                                </li>
                                <li>
                                    <form action="{{ route('seller.logout') }}" method="POST" class="m-0">
                                        @csrf
                                        <button type="submit" class="dropdown-item text-danger py-2" style="color: #e11d48 !important;">
                                            <i class="bi bi-box-arrow-right me-2"></i> Logout Seller
                                        </button>
                                    </form>
                                </li>
                            </ul>
                        </div>
                    </div>

                    <!-- Mobile Hamburger Button (Layar < 992px) -->
                    <button class="navbar-toggler border-0 position-relative d-lg-none" type="button" data-bs-toggle="collapse" data-bs-target="#sellerNavbarMenu" aria-controls="sellerNavbarMenu" aria-expanded="false" aria-label="Toggle navigasi seller">
                        <span class="navbar-toggler-icon"></span>
                        <span id="nav-chat-dot-mobile" class="position-absolute top-0 end-0 translate-middle p-1 bg-danger border border-light rounded-circle {{ $sellerTotalNotif > 0 ? '' : 'd-none' }}">
                            <span class="visually-hidden">Notifikasi Baru</span>
                        </span>
                    </button>
                </div>

                <!-- Mobile Menu Collapse (HANYA AKTIF & MUNCUL DI MOBILE SAAT DIKLIK) -->
                <div class="collapse w-100 mt-3 d-lg-none seller-mobile-collapse" id="sellerNavbarMenu">
                    <div class="p-3 rounded-4 shadow-sm" style="background: rgba(0, 27, 39, 0.98); border: 1px solid rgba(0, 184, 190, 0.3);">
                        <div class="d-flex align-items-center gap-3 pb-3 mb-2 border-bottom" style="border-color: rgba(255,255,255,0.12) !important;">
                            <div class="seller-avatar" style="width: 40px; height: 40px; font-size: 1rem;">{{ strtoupper(mb_substr($activeUser->Nama ?? 'S', 0, 1)) }}</div>
                            <div class="overflow-hidden">
                                <div class="fw-bold text-white text-truncate">{{ $activeUser->Nama ?? 'Seller' }}</div>
                                <span class="badge bg-danger" style="font-size: 0.68rem;">Seller</span>
                            </div>
                        </div>

                        <div class="d-grid gap-1">
                            <a href="{{ route('seller.produk.index') }}" class="glass-btn text-start {{ request()->routeIs('seller.produk.index', 'seller.produk.edit') ? 'active' : '' }}">
                                <i class="bi bi-speedometer2 me-2"></i> Dashboard
                            </a>
                            <a href="{{ route('seller.produk.create') }}" class="glass-btn text-start {{ request()->routeIs('seller.produk.create') ? 'active' : '' }}">
                                <i class="bi bi-plus-circle me-2"></i> Tambah Produk
                            </a>
                            <a href="{{ route('seller.pesanan.index') }}" class="glass-btn text-start d-flex justify-content-between align-items-center {{ request()->routeIs('seller.pesanan.*') ? 'active' : '' }}">
                                <span><i class="bi bi-inbox me-2"></i> Pesanan Masuk</span>
                                <span id="nav-pesanan-masuk-badge-seller" class="badge rounded-pill bg-danger {{ $sellerPesananMasukCount > 0 ? '' : 'd-none' }}" style="font-size: 0.72rem; padding: 0.25em 0.55em;">
                                    {{ $sellerPesananMasukCount > 99 ? '99+' : $sellerPesananMasukCount }}
                                </span>
                            </a>
                            <a href="{{ route('seller.pencairan.index') }}" class="glass-btn text-start {{ request()->routeIs('seller.pencairan.*') ? 'active' : '' }}">
                                <i class="bi bi-wallet2 me-2"></i> Saldo &amp; Pencairan
                            </a>
                            <a href="{{ route('seller.komisi.index') }}" class="glass-btn text-start d-flex justify-content-between align-items-center {{ request()->routeIs('seller.komisi.*') ? 'active' : '' }}">
                                <span><i class="bi bi-receipt-cutoff me-2"></i> Komisi COD</span>
                                <span id="nav-komisi-badge-seller" class="badge rounded-pill bg-warning text-dark {{ $sellerKomisiPendingCount > 0 ? '' : 'd-none' }}" style="font-size: 0.72rem; padding: 0.25em 0.55em;">
                                    {{ $sellerKomisiPendingCount > 99 ? '99+' : $sellerKomisiPendingCount }}
                                </span>
                            </a>
                            <a href="{{ route('seller.chat.index') }}" class="glass-btn text-start d-flex justify-content-between align-items-center {{ request()->routeIs('seller.chat.*') ? 'active' : '' }}">
                                <span><i class="bi bi-chat-dots me-2"></i> Pesan Pembeli</span>
                                <span id="nav-chat-badge-seller" class="badge rounded-pill bg-danger {{ $unreadChatCount > 0 ? '' : 'd-none' }}" style="font-size: 0.72rem; padding: 0.25em 0.55em;">
                                    {{ $unreadChatCount > 99 ? '99+' : $unreadChatCount }}
                                </span>
                            </a>
                            <a href="{{ route('seller.notifikasi') }}" class="glass-btn text-start d-flex justify-content-between align-items-center {{ request()->routeIs('seller.notifikasi*') ? 'active' : '' }}">
                                <span><i class="bi bi-bell me-2"></i> Pusat Notifikasi</span>
                                <span class="badge rounded-pill bg-danger notif-badge {{ $unreadNotifCount > 0 ? '' : 'd-none' }}" style="font-size: 0.72rem; padding: 0.25em 0.55em;">
                                    {{ $unreadNotifCount > 99 ? '99+' : $unreadNotifCount }}
                                </span>
                            </a>
                            <a href="{{ route('seller.pengaturan.index') }}" class="glass-btn text-start {{ request()->routeIs('seller.pengaturan.*') ? 'active' : '' }}">
                                <i class="bi bi-gear me-2"></i> Pengaturan Toko
                            </a>

                            <hr class="my-2" style="border-color: rgba(255,255,255,0.12) !important;">

                            <a href="{{ route('pelanggan.katalog') }}" class="glass-btn text-start">
                                <i class="bi bi-grid me-2"></i> Lihat Marketplace
                            </a>
                            <a href="{{ route('pelanggan.pesanan_aktif') }}" class="glass-btn text-start">
                                <i class="bi bi-bag me-2"></i> Belanja Saya
                            </a>

                            <form action="{{ route('seller.logout') }}" method="POST" class="mt-2">
                                @csrf
                                <button type="submit" class="btn btn-outline-danger btn-sm w-100 rounded-pill py-2">
                                    <i class="bi bi-box-arrow-right me-1"></i> Logout Seller
                                </button>
                            </form>
                        </div>
                    </div>
                </div>

            @else
                {{-- ==================== NAVBAR PUBLIK & PELANGGAN ==================== --}}
                <a class="navbar-brand" href="{{ route('home') }}">
                    <img src="{{ asset('assets/images/logo.jpeg') }}" alt="SecondSpace Logo" class="navbar-logo">
                    <span>SECOND</span>SPACE
                </a>

                <button class="navbar-toggler border-0 position-relative" type="button" data-bs-toggle="collapse" data-bs-target="#navbarMenu">
                    <span class="navbar-toggler-icon"></span>
                    <span id="nav-chat-dot-mobile" class="position-absolute top-0 end-0 translate-middle p-1 bg-danger border border-light rounded-circle {{ ($activeGuard === 'admin' ? $adminPending['total'] : ($activeGuard === 'seller' ? $sellerTotalNotif : $buyerTotalNotif)) > 0 ? '' : 'd-none' }}">
                        <span class="visually-hidden">Notifikasi Baru</span>
                    </span>
                </button>

                <div class="collapse navbar-collapse justify-content-end" id="navbarMenu">
                    <div class="d-flex gap-2 flex-lg-row flex-column align-items-lg-center mt-3 mt-lg-0">

                        <a href="{{ route('pelanggan.katalog') }}" class="glass-btn {{ request()->routeIs('pelanggan.katalog') ? 'active' : '' }}">
                            <i class="bi bi-grid"></i> Katalog
                        </a>

                        @if($activeUser)
                            @if($activeGuard === 'admin')
                                <a href="{{ route('admin.dashboard') }}" class="glass-btn position-relative {{ request()->is('admin*') ? 'active' : '' }}">
                                    <i class="bi bi-speedometer2"></i> Panel Admin
                                    <span class="badge rounded-pill bg-danger ms-1 {{ $adminPending['total'] > 0 ? '' : 'd-none' }}" style="font-size: 0.72rem; padding: 0.25em 0.55em;">
                                        {{ $adminPending['total'] > 99 ? '99+' : $adminPending['total'] }}
                                    </span>
                                </a>

                                <!-- User Profile Dropdown Admin -->
                                <div class="dropdown">
                                    <button class="glass-btn dropdown-toggle border-0" type="button" data-bs-toggle="dropdown">
                                        <i class="bi bi-shield-check" style="color: var(--color-primary);"></i> {{ $activeUser->Nama }}
                                        <span class="badge bg-danger ms-1" style="font-size: 0.7rem;">Admin</span>
                                    </button>
                                    <ul class="dropdown-menu dropdown-menu-end shadow border-0 rounded-3 mt-2">
                                        <li class="px-3 py-2 text-muted border-bottom" style="font-size: 0.85rem;">
                                            Masuk sebagai <strong>Administrator</strong><br>
                                            <small>{{ $activeUser->NoHP_Email }}</small>
                                        </li>
                                        <li>
                                            <a class="dropdown-item py-2" href="{{ route('admin.dashboard') }}">
                                                <i class="bi bi-speedometer2 me-2"></i> Dashboard Admin
                                            </a>
                                        </li>
                                        <li>
                                            <a class="dropdown-item py-2 d-flex justify-content-between align-items-center" href="{{ route('admin.seller.index') }}">
                                                <span><i class="bi bi-person-check me-2"></i> Pengajuan Seller</span>
                                                @if($adminPending['pengajuan_seller'] > 0)
                                                    <span class="badge rounded-pill bg-danger" style="font-size: 0.7rem;">{{ $adminPending['pengajuan_seller'] }}</span>
                                                @endif
                                            </a>
                                        </li>
                                        <li>
                                            <a class="dropdown-item py-2 d-flex justify-content-between align-items-center" href="{{ route('admin.pembayaran.index') }}">
                                                <span><i class="bi bi-credit-card me-2"></i> Verifikasi Pembayaran</span>
                                                @if($adminPending['pembayaran'] > 0)
                                                    <span class="badge rounded-pill bg-danger" style="font-size: 0.7rem;">{{ $adminPending['pembayaran'] }}</span>
                                                @endif
                                            </a>
                                        </li>
                                        <li>
                                            <a class="dropdown-item py-2 d-flex justify-content-between align-items-center" href="{{ route('admin.pencairan.index') }}">
                                                <span><i class="bi bi-cash-coin me-2"></i> Pencairan Seller</span>
                                                @if($adminPending['pencairan'] > 0)
                                                    <span class="badge rounded-pill bg-danger" style="font-size: 0.7rem;">{{ $adminPending['pencairan'] }}</span>
                                                @endif
                                            </a>
                                        </li>
                                        <li>
                                            <a class="dropdown-item py-2 d-flex justify-content-between align-items-center" href="{{ route('admin.komisi.index') }}">
                                                <span><i class="bi bi-receipt-cutoff me-2"></i> Komisi COD</span>
                                                @if($adminPending['komisi'] > 0)
                                                    <span class="badge rounded-pill bg-danger" style="font-size: 0.7rem;">{{ $adminPending['komisi'] }}</span>
                                                @endif
                                            </a>
                                        </li>
                                        <li>
                                            <a class="dropdown-item py-2" href="{{ route('admin.produk.index') }}">
                                                <i class="bi bi-box me-2"></i> Data Produk
                                            </a>
                                        </li>
                                        <li>
                                            <form action="{{ route('admin.logout') }}" method="POST" class="m-0">
                                                @csrf
                                                <button type="submit" class="dropdown-item text-danger py-2" style="color: #e11d48 !important;">
                                                    <i class="bi bi-box-arrow-right me-2"></i> Logout Admin
                                                </button>
                                            </form>
                                        </li>
                                    </ul>
                                </div>
                            @elseif($activeGuard === 'seller')
                                {{-- Seller sedang di halaman publik/katalog -> Berikan tombol cepat kembali ke Dashboard Toko --}}
                                <a href="{{ route('seller.produk.index') }}" class="glass-btn position-relative">
                                    <i class="bi bi-shop text-danger"></i> Toko Saya
                                    <span id="nav-seller-toko-badge" class="badge rounded-pill bg-danger ms-1 {{ $sellerPesananMasukCount > 0 ? '' : 'd-none' }}" style="font-size: 0.72rem; padding: 0.25em 0.55em;">
                                        {{ $sellerPesananMasukCount > 99 ? '99+' : $sellerPesananMasukCount }}
                                    </span>
                                </a>
                                <a href="{{ route('seller.chat.index') }}" class="glass-btn position-relative">
                                    <i class="bi bi-chat-dots"></i> Pesan
                                    <span id="nav-chat-badge-seller" class="badge rounded-pill bg-danger ms-1 {{ $unreadChatCount > 0 ? '' : 'd-none' }}" style="font-size: 0.72rem; padding: 0.25em 0.55em;">
                                        {{ $unreadChatCount > 99 ? '99+' : $unreadChatCount }}
                                    </span>
                                </a>

                                <!-- Notification Bell Dropdown (Seller on Public Mode) -->
                                <div class="dropdown notif-dropdown">
                                    <button class="notif-bell-btn border-0" type="button" data-bs-toggle="dropdown" aria-expanded="false" title="Pusat Notifikasi">
                                        <i class="bi bi-bell fs-5"></i>
                                        <span class="notif-badge-pill notif-badge {{ $unreadNotifCount > 0 ? '' : 'd-none' }}">
                                            {{ $unreadNotifCount > 99 ? '99+' : $unreadNotifCount }}
                                        </span>
                                    </button>
                                    <div class="dropdown-menu dropdown-menu-end shadow-lg border-0 rounded-4 p-0 notif-dropdown-menu" style="width: 340px; max-width: 90vw;">
                                        <div class="p-3 border-bottom d-flex align-items-center justify-content-between" style="background: #001B27; border-top-left-radius: 1rem; border-top-right-radius: 1rem;">
                                            <div class="fw-bold text-white d-flex align-items-center gap-2">
                                                <i class="bi bi-bell-fill" style="color: #00B8BE;"></i>
                                                <span>Notifikasi Toko</span>
                                                <span class="badge rounded-pill bg-danger notif-badge notif-badge-header {{ $unreadNotifCount > 0 ? '' : 'd-none' }}" style="font-size: 0.68rem;">{{ $unreadNotifCount }} Baru</span>
                                            </div>
                                            <a href="{{ route('seller.notifikasi') }}" class="small text-decoration-none" style="color: #00B8BE; font-size: 0.78rem;">Lihat Semua</a>
                                        </div>
                                        <div class="notif-feed-container" style="max-height: 320px; overflow-y: auto;">
                                            <div class="p-3 text-center text-muted small"><span class="spinner-border spinner-border-sm me-1 text-primary"></span> Memuat notifikasi...</div>
                                        </div>
                                        <div class="p-2 border-top text-center bg-light" style="border-bottom-left-radius: 1rem; border-bottom-right-radius: 1rem;">
                                            <a href="{{ route('seller.notifikasi') }}" class="small fw-semibold text-decoration-none" style="color: #005C66;">
                                                Buka Pusat Notifikasi Toko &rarr;
                                            </a>
                                        </div>
                                    </div>
                                </div>

                                <!-- User Profile Dropdown Seller -->
                                <div class="dropdown">
                                    <button class="glass-btn dropdown-toggle border-0" type="button" data-bs-toggle="dropdown">
                                        <i class="bi bi-shop"></i> {{ $activeUser->Nama }}
                                        <span class="badge bg-danger ms-1" style="font-size: 0.7rem;">Seller</span>
                                    </button>
                                    <ul class="dropdown-menu dropdown-menu-end shadow border-0 rounded-3 mt-2">
                                        <li class="px-3 py-2 text-muted border-bottom" style="font-size: 0.85rem;">
                                            Masuk sebagai <strong>Seller</strong><br>
                                            <small>{{ $activeUser->NoHP_Email }}</small>
                                        </li>
                                        <li>
                                            <a class="dropdown-item py-2" href="{{ route('seller.produk.index') }}">
                                                <i class="bi bi-speedometer2 me-2"></i> Dashboard Seller
                                            </a>
                                        </li>
                                        <li>
                                            <a class="dropdown-item py-2 d-flex justify-content-between align-items-center" href="{{ route('seller.notifikasi') }}">
                                                <span><i class="bi bi-bell me-2"></i> Pusat Notifikasi</span>
                                                @if($unreadNotifCount > 0)
                                                    <span class="badge rounded-pill bg-danger" style="font-size: 0.7rem;">{{ $unreadNotifCount }}</span>
                                                @endif
                                            </a>
                                        </li>
                                        <li>
                                            <a class="dropdown-item py-2 d-flex justify-content-between align-items-center" href="{{ route('seller.pesanan.index') }}">
                                                <span><i class="bi bi-inbox me-2"></i> Pesanan Masuk</span>
                                                @if($sellerPesananMasukCount > 0)
                                                    <span class="badge rounded-pill bg-danger" style="font-size: 0.7rem;">{{ $sellerPesananMasukCount }}</span>
                                                @endif
                                            </a>
                                        </li>
                                        <li>
                                            <a class="dropdown-item py-2 d-flex justify-content-between align-items-center" href="{{ route('seller.komisi.index') }}">
                                                <span><i class="bi bi-receipt-cutoff me-2"></i> Komisi COD</span>
                                                @if($sellerKomisiPendingCount > 0)
                                                    <span class="badge rounded-pill bg-warning text-dark" style="font-size: 0.7rem;">{{ $sellerKomisiPendingCount }}</span>
                                                @endif
                                            </a>
                                        </li>
                                        <li>
                                            <a class="dropdown-item py-2" href="{{ route('seller.pengaturan.index') }}">
                                                <i class="bi bi-gear me-2"></i> Pengaturan Toko
                                            </a>
                                        </li>
                                        <li>
                                            <form action="{{ route('seller.logout') }}" method="POST" class="m-0">
                                                @csrf
                                                <button type="submit" class="dropdown-item text-danger py-2" style="color: #e11d48 !important;">
                                                    <i class="bi bi-box-arrow-right me-2"></i> Logout Seller
                                                </button>
                                            </form>
                                        </li>
                                    </ul>
                                </div>
                            @else
                                {{-- Guard Web (Pelanggan) --}}
                                <a href="{{ route('pelanggan.chat.index') }}" class="glass-btn position-relative {{ request()->routeIs('pelanggan.chat.*') ? 'active' : '' }}">
                                    <i class="bi bi-chat-dots"></i> Pesan
                                    <span id="nav-chat-badge-buyer" class="badge rounded-pill bg-danger ms-1 {{ $unreadChatCount > 0 ? '' : 'd-none' }}" style="font-size: 0.72rem; padding: 0.25em 0.55em;">
                                        {{ $unreadChatCount > 99 ? '99+' : $unreadChatCount }}
                                    </span>
                                </a>
                                <a href="{{ route('pelanggan.keranjang') }}" class="glass-btn position-relative {{ request()->routeIs('pelanggan.keranjang') ? 'active' : '' }}">
                                    <i class="bi bi-cart3"></i> Keranjang
                                    <span id="nav-keranjang-badge-buyer" class="badge rounded-pill bg-danger ms-1 {{ $buyerKeranjangCount > 0 ? '' : 'd-none' }}" style="font-size: 0.72rem; padding: 0.25em 0.55em;">
                                        {{ $buyerKeranjangCount > 99 ? '99+' : $buyerKeranjangCount }}
                                    </span>
                                </a>
                                <a href="{{ route('pelanggan.pesanan_aktif') }}" class="glass-btn position-relative {{ request()->routeIs('pelanggan.pesanan_aktif') ? 'active' : '' }}">
                                    <i class="bi bi-clock-history text-warning"></i> Pesanan Aktif
                                    <span id="nav-pesanan-aktif-badge-buyer" class="badge rounded-pill bg-warning text-dark ms-1 {{ $buyerPesananAktifCount > 0 ? '' : 'd-none' }}" style="font-size: 0.72rem; padding: 0.25em 0.55em;">
                                        {{ $buyerPesananAktifCount > 99 ? '99+' : $buyerPesananAktifCount }}
                                    </span>
                                </a>
                                <a href="{{ route('pelanggan.pesanan') }}" class="glass-btn {{ request()->routeIs('pelanggan.pesanan') ? 'active' : '' }}">
                                    <i class="bi bi-archive"></i> Riwayat Pesanan
                                </a>

                                <!-- Notification Bell Dropdown (Pelanggan) -->
                                <div class="dropdown notif-dropdown">
                                    <button class="notif-bell-btn border-0" type="button" data-bs-toggle="dropdown" aria-expanded="false" id="notifDropdownBtnBuyer" title="Pusat Notifikasi">
                                        <i class="bi bi-bell fs-5"></i>
                                        <span class="notif-badge-pill notif-badge {{ $unreadNotifCount > 0 ? '' : 'd-none' }}">
                                            {{ $unreadNotifCount > 99 ? '99+' : $unreadNotifCount }}
                                        </span>
                                    </button>
                                    <div class="dropdown-menu dropdown-menu-end shadow-lg border-0 rounded-4 p-0 notif-dropdown-menu" style="width: 340px; max-width: 90vw;">
                                        <div class="p-3 border-bottom d-flex align-items-center justify-content-between" style="background: #001B27; border-top-left-radius: 1rem; border-top-right-radius: 1rem;">
                                            <div class="fw-bold text-white d-flex align-items-center gap-2">
                                                <i class="bi bi-bell-fill" style="color: #00B8BE;"></i>
                                                <span>Notifikasi</span>
                                                <span class="badge rounded-pill bg-danger notif-badge notif-badge-header {{ $unreadNotifCount > 0 ? '' : 'd-none' }}" style="font-size: 0.68rem;">{{ $unreadNotifCount }} Baru</span>
                                            </div>
                                            <a href="{{ route('pelanggan.notifikasi') }}" class="small text-decoration-none" style="color: #00B8BE; font-size: 0.78rem;">Lihat Semua</a>
                                        </div>
                                        <div class="notif-feed-container" style="max-height: 320px; overflow-y: auto;">
                                            <div class="p-3 text-center text-muted small"><span class="spinner-border spinner-border-sm me-1 text-primary"></span> Memuat notifikasi...</div>
                                        </div>
                                        <div class="p-2 border-top text-center bg-light" style="border-bottom-left-radius: 1rem; border-bottom-right-radius: 1rem;">
                                            <a href="{{ route('pelanggan.notifikasi') }}" class="small fw-semibold text-decoration-none" style="color: #005C66;">
                                                Buka Pusat Notifikasi &rarr;
                                            </a>
                                        </div>
                                    </div>
                                </div>

                                <!-- User Profile Dropdown Pelanggan -->
                                <div class="dropdown">
                                    <button class="glass-btn dropdown-toggle border-0" type="button" data-bs-toggle="dropdown">
                                        <i class="bi bi-person-circle"></i> {{ $activeUser->Nama }}
                                        <span class="badge bg-danger ms-1" style="font-size: 0.7rem;">Pelanggan</span>
                                    </button>
                                    <ul class="dropdown-menu dropdown-menu-end shadow border-0 rounded-3 mt-2">
                                        <li class="px-3 py-2 text-muted border-bottom" style="font-size: 0.85rem;">
                                            Masuk sebagai <strong>Pelanggan</strong><br>
                                            <small>{{ $activeUser->NoHP_Email }}</small>
                                        </li>
                                        <li>
                                            <a class="dropdown-item py-2" href="{{ route('pelanggan.profil') }}">
                                                <i class="bi bi-person-gear me-2"></i> Profil Saya
                                            </a>
                                        </li>
                                        <li>
                                            <a class="dropdown-item py-2 d-flex justify-content-between align-items-center" href="{{ route('pelanggan.notifikasi') }}">
                                                <span><i class="bi bi-bell me-2"></i> Pusat Notifikasi</span>
                                                @if($unreadNotifCount > 0)
                                                    <span class="badge rounded-pill bg-danger" style="font-size: 0.7rem;">{{ $unreadNotifCount }}</span>
                                                @endif
                                            </a>
                                        </li>
                                        <li>
                                            <a class="dropdown-item py-2" href="{{ route('pelanggan.seller.status') }}">
                                                <i class="bi bi-shop me-2"></i> Daftar Jadi Seller
                                            </a>
                                        </li>
                                        <li>
                                            <form action="{{ route('logout') }}" method="POST" class="m-0">
                                                @csrf
                                                <button type="submit" class="dropdown-item text-danger py-2" style="color: #e11d48 !important;">
                                                    <i class="bi bi-box-arrow-right me-2"></i> Logout
                                                </button>
                                            </form>
                                        </li>
                                    </ul>
                                </div>
                            @endif
                        @else
                            {{-- Tombol Login Publik untuk Pelanggan / Seller --}}
                            <a href="{{ route('login') }}" class="glass-btn glass-btn-primary">
                                <i class="bi bi-box-arrow-in-right"></i> Login / Register
                            </a>
                        @endif

                    </div>
                </div>
            @endif
        </div>
    </nav>

    <!-- FLASH MESSAGES -->
    @if(session('success') || session('error') || session('info') || (isset($errors) && $errors->any()))
    <div class="container mt-3">
        @if(session('success'))
            <div class="alert alert-success alert-custom alert-dismissible fade show" role="alert">
                <i class="bi bi-check-circle-fill fs-5 text-success"></i>
                <div class="flex-grow-1">{{ session('success') }}</div>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @if(session('error'))
            <div class="alert alert-danger alert-custom alert-dismissible fade show" role="alert" style="background: #fff1f2; border: 1px solid #fecdd3; color: #9f1239;">
                <i class="bi bi-exclamation-triangle-fill fs-5" style="color: #e11d48;"></i>
                <div class="flex-grow-1">{{ session('error') }}</div>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @if(session('info'))
            <div class="alert alert-info alert-custom alert-dismissible fade show" role="alert">
                <i class="bi bi-info-circle-fill fs-5 text-info"></i>
                <div class="flex-grow-1">{{ session('info') }}</div>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @if(isset($errors) && $errors->any())
            <div class="alert alert-warning alert-custom alert-dismissible fade show" role="alert">
                <i class="bi bi-exclamation-circle-fill fs-5 text-warning"></i>
                <div class="flex-grow-1">
                    <ul class="mb-0 ps-3">
                        @foreach($errors->all() as $err)
                            <li>{{ $err }}</li>
                        @endforeach
                    </ul>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif
    </div>
    @endif

    <!-- MAIN CONTENT -->
    <main class="flex-grow-1">
        @yield('content')
    </main>

    <!-- FOOTER (Disembunyikan pada room chat, pesan, admin backoffice, seller centre, checkout, dan auth agar tampilan bersih dan fokus) -->
    @php
        $sembunyikanFooter = View::hasSection('no_footer') || request()->routeIs([
            '*.chat.*',
            'chat.*',
            'admin.*',
            'seller.*',
            'login',
            'register',
            'register.seller',
            'admin.login',
            'pelanggan.checkout.*',
        ]);
    @endphp

    @unless($sembunyikanFooter)
    <footer class="footer">
        <div class="container text-center">
            <a href="{{ route('home') }}" class="d-inline-block mb-3 text-decoration-none">
                <img src="{{ asset('assets/images/logo.jpeg') }}" alt="SecondSpace" class="rounded-3" style="width: 44px; height: 44px; object-fit: cover; border: 1.5px solid rgba(0, 184, 190, 0.35); box-shadow: 0 4px 15px rgba(0, 184, 190, 0.25);">
            </a>
            <p class="mb-1 fw-bold text-white">SecondSpace &copy; {{ date('Y') }}</p>
            <p class="mb-0" style="color: #89a9b7; font-size: 0.85rem;">Marketplace Pre-Loved & Thrift Terkurasi &bull; Elektronik &bull; Fashion &bull; Peralatan Rumah Tangga &bull; ATK</p>
        </div>
    </footer>
    @endunless

    <!-- Bootstrap Bundle JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <!-- Swiper JS -->
    <script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>

    @if($activeUser && !request()->routeIs('*.chat.show'))
    <script>
    (function() {
        const unreadUrl = '{{ $activeGuard === "seller" ? route("seller.chat.unread_count") : ($activeGuard === "web" ? route("pelanggan.chat.unread_count") : "") }}';
        if (!unreadUrl) return;

        function setBadge(selector, val) {
            const count = parseInt(val || 0);
            const txt = count > 99 ? '99+' : count;
            document.querySelectorAll(selector).forEach(b => {
                if (count > 0) {
                    b.textContent = txt;
                    b.classList.remove('d-none');
                } else {
                    b.classList.add('d-none');
                }
            });
            return count;
        }

        async function checkUnreadChat() {
            try {
                const r = await fetch(unreadUrl, { headers: { 'Accept': 'application/json' } });
                if (!r.ok) return;
                const d = await r.json();

                let totalBadge = 0;
                totalBadge += setBadge('#nav-chat-badge-buyer, #nav-chat-badge-seller, #sidebar-chat-badge-seller', d.unread);

                if (d.is_seller) {
                    totalBadge += setBadge('#sidebar-pesanan-badge-seller, #nav-pesanan-masuk-badge-seller, #nav-seller-toko-badge', d.pesanan_masuk);
                    totalBadge += setBadge('#sidebar-komisi-badge-seller, #nav-komisi-badge-seller', d.komisi_pending);
                } else {
                    totalBadge += setBadge('#nav-keranjang-badge-buyer', d.keranjang);
                    totalBadge += setBadge('#nav-pesanan-aktif-badge-buyer', d.pesanan_aktif);
                }

                const dot = document.getElementById('nav-chat-dot-mobile');
                if (dot) {
                    if (totalBadge > 0) dot.classList.remove('d-none');
                    else dot.classList.add('d-none');
                }
            } catch (e) {}
        }

        const notifFeedUrl = '{{ route("notifikasi.feed") }}?as={{ $activeGuard === "seller" ? "seller" : "web" }}';

        async function checkNotifikasi(populateFeed = false) {
            try {
                const r = await fetch(notifFeedUrl, { headers: { 'Accept': 'application/json' } });
                if (!r.ok) return;
                const d = await r.json();

                const unread = parseInt(d.unread || 0);
                const txt = unread > 99 ? '99+' : unread;

                document.querySelectorAll('.notif-badge').forEach(b => {
                    if (unread > 0) {
                        if (b.textContent.includes('Baru')) {
                            b.textContent = `${unread} Baru`;
                        } else {
                            b.textContent = txt;
                        }
                        b.classList.remove('d-none');
                    } else {
                        b.classList.add('d-none');
                    }
                });

                if (populateFeed) {
                    renderNotifDropdown(d.items);
                }
            } catch (e) {}
        }

        function renderNotifDropdown(items) {
            const containers = document.querySelectorAll('.notif-feed-container');
            if (!containers.length) return;

            let html = '';
            if (!items || items.length === 0) {
                html = '<div class="p-4 text-center text-muted small"><i class="bi bi-bell-slash fs-4 d-block mb-1 text-secondary"></i>Belum ada notifikasi</div>';
            } else {
                items.forEach(it => {
                    html += `
                        <a href="${it.url}" class="d-flex align-items-start gap-2 p-2.5 px-3 border-bottom text-decoration-none text-dark notif-item ${it.is_read ? '' : 'bg-light'}" style="transition: background 0.15s ease;">
                            <div class="rounded-circle d-flex align-items-center justify-content-center flex-shrink-0 mt-1" style="width: 32px; height: 32px; background: rgba(0, 184, 190, 0.15); color: ${it.icon_color || '#00B8BE'}; font-size: 0.95rem;">
                                <i class="bi ${it.icon || 'bi-bell-fill'}"></i>
                            </div>
                            <div class="overflow-hidden flex-grow-1">
                                <div class="d-flex align-items-center justify-content-between gap-1 mb-0.5">
                                    <div class="fw-bold small text-truncate" style="color: #001B27; max-width: 170px;">${it.judul}</div>
                                    <small class="text-muted" style="font-size: 0.68rem;">${it.waktu}</small>
                                </div>
                                <div class="text-muted small text-truncate" style="font-size: 0.78rem; line-height: 1.3;">${it.pesan}</div>
                            </div>
                            ${!it.is_read ? '<span class="badge rounded-circle p-1 bg-primary mt-2" style="width: 6px; height: 6px;"></span>' : ''}
                        </a>
                    `;
                });
            }

            containers.forEach(c => { c.innerHTML = html; });
        }

        document.querySelectorAll('.notif-dropdown').forEach(dd => {
            dd.addEventListener('show.bs.dropdown', function() {
                checkNotifikasi(true);
            });
        });

        // Polling berkala
        setInterval(() => {
            if (!document.hidden) {
                checkUnreadChat();
                checkNotifikasi(false);
            }
        }, 12000);
    })();
    </script>
    @endif
    @stack('scripts')
</body>
</html>