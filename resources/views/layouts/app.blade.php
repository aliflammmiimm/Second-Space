<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'SecondSpace - Pre-Loved & Thrift Marketplace')</title>
    
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
            gap: 8px;
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
            }
        }
    @endphp

    <!-- NAVBAR -->
    <nav class="navbar navbar-dark navbar-expand-lg navbar-custom">
        <div class="container">
            <a class="navbar-brand" href="{{ route('home') }}">
                <i class="bi bi-box-seam"></i> SECOND<span>SPACE</span>
            </a>

            <button class="navbar-toggler border-0" type="button" data-bs-toggle="collapse" data-bs-target="#navbarMenu">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse justify-content-end" id="navbarMenu">
                <div class="d-flex gap-2 flex-lg-row flex-column align-items-lg-center mt-3 mt-lg-0">

                    <a href="{{ route('pelanggan.katalog') }}" class="glass-btn {{ request()->routeIs('pelanggan.katalog') ? 'active' : '' }}">
                        <i class="bi bi-grid"></i> Katalog
                    </a>

                    @if($activeUser)
                        @if($activeGuard === 'admin')
                            <a href="{{ route('admin.dashboard') }}" class="glass-btn {{ request()->is('admin*') ? 'active' : '' }}">
                                <i class="bi bi-speedometer2"></i> Panel Admin
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
                            <a href="{{ route('seller.produk.index') }}" class="glass-btn {{ request()->routeIs('seller.produk.*') ? 'active' : '' }}">
                                <i class="bi bi-shop text-danger"></i> Toko Saya
                            </a>
                            <a href="{{ route('seller.pesanan.index') }}" class="glass-btn {{ request()->routeIs('seller.pesanan.*') ? 'active' : '' }}">
                                <i class="bi bi-inbox"></i> Pesanan Masuk
                            </a>

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
                                        <a class="dropdown-item py-2" href="{{ route('seller.produk.create') }}">
                                            <i class="bi bi-plus-circle me-2"></i> Tambah Produk
                                        </a>
                                    </li>
                                    <li>
                                        <a class="dropdown-item py-2" href="{{ route('seller.pencairan.index') }}">
                                            <i class="bi bi-wallet2 me-2"></i> Saldo & Pencairan
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
                            <a href="{{ route('pelanggan.keranjang') }}" class="glass-btn {{ request()->routeIs('pelanggan.keranjang') ? 'active' : '' }}">
                                <i class="bi bi-cart3"></i> Keranjang
                            </a>
                            <a href="{{ route('pelanggan.pesanan_aktif') }}" class="glass-btn {{ request()->routeIs('pelanggan.pesanan_aktif') ? 'active' : '' }}">
                                <i class="bi bi-clock-history text-warning"></i> Pesanan Aktif
                            </a>
                            <a href="{{ route('pelanggan.pesanan') }}" class="glass-btn {{ request()->routeIs('pelanggan.pesanan') ? 'active' : '' }}">
                                <i class="bi bi-archive"></i> Riwayat Pesanan
                            </a>

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
                        {{-- Tombol Login Publik untuk Pelanggan / Seller (Tanpa tombol admin) --}}
                        <a href="{{ route('login') }}" class="glass-btn glass-btn-primary">
                            <i class="bi bi-box-arrow-in-right"></i> Login / Register
                        </a>
                    @endif

                </div>
            </div>
        </div>
    </nav>

    <!-- FLASH MESSAGES -->
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

    <!-- MAIN CONTENT -->
    <main class="flex-grow-1">
        @yield('content')
    </main>

    <!-- FOOTER -->
    <footer class="footer">
        <div class="container text-center">
            <p class="mb-1 fw-bold text-white">SecondSpace &copy; {{ date('Y') }}</p>
            <p class="mb-0" style="color: #89a9b7; font-size: 0.85rem;">Marketplace Pre-Loved & Thrift Terkurasi &bull; Elektronik &bull; Fashion &bull; Peralatan Rumah Tangga &bull; ATK</p>
        </div>
    </footer>

    <!-- Bootstrap Bundle JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <!-- Swiper JS -->
    <script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>
    @stack('scripts')
</body>
</html>