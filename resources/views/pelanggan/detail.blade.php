@extends('layouts.app')

@section('title', $produk->NamaProduk . ' - SecondSpace')

@push('styles')
<style>
    .product-action-group {
        margin-top: 1.25rem;
    }
    .btn-product-action {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 0.45rem;
        font-size: 0.88rem;
        font-weight: 600;
        padding: 0.55rem 1.25rem;
        border-radius: 50rem;
        text-decoration: none;
        transition: all 0.22s ease-in-out;
        border: 1.5px solid transparent;
        cursor: pointer;
        line-height: 1.4;
    }
    .btn-product-buy {
        background: #00B8BE;
        color: #001B27 !important;
        border-color: #00B8BE;
        font-weight: 700;
        box-shadow: 0 4px 12px rgba(0, 184, 190, 0.25);
    }
    .btn-product-buy:hover {
        background: #009ea3;
        border-color: #009ea3;
        color: #001B27 !important;
        transform: translateY(-1px);
        box-shadow: 0 6px 16px rgba(0, 184, 190, 0.4);
    }
    .btn-product-cart {
        background: #ffffff;
        color: #005C66 !important;
        border-color: #005C66;
        font-weight: 600;
    }
    .btn-product-cart:hover {
        background: #005C66;
        color: #ffffff !important;
        border-color: #005C66;
        transform: translateY(-1px);
        box-shadow: 0 4px 12px rgba(0, 92, 102, 0.2);
    }
    .btn-product-chat {
        background: #001B27;
        color: #ffffff !important;
        border-color: #005C66;
        font-weight: 600;
    }
    .btn-product-chat i {
        color: #00B8BE;
    }
    .btn-product-chat:hover {
        background: #003043;
        border-color: #00B8BE;
        color: #ffffff !important;
        transform: translateY(-1px);
        box-shadow: 0 4px 12px rgba(0, 27, 39, 0.25);
    }
    .btn-product-disabled {
        background: #e2e8f0;
        color: #64748b;
        border-color: #cbd5e1;
        cursor: not-allowed;
    }
    @media (max-width: 576px) {
        .product-action-group .action-buttons-wrap {
            display: flex !important;
            flex-direction: row !important;
            flex-wrap: wrap !important;
            gap: 0.5rem !important;
        }
        .product-action-group .btn-product-buy {
            flex: 1 1 calc(50% - 0.25rem);
            width: auto;
            padding: 0.55rem 0.5rem;
            font-size: 0.84rem;
            text-align: center;
        }
        .product-action-group .action-cart-form {
            flex: 1 1 calc(50% - 0.25rem);
        }
        .product-action-group .action-cart-form .btn-product-cart {
            width: 100%;
            padding: 0.55rem 0.5rem;
            font-size: 0.84rem;
        }
        .product-action-group .action-chat-form,
        .product-action-group a.btn-product-chat {
            flex: 1 1 100%;
            width: 100%;
        }
        .product-action-group .action-chat-form .btn-product-chat {
            width: 100%;
            padding: 0.55rem 0.75rem;
            font-size: 0.86rem;
        }
        #mainProductImage {
            max-height: 360px !important;
            min-height: 240px !important;
        }
    }
</style>
@endpush

@section('content')
<div class="container py-5">
    
    <nav class="mb-4">
        <a href="{{ route('pelanggan.katalog') }}" class="btn btn-outline-secondary btn-sm rounded-pill">
            <i class="bi bi-arrow-left me-1"></i> Kembali ke Katalog
        </a>
    </nav>

    <div class="card custom-card border-0 shadow-sm p-4 p-md-5 mb-5">
        <div class="row g-4 align-items-center">
            
            <!-- GAMBAR PRODUK & GALERI DETAIL -->
            <div class="col-md-5">
                <div class="position-relative rounded-4 overflow-hidden shadow-sm mb-3" style="background: #f4f9fa; border: 1px solid rgba(0, 92, 102, 0.12);">
                    <img id="mainProductImage" src="{{ $produk->gambar_url }}" alt="{{ $produk->NamaProduk }}" class="w-100" 
                         style="max-height: 460px; min-height: 320px; object-fit: cover; object-position: center; transition: opacity 0.2s ease;"
                         onerror="this.onerror=null; this.src='{{ asset('upload/default.jpg') }}';">
                    
                    @if($produk->Status !== 'Tersedia')
                        <div class="position-absolute top-0 start-0 w-100 h-100 d-flex align-items-center justify-content-center" style="background: rgba(0, 27, 39, 0.85);">
                            <span class="badge fs-5 px-3 py-2" style="background: #001B27; color: #00B8BE; border: 1px solid #00B8BE;">
                                {{ $produk->Status === 'Dipesan' ? 'SEDANG DIPESAN' : 'SUDAH TERJUAL' }}
                            </span>
                        </div>
                    @endif
                </div>

                @php($galeri = $produk->semua_gambar)
                @if(count($galeri) > 1)
                    <div class="d-flex gap-2 overflow-auto pb-2" id="galleryThumbnails">
                        @foreach($galeri as $index => $g)
                            <div class="thumbnail-wrapper rounded-3 overflow-hidden" 
                                 style="width: 72px; height: 72px; flex-shrink: 0; cursor: pointer; border: 2px solid {{ $loop->first ? '#00B8BE' : 'rgba(0, 92, 102, 0.2)' }}; transition: border-color 0.2s;"
                                 onclick="changeMainImage('{{ $g['url'] }}', this)">
                                <img src="{{ $g['url'] }}" alt="Thumbnail {{ $index + 1 }}" class="w-100 h-100" style="object-fit: cover;" onerror="this.onerror=null; this.src='{{ asset('upload/default.jpg') }}';">
                            </div>
                        @endforeach
                    </div>
                    <small class="text-muted d-block mt-1" style="font-size: 0.75rem;"><i class="bi bi-hand-index-thumb me-1" style="color: #00B8BE;"></i> Klik foto kecil untuk memperbesar detail (tampak belakang, label, minus).</small>

                    <script>
                        function changeMainImage(url, el) {
                            const mainImg = document.getElementById('mainProductImage');
                            if (!mainImg) return;
                            mainImg.style.opacity = '0.3';
                            setTimeout(() => {
                                mainImg.src = url;
                                mainImg.style.opacity = '1';
                            }, 150);

                            document.querySelectorAll('#galleryThumbnails .thumbnail-wrapper').forEach(item => {
                                item.style.borderColor = 'rgba(0, 92, 102, 0.2)';
                            });
                            el.style.borderColor = '#00B8BE';
                        }
                    </script>
                @endif
            </div>

            <!-- DETAIL INFO -->
            <div class="col-md-7">
                <div class="ps-md-3">
                    <div class="d-flex gap-2 mb-2 flex-wrap">
                        <span class="badge" style="background: #001B27; color: #fff;">{{ $produk->jenisProduk ? $produk->jenisProduk->NamaJenis : 'SecondSpace' }}</span>
                        <span class="badge bg-light text-secondary border">Kondisi: {{ \App\Models\Produk::KONDISI[$produk->Kondisi] ?? $produk->Kondisi }}</span>
                        @if($produk->Status == 'Tersedia')
                            <span class="badge" style="background: rgba(0, 184, 190, 0.15); color: #005C66; border: 1px solid rgba(0, 184, 190, 0.4);">
                                <i class="bi bi-check-circle me-1"></i> Tersedia (1 Pcs)
                            </span>
                        @endif
                    </div>

                    <h2 class="fw-bold mb-2" style="color: #001B27;">{{ $produk->NamaProduk }}</h2>
                    <div class="text-muted mb-2 d-flex align-items-center gap-2 flex-wrap">
                        <span>Seller: <strong>{{ $produk->seller?->Nama ?? '-' }}</strong></span>
                        @if($produk->seller)
                            @php($st = $produk->seller->statusOperasionalToko())
                            <span class="badge {{ $st['badge_class'] }} rounded-pill px-2.5 py-1" style="font-size: 0.74rem;" title="{{ $st['pesan'] }}">
                                <i class="bi {{ $st['buka'] ? 'bi-clock-fill text-success' : 'bi-moon-stars-fill text-muted' }} me-1"></i>
                                {{ $st['label'] }} ({{ $st['jam'] }})
                            </span>
                        @endif
                    </div>
                    <p class="small mb-3" style="color: #005C66;">
                        <i class="bi bi-truck me-1"></i>{{ $produk->seller?->infoPengantaran() }}
                        @if($produk->seller)
                            <span class="text-muted ms-1">&bull; Operasional: {{ $produk->seller->hariOperasionalLabel() }}</span>
                        @endif
                    </p>

                    <div class="d-flex align-items-baseline gap-3 mb-4 flex-wrap">
                        <span class="fs-2 fw-bolder" style="color: #005C66;">Rp {{ number_format($produk->Harga, 0, ',', '.') }}</span>
                        <span class="badge px-3 py-2 rounded-pill small" style="background: rgba(0, 184, 190, 0.12); color: #005C66; border: 1px solid rgba(0, 184, 190, 0.35);">
                            <i class="bi bi-gem me-1" style="color: #00B8BE;"></i> Item Thrift Eksklusif (1 Unit)
                        </span>
                        @php($jmlUlasan = $produk->totalUlasan())
                        @if($jmlUlasan > 0)
                            <span class="badge px-3 py-2 rounded-pill small" style="background: rgba(245, 158, 11, 0.12); color: #b45309; border: 1px solid rgba(245, 158, 11, 0.35);">
                                <i class="bi bi-star-fill text-warning me-1"></i> {{ $produk->ratingRataRata() }} / 5.0 ({{ $jmlUlasan }} Ulasan)
                            </span>
                        @endif
                    </div>

                    <!-- DESKRIPSI PRODUK -->
                    <div class="p-3 rounded-3 mb-4" style="background: #f4f9fa; border: 1px solid rgba(0, 92, 102, 0.15);">
                        <h6 class="fw-bold mb-2" style="color: #001B27;"><i class="bi bi-card-text me-1" style="color: #00B8BE;"></i> Deskripsi & Kondisi Barang:</h6>
                        <p class="text-muted mb-0" style="line-height: 1.7; white-space: pre-line;">{{ $produk->Deskripsi ?? 'Kondisi barang thrift / pre-loved pilihan terkurasi, bersih dan siap pakai.' }}</p>
                    </div>

                    <!-- JAMINAN KUALITAS -->
                    <div class="p-3 rounded-3 bg-white border mb-4 shadow-sm" style="border-color: rgba(0, 92, 102, 0.15) !important;">
                        <h6 class="fw-bold mb-2 small text-uppercase" style="color: #005C66;"><i class="bi bi-shield-check text-success me-1"></i> Jaminan Belanja di SecondSpace:</h6>
                        <ul class="mb-0 text-muted small ps-3">
                            <li>Barang dijual oleh seller yang telah disetujui Admin SecondSpace.</li>
                            <li>Tersedia pembayaran aman via QRIS (E-Wallet & m-Banking) & Cash on Delivery (COD).</li>
                            <li>Barang thrift/pre-loved bersifat eksklusif (hanya tersedia 1 unit untuk setiap produk).</li>
                        </ul>
                    </div>

                    @php($currentUid = Auth::id())
                    @php($isOwner = $produk->SellerID && (int) $produk->SellerID === (int) $currentUid)

                    @if($isOwner)
                        <div class="alert alert-info mb-3">
                            <i class="bi bi-info-circle me-1"></i> Ini adalah barang dagangan milik Anda sendiri di SecondSpace.
                        </div>
                    @endif

                    <div class="product-action-group">
                        <div class="action-buttons-wrap d-flex gap-2 flex-wrap align-items-center">
                            @if($produk->Status == 'Tersedia')
                                @auth
                                    @if(!$isOwner)
                                        <a href="{{ route('pelanggan.checkout.show', $produk->ProdukID) }}" class="btn-product-action btn-product-buy">
                                            <i class="bi bi-bag-check-fill"></i> Beli Sekarang
                                        </a>

                                        <form action="{{ route('pelanggan.keranjang.tambah', $produk->ProdukID) }}" method="POST" class="m-0 action-cart-form">
                                            @csrf
                                            <button type="submit" class="btn-product-action btn-product-cart">
                                                <i class="bi bi-cart-plus"></i> Tambah ke Keranjang
                                            </button>
                                        </form>
                                    @endif
                                @else
                                    <a href="{{ route('login') }}" class="btn-product-action btn-product-buy">
                                        <i class="bi bi-box-arrow-in-right"></i> Login untuk Membeli
                                    </a>
                                @endauth
                            @else
                                <button class="btn-product-action btn-product-disabled" disabled>
                                    <i class="bi bi-slash-circle"></i> {{ $produk->Status === 'Dipesan' ? 'Barang Sedang Dipesan' : 'Barang Telah Terjual' }}
                                </button>
                            @endif

                            {{-- TOMBOL TANYA PENJUAL (CHAT REAL-TIME) --}}
                            @if(!$isOwner)
                                @auth
                                    <form action="{{ route('pelanggan.chat.mulai', $produk->ProdukID) }}" method="POST" class="m-0 action-chat-form">
                                        @csrf
                                        <input type="hidden" name="ref" value="{{ url()->current() }}">
                                        <button type="submit" class="btn-product-action btn-product-chat">
                                            <i class="bi bi-chat-dots-fill"></i> Tanya Penjual
                                        </button>
                                    </form>
                                @else
                                    <a href="{{ route('login') }}" class="btn-product-action btn-product-chat">
                                        <i class="bi bi-chat-dots-fill"></i> Tanya Penjual
                                    </a>
                                @endauth
                            @endif
                        </div>
                    </div>

                    {{-- TOMBOL BAGIKAN KE MEDIA SOSIAL --}}
                    <div class="mt-4 pt-3 border-top">
                        <div class="d-flex flex-column flex-sm-row align-items-sm-center justify-content-between gap-2">
                            <span class="small fw-bold text-muted text-uppercase d-inline-flex align-items-center" style="letter-spacing: 0.5px; font-size: 0.78rem;">
                                <i class="bi bi-share-fill me-2" style="color: #00B8BE;"></i> Bagikan Produk:
                            </span>
                            <div class="d-flex align-items-center flex-wrap gap-2 pt-1 pt-sm-0">
                                {{-- WhatsApp --}}
                                <a href="https://api.whatsapp.com/send?text={{ rawurlencode('Cek produk thrift keren ini di SecondSpace: ' . $produk->NamaProduk . ' (Rp ' . number_format($produk->Harga, 0, ',', '.') . ') 👉 ' . url()->current()) }}" 
                                   target="_blank" rel="noopener noreferrer" 
                                   class="btn btn-sm rounded-pill text-white fw-semibold shadow-sm d-inline-flex align-items-center gap-1.5"
                                   style="background: #25D366; font-size: 0.8rem; padding: 6px 14px; text-decoration: none;" title="Bagikan ke WhatsApp">
                                    <i class="bi bi-whatsapp"></i> <span>WhatsApp</span>
                                </a>

                                {{-- Facebook --}}
                                <a href="https://www.facebook.com/sharer/sharer.php?u={{ rawurlencode(url()->current()) }}" 
                                   target="_blank" rel="noopener noreferrer" 
                                   class="btn btn-sm rounded-pill text-white fw-semibold shadow-sm d-inline-flex align-items-center gap-1.5"
                                   style="background: #1877F2; font-size: 0.8rem; padding: 6px 14px; text-decoration: none;" title="Bagikan ke Facebook">
                                    <i class="bi bi-facebook"></i> <span>Facebook</span>
                                </a>

                                {{-- X / Twitter --}}
                                <a href="https://twitter.com/intent/tweet?text={{ rawurlencode('Cek ' . $produk->NamaProduk . ' di SecondSpace: ') }}&url={{ rawurlencode(url()->current()) }}" 
                                   target="_blank" rel="noopener noreferrer" 
                                   class="btn btn-sm btn-dark rounded-pill fw-semibold shadow-sm d-inline-flex align-items-center"
                                   style="font-size: 0.8rem; background: #000; padding: 6px 12px; text-decoration: none;" title="Bagikan ke X / Twitter">
                                    <i class="bi bi-twitter-x"></i>
                                </a>

                                {{-- Salin Tautan --}}
                                <button type="button" class="btn btn-sm btn-outline-secondary rounded-pill fw-semibold d-inline-flex align-items-center gap-1.5" 
                                        onclick="salinTautanProduk(this)" style="font-size: 0.8rem; padding: 6px 14px;" title="Salin Tautan Produk">
                                    <i class="bi bi-link-45deg fs-6"></i> <span class="btn-copy-text">Salin Link</span>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>

    <!-- RATING & ULASAN PRODUK -->
    <div class="card custom-card border-0 p-4 mt-4 shadow-sm" id="ulasan-produk-section">
        <div class="d-flex align-items-center justify-content-between mb-4 flex-wrap gap-2">
            <div>
                <h4 class="fw-bold mb-1" style="color: #001B27;">
                    <i class="bi bi-star-fill text-warning me-2"></i> Penilaian &amp; Ulasan Pembeli
                </h4>
                <p class="text-muted small mb-0">Ulasan nyata dari pembeli terverifikasi yang telah menyelesaikan pesanan.</p>
            </div>
            @if($produk->totalUlasan() > 0)
                <div class="text-end">
                    <span class="fs-4 fw-bolder" style="color: #005C66;">{{ $produk->ratingRataRata() }}</span>
                    <span class="text-muted small"> / 5.0</span>
                    <div class="d-flex text-warning small mt-1 justify-content-end">
                        @php($avg = (int) round($produk->ratingRataRata()))
                        @for($i = 1; $i <= 5; $i++)
                            <i class="bi {{ $i <= $avg ? 'bi-star-fill' : 'bi-star' }} me-0.5"></i>
                        @endfor
                    </div>
                </div>
            @endif
        </div>

        @php($daftarUlasan = $produk->ulasan)
        @if($daftarUlasan->isEmpty())
            <div class="text-center py-5 rounded-4" style="background: #f8fafc; border: 1px dashed rgba(0, 92, 102, 0.2);">
                <div class="display-6 text-muted mb-2"><i class="bi bi-chat-square-heart"></i></div>
                <h6 class="fw-bold" style="color: #001B27;">Belum Ada Ulasan</h6>
                <p class="text-muted small mb-0">Jadilah orang pertama yang memberikan penilaian setelah membeli produk ini!</p>
            </div>
        @else
            <!-- Summary Rating Card -->
            <div class="p-3 rounded-4 mb-4" style="background: linear-gradient(135deg, rgba(0, 184, 190, 0.08), rgba(0, 92, 102, 0.05)); border: 1px solid rgba(0, 184, 190, 0.2);">
                <div class="row align-items-center g-3">
                    <div class="col-md-3 text-center border-end">
                        <div class="display-4 fw-bold" style="color: #005C66;">{{ $produk->ratingRataRata() }}</div>
                        <div class="text-warning fs-5 mb-1">
                            @php($avgStar = (int) round($produk->ratingRataRata()))
                            @for($i = 1; $i <= 5; $i++)
                                <i class="bi {{ $i <= $avgStar ? 'bi-star-fill' : 'bi-star' }}"></i>
                            @endfor
                        </div>
                        <div class="text-muted small fw-semibold">Berdasarkan {{ $daftarUlasan->count() }} Penilaian</div>
                    </div>
                    <div class="col-md-9">
                        <div class="d-flex flex-wrap gap-2" id="filterUlasanButtonGroup">
                            <button type="button" class="btn btn-sm btn-dark rounded-pill px-3 py-1.5 fw-semibold filter-ulasan-btn active" data-filter="all">Semua ({{ $daftarUlasan->count() }})</button>
                            <button type="button" class="btn btn-sm btn-outline-secondary rounded-pill px-3 py-1.5 fw-semibold filter-ulasan-btn" data-filter="foto">Dengan Foto ({{ $daftarUlasan->whereNotNull('Foto')->count() }})</button>
                            @for($s = 5; $s >= 1; $s--)
                                @php($cntStar = $daftarUlasan->where('Bintang', $s)->count())
                                @if($cntStar > 0)
                                    <button type="button" class="btn btn-sm btn-outline-secondary rounded-pill px-3 py-1.5 fw-semibold filter-ulasan-btn" data-filter="{{ $s }}">
                                        <i class="bi bi-star-fill text-warning me-1"></i> {{ $s }} ({{ $cntStar }})
                                    </button>
                                @endif
                            @endfor
                        </div>
                    </div>
                </div>
            </div>

            <!-- List of Reviews -->
            <div class="d-flex flex-column gap-3" id="ulasanListContainer">
                @foreach($daftarUlasan as $ul)
                    <div class="p-3 rounded-4 ulasan-item" data-bintang="{{ $ul->Bintang }}" data-has-foto="{{ $ul->Foto ? '1' : '0' }}" style="background: #ffffff; border: 1px solid rgba(0, 92, 102, 0.12); box-shadow: 0 2px 8px rgba(0, 27, 39, 0.03);">
                        <div class="d-flex align-items-center justify-content-between mb-2 flex-wrap gap-2">
                            <div class="d-flex align-items-center gap-2">
                                <div class="rounded-circle d-flex align-items-center justify-content-center text-white fw-bold shadow-sm" style="width: 38px; height: 38px; background: linear-gradient(135deg, #00B8BE, #005C66); font-size: 0.95rem;">
                                    {{ strtoupper(mb_substr($ul->user?->Nama ?? 'P', 0, 1)) }}
                                </div>
                                <div>
                                    <div class="fw-bold d-flex align-items-center gap-2" style="color: #001B27; font-size: 0.92rem;">
                                        <span>{{ $ul->nama_samar }}</span>
                                        <span class="badge rounded-pill" style="background: rgba(16, 185, 129, 0.12); color: #047857; border: 1px solid rgba(16, 185, 129, 0.3); font-size: 0.68rem; font-weight: 600;">
                                            <i class="bi bi-patch-check-fill me-1"></i> Terverifikasi Beli
                                        </span>
                                    </div>
                                    <div class="text-muted" style="font-size: 0.75rem;">
                                        {{ \Carbon\Carbon::parse($ul->DibuatPada)->timezone('Asia/Makassar')->translatedFormat('d M Y, H:i') }} WITA
                                    </div>
                                </div>
                            </div>
                            <div class="text-warning">
                                @for($i = 1; $i <= 5; $i++)
                                    <i class="bi {{ $i <= $ul->Bintang ? 'bi-star-fill' : 'bi-star' }} fs-6"></i>
                                @endfor
                            </div>
                        </div>

                        @if($ul->Komentar)
                            <p class="mb-2" style="color: #334155; font-size: 0.9rem; line-height: 1.6; white-space: pre-line;">{{ $ul->Komentar }}</p>
                        @endif

                        @if($ul->Foto)
                            <div class="mb-2">
                                <a href="{{ $ul->foto_url }}" target="_blank" rel="noopener noreferrer" class="d-inline-block rounded-3 overflow-hidden border shadow-sm" style="width: 88px; height: 88px;" title="Klik untuk memperbesar">
                                    <img src="{{ $ul->foto_url }}" alt="Foto Ulasan Pembeli" class="w-100 h-100" style="object-fit: cover;" onerror="this.onerror=null; this.src='{{ asset('upload/default.jpg') }}';">
                                </a>
                            </div>
                        @endif

                        @if($ul->DibalasOlehSeller)
                            <div class="mt-2 p-3 rounded-3" style="background: #f1f5f9; border-left: 3px solid #00B8BE; font-size: 0.85rem;">
                                <div class="fw-bold mb-1" style="color: #005C66;">
                                    <i class="bi bi-shop me-1"></i> Respon Penjual:
                                </div>
                                <div class="text-muted" style="white-space: pre-line;">{{ $ul->DibalasOlehSeller }}</div>
                                @if($ul->DibalasPada)
                                    <small class="text-muted d-block mt-1" style="font-size: 0.72rem;">{{ \Carbon\Carbon::parse($ul->DibalasPada)->timezone('Asia/Makassar')->translatedFormat('d M Y, H:i') }} WITA</small>
                                @endif
                            </div>
                        @endif
                    </div>
                @endforeach
            </div>
        @endif
    </div>

    <!-- PRODUK SERUPA -->
    @if($relatedProducts->isNotEmpty())
        <div class="mt-5">
            <h4 class="fw-bold mb-3" style="color: #001B27;">Produk Lain di Kategori yang Sama</h4>
            <div class="row g-3">
                @foreach($relatedProducts as $rel)
                    <div class="col-md-3 col-6">
                        <div class="card custom-card border-0 p-2 h-100 shadow-sm">
                            <img src="{{ $rel->gambar_url }}" class="rounded-3" style="height: 180px; object-fit: cover;" onerror="this.onerror=null; this.src='{{ asset('upload/default.jpg') }}';">
                            <div class="p-2">
                                <div class="small fw-bold text-truncate" style="color: #001B27;">{{ $rel->NamaProduk }}</div>
                                <div class="fw-bold mt-1" style="color: #005C66;">Rp {{ number_format($rel->Harga, 0, ',', '.') }}</div>
                                <a href="{{ route('pelanggan.produk.detail', $rel->ProdukID) }}" class="btn btn-dark btn-sm rounded-pill w-100 mt-2">
                                    Lihat
                                </a>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    @endif

</div>

@push('scripts')
<script>
function salinTautanProduk(btn) {
    const url = window.location.href;
    if (navigator.clipboard && window.isSecureContext) {
        navigator.clipboard.writeText(url).then(() => {
            tampilkanFeedbackSalin(btn);
        }).catch(() => fallbackSalin(url, btn));
    } else {
        fallbackSalin(url, btn);
    }
}

function fallbackSalin(text, btn) {
    const temp = document.createElement('textarea');
    temp.value = text;
    temp.style.position = 'fixed';
    temp.style.opacity = '0';
    document.body.appendChild(temp);
    temp.select();
    try {
        document.execCommand('copy');
        tampilkanFeedbackSalin(btn);
    } catch (e) {
        alert('Gagal menyalin link secara otomatis. Silakan salin URL di browser Anda.');
    }
    document.body.removeChild(temp);
}

function tampilkanFeedbackSalin(btn) {
    const span = btn.querySelector('.btn-copy-text');
    const originalText = span ? span.textContent : 'Salin Link';
    if (span) span.textContent = 'Tersalin!';
    btn.classList.remove('btn-outline-secondary');
    btn.classList.add('btn-success', 'text-white');
    setTimeout(() => {
        if (span) span.textContent = originalText;
        btn.classList.remove('btn-success', 'text-white');
        btn.classList.add('btn-outline-secondary');
    }, 2000);
}
// Filter Ulasan Pembeli
document.querySelectorAll('#filterUlasanButtonGroup .filter-ulasan-btn').forEach(btn => {
    btn.addEventListener('click', function() {
        document.querySelectorAll('#filterUlasanButtonGroup .filter-ulasan-btn').forEach(b => {
            b.classList.remove('btn-dark', 'active');
            b.classList.add('btn-outline-secondary');
        });
        this.classList.remove('btn-outline-secondary');
        this.classList.add('btn-dark', 'active');

        const filter = this.getAttribute('data-filter');
        const items = document.querySelectorAll('#ulasanListContainer .ulasan-item');
        items.forEach(item => {
            if (filter === 'all') {
                item.style.display = 'block';
            } else if (filter === 'foto') {
                item.style.display = item.getAttribute('data-has-foto') === '1' ? 'block' : 'none';
            } else {
                item.style.display = item.getAttribute('data-bintang') === filter ? 'block' : 'none';
            }
        });
    });
});
</script>
@endpush
@endsection