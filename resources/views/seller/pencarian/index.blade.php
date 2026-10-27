@extends('layouts.seller')
@section('title', 'Saldo & Pencairan - Seller')

@section('seller_content')
@php($warna = ['Menunggu' => 'warning text-dark', 'Selesai' => 'success', 'Ditolak' => 'danger'])
<div>
    <h4 class="fw-bold mb-3"><i class="bi bi-wallet2 text-danger me-2"></i> Saldo &amp; Pencairan</h4>

    <div class="row g-3 mb-4">
        <div class="col-6 col-md-4"><div class="seller-stat"><div class="label">Total Pendapatan Bersih</div><div class="angka" style="font-size:1.25rem;">Rp {{ number_format($totalPendapatan, 0, ',', '.') }}</div></div></div>
        <div class="col-6 col-md-4"><div class="seller-stat"><div class="label">Saldo Dapat Dicairkan</div><div class="angka text-success" style="font-size:1.25rem;">Rp {{ number_format($saldo, 0, ',', '.') }}</div></div></div>
        <div class="col-12 col-md-4"><div class="seller-stat"><div class="label">Komisi Website</div><div class="angka" style="font-size:1.25rem;">{{ config('santara.komisi_persen') }}% per penjualan</div></div></div>
    </div>

    <div class="row g-3 mb-4">
        {{-- REKENING --}}
        <div class="col-md-6">
            <div class="card custom-card border-0 p-3 h-100">
                <h6 class="fw-bold mb-3">Rekening / E-Wallet Tujuan</h6>
                <form method="POST" action="{{ route('seller.pencairan.rekening') }}">
                    @csrf
                    @method('PUT')
                    <div class="mb-2">
                        <label class="form-label small">Bank / E-Wallet</label>
                        <input type="text" name="Bank" class="form-control" placeholder="BCA / Mandiri / DANA" value="{{ old('Bank', $user->Bank) }}" required>
                        @error('Bank')<div class="text-danger small">{{ $message }}</div>@enderror
                    </div>
                    <div class="mb-2">
                        <label class="form-label small">Nomor Rekening</label>
                        <input type="text" name="NoRekening" class="form-control" value="{{ old('NoRekening', $user->NoRekening) }}" required>
                        @error('NoRekening')<div class="text-danger small">{{ $message }}</div>@enderror
                    </div>
                    <div class="mb-3">
                        <label class="form-label small">Atas Nama</label>
                        <input type="text" name="AtasNama" class="form-control" value="{{ old('AtasNama', $user->AtasNama) }}" required>
                        @error('AtasNama')<div class="text-danger small">{{ $message }}</div>@enderror
                    </div>
                    <button class="btn btn-dark rounded-pill px-4">Simpan Rekening</button>
                </form>
            </div>
        </div>

        {{-- AJUKAN PENCAIRAN --}}
        <div class="col-md-6">
            <div class="card custom-card border-0 p-3 h-100">
                <h6 class="fw-bold mb-3">Ajukan Pencairan</h6>
                <form method="POST" action="{{ route('seller.pencairan.store') }}">
                    @csrf
                    <label class="form-label small">Jumlah (minimal Rp {{ number_format($minimal, 0, ',', '.') }})</label>
                    <input type="number" name="Jumlah" class="form-control mb-1" min="{{ $minimal }}" max="{{ max($saldo, 0) }}" value="{{ old('Jumlah') }}" required>
                    @error('Jumlah')<div class="text-danger small mb-2">{{ $message }}</div>@enderror
                    <div class="text-muted small mb-3">Dana ditransfer manual oleh admin ke rekening di samping.</div>
                    <button class="btn btn-danger rounded-pill px-4" {{ $saldo < $minimal ? 'disabled' : '' }}>Ajukan Pencairan</button>
                </form>
            </div>
        </div>
    </div>

    {{-- RIWAYAT --}}
    <div class="card custom-card border-0 p-3"><div class="table-responsive">
        <table class="table align-middle mb-0">
            <thead class="table-light"><tr><th>#</th><th>Tanggal</th><th>Jumlah</th><th>Tujuan</th><th>Status</th><th>Catatan</th></tr></thead>
            <tbody>
            @forelse($riwayat as $r)
                <tr>
                    <td>#{{ $r->PencairanID }}</td>
                    <td>{{ $r->created_at->format('d M Y H:i') }}</td>
                    <td class="fw-bold">Rp {{ number_format($r->Jumlah, 0, ',', '.') }}</td>
                    <td class="small">{{ $r->Bank }} · {{ $r->NoRekening }}<br><span class="text-muted">{{ $r->AtasNama }}</span></td>
                    <td><span class="badge bg-{{ $warna[$r->Status] ?? 'secondary' }}">{{ $r->Status }}</span></td>
                    <td class="small text-muted">{{ $r->Catatan ?? '-' }}</td>
                </tr>
            @empty
                <tr><td colspan="6" class="text-center text-muted py-4">Belum ada riwayat pencairan.</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
    <div class="mt-3">{{ $riwayat->links() }}</div></div>
</div>
@endsection