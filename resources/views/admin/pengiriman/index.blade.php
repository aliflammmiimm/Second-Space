@extends('layouts.app')

@section('title', 'Kelola Pengiriman - Admin SecondSpace')

@section('content')
<div class="container py-4">

    <div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-3">
        <div>
            <h2 class="fw-bold mb-1"><i class="bi bi-truck text-danger me-2"></i> Logistik & Pengiriman</h2>
            <p class="text-muted mb-0">Perbarui status pengiriman dan nomor resi kurir</p>
        </div>
        <a href="{{ route('admin.dashboard') }}" class="btn btn-outline-secondary rounded-pill">
            <i class="bi bi-arrow-left me-1"></i> Dashboard
        </a>
    </div>

    <!-- FILTER STATUS -->
    <div class="card custom-card border-0 p-3 mb-4">
        <div class="d-flex gap-2 flex-wrap align-items-center">
            <span class="fw-bold text-muted small me-2"><i class="bi bi-funnel"></i> Status:</span>
            <a href="{{ route('admin.pengiriman.index') }}" class="btn btn-sm {{ !request('status') ? 'btn-dark' : 'btn-outline-secondary' }} rounded-pill">
                Semua
            </a>
            @foreach(['Dikemas', 'Dikirim', 'Sampai Tujuan'] as $st)
                <a href="{{ route('admin.pengiriman.index', ['status' => $st]) }}" class="btn btn-sm {{ request('status') == $st ? 'btn-dark' : 'btn-outline-secondary' }} rounded-pill">
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
                        <th>ID Kirim</th>
                        <th>ID Pesanan</th>
                        <th>Nama Penerima</th>
                        <th>Alamat Lengkap</th>
                        <th>Nomor Resi</th>
                        <th>Status</th>
                        <th class="text-center" style="width: 220px;">Aksi Update</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($pengiriman as $row)
                        <tr>
                            <td class="fw-bold">#{{ $row->PengirimanID }}</td>
                            <td>
                                <a href="{{ route('admin.pesanan.detail', $row->PesananID) }}" class="fw-bold text-decoration-none">
                                    #{{ $row->PesananID }}
                                </a>
                            </td>
                            <td>{{ $row->pesanan && $row->pesanan->user ? $row->pesanan->user->Nama : '-' }}</td>
                            <td>
                                <div class="text-truncate" style="max-width: 220px;" title="{{ $row->AlamatPengiriman }}">
                                    {{ $row->AlamatPengiriman }}
                                </div>
                            </td>
                            <td>
                                @if($row->NoResi)
                                    <span class="badge bg-light text-dark border font-monospace">{{ $row->NoResi }}</span>
                                @else
                                    <span class="text-muted small">Belum ada</span>
                                @endif
                            </td>
                            <td>
                                @if($row->StatusPengiriman == 'Dikemas')
                                    <span class="badge bg-primary">Dikemas</span>
                                @elseif($row->StatusPengiriman == 'Dikirim')
                                    <span class="badge bg-info text-dark">Dikirim</span>
                                @elseif($row->StatusPengiriman == 'Sampai Tujuan')
                                    <span class="badge bg-success">Sampai Tujuan</span>
                                @endif
                            </td>
                            <td>
                                <form action="{{ route('admin.pengiriman.update_status', $row->PengirimanID) }}" method="POST" class="d-flex gap-1 justify-content-center">
                                    @csrf
                                    @method('PUT')

                                    @if($row->StatusPengiriman == 'Dikemas')
                                        <input type="hidden" name="StatusPengiriman" value="Dikirim">
                                        <input type="text" name="NoResi" class="form-control form-control-sm" placeholder="No Resi..." style="width: 100px;">
                                        <button type="submit" class="btn btn-sm btn-info text-dark rounded-pill px-3">
                                            Kirim
                                        </button>
                                    @elseif($row->StatusPengiriman == 'Dikirim')
                                        <input type="hidden" name="StatusPengiriman" value="Sampai Tujuan">
                                        <button type="submit" class="btn btn-sm btn-success rounded-pill px-3">
                                            Tiba di Tujuan
                                        </button>
                                    @else
                                        <span class="badge bg-light text-muted border px-3 py-2">Selesai</span>
                                    @endif
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center text-muted py-5">
                                Belum ada data pengiriman.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="d-flex justify-content-center mt-4">
            {{ $pengiriman->links() }}
        </div>
    </div>

</div>
@endsection