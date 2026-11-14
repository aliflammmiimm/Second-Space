@extends($layout)

@section('title', 'Pusat Notifikasi - SecondSpace')

@section($section)
@if(!$isSeller)
<div class="container py-4">
@endif

    <div class="d-flex align-items-center justify-content-between mb-4 flex-wrap gap-3">
        <div>
            <h3 class="fw-bold mb-1" style="color: #001B27;">
                <i class="bi bi-bell-fill me-2" style="color: #00B8BE;"></i> Pusat Notifikasi
            </h3>
            <p class="text-muted small mb-0">Pantau pembaruan pesanan, penawaran harga, ulasan produk, dan informasi penting lainnya.</p>
        </div>
        @if($daftar->total() > 0 && $unreadCount > 0)
            <form action="{{ route('notifikasi.tandai_semua_dibaca') }}" method="POST" class="m-0">
                @csrf
                <input type="hidden" name="as" value="{{ $isSeller ? 'seller' : 'web' }}">
                <button type="submit" class="btn btn-sm btn-outline-secondary rounded-pill px-3 py-2 fw-semibold d-inline-flex align-items-center gap-1 shadow-sm">
                    <i class="bi bi-check2-all fs-6 text-success"></i>
                    <span>Tandai Semua Dibaca</span>
                </button>
            </form>
        @endif
    </div>

    <!-- Filter Tabs -->
    <div class="d-flex align-items-center gap-2 mb-4 border-bottom pb-3 flex-wrap">
        <a href="{{ route($rp, ['filter' => 'semua']) }}" class="btn btn-sm rounded-pill px-3 py-1.5 fw-semibold {{ $filter !== 'belum_dibaca' ? 'btn-dark' : 'btn-outline-secondary' }}">
            Semua Notifikasi ({{ $daftar->total() }})
        </a>
        <a href="{{ route($rp, ['filter' => 'belum_dibaca']) }}" class="btn btn-sm rounded-pill px-3 py-1.5 fw-semibold position-relative {{ $filter === 'belum_dibaca' ? 'btn-dark' : 'btn-outline-secondary' }}">
            Belum Dibaca
            @if($unreadCount > 0)
                <span class="badge rounded-pill bg-danger ms-1" style="font-size: 0.7rem;">{{ $unreadCount }}</span>
            @endif
        </a>
    </div>

    @if($daftar->isEmpty())
        <div class="card custom-card border-0 p-5 text-center shadow-sm">
            <div class="display-4 text-muted mb-3"><i class="bi bi-bell-slash"></i></div>
            <h5 class="fw-bold" style="color: #001B27;">Tidak Ada Notifikasi</h5>
            <p class="text-muted small mb-0">
                {{ $filter === 'belum_dibaca' ? 'Semua notifikasi sudah dibaca.' : 'Saat ini belum ada notifikasi untuk akun Anda.' }}
            </p>
        </div>
    @else
        <div class="d-flex flex-column gap-3 mb-4">
            @foreach($daftar as $item)
                @php($isRead = !is_null($item->DibacaPada))
                <div class="card custom-card border-0 p-3 shadow-sm" style="{{ $isRead ? 'background: #ffffff;' : 'background: rgba(0, 184, 190, 0.04); border-left: 4px solid #00B8BE !important;' }}">
                    <div class="d-flex align-items-start justify-content-between gap-3 flex-wrap flex-md-nowrap">
                        <div class="d-flex align-items-start gap-3">
                            <div class="rounded-circle d-flex align-items-center justify-content-center flex-shrink-0 shadow-sm" style="width: 44px; height: 44px; background: rgba(0, 184, 190, 0.12); color: {{ $item->IconColor ?: '#00B8BE' }}; font-size: 1.25rem;">
                                <i class="bi {{ $item->Icon ?: 'bi-bell-fill' }}"></i>
                            </div>
                            <div>
                                <div class="d-flex align-items-center gap-2 mb-1 flex-wrap">
                                    <h6 class="fw-bold mb-0" style="color: #001B27;">{{ $item->Judul }}</h6>
                                    @if(!$isRead)
                                        <span class="badge rounded-pill" style="background: rgba(0, 184, 190, 0.2); color: #005C66; font-size: 0.68rem; font-weight: 700;">
                                            BARU
                                        </span>
                                    @endif
                                </div>
                                <p class="text-muted small mb-2" style="line-height: 1.55; white-space: pre-line;">{{ $item->Pesan }}</p>
                                <div class="text-muted d-flex align-items-center gap-2" style="font-size: 0.75rem;">
                                    <span><i class="bi bi-clock me-1"></i>{{ \Carbon\Carbon::parse($item->DibuatPada)->timezone('Asia/Makassar')->translatedFormat('d M Y, H:i') }} WITA</span>
                                    <span>&bull;</span>
                                    <span>{{ \Carbon\Carbon::parse($item->DibuatPada)->timezone('Asia/Makassar')->diffForHumans() }}</span>
                                </div>
                            </div>
                        </div>
                        <div class="d-flex align-items-center gap-2 align-self-md-center ms-auto ms-md-0 mt-2 mt-md-0">
                            @if($item->Url)
                                <a href="{{ route('notifikasi.baca', $item->NotifikasiID) }}" class="btn btn-sm btn-dark rounded-pill px-3 py-1.5 fw-semibold d-inline-flex align-items-center gap-1 shadow-sm text-nowrap" style="font-size: 0.82rem;">
                                    <span>Buka Detail</span>
                                    <i class="bi bi-arrow-right"></i>
                                </a>
                            @else
                                @if(!$isRead)
                                    <a href="{{ route('notifikasi.baca', $item->NotifikasiID) }}" class="btn btn-sm btn-outline-secondary rounded-pill px-3 py-1.5 fw-semibold text-nowrap" style="font-size: 0.82rem;">
                                        Tandai Dibaca
                                    </a>
                                @endif
                            @endif
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <div class="mt-4 d-flex justify-content-center">
            {{ $daftar->links() }}
        </div>
    @endif

@if(!$isSeller)
</div>
@endif
@endsection

