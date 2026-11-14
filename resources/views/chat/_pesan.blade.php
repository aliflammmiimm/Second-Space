@php
    $uid = (int) ($uid ?? Auth::id());
    $sebagaiSeller = $sebagaiSeller ?? request()->routeIs('seller.*');
@endphp

@foreach($pesan as $m)
    @php
        $milikku = (int) $m->PengirimID === (int) $uid;
        $waktu = \Carbon\Carbon::parse($m->DibuatPada)->timezone('Asia/Makassar');
        $jamFormat = $waktu->isToday() 
            ? $waktu->format('H:i') 
            : ($waktu->isYesterday() ? 'Kemarin ' . $waktu->format('H:i') : $waktu->format('d M H:i'));
    @endphp

    <div id="chat-item-{{ $m->PesanID }}" class="d-flex mb-3 {{ $milikku ? 'justify-content-end' : 'justify-content-start' }} message-row">
        <div class="message-bubble rounded-4 shadow-sm position-relative {{ $milikku ? 'text-white' : 'text-dark' }}" 
             style="max-width: 82%; min-width: 260px; padding: 12px 16px; {{ $milikku ? 'background: #005C66;' : 'background: #ffffff; border: 1px solid rgba(0, 92, 102, 0.15);' }}">

            {{-- KARTU TAWARAN HARGA INTERAKTIF (SHOPEE STYLE) --}}
            @if($m->tawaran)
                @php($tw = $m->tawaran)
                @php($prodTw = $tw->produk)
                <div class="tawaran-chat-box p-3 rounded-3 mb-2 bg-white text-dark shadow-sm border" style="border-color: rgba(0, 92, 102, 0.28) !important;">
                    <div class="d-flex align-items-center justify-content-between mb-2 pb-1 border-bottom flex-wrap gap-1">
                        <span class="small fw-bold text-uppercase d-inline-flex align-items-center" style="font-size: 0.76rem; color: #005C66; letter-spacing: 0.5px;">
                            <i class="bi bi-tag-fill me-1 text-warning"></i> Penawaran Harga
                        </span>
                        @if($tw->isMenunggu())
                            <span class="badge bg-warning-subtle text-dark border border-warning-subtle" style="font-size: 0.72rem;">
                                <i class="bi bi-hourglass-split me-1"></i> Menunggu Respon
                            </span>
                        @elseif($tw->isDiterima())
                            <span class="badge bg-success-subtle text-success border border-success-subtle" style="font-size: 0.72rem;">
                                <i class="bi bi-check-circle-fill me-1"></i> Tawaran Diterima
                            </span>
                        @elseif($tw->isDitolak())
                            <span class="badge bg-danger-subtle text-danger border border-danger-subtle" style="font-size: 0.72rem;">
                                <i class="bi bi-x-circle-fill me-1"></i> Tawaran Ditolak
                            </span>
                        @elseif($tw->isDibatalkan())
                            <span class="badge bg-secondary-subtle text-muted border border-secondary-subtle" style="font-size: 0.72rem;">
                                Dibatalkan
                            </span>
                        @elseif($tw->isSelesai())
                            <span class="badge bg-primary-subtle text-primary border border-primary-subtle" style="font-size: 0.72rem;">
                                <i class="bi bi-bag-check-fill me-1"></i> Telah Di-Checkout
                            </span>
                        @endif
                    </div>

                    @if($prodTw)
                        <div class="d-flex align-items-center gap-2.5 mb-2">
                            <img src="{{ $prodTw->gambar_url }}" class="rounded-2 flex-shrink-0" style="width: 52px; height: 52px; object-fit: cover; border: 1px solid #edf2f7;" onerror="this.onerror=null; this.src='{{ asset('upload/default.jpg') }}';">
                            <div class="overflow-hidden flex-grow-1">
                                <div class="fw-bold text-truncate mb-0.5" style="font-size: 0.86rem; color: #001B27;" title="{{ $prodTw->NamaProduk }}">
                                    {{ $prodTw->NamaProduk }}
                                </div>
                                <div class="d-flex align-items-baseline gap-2 flex-wrap">
                                    <span class="text-muted text-decoration-line-through small" style="font-size: 0.76rem;">
                                        Rp {{ number_format($tw->HargaAsli, 0, ',', '.') }}
                                    </span>
                                    <span class="fw-bolder fs-6" style="color: #005C66;">
                                        Rp {{ number_format($tw->HargaTawaran, 0, ',', '.') }}
                                    </span>
                                    <span class="badge bg-success-subtle text-success" style="font-size: 0.68rem;">
                                        Hemat Rp {{ number_format($tw->HargaAsli - $tw->HargaTawaran, 0, ',', '.') }}
                                    </span>
                                </div>
                            </div>
                        </div>
                    @endif

                    @if(!empty($tw->Catatan))
                        <div class="p-1.5 px-2 rounded-2 bg-light text-muted small mb-2 fst-italic" style="font-size: 0.76rem; border-left: 3px solid #00B8BE;">
                            "{{ $tw->Catatan }}"
                        </div>
                    @endif

                    {{-- RESPON INTERAKTIF SELLER / AKSI PEMBELI --}}
                    @if($tw->isMenunggu())
                        @if($sebagaiSeller)
                            <div class="d-flex gap-2 pt-2 border-top mt-2">
                                <button type="button" class="btn btn-sm btn-success rounded-pill px-3 py-1 fw-bold flex-grow-1 d-inline-flex align-items-center justify-content-center gap-1 shadow-sm" style="font-size: 0.78rem;" onclick="responTawaran({{ $tw->TawaranID }}, 'terima')">
                                    <i class="bi bi-check-lg"></i> Terima
                                </button>
                                <button type="button" class="btn btn-sm btn-outline-danger rounded-pill px-3 py-1 flex-grow-1 d-inline-flex align-items-center justify-content-center gap-1" style="font-size: 0.78rem;" onclick="responTawaran({{ $tw->TawaranID }}, 'tolak')">
                                    <i class="bi bi-x-lg"></i> Tolak
                                </button>
                            </div>
                        @else
                            <div class="d-flex justify-content-between align-items-center pt-2 border-top mt-2 small text-muted" style="font-size: 0.75rem;">
                                <span><i class="bi bi-hourglass-split me-1"></i> Menunggu persetujuan seller</span>
                                <button type="button" class="btn btn-link p-0 text-danger text-decoration-none fw-semibold" style="font-size: 0.75rem;" onclick="batalTawaran({{ $tw->TawaranID }})">
                                    Batalkan
                                </button>
                            </div>
                        @endif
                    @elseif($tw->isDiterima() && !$sebagaiSeller && $prodTw && $prodTw->Status === 'Tersedia')
                        <div class="pt-2 border-top mt-2">
                            <a href="{{ route('pelanggan.checkout.show', [$prodTw->ProdukID, 'tawaran_id' => $tw->TawaranID]) }}" class="btn btn-sm w-100 rounded-pill py-1.5 text-white fw-bold shadow-sm d-inline-flex align-items-center justify-content-center gap-1" style="background: #005C66; font-size: 0.82rem;">
                                <i class="bi bi-bag-check-fill"></i> Beli Sekarang (Rp {{ number_format($tw->HargaTawaran, 0, ',', '.') }})
                            </a>
                        </div>
                    @elseif($tw->isDitolak() && !$sebagaiSeller && $prodTw && $prodTw->Status === 'Tersedia')
                        <div class="pt-1.5 border-top mt-2 text-end">
                            <button type="button" class="btn btn-link p-0 text-decoration-none small fw-semibold" style="color: #005C66; font-size: 0.75rem;" onclick="bukaModalTawar({{ $prodTw->ProdukID }}, '{{ addslashes($prodTw->NamaProduk) }}', {{ (int) $prodTw->Harga }}, '{{ $prodTw->gambar_url }}')">
                                <i class="bi bi-arrow-repeat me-1"></i> Tawar Ulang
                            </button>
                        </div>
                    @endif
                </div>

            {{-- BOX KARTU PRODUK BIASA (JIKA BUKAN TAWARAN) --}}
            @elseif($m->produk)
                <div class="product-chat-box p-2.5 rounded-3 mb-2 bg-white text-dark shadow-sm" style="border: 1px solid rgba(0, 92, 102, 0.18);">
                    <div class="d-flex align-items-center gap-2.5">
                        <img src="{{ $m->produk->gambar_url }}" alt="{{ $m->produk->NamaProduk }}" 
                             class="rounded-3 flex-shrink-0" 
                             style="width: 58px; height: 58px; object-fit: cover; border: 1px solid #edf2f7;"
                             onerror="this.onerror=null; this.src='{{ asset('upload/default.jpg') }}';">
                        
                        <div class="flex-grow-1 overflow-hidden">
                            <div class="fw-bold text-truncate mb-1" style="color: #001B27; font-size: 0.86rem;" title="{{ $m->produk->NamaProduk }}">
                                {{ $m->produk->NamaProduk }}
                            </div>
                            <div class="d-flex align-items-center justify-content-between flex-wrap gap-2">
                                <span class="fw-bolder" style="color: #005C66; font-size: 0.92rem;">
                                    Rp {{ number_format($m->produk->Harga, 0, ',', '.') }}
                                </span>
                                <div class="d-flex align-items-center" style="gap: 6px;">
                                    <a href="{{ route('pelanggan.produk.detail', $m->produk->ProdukID) }}" 
                                       target="_blank" 
                                       class="btn btn-outline-secondary btn-sm py-1 px-2.5 rounded-pill text-decoration-none d-inline-flex align-items-center" 
                                       style="font-size: 0.74rem; line-height: 1.4; gap: 4px;">
                                        <i class="bi bi-box-arrow-up-right me-1"></i> Lihat
                                    </a>
                                    @if(!$sebagaiSeller)
                                        <button type="button" 
                                                class="btn btn-outline-info btn-sm py-1 px-2.5 rounded-pill text-decoration-none d-inline-flex align-items-center" 
                                                style="font-size: 0.74rem; line-height: 1.4; gap: 4px; border-color: #00B8BE; color: #005C66;"
                                                onclick="bukaModalTawar({{ $m->produk->ProdukID }}, '{{ addslashes($m->produk->NamaProduk) }}', {{ (int) $m->produk->Harga }}, '{{ $m->produk->gambar_url }}')">
                                            <i class="bi bi-tag-fill text-warning me-0.5"></i> Tawar
                                        </button>
                                        <a href="{{ route('pelanggan.checkout.show', $m->produk->ProdukID) }}" 
                                           class="btn btn-sm py-1 px-2.5 rounded-pill text-white fw-bold text-decoration-none d-inline-flex align-items-center" 
                                           style="font-size: 0.74rem; line-height: 1.4; background: #005C66; gap: 4px;">
                                            <i class="bi bi-bag-fill me-1"></i> Beli
                                        </a>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endif

            {{-- ISI PESAN --}}
            <div id="pesan-text-{{ $m->PesanID }}" class="chat-text-content" style="white-space: pre-line; word-break: break-word; font-size: 0.92rem; line-height: 1.45;">{{ $m->Isi }}</div>

            {{-- FORM EDIT INLINE (HIDDEN DEFAULT) --}}
            @if($milikku)
                <div id="pesan-edit-form-{{ $m->PesanID }}" class="d-none mt-2">
                    <textarea id="edit-input-{{ $m->PesanID }}" class="form-control form-control-sm mb-2" rows="2" maxlength="1000" style="font-size: 0.88rem;">{{ $m->Isi }}</textarea>
                    <div class="d-flex justify-content-end gap-2">
                        <button type="button" class="btn btn-sm btn-light py-1 px-3" style="font-size: 0.75rem;" onclick="batalEditPesan({{ $m->PesanID }})">Batal</button>
                        <button type="button" class="btn btn-sm btn-warning py-1 px-3 fw-semibold" style="font-size: 0.75rem;" onclick="simpanEditPesan({{ $m->PesanID }})">Simpan</button>
                    </div>
                </div>
            @endif

            {{-- TIMESTAMP & AKSI EDIT / HAPUS --}}
            <div class="d-flex justify-content-between align-items-center mt-2 pt-2 border-top flex-wrap" 
                 style="gap: 6px 14px; border-color: {{ $milikku ? 'rgba(255,255,255,0.22)' : 'rgba(0,0,0,0.08)' }} !important; font-size: 0.72rem;">
                <div class="d-flex align-items-center gap-2 flex-wrap">
                    @if($milikku)
                        <button type="button" class="btn btn-link p-0 text-decoration-none opacity-75 hover-opacity-100 d-inline-flex align-items-center me-2" 
                                style="color: #ffffff; font-size: 0.72rem; gap: 4px;" 
                                onclick="mulaiEditPesan({{ $m->PesanID }})" 
                                title="Edit pesan">
                            <i class="bi bi-pencil-square"></i> <span>Edit</span>
                        </button>
                        <button type="button" class="btn btn-link p-0 text-decoration-none opacity-75 hover-opacity-100 d-inline-flex align-items-center me-1" 
                                style="color: #ffcdd2; font-size: 0.72rem; gap: 4px;" 
                                onclick="hapusPesanItem({{ $m->PesanID }})" 
                                title="Hapus pesan">
                            <i class="bi bi-trash3"></i> <span>Hapus</span>
                        </button>
                    @endif
                </div>
                <div class="text-end opacity-75 ms-auto text-nowrap d-inline-flex align-items-center ps-2 {{ $milikku ? 'text-white' : 'text-muted' }}" style="gap: 5px;">
                    <span id="badge-edited-{{ $m->PesanID }}" class="me-1 d-none fst-italic">(diedit)</span>
                    <i class="bi bi-clock"></i> <span>{{ $jamFormat }} WITA</span>
                </div>
            </div>

        </div>
    </div>
@endforeach
