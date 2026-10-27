@extends('layouts.app')
@section('title', 'Pengajuan Seller - Admin')

@section('content')
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h2 class="fw-bold mb-0"><i class="bi bi-person-check text-danger me-2"></i> Pengajuan Seller</h2>
        <a href="{{ route('admin.dashboard') }}" class="btn btn-outline-secondary rounded-pill btn-sm">Dashboard</a>
    </div>

    <div class="mb-3 d-flex gap-2">
        @foreach(['pending' => 'Menunggu', 'approved' => 'Disetujui', 'rejected' => 'Ditolak'] as $s => $label)
            <a href="{{ route('admin.seller.index', ['status' => $s]) }}" class="btn btn-sm rounded-pill {{ $status === $s ? 'btn-dark' : 'btn-outline-dark' }}">{{ $label }}</a>
        @endforeach
    </div>

    @forelse($pengajuan as $p)
        <div class="card custom-card border-0 p-3 mb-3">
            <div class="d-flex justify-content-between flex-wrap gap-2">
                <div>
                    <div class="fw-bold">{{ $p->Nama }} <small class="text-muted">({{ $p->user->NoHP_Email ?? '-' }})</small></div>
                    <div class="small text-muted">Telp: {{ $p->NoHP }} &bull; {{ $p->Alamat }}</div>
                    <p class="mb-1 mt-2"><strong>Alasan:</strong> {{ $p->Alasan }}</p>
                    @if($p->InfoTambahan)<p class="mb-1 small"><strong>Info:</strong> {{ $p->InfoTambahan }}</p>@endif
                    @if($p->Status === 'rejected')<p class="mb-0 small text-danger">Ditolak: {{ $p->AlasanPenolakan }}</p>@endif
                </div>
                @if($p->Status === 'pending')
                    <div style="min-width: 260px;">
                        <form action="{{ route('admin.seller.approve', $p->PengajuanID) }}" method="POST" class="mb-2">
                            @csrf @method('PUT')
                            <button class="btn btn-success btn-sm rounded-pill w-100">Setujui</button>
                        </form>
                        <form action="{{ route('admin.seller.reject', $p->PengajuanID) }}" method="POST">
                            @csrf @method('PUT')
                            <input name="AlasanPenolakan" class="form-control form-control-sm mb-1" placeholder="Alasan penolakan (wajib)" required maxlength="500">
                            <button class="btn btn-outline-danger btn-sm rounded-pill w-100">Tolak</button>
                        </form>
                    </div>
                @else
                    <span class="badge align-self-start bg-{{ $p->Status === 'approved' ? 'success' : 'danger' }}">{{ strtoupper($p->Status) }}</span>
                @endif
            </div>
        </div>
    @empty
        <div class="text-center text-muted p-5 bg-white rounded-4 border">Tidak ada pengajuan.</div>
    @endforelse

    {{ $pengajuan->links() }}
</div>
@endsection