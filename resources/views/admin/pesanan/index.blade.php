@extends('layouts.app')

@section('title', 'Kelola Pesanan - Admin SecondSpace')

@section('content')
<div class="container py-4">

    <div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-3">
        <div>
            <h2 class="fw-bold mb-1"><i class="bi bi-cart-check text-danger me-2"></i> Kelola Pesanan Pelanggan</h2>
            <p class="text-muted mb-0">Pantau dan update status pesanan dari pelanggan</p>
        </div>
        <a href="{{ route('admin.dashboard') }}" class="btn btn-outline-secondary rounded-pill">
            <i class="bi bi-arrow-left me-1"></i> Dashboard
        </a>
    </div>

    <!-- FILTER STATUS -->
    <div class="card custom-card border-0 p-3 mb-4">
        <form action="{{ route('admin.pesanan.index') }}" method="GET" class="d-flex gap-2 flex-wrap align-items-center">
            <span class="fw-bold text-muted small me-2"><i class="bi bi-funnel"></i> Status:</span>
            <a href="{{ route('admin.pesanan.index') }}" class="btn btn-sm {{ !request('status') ? 'btn-dark' : 'btn-outline-secondary' }} rounded-pill">
                Semua Status
            </a>
            @foreach(['Diproses', 'Dikemas', 'Dikirim', 'Sampai Tujuan', 'Selesai'] as $st)
                <a href="{{ route('admin.pesanan.index', ['status' => $st]) }}" class="btn btn-sm {{ request('status') == $st ? 'btn-dark' : 'btn-outline-secondary' }} rounded-pill">
                    {{ $st }}
                </a>
            @endforeach
        </form>
    </div>

    <!-- TABEL PESANAN -->
    <div class="card custom-card border-0 p-4">
        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead class="table-light">
                    <tr>
                        <th>ID</th>
                        <th>Nama Pembeli</th>
                        <th>Tanggal Pesanan</th>
                        <th>Metode Bayar</th>
                        <th>Total Harga</th>
                        <th>Status</th>
                        <th class="text-center" style="width: 140px;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($pesanan as $ps)
                        <tr>
                            <td class="fw-bold">#{{ $ps->PesananID }}</td>
                            <td>
                                <div class="fw-bold">{{ $ps->user ? $ps->user->Nama : 'Tamu' }}</div>
                                <small class="text-muted">{{ $ps->user ? $ps->user->NoHP_Email : '-' }}</small>
                            </td>
                            <td>{{ $ps->TanggalPesanan ? $ps->TanggalPesanan->format('d/m/Y H:i') : '-' }}</td>
                            <td><span class="badge bg-secondary">{{ $ps->MetodePembayaran }}</span></td>
                            <td class="fw-bold text-danger">Rp {{ number_format($ps->TotalHarga, 0, ',', '.') }}</td>
                            <td>
                                @if($ps->StatusPesanan == 'Diproses')
                                    <span class="badge bg-warning text-dark">Diproses</span>
                                @elseif($ps->StatusPesanan == 'Dikemas')
                                    <span class="badge bg-primary">Dikemas</span>
                                @elseif($ps->StatusPesanan == 'Dikirim')
                                    <span class="badge bg-info text-dark">Dikirim</span>
                                @elseif($ps->StatusPesanan == 'Sampai Tujuan')
                                    <span class="badge bg-success">Sampai Tujuan</span>
                                @elseif($ps->StatusPesanan == 'Selesai')
                                    <span class="badge bg-dark">Selesai</span>
                                @elseif($ps->StatusPesanan == 'Menunggu Pembayaran')
                                    <span class="badge bg-secondary">Menunggu Pembayaran</span>
                                @elseif($ps->StatusPesanan == 'Dibatalkan')
                                    <span class="badge bg-danger">Dibatalkan</span>
                                @elseif($ps->StatusPesanan == 'Kedaluwarsa')
                                    <span class="badge bg-light text-dark border">Kedaluwarsa</span>
                                @endif
                            </td>
                            <td>
                                <div class="d-flex gap-1 justify-content-center">
                                    <a href="{{ route('admin.pesanan.detail', $ps->PesananID) }}" class="btn btn-sm btn-dark rounded-pill px-3">
                                        Detail
                                    </a>
                                    @if(in_array($ps->StatusPesanan, ['Menunggu Pembayaran', 'Diproses', 'Dikemas'], true))
                                        <form action="{{ route('admin.pesanan.batalkan', $ps->PesananID) }}" method="POST" onsubmit="return confirm('Batalkan pesanan ini? Barang akan dibuka kembali.')">
                                            @csrf
                                            @method('PUT')
                                            <button type="submit" class="btn btn-sm btn-outline-danger" title="Batalkan">
                                                <i class="bi bi-x-circle"></i>
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center text-muted py-5">
                                Belum ada pesanan yang sesuai dengan filter.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="d-flex justify-content-center mt-4">
            {{ $pesanan->links() }}
        </div>
    </div>

</div>
@endsection