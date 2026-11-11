@extends($layout)
@section('title', 'Chat dengan ' . ($lawan->Nama ?? 'Pengguna') . ' - SecondSpace')

@section($section)
<div class="{{ $sebagaiSeller ? '' : 'container py-4' }}" style="max-width: 820px; {{ $sebagaiSeller ? '' : 'margin: 0 auto;' }}">
    <div class="d-flex align-items-center gap-2 mb-3">
        <a href="{{ route($rp . '.index') }}" class="btn btn-outline-secondary btn-sm rounded-pill">
            <i class="bi bi-arrow-left"></i>
        </a>
        <h5 class="fw-bold mb-0" style="color:#001B27;">
            <i class="bi bi-person-circle me-1"></i> {{ $lawan->Nama ?? 'Pengguna' }}
            <small class="text-muted fw-normal">({{ $sebagaiSeller ? 'Pembeli' : 'Penjual' }})</small>
        </h5>
    </div>

    <div id="chatBox" class="p-3 rounded-4 mb-3" style="background:#f4f9fa; border:1px solid rgba(0,92,102,.15); height:60vh; overflow-y:auto;">
        @if($pesan->isEmpty())
            <div id="chatKosong" class="text-center text-muted small mt-5">Belum ada pesan. Mulai percakapan di bawah.</div>
        @endif
        @include('chat._pesan', ['pesan' => $pesan, 'uid' => $uid])
    </div>

    <form id="chatForm" method="POST" action="{{ route($rp . '.kirim', $c->PercakapanID) }}">
        @csrf
        @if($produkRef)
            <input type="hidden" name="produk_id" id="chatProduk" value="{{ $produkRef->ProdukID }}">
            <div id="chatProdukChip" class="small mb-2" style="color:#005C66;">
                <i class="bi bi-tag me-1"></i> Menanyakan: <strong>{{ $produkRef->NamaProduk }}</strong>
            </div>
        @endif
        <div class="d-flex gap-2 align-items-end">
            <textarea name="isi" rows="2" maxlength="1000" required class="form-control"
                      placeholder="Tulis pesan... (Enter = kirim, Shift+Enter = baris baru)">@if($produkRef)Halo, saya tertarik dengan "{{ $produkRef->NamaProduk }}" di SecondSpace.@endif</textarea>
            <button type="submit" class="btn btn-danger px-4 fw-bold"><i class="bi bi-send"></i></button>
        </div>
    </form>
</div>
@endsection

@push('scripts')
<script>
(function () {
    const box = document.getElementById('chatBox');
    const form = document.getElementById('chatForm');
    const urlPesan = @json(route($rp . '.pesan', $c->PercakapanID));
    let terakhir = {{ (int) ($pesan->max('PesanID') ?? 0) }};
    let sedang = false;

    const keBawah = () => { box.scrollTop = box.scrollHeight; };
    keBawah();

    async function ambil() {
        if (sedang) return;
        sedang = true;
        try {
            const r = await fetch(urlPesan + '?setelah=' + terakhir, { headers: { 'Accept': 'application/json' } });
            if (!r.ok) return;
            const d = await r.json();
            if (d.jumlah > 0) {
                document.getElementById('chatKosong')?.remove();
                const dekat = box.scrollHeight - box.scrollTop - box.clientHeight < 120;
                box.insertAdjacentHTML('beforeend', d.html);
                terakhir = d.terakhir;
                if (dekat) keBawah();
            }
        } catch (e) { /* abaikan, coba lagi di putaran berikutnya */ }
        finally { sedang = false; }
    }

    setInterval(() => { if (!document.hidden) ambil(); }, 3000);

    form.addEventListener('submit', async function (e) {
        e.preventDefault();
        const tombol = form.querySelector('button[type=submit]');
        tombol.disabled = true;
        try {
            const r = await fetch(form.action, {
                method: 'POST',
                headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
                body: new FormData(form),
            });
            if (r.ok) {
                form.isi.value = '';
                document.getElementById('chatProduk')?.remove();
                document.getElementById('chatProdukChip')?.remove();
                await ambil();
                keBawah();
            } else {
                alert('Pesan gagal dikirim. Pastikan pesan tidak kosong dan maksimal 1000 karakter.');
            }
        } catch (err) {
            alert('Koneksi bermasalah. Coba lagi.');
        } finally {
            tombol.disabled = false;
            form.isi.focus();
        }
    });

    form.isi.addEventListener('keydown', function (e) {
        if (e.key === 'Enter' && !e.shiftKey) {
            e.preventDefault();
            form.requestSubmit();
        }
    });
})();
</script>
@endpush

