@extends($layout)
@section('title', 'Pesan - SecondSpace')
@section('no_footer', true)

@section($section)
<div class="{{ $sebagaiSeller ? '' : 'container py-4' }}" style="max-width: 820px; {{ $sebagaiSeller ? '' : 'margin: 0 auto;' }}">
    <h4 class="fw-bold mb-3" style="color: #001B27;"><i class="bi bi-chat-dots me-2" style="color: #00B8BE;"></i> Pesan</h4>

    @forelse($daftar as $c)
        @php($lawan = $sebagaiSeller ? $c->pembeli : $c->seller)
        <a href="{{ route($rp . '.show', ['id' => $c->PercakapanID, 'ref' => url()->full()]) }}" class="card custom-card border-0 shadow-sm p-3 mb-2 text-decoration-none">
            <div class="d-flex justify-content-between align-items-center gap-3">
                <div class="d-flex align-items-center gap-3 overflow-hidden">
                    <div class="rounded-circle d-flex align-items-center justify-content-center flex-shrink-0" style="width: 44px; height: 44px; background: rgba(0, 92, 102, 0.1); color: #005C66;">
                        <i class="bi bi-person-circle fs-4"></i>
                    </div>
                    <div class="overflow-hidden">
                        <div class="fw-bold d-flex align-items-center gap-2" style="color:#001B27;">
                            <span>{{ $lawan->Nama ?? 'Pengguna' }}</span>
                            <span class="badge rounded-pill" style="font-size: 0.68rem; font-weight: normal; background: rgba(0, 92, 102, 0.08); color: #005C66;">
                                {{ $sebagaiSeller ? 'Pembeli' : 'Penjual' }}
                            </span>
                        </div>
                        <div class="small text-muted text-truncate mt-0.5">
                            {{ \Illuminate\Support\Str::limit($c->pesanTerakhir->Isi ?? '', 70) }}
                        </div>
                    </div>
                </div>
                <div class="text-end flex-shrink-0">
                    <div class="small text-muted mb-1">{{ $c->TerakhirPada ? \Carbon\Carbon::parse($c->TerakhirPada)->timezone('Asia/Makassar')->diffForHumans() : '' }}</div>
                    @if($c->belum_dibaca > 0)
                        <span class="badge bg-danger rounded-pill px-2 py-1">{{ $c->belum_dibaca }}</span>
                    @endif
                </div>
            </div>
        </a>
    @empty
        <div class="card custom-card border-0 p-5 text-center text-muted">Belum ada percakapan.</div>
    @endforelse

    <div class="mt-3">{{ $daftar->links() }}</div>
</div>
@endsection

