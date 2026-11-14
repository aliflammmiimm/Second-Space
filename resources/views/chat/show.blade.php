@extends($layout)
@section('title', 'Chat dengan ' . ($lawan->Nama ?? 'Pengguna') . ' - SecondSpace')
@section('no_footer', true)

@section($section)
<div class="{{ $sebagaiSeller ? 'pb-4' : 'container py-4' }}" style="max-width: 820px; {{ $sebagaiSeller ? '' : 'margin: 0 auto;' }}">
    
    {{-- HEADER PERCAKAPAN --}}
    <div class="d-flex align-items-center justify-content-between gap-3 mb-3 bg-white p-3 rounded-4 shadow-sm border" style="border-color: rgba(0,92,102,.12) !important;">
        <div class="d-flex align-items-center gap-3">
            <a href="{{ $backUrl ?? route($rp . '.index') }}" id="btnKembaliChat" class="btn btn-outline-secondary btn-sm rounded-pill d-inline-flex align-items-center justify-content-center" style="width: 38px; height: 38px; border-color: rgba(0,0,0,0.15);" title="{{ $backLabel ?? 'Kembali ke Halaman Sebelumnya' }}">
                <i class="bi bi-arrow-left fs-5"></i>
            </a>
            <div>
                <h5 class="fw-bold mb-1 d-flex align-items-center flex-wrap" style="color:#001B27; font-size: 1.05rem;">
                    <i class="bi bi-person-circle fs-5 me-2" style="color: #00B8BE;"></i> 
                    <span>{{ $lawan->Nama ?? 'Pengguna' }}</span>
                    <span class="badge rounded-pill ms-2" style="font-size: 0.72rem; padding: 0.35em 0.75em; background: rgba(0, 92, 102, 0.1); color: #005C66; border: 1px solid rgba(0, 92, 102, 0.25);">
                        {{ $sebagaiSeller ? 'Pembeli' : 'Penjual' }}
                    </span>
                    @if(!$sebagaiSeller && $lawan)
                        @php($stLawan = $lawan->statusOperasionalToko())
                        <span class="badge {{ $stLawan['badge_class'] }} rounded-pill ms-2" style="font-size: 0.72rem; padding: 0.35em 0.75em;" title="{{ $stLawan['pesan'] }}">
                            <i class="bi {{ $stLawan['buka'] ? 'bi-clock-fill text-success' : 'bi-moon-stars-fill' }} me-1"></i>
                            {{ $stLawan['label'] }}
                        </span>
                    @endif
                </h5>
                <small class="text-muted d-flex align-items-center" style="font-size: 0.78rem;">
                    <i class="bi bi-clock-history me-1"></i> Waktu pesan otomatis dalam WITA (Makassar)
                    @if(!$sebagaiSeller && !empty($stLawan))
                        <span class="ms-2 text-dark">&bull; Jam Toko: {{ $stLawan['jam'] }}</span>
                    @endif
                </small>
            </div>
        </div>
        <div class="text-end">
            <button type="button" class="btn btn-light btn-sm rounded-pill border px-3 py-1 d-inline-flex align-items-center" style="gap: 6px;" onclick="ambil(true)" title="Perbarui percakapan">
                <i class="bi bi-arrow-clockwise"></i> <span>Refresh</span>
            </button>
        </div>
    </div>

    {{-- PERINGATAN TOKO SEDANG TUTUP / DI LUAR JAM OPERASIONAL --}}
    @if(!$sebagaiSeller && !empty($stLawan) && !$stLawan['buka'])
        <div class="alert alert-warning py-2.5 px-3 rounded-4 mb-3 d-flex align-items-center gap-2.5 small border shadow-xs" style="font-size: 0.8rem; background: rgba(245, 158, 11, 0.08); border-color: rgba(245, 158, 11, 0.3) !important; color: #92400e;">
            <i class="bi bi-info-circle-fill fs-5 text-warning flex-shrink-0"></i>
            <div>
                <strong>Toko saat ini di luar jam operasional ({{ $stLawan['jam'] }}).</strong> {{ $stLawan['pesan'] }} Anda tetap dapat mengirim pesan atau menawar harga, seller akan merespons saat jam operasional aktif.
            </div>
        </div>
    @endif

    {{-- KARTU PRODUK STICKY DI ATAS CHAT (SHOPEE STYLE) --}}
    @if($produkRef)
        <div id="stickyProductBox" class="p-3 rounded-4 mb-3 shadow-sm bg-white border d-flex align-items-center justify-content-between gap-3 flex-wrap" style="border-color: rgba(0, 92, 102, 0.22) !important; background: linear-gradient(135deg, #ffffff 0%, #f0fdfa 100%);">
            <div class="d-flex align-items-center gap-3 overflow-hidden">
                <a href="{{ route('pelanggan.produk.detail', $produkRef->ProdukID) }}" class="flex-shrink-0 text-decoration-none">
                    <img src="{{ $produkRef->gambar_url }}" alt="{{ $produkRef->NamaProduk }}" 
                         class="rounded-3" 
                         style="width: 58px; height: 58px; object-fit: cover; border: 1.5px solid rgba(0, 92, 102, 0.2);"
                         onerror="this.onerror=null; this.src='{{ asset('upload/default.jpg') }}';">
                </a>
                <div class="overflow-hidden">
                    <div class="small fw-semibold text-muted mb-1 d-flex align-items-center" style="font-size: 0.76rem;">
                        <i class="bi bi-tag-fill me-1" style="color: #00B8BE;"></i> Menanyakan Produk:
                    </div>
                    <a href="{{ route('pelanggan.produk.detail', $produkRef->ProdukID) }}" class="fw-bold text-truncate text-decoration-none d-block mb-1" style="color: #001B27; max-width: 320px; font-size: 0.95rem;">
                        {{ $produkRef->NamaProduk }}
                    </a>
                    <div class="fw-bolder" style="color: #005C66; font-size: 1rem;">
                        Rp {{ number_format($produkRef->Harga, 0, ',', '.') }}
                    </div>
                </div>
            </div>
            <div class="d-flex align-items-center gap-2 ms-auto flex-wrap">
                <a href="{{ route('pelanggan.produk.detail', $produkRef->ProdukID) }}" class="btn btn-outline-secondary btn-sm rounded-pill px-3 py-1 d-inline-flex align-items-center" style="font-size: 0.8rem; gap: 4px;">
                    <i class="bi bi-box-arrow-up-right me-1"></i> Detail
                </a>
                @if(!$sebagaiSeller && $produkRef->Status === 'Tersedia')
                    <button type="button" class="btn btn-sm rounded-pill px-3 py-1 fw-bold shadow-sm d-inline-flex align-items-center" 
                            style="background: #ffffff; border: 1.5px solid #00B8BE; color: #005C66; font-size: 0.8rem; gap: 4px;"
                            onclick="bukaModalTawar({{ $produkRef->ProdukID }}, '{{ addslashes($produkRef->NamaProduk) }}', {{ (int) $produkRef->Harga }}, '{{ $produkRef->gambar_url }}')">
                        <i class="bi bi-tag-fill text-warning"></i> Tawar
                    </button>
                    <a href="{{ route('pelanggan.checkout.show', $produkRef->ProdukID) }}" class="btn btn-sm rounded-pill px-3 py-1 text-white fw-bold shadow-sm d-inline-flex align-items-center" style="background: #005C66; font-size: 0.8rem; gap: 4px;">
                        <i class="bi bi-bag-check-fill me-1"></i> Beli Sekarang
                    </a>
                @endif
                <button type="button" class="btn-close ms-2" aria-label="Close" onclick="document.getElementById('stickyProductBox')?.remove(); document.getElementById('chatProduk')?.remove(); document.getElementById('chatProdukChip')?.remove();" title="Tutup kartu produk"></button>
            </div>
        </div>
    @endif

    {{-- KOTAK DAFTAR PESAN --}}
    <div id="chatBox" class="p-3 rounded-4 mb-3" style="background:#f4f9fa; border:1px solid rgba(0,92,102,.15); height:60vh; overflow-y:auto; scroll-behavior: smooth;">
        @if($pesan->isEmpty())
            <div id="chatKosong" class="text-center text-muted small mt-5">
                <i class="bi bi-chat-heart fs-2 d-block mb-2" style="color: #00B8BE;"></i>
                Belum ada pesan. Mulai percakapan di bawah.
            </div>
        @endif
        @include('chat._pesan', [
            'pesan' => $pesan, 
            'uid' => $uid, 
            'sebagaiSeller' => $sebagaiSeller, 
            'rp' => $rp, 
            'percakapanId' => $c->PercakapanID
        ])
    </div>

    {{-- FORM INPUT CHAT --}}
    <form id="chatForm" method="POST" action="{{ route($rp . '.kirim', $c->PercakapanID) }}" class="mb-4">
        @csrf
        @if($produkRef && !empty($isProdukBaru))
            <input type="hidden" name="produk_id" id="chatProduk" value="{{ $produkRef->ProdukID }}">
            <div id="chatProdukChip" class="small mb-2 d-flex align-items-center justify-content-between p-2 px-3 rounded-3 shadow-sm bg-white" style="border: 1px dashed rgba(0, 92, 102, 0.4); color: #005C66;">
                <span>
                    <i class="bi bi-card-image me-1" style="color: #00B8BE;"></i> Kartu produk <strong>{{ $produkRef->NamaProduk }}</strong> akan dikirim bersama pesan pertama.
                </span>
                <button type="button" class="btn btn-link p-0 text-muted" onclick="document.getElementById('chatProduk')?.remove(); document.getElementById('chatProdukChip')?.remove();" title="Batal sertakan produk">
                    <i class="bi bi-x-circle-fill text-secondary"></i>
                </button>
            </div>
        @endif
        @if(!$sebagaiSeller && $produkRef && $produkRef->Status === 'Tersedia')
            <div class="mb-2 d-flex align-items-center gap-2">
                <button type="button" class="btn btn-sm rounded-pill px-3 py-1 fw-semibold d-inline-flex align-items-center gap-1 shadow-xs" 
                        style="background: #e6fffa; border: 1.5px solid #00B8BE; color: #005C66; font-size: 0.78rem;"
                        onclick="bukaModalTawar({{ $produkRef->ProdukID }}, '{{ addslashes($produkRef->NamaProduk) }}', {{ (int) $produkRef->Harga }}, '{{ $produkRef->gambar_url }}')">
                    <i class="bi bi-tag-fill text-warning"></i> Nego / Tawar Harga Produk (Rp {{ number_format($produkRef->Harga, 0, ',', '.') }})
                </button>
            </div>
        @endif
        <div class="d-flex gap-2 align-items-end">
            <textarea name="isi" id="chatInput" rows="2" maxlength="1000" required class="form-control rounded-3"
                      style="resize: none; border-color: rgba(0, 92, 102, 0.25); font-size: 0.93rem;"
                      placeholder="Tulis pesan... (Enter = kirim, Shift+Enter = baris baru)">@if($produkRef && !empty($isProdukBaru))Halo, saya tertarik dengan "{{ $produkRef->NamaProduk }}" di SecondSpace.@endif</textarea>
            <button type="submit" id="btnKirim" class="btn px-4 fw-bold text-white rounded-3 shadow-sm d-flex align-items-center justify-content-center" 
                    style="background: #005C66; min-height: 58px; transition: background 0.2s;" 
                    title="Kirim pesan">
                <i class="bi bi-send-fill fs-5"></i>
            </button>
        </div>
    </form>
</div>

{{-- MODAL TAWAR HARGA INTERAKTIF (SHOPEE STYLE) --}}
@if(!$sebagaiSeller)
<div class="modal fade" id="modalTawarHarga" tabindex="-1" aria-labelledby="modalTawarHargaLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" style="max-width: 440px;">
        <div class="modal-content rounded-4 border-0 shadow">
            <div class="modal-header border-bottom pb-2">
                <h5 class="modal-title fw-bold fs-6 d-flex align-items-center gap-2" id="modalTawarHargaLabel" style="color: #001B27;">
                    <i class="bi bi-tag-fill text-warning"></i> Tawar Harga Produk
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="formTawarHarga" onsubmit="submitTawaran(event)">
                @csrf
                <input type="hidden" id="tawarProdukId" name="produk_id" value="">
                <input type="hidden" id="tawarHargaAsli" value="0">
                <div class="modal-body p-3">
                    {{-- Ringkasan Produk --}}
                    <div class="d-flex align-items-center gap-3 p-2.5 p-2 rounded-3 bg-light border mb-3">
                        <img id="tawarImg" src="" class="rounded-2 flex-shrink-0" style="width: 54px; height: 54px; object-fit: cover;" alt="Produk">
                        <div class="overflow-hidden">
                            <div id="tawarNamaProduk" class="fw-bold text-truncate small" style="color: #001B27;"></div>
                            <div class="small text-muted">Harga Pas: <strong id="tawarHargaNormal" class="text-dark"></strong></div>
                        </div>
                    </div>

                    {{-- Input Harga Tawaran --}}
                    <div class="mb-3">
                        <label class="form-label fw-bold small text-dark">
                            Ajukan Harga Tawaran Anda (Rp) <span class="text-danger">*</span>
                        </label>
                        <div class="input-group">
                            <span class="input-group-text bg-white fw-bold" style="color: #005C66; border-color: rgba(0, 92, 102, 0.3);">Rp</span>
                            <input type="text" id="displayTawarHarga" class="form-control fw-bold" placeholder="Contoh: 65.000" inputmode="numeric" required style="border-color: rgba(0, 92, 102, 0.3); color: #001B27; font-size: 1.1rem;" autocomplete="off">
                        </div>
                        <input type="hidden" id="inputTawarHarga" name="harga_tawaran" value="">
                        <div id="tawarErrorText" class="text-danger small mt-1 d-none"></div>

                        {{-- Chips Diskon Cepat --}}
                        <div class="mt-2 pt-1">
                            <small class="text-muted d-block mb-1" style="font-size: 0.72rem;">Pilihan Penawaran Cepat:</small>
                            <div class="d-flex flex-wrap" id="chipsDiskon" style="gap: 6px;"></div>
                        </div>
                    </div>

                    {{-- Catatan Opsional --}}
                    <div class="mb-2">
                        <label class="form-label fw-semibold small text-muted">Catatan ke Penjual (Opsional)</label>
                        <input type="text" id="tawarCatatan" name="catatan" class="form-control form-control-sm" maxlength="255" placeholder="Contoh: Bisa nego tipis kak? Langsung saya CO">
                    </div>
                </div>
                <div class="modal-footer border-top pt-2">
                    <button type="button" class="btn btn-sm btn-light rounded-pill px-3" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" id="btnSubmitTawar" class="btn btn-sm rounded-pill px-4 fw-bold text-white shadow-sm" style="background: #005C66;">
                        <i class="bi bi-send-fill me-1"></i> Kirim Tawaran
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endif
@endsection

@push('scripts')
<script>
(function () {
    const box = document.getElementById('chatBox');
    const form = document.getElementById('chatForm');
    const input = document.getElementById('chatInput');
    const btnKirim = document.getElementById('btnKirim');
    const csrfToken = '{{ csrf_token() }}';

    const urlPesan = @json(route($rp . '.pesan', $c->PercakapanID));
    const urlUpdatePesanBase = '{{ url($sebagaiSeller ? "seller/chat" : "pelanggan/chat") }}/{{ $c->PercakapanID }}/pesan/';
    const urlHapusPesanBase = '{{ url($sebagaiSeller ? "seller/chat" : "pelanggan/chat") }}/{{ $c->PercakapanID }}/pesan/';
    const urlTawar = '{{ url("pelanggan/chat/" . $c->PercakapanID . "/tawar") }}';
    const urlResponTawarBase = '{{ url("seller/chat/" . $c->PercakapanID . "/tawar") }}/';
    const urlBatalTawarBase = '{{ url("pelanggan/chat/" . $c->PercakapanID . "/tawar") }}/';

    let terakhir = {{ (int) ($pesan->max('PesanID') ?? 0) }};
    let sedangAmbil = false;
    let sedangKirim = false;

    const keBawah = () => { 
        box.scrollTop = box.scrollHeight; 
    };
    keBawah();

    // Fungsi polling pesan baru
    async function ambil(manual = false) {
        if (sedangAmbil || sedangKirim) return;
        sedangAmbil = true;
        try {
            const r = await fetch(urlPesan + '?setelah=' + terakhir, { 
                headers: { 
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                } 
            });
            if (!r.ok) return;
            const d = await r.json();
            if (d.jumlah > 0) {
                document.getElementById('chatKosong')?.remove();
                const dekatBawah = box.scrollHeight - box.scrollTop - box.clientHeight < 120;
                box.insertAdjacentHTML('beforeend', d.html);
                terakhir = Math.max(terakhir, d.terakhir);
                if (dekatBawah || manual) keBawah();
            }
        } catch (e) {
            // Silently ignore polling network glitch
        } finally { 
            sedangAmbil = false; 
        }
    }

    // Polling periodik (setiap 4 detik jika tab aktif)
    const pollInterval = setInterval(() => { 
        if (!document.hidden && !sedangKirim) ambil(); 
    }, 4000);

    // Pengiriman pesan via AJAX
    form.addEventListener('submit', async function (e) {
        e.preventDefault();
        const pesanTeks = input.value.trim();
        if (!pesanTeks) {
            alert('Pesan tidak boleh kosong.');
            input.focus();
            return;
        }

        btnKirim.disabled = true;
        btnKirim.innerHTML = '<span class="spinner-border spinner-border-sm"></span>';
        sedangKirim = true;

        try {
            const formData = new FormData(form);
            const r = await fetch(form.action, {
                method: 'POST',
                headers: { 
                    'Accept': 'application/json', 
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': csrfToken
                },
                body: formData,
            });

            if (r.ok) {
                const resData = await r.json();
                input.value = '';
                document.getElementById('chatProduk')?.remove();
                document.getElementById('chatProdukChip')?.remove();
                document.getElementById('chatKosong')?.remove();

                if (resData.html) {
                    box.insertAdjacentHTML('beforeend', resData.html);
                    if (resData.pesan_id) {
                        terakhir = Math.max(terakhir, parseInt(resData.pesan_id));
                    }
                    keBawah();
                } else {
                    await ambil(true);
                }
            } else if (r.status === 419) {
                alert('Sesi Anda telah kedaluwarsa. Silakan refresh halaman.');
            } else if (r.status === 422) {
                const errData = await r.json().catch(() => ({}));
                const pesanError = errData.errors?.isi?.[0] || errData.message || 'Pesan tidak valid.';
                alert(pesanError);
            } else {
                const errData = await r.json().catch(() => ({}));
                alert(errData.message || 'Pesan gagal dikirim (Server error ' + r.status + '). Coba lagi.');
            }
        } catch (err) {
            alert('Koneksi bermasalah atau terputus. Pastikan server aktif dan coba lagi.');
        } finally {
            btnKirim.disabled = false;
            btnKirim.innerHTML = '<i class="bi bi-send-fill fs-5"></i>';
            sedangKirim = false;
            input.focus();
        }
    });

    // Enter untuk kirim, Shift+Enter untuk baris baru
    input.addEventListener('keydown', function (e) {
        if (e.key === 'Enter' && !e.shiftKey) {
            e.preventDefault();
            form.requestSubmit();
        }
    });

    // EDIT PESAN
    window.mulaiEditPesan = function (pesanId) {
        const textEl = document.getElementById('pesan-text-' + pesanId);
        const formEl = document.getElementById('pesan-edit-form-' + pesanId);
        if (textEl && formEl) {
            textEl.classList.add('d-none');
            formEl.classList.remove('d-none');
            const textarea = document.getElementById('edit-input-' + pesanId);
            if (textarea) textarea.focus();
        }
    };

    window.batalEditPesan = function (pesanId) {
        const textEl = document.getElementById('pesan-text-' + pesanId);
        const formEl = document.getElementById('pesan-edit-form-' + pesanId);
        if (textEl && formEl) {
            formEl.classList.add('d-none');
            textEl.classList.remove('d-none');
        }
    };

    window.simpanEditPesan = async function (pesanId) {
        const textarea = document.getElementById('edit-input-' + pesanId);
        if (!textarea) return;
        const isiBaru = textarea.value.trim();
        if (!isiBaru) {
            alert('Isi pesan tidak boleh kosong.');
            return;
        }

        try {
            const r = await fetch(urlUpdatePesanBase + pesanId, {
                method: 'PUT',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': csrfToken
                },
                body: JSON.stringify({ isi: isiBaru })
            });

            if (r.ok) {
                const data = await r.json();
                const textEl = document.getElementById('pesan-text-' + pesanId);
                const badgeEl = document.getElementById('badge-edited-' + pesanId);
                if (textEl) {
                    textEl.textContent = data.isi || isiBaru;
                    textEl.classList.remove('d-none');
                }
                if (badgeEl) badgeEl.classList.remove('d-none');
                document.getElementById('pesan-edit-form-' + pesanId)?.classList.add('d-none');
            } else {
                const errData = await r.json().catch(() => ({}));
                alert(errData.errors?.isi?.[0] || errData.message || 'Gagal mengubah pesan.');
            }
        } catch (e) {
            alert('Gagal menghubungi server untuk mengedit pesan.');
        }
    };

    // HAPUS PESAN
    window.hapusPesanItem = async function (pesanId) {
        if (!confirm('Apakah Anda yakin ingin menghapus pesan ini?')) {
            return;
        }

        try {
            const r = await fetch(urlHapusPesanBase + pesanId, {
                method: 'DELETE',
                headers: {
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': csrfToken
                }
            });

            if (r.ok) {
                const row = document.getElementById('chat-item-' + pesanId);
                if (row) {
                    row.style.transition = 'opacity 0.25s, transform 0.25s, max-height 0.25s';
                    row.style.opacity = '0';
                    row.style.transform = 'scale(0.9)';
                    setTimeout(() => row.remove(), 250);
                }
            } else {
                const errData = await r.json().catch(() => ({}));
                alert(errData.message || 'Gagal menghapus pesan.');
            }
        } catch (e) {
            alert('Gagal menghubungi server untuk menghapus pesan.');
        }
    };

    // NAVIGASI KEMBALI KE HALAMAN SEBELUMNYA SECARA CERDAS
    const btnKembali = document.getElementById('btnKembaliChat');
    if (btnKembali) {
        btnKembali.addEventListener('click', function (e) {
            // Cek jika referrer browser tersedia dan bukan chat room saat ini / action
            if (document.referrer) {
                try {
                    const refUrl = new URL(document.referrer);
                    const curUrl = new URL(window.location.href);

                    const isLocal = (h) => ['localhost', '127.0.0.1'].includes(h);
                    const isSameHost = (refUrl.hostname === curUrl.hostname) || (isLocal(refUrl.hostname) && isLocal(curUrl.hostname));

                    const isIgnored = refUrl.pathname.includes('/chat/mulai') || 
                                      refUrl.pathname.endsWith('/pesan') || 
                                      refUrl.pathname.includes('/logout') || 
                                      (refUrl.pathname === curUrl.pathname);

                    if (isSameHost && !isIgnored) {
                        e.preventDefault();
                        if (window.history.length > 1) {
                            window.history.back();
                            return;
                        }
                        window.location.href = document.referrer;
                        return;
                    }
                } catch (err) {}
            }
            // Jika tidak ada riwayat browser / referrer, biarkan link langsung menuju href ($backUrl) yang sudah disiapkan
        });
    }

    // ==========================================
    // LOGIKA TAWAR HARGA INTERAKTIF (SHOPEE STYLE)
    // ==========================================
    window.bukaModalTawar = function (prodId, nama, harga, img) {
        const prodInput = document.getElementById('tawarProdukId');
        if (!prodInput) return;

        prodInput.value = prodId;
        document.getElementById('tawarHargaAsli').value = harga;
        document.getElementById('tawarNamaProduk').textContent = nama;
        document.getElementById('tawarHargaNormal').textContent = 'Rp ' + Number(harga).toLocaleString('id-ID');
        document.getElementById('tawarImg').src = img || '{{ asset("upload/default.jpg") }}';

        // Reset input
        document.getElementById('displayTawarHarga').value = '';
        document.getElementById('inputTawarHarga').value = '';
        document.getElementById('tawarCatatan').value = '';
        document.getElementById('tawarErrorText').classList.add('d-none');

        // Render Chip Diskon Cepat (5%, 10%, 15%, 20%)
        const chipsContainer = document.getElementById('chipsDiskon');
        chipsContainer.innerHTML = '';
        [5, 10, 15, 20].forEach(p => {
            const potongan = Math.round(harga * (p / 100));
            const hargaBaru = Math.round((harga - potongan) / 500) * 500;
            const btn = document.createElement('button');
            btn.type = 'button';
            btn.className = 'btn btn-outline-secondary btn-sm py-0 px-2 rounded-pill';
            btn.style.fontSize = '0.72rem';
            btn.textContent = `-${p}% (Rp ${hargaBaru.toLocaleString('id-ID')})`;
            btn.onclick = () => {
                document.getElementById('inputTawarHarga').value = hargaBaru;
                document.getElementById('displayTawarHarga').value = hargaBaru.toLocaleString('id-ID');
                document.getElementById('tawarErrorText').classList.add('d-none');
            };
            chipsContainer.appendChild(btn);
        });

        const modalEl = document.getElementById('modalTawarHarga');
        if (modalEl && window.bootstrap) {
            const modal = bootstrap.Modal.getOrCreateInstance(modalEl);
            modal.show();
        }
    };

    // Format input ribuan real-time
    const displayTawar = document.getElementById('displayTawarHarga');
    const inputTawar = document.getElementById('inputTawarHarga');
    if (displayTawar && inputTawar) {
        displayTawar.addEventListener('input', function () {
            let val = this.value.replace(/\D/g, '');
            if (!val) {
                this.value = '';
                inputTawar.value = '';
                return;
            }
            inputTawar.value = val;
            this.value = Number(val).toLocaleString('id-ID');
            document.getElementById('tawarErrorText').classList.add('d-none');
        });
    }

    // Submit Penawaran Harga (AJAX)
    window.submitTawaran = async function (e) {
        e.preventDefault();
        const prodId = document.getElementById('tawarProdukId').value;
        const hargaAsli = parseInt(document.getElementById('tawarHargaAsli').value) || 0;
        const hargaTawar = parseInt(document.getElementById('inputTawarHarga').value) || 0;
        const catatan = document.getElementById('tawarCatatan').value;
        const errEl = document.getElementById('tawarErrorText');
        const btn = document.getElementById('btnSubmitTawar');

        if (!hargaTawar || hargaTawar <= 0) {
            errEl.textContent = 'Masukkan nominal harga tawaran.';
            errEl.classList.remove('d-none');
            return;
        }

        if (hargaTawar >= hargaAsli) {
            errEl.textContent = 'Harga tawaran harus lebih rendah dari harga normal (Rp ' + hargaAsli.toLocaleString('id-ID') + ').';
            errEl.classList.remove('d-none');
            return;
        }

        btn.disabled = true;
        btn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span> Mengirim...';

        try {
            const r = await fetch(urlTawar, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': csrfToken
                },
                body: JSON.stringify({
                    produk_id: prodId,
                    harga_tawaran: hargaTawar,
                    catatan: catatan
                })
            });

            const res = await r.json();
            if (r.ok && res.ok) {
                const modalEl = document.getElementById('modalTawarHarga');
                if (modalEl && window.bootstrap) {
                    const modal = bootstrap.Modal.getInstance(modalEl);
                    if (modal) modal.hide();
                }

                document.getElementById('chatKosong')?.remove();
                box.insertAdjacentHTML('beforeend', res.html);
                terakhir = Math.max(terakhir, res.pesan_id || 0);
                keBawah();
            } else {
                errEl.textContent = res.pesan || 'Gagal mengirim penawaran harga.';
                errEl.classList.remove('d-none');
            }
        } catch (e) {
            errEl.textContent = 'Terjadi kesalahan jaringan saat mengirim tawaran.';
            errEl.classList.remove('d-none');
        } finally {
            btn.disabled = false;
            btn.innerHTML = '<i class="bi bi-send-fill me-1"></i> Kirim Tawaran';
        }
    };

    // Respon Penjual Terhadap Tawaran (Terima / Tolak)
    window.responTawaran = async function (tawaranId, aksi) {
        const pesanKonfirmasi = aksi === 'terima' 
            ? 'Apakah Anda yakin ingin MENERIMA tawaran harga ini? Pembeli dapat langsung checkout dengan harga yang disepakati.' 
            : 'Apakah Anda yakin ingin MENOLAK tawaran harga ini?';

        if (!confirm(pesanKonfirmasi)) {
            return;
        }

        try {
            const r = await fetch(urlResponTawarBase + tawaranId + '/respon', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': csrfToken
                },
                body: JSON.stringify({ aksi: aksi })
            });

            const res = await r.json();
            if (r.ok && res.ok) {
                // Refresh riwayat pesan langsung agar status kartu berubah menjadi Diterima/Ditolak
                ambil(true);
            } else {
                alert(res.pesan || 'Gagal merespons tawaran.');
            }
        } catch (e) {
            alert('Terjadi kesalahan jaringan saat merespons tawaran.');
        }
    };

    // Pembeli Membatalkan Tawaran
    window.batalTawaran = async function (tawaranId) {
        if (!confirm('Apakah Anda yakin ingin membatalkan pengajuan tawaran ini?')) {
            return;
        }

        try {
            const r = await fetch(urlBatalTawarBase + tawaranId + '/batal', {
                method: 'POST',
                headers: {
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': csrfToken
                }
            });

            const res = await r.json();
            if (r.ok && res.ok) {
                ambil(true);
            } else {
                alert(res.pesan || 'Gagal membatalkan tawaran.');
            }
        } catch (e) {
            alert('Terjadi kesalahan jaringan saat membatalkan tawaran.');
        }
    };

})();
</script>
@endpush
