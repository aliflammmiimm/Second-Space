@extends($layout)
@section('title', 'Pesan - SecondSpace')

@section($section)
<div class="{{ $sebagaiSeller ? '' : 'container py-4' }}" style="max-width: 820px; {{ $sebagaiSeller ? '' : 'margin: 0 auto;' }}">
    <h4 class="fw-bold mb-3"><i class="bi bi-chat-dots text-danger me-2"></i> Pesan</h4>

    @forelse($daftar as $c)
        @php($lawan = $sebagaiSeller ? $c->pembeli : $c->seller)
        <a href="{{ route($rp . '.show', $c->PercakapanID) }}" class="card custom-card border-0 shadow-sm p-3 mb-2 text-decoration-none">
            <div class="d-flex justify-content-between align-items-center gap-3">
                <div class="overflow-hidden">
                    <div class="fw-bold" style="color:#001B27;">{{ $lawan->Nama ?? 'Pengguna' }}</div>
                    <div class="small text-muted text-truncate">
                        {{ \Illuminate\Support\Str::limit($c->pesanTerakhir->Isi ?? '', 70) }}
                    </div>
                </div>
                <div class="text-end flex-shrink-0">
                    <div class="small text-muted">{{ $c->TerakhirPada?->diffForHumans() }}</div>
                    @if($c->belum_dibaca > 0)
                        <span class="badge bg-danger rounded-pill">{{ $c->belum_dibaca }}</span>
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

