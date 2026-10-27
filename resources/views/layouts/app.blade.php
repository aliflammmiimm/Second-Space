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
            --primary-accent: #e10600;
            --primary-hover: #ff1a1a;
            --dark-bg: #0a0a0c;
            --card-dark: rgba(22, 22, 28, 0.85);
            --border-glass: rgba(255, 255, 255, 0.1);
        }

        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background-color: #f8fafc;
            color: #1e293b;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }

        /* NAVBAR */
        .navbar-custom {
            background: rgba(10, 10, 12, 0.94);
            backdrop-filter: blur(16px);
            -webkit-backdrop-filter: blur(16px);
            border-bottom: 1px solid rgba(255, 255, 255, 0.08);
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

        .navbar-brand span {
            color: var(--primary-accent);
        }

        /* GLASS BUTTON */
        .glass-btn {
            position: relative;
            padding: 8px 18px;
            border-radius: 50px;
            background: rgba(255, 255, 255, 0.08);
            border: 1px solid rgba(255, 255, 255, 0.18);
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
            box-shadow: 0 8px 25px rgba(225, 6, 0, 0.25);
            background: rgba(255, 255, 255, 0.14);
            border-color: rgba(255, 255, 255, 0.35);
            color: #fff;
        }

        .glass-btn-primary {
            background: var(--primary-accent);
            border-color: var(--primary-accent);
        }

        .glass-btn-primary:hover {
            background: var(--primary-hover);
            box-shadow: 0 8px 25px rgba(225, 6, 0, 0.45);
        }

        .glass-btn.active {
            background: rgba(225, 6, 0, 0.2);
            border-color: rgba(225, 6, 0, 0.5);
            color: #fff;
        }

        /* ALERTS */
        .alert-custom {
            border-radius: 14px;
            border: none;
            box-shadow: 0 10px 30px rgba(0,0,0,0.06);
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 14px 20px;
        }

        /* CARD STYLES */
        .custom-card {
            background: #ffffff;
            border: 1px solid rgba(0, 0, 0, 0.06);
            border-radius: 18px;
            box-shadow: 0 8px 24px rgba(0, 0, 0, 0.04);
            transition: all 0.3s ease;
        }

        .custom-card:hover {
            box-shadow: 0 14px 35px rgba(0, 0, 0, 0.08);
        }

        .footer {
            margin-top: auto;
            background: #0a0a0c;
            color: #94a3b8;
            padding: 30px 0;
            border-top: 1px solid rgba(255, 255, 255, 0.06);
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
            border: 1px solid #e2e8f0;
            color: #0f172a;
            font-weight: 600;
            padding: 7px 14px;
            margin: 0 2px;
            transition: all 0.2s ease;
            box-shadow: 0 1px 3px rgba(0,0,0,0.05);
        }
        .page-item.active .page-link {
            background-color: #0f172a !important;
            border-color: #0f172a !important;
            color: #fff !important;
        }
        .page-item.disabled .page-link {
            color: #94a3b8;
            background-color: #f8fafc;
            border-color: #e2e8f0;
        }
        .page-item .page-link:hover {
            background-color: #f1f5f9;
            color: #e10600;
        }
    </style>
    @stack('styles')
</head>
<body>

    <!-- NAVBAR -->
    <nav class="navbar navbar-dark navbar-expand-lg navbar-custom">
        <div class="container">
            <a class="navbar-brand" href="{{ route('home') }}">
                <i class="bi bi-box-seam text-danger"></i> SECOND<span>SPACE</span>
            </a>

            <button class="navbar-toggler border-0" type="button" data-bs-toggle="collapse" data-bs-target="#navbarMenu">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse justify-content-end" id="navbarMenu">
                <div class="d-flex gap-2 flex-lg-row flex-column align-items-lg-center mt-3 mt-lg-0">

                    <a href="{{ route('pelanggan.katalog') }}" class="glass-btn {{ request()->routeIs('pelanggan.katalog') ? 'active' : '' }}">
                        <i class="bi bi-grid"></i> Katalog
                    </a>

                    @auth
                        @if(Auth::user()->isAdmin())
                            <a href="{{ route('admin.dashboard') }}" class="glass-btn {{ request()->is('admin*') ? 'active' : '' }}">
                                <i class="bi bi-speedometer2"></i> Panel Admin
                            </a>
                        @else
                            @if(Auth::user()->isSeller())
                                <a href="{{ route('seller.produk.index') }}" class="glass-btn {{ request()->routeIs('seller.produk.*') ? 'active' : '' }}">
                                    <i class="bi bi-shop text-danger"></i> Produk Saya
                                </a>
                                <a href="{{ route('seller.pesanan.index') }}" class="glass-btn {{ request()->routeIs('seller.pesanan.*') ? 'active' : '' }}">
                                    <i class="bi bi-inbox"></i> Pesanan Masuk
                                </a>
                            @endif
                            <a href="{{ route('pelanggan.keranjang') }}" class="glass-btn {{ request()->routeIs('pelanggan.keranjang') ? 'active' : '' }}">
                                <i class="bi bi-cart3"></i> Keranjang
                            </a>
                            <a href="{{ route('pelanggan.pesanan_aktif') }}" class="glass-btn {{ request()->routeIs('pelanggan.pesanan_aktif') ? 'active' : '' }}">
                                <i class="bi bi-clock-history text-warning"></i> Pesanan Aktif
                            </a>
                            <a href="{{ route('pelanggan.pesanan') }}" class="glass-btn {{ request()->routeIs('pelanggan.pesanan') ? 'active' : '' }}">
                                <i class="bi bi-archive"></i> Riwayat Pesanan
                            </a>
                        @endif

                        <!-- User Profile Dropdown -->
                        <div class="dropdown">
                            <button class="glass-btn dropdown-toggle border-0" type="button" data-bs-toggle="dropdown">
                                <i class="bi bi-person-circle"></i> {{ Auth::user()->Nama }}
                                <span class="badge bg-danger ms-1" style="font-size: 0.7rem;">{{ Auth::user()->isSeller() ? 'Seller' : Auth::user()->Role }}</span>
                            </button>
                            <ul class="dropdown-menu dropdown-menu-end shadow border-0 rounded-3 mt-2">
                                <li class="px-3 py-2 text-muted border-bottom" style="font-size: 0.85rem;">
                                    Masuk sebagai <strong>{{ Auth::user()->isSeller() ? 'Seller' : Auth::user()->Role }}</strong><br>
                                    <small>{{ Auth::user()->NoHP_Email }}</small>
                                </li>
                                @if(Auth::user()->isSeller())
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
                                @elseif(Auth::user()->isPelanggan())
                                    <li>
                                        <a class="dropdown-item py-2" href="{{ route('pelanggan.seller.status') }}">
                                            <i class="bi bi-shop me-2"></i> Jadi Seller
                                        </a>
                                    </li>
                                @endif
                                <li>
                                    <form action="{{ route('logout') }}" method="POST" class="m-0">
                                        @csrf
                                        <button type="submit" class="dropdown-item text-danger py-2">
                                            <i class="bi bi-box-arrow-right me-2"></i> Logout
                                        </button>
                                    </form>
                                </li>
                            </ul>
                        </div>
                    @else
                        <a href="{{ route('login') }}" class="glass-btn glass-btn-primary">
                            <i class="bi bi-box-arrow-in-right"></i> Login / Register
                        </a>
                    @endauth

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
            <div class="alert alert-danger alert-custom alert-dismissible fade show" role="alert">
                <i class="bi bi-exclamation-triangle-fill fs-5 text-danger"></i>
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
            <p class="mb-0 text-muted" style="font-size: 0.85rem;">Marketplace Pre-Loved & Thrift Terkurasi &bull; Elektronik &bull; Fashion &bull; Peralatan Rumah Tangga &bull; ATK</p>
        </div>
    </footer>

    <!-- Bootstrap Bundle JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <!-- Swiper JS -->
    <script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>
    @stack('scripts')
</body>
</html>