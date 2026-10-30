@extends('layouts.app')

@section('title', 'Kelola Pembayaran - Admin SecondSpace')

@section('content')
<div class="container py-4">

    <div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-3">
        <div>
            <h2 class="fw-bold mb-1"><i class="bi bi-credit-card text-danger me-2"></i> Verifikasi Pembayaran</h2>
            <p class="text-muted mb-0">Validasi pembayaran transfer dan COD dari pelanggan</p>
        </div>
        <a href="{{ route('admin.dashboard') }}" class="btn btn-outline-secondary rounded-pill">
            <i class="bi bi-arrow-left me-1"></i> Dashboard
        </a>
    </div>

    <!-- FILTER STATUS -->
    <div class="card custom-card border-0 p-3 mb-4">
        <div class="d-flex gap-2 flex-wrap align-items-center">
            <span class="fw-bold text-muted small me-2"><i class="bi bi-funnel"></i> Status:</span>
            <a href="{{ route('admin.pembayaran.index') }}" class="btn btn-sm {{ !request('status') ? 'btn-dark' : 'btn-outline-secondary' }} rounded-pill">
                Semua
            </a>
            @foreach(['Menunggu', 'Valid', 'Tidak Valid'] as $st)
                <a href="{{ route('admin.pembayaran.index', ['status' => $st]) }}" class="btn btn-sm {{ request('status') == $st ? 'btn-dark' : 'btn-outline-secondary' }} rounded-pill">
                    {{ $st }}
                </a>
            @endforeach
        </div>
    </div>

    <div class="card custom-card border-0 p-4">
        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead class="table-light">
                    <tr>
                        <th>ID Bayar</th>
                        <th>ID Pesanan</th>
                        <th>Nama Pembeli</th>
                        <th>Total Tagihan</th>
                        <th>Metode</th>
                        <th>Status Pembayaran</th>
                        <th class="text-center" style="width: 180px;">Aksi Validasi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($pembayaran as $row)
                        <tr>
                            <td class="fw-bold">#{{ $row->PembayaranID }}</td>
                            <td>
                                <a href="{{ route('admin.pesanan.detail', $row->PesananID) }}" class="fw-bold text-decoration-none">
                                    #{{ $row->PesananID }}
                                </a>
                            </td>
                            <td>{{ $row->pesanan && $row->pesanan->user ? $row->pesanan->user->Nama : '-' }}</td>
                            <td class="fw-bold text-danger">Rp {{ number_format($row->pesanan ? $row->pesanan->TotalHarga : 0, 0, ',', '.') }}</td>
                                                        <td>
                                <span class="badge bg-secondary">{{ $row->Metode }}</span>
                                @if($row->PaymentType)
                                    <div class="small text-muted font-monospace mt-1" style="font-size: 0.75rem;">Midtrans: {{ $row->PaymentType }}</div>
                                @endif
                                @if($row->TransactionID)
                                    <div class="small text-muted font-monospace" style="font-size: 0.7rem;">TX: {{ Str::limit($row->TransactionID, 14) }}</div>
                                @endif
                            </td>
                            <td>
                                @if($row->StatusPembayaran == 'Valid')
                                    <span class="badge bg-success"><i class="bi bi-check-circle me-1"></i> Valid</span>
                                @elseif($row->StatusPembayaran == 'Tidak Valid')
                                    <span class="badge bg-danger"><i class="bi bi-x-circle me-1"></i> Ditolak</span>
                                @else
                                    <span class="badge bg-warning text-dark"><i class="bi bi-clock me-1"></i> Menunggu</span>
                                @endif
                            </td>
                            <td>
                                <div class="d-flex gap-1 justify-content-center">
                                    <form action="{{ route('admin.pembayaran.update_status', $row->PembayaranID) }}" method="POST">
                                        @csrf
                                        @method('PUT')
                                        <input type="hidden" name="StatusPembayaran" value="Valid">
                                        <button type="submit" class="btn btn-sm btn-success rounded-pill px-3" {{ $row->StatusPembayaran == 'Valid' ? 'disabled' : '' }}>
                                            Valid
                                        </button>
                                    </form>

                                    <form action="{{ route('admin.pembayaran.update_status', $row->PembayaranID) }}" method="POST">
                                        @csrf
                                        @method('PUT')
                                        <input type="hidden" name="StatusPembayaran" value="Tidak Valid">
                                        <button type="submit" class="btn btn-sm btn-outline-danger rounded-pill px-3" {{ $row->StatusPembayaran == 'Tidak Valid' ? 'disabled' : '' }}>
                                            Tolak
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center text-muted py-5">
                                Belum ada data pembayaran.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="d-flex justify-content-center mt-4">
            {{ $pembayaran->links() }}
        </div>
    </div>

</div>
@endsection