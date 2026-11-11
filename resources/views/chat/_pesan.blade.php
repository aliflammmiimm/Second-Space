@foreach($pesan as $m)
    @php($milikku = (int) $m->PengirimID === (int) $uid)
    <div class="d-flex mb-2 {{ $milikku ? 'justify-content-end' : 'justify-content-start' }}">
        <div class="px-3 py-2 rounded-4 shadow-sm" style="max-width: 75%; {{ $milikku ? 'background:#005C66; color:#fff;' : 'background:#fff; color:#001B27; border:1px solid rgba(0,92,102,.15);' }}">
            @if($m->produk)
                <a href="{{ route('pelanggan.produk.detail', $m->produk->ProdukID) }}" class="d-block small mb-1 text-decoration-none" style="color: {{ $milikku ? '#bff' : '#005C66' }};">
                    <i class="bi bi-tag me-1"></i>{{ $m->produk->NamaProduk }}
                </a>
            @endif
            <div style="white-space: pre-line; word-break: break-word;">{{ $m->Isi }}</div>
            <div class="text-end opacity-75" style="font-size: .7rem;">{{ $m->DibuatPada->format('d M H:i') }}</div>
        </div>
    </div>
@endforeach

