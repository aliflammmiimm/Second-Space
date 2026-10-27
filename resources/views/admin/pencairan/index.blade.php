@extends('layouts.app')

@section('title', 'Pencairan Seller - Admin SecondSpace')

@section('content')
<div class="container py-4">

    <div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-3">
        <div>
            <h2 class="fw-bold mb-1"><i class="bi bi-cash-coin text-danger me-2"></i> Pencairan Dana Seller</h2>
            <p class="text-muted mb-0">Transfer manual ke rekening seller, lalu tandai selesai</p>
        </div>
        <a href="{{ route('admin.dashboard') }}" class="btn btn-outline-secondary rounded-pill">
            <i class="bi bi-arrow-left me-1"></i> Dashboard
        </a>
    </div>

    <div class="card custom-card border-0 p-3 mb-4">
        <div class="d-flex gap-2 flex-wrap align-items-center">
            <span class="fw-bold text-muted small me-2"><i class="bi bi-funnel"></i> Status:</span>
            <a href="{{ route('admin.pencairan.index') }}" class="btn btn-sm {{ !request('status') ? 'btn-dark' : 'btn-outline-secondary' }} rounded-pill">Semua</a>
            @foreach(['Menunggu', 'Selesai', 'Ditolak'] as $st)
                <a href="{{ route('admin.pencairan.index', ['status' => $st]) }}" class="btn btn-sm {{ request('status') == $st ? 'btn-dark' : 'btn-outline-secondary' }} rounded-pill">{{ $st }}</a>
            @endforeach
        </div>
    </div>

    @php($warna = ['Menunggu' => 'warning text-dark', 'Selesai' => 'success', 'Ditolak' => 'danger'])
    <div class="card custom-card border-0 p-3"><div class="table-responsive">
        <table class="table align-middle mb-0">
            <thead class="table-light"><tr><th>#</th><th>Seller</th><th>Jumlah</th><th>Rekening Tujuan</th><th>Status</th><th style="width:300px;">Aksi</th></tr></thead>
            <tbody>
            @forelse($pencairan as $p)
                <tr>
                    <td>#{{ $p->PencairanID }}<br><small class="text-muted">{{ $p->created_at->format('d M Y H:i') }}</small></td>
                    <td>{{ $p->seller->Nama ?? '(seller dihapus)' }}<br><small class="text-muted">{{ $p->seller->NoHP_Email ?? '' }}</small></td>
                    <td class="fw-bold">Rp {{ number_format($p->Jumlah, 0, ',', '.') }}</td>
                    <td class="small">{{ $p->Bank }}<br><strong>{{ $p->NoRekening }}</strong><br>a.n. {{ $p->AtasNama }}</td>
                    <td><span class="badge bg-{{ $warna[$p->Status] ?? 'secondary' }}">{{ $p->Status }}</span>
                        @if($p->Catatan)<div class="small text-muted mt-1">{{ $p->Catatan }}</div>@endif</td>
                    <td>
                        @if($p->Status === 'Menunggu')
                            <form method="POST" action="{{ route('admin.pencairan.selesai', $p->PencairanID) }}" class="mb-1" onsubmit="return confirm('Pastikan transfer sudah dilakukan. Tandai selesai?')">
                                @csrf @method('PUT')
                                <button class="btn btn-sm btn-success w-100">Sudah Ditransfer</button>
                            </form>
                            <form method="POST" action="{{ route('admin.pencairan.tolak', $p->PencairanID) }}" class="d-flex gap-1">
                                @csrf @method('PUT')
                                <input type="text" name="Catatan" class="form-control form-control-sm" placeholder="Alasan penolakan" required maxlength="255">
                                <button class="btn btn-sm btn-outline-danger text-nowrap">Tolak</button>
                            </form>
                        @else
                            <span class="text-muted small">Diproses {{ optional($p->DiprosesPada)->format('d M Y H:i') }}</span>
                        @endif
                    </td>
                </tr>
            @empty
                <tr><td colspan="6" class="text-center text-muted py-5">Belum ada permintaan pencairan.</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
    <div class="mt-3">{{ $pencairan->links() }}</div></div>
</div>
@endsection