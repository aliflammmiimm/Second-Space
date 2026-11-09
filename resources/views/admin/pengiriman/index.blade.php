@extends('layouts.app')

@section('title', 'Kelola Pengiriman - Admin SecondSpace')

@section('content')
<div class="container py-4">

    <div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-3">
        <div>
            <h2 class="fw-bold mb-1"><i class="bi bi-truck text-danger me-2"></i> Logistik &amp; Pengiriman</h2>
            <p class="text-muted mb-0">Pusat monitoring logistik dan status nomor resi pengiriman kurir oleh masing-masing Seller</p>
        </div>
        <a href="{{ route('admin.dashboard') }}" class="btn btn-outline-secondary rounded-pill">
            <i class="bi bi-arrow-left me-1"></i> Dashboard
        </a>
    </div>

    <!-- PEMBERITAHUAN ALUR PENGIRIMAN MANDIRI SELLER -->
    <div class="alert alert-info border-0 shadow-sm d-flex align-items-center gap-3 mb-4 py-3 rounded-3" style="background: rgba(0, 184, 190, 0.08); border-left: 4px solid #00B8BE !important; color: #005C66;">
        <i class="bi bi-info-circle-fill fs-4" style="color: #00B8BE;"></i>
        <div class="small">
            <strong>Sistem Pengiriman Mandiri:</strong> Proses pengemasan, penyerahan barang ke kurir, dan penginputan nomor resi sepenuhnya ditangani langsung oleh <strong>Seller</strong> melalui panel Seller masing-masing. Halaman ini berfungsi sebagai pusat monitoring (*read-only tracking*) logistik bagi Administrator.
        </div>
    </div>

    <!-- FILTER STATUS -->
    <div class="card custom-card border-0 p-3 mb-4">
        <div class="d-flex gap-2 flex-wrap align-items-center">
            <span class="fw-bold text-muted small me-2"><i class="bi bi-funnel"></i> Filter Status:</span>
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
                        <th>Toko (Seller)</th>
                        <th>Penerima &amp; Alamat</th>
                        <th>Metode Bayar</th>
                        <th>Nomor Resi Kurir</th>
                        <th>Status Pengiriman</th>
                        <th class="text-center" style="width: 140px;">Detail Pesanan</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($pengiriman as $row)
                        @php
                            $sellerNames = $row->pesanan && $row->pesanan->detail 
                                ? $row->pesanan->detail->map(fn($d) => $d->seller->Nama ?? null)->filter()->unique()->join(', ')
                                : null;
                        @endphp
                        <tr>
                            <td class="fw-bold text-muted">#{{ $row->PengirimanID }}</td>
                            <td>
                                <a href="{{ route('admin.pesanan.detail', $row->PesananID) }}" class="fw-bold text-decoration-none" style="color: #005C66;">
                                    #{{ $row->PesananID }}
                                </a>
                                @if($row->pesanan && $row->pesanan->TanggalPesanan)
                                    <div class="text-muted" style="font-size: 0.75rem;">{{ $row->pesanan->TanggalPesanan->format('d M Y') }}</div>
                                @endif
                            </td>
                            <td>
                                <span class="fw-semibold text-dark">
                                    <i class="bi bi-shop me-1 text-primary"></i>{{ $sellerNames ?: 'Seller SecondSpace' }}
                                </span>
                            </td>
                            <td>
                                <div class="fw-bold text-dark">{{ $row->pesanan && $row->pesanan->user ? $row->pesanan->user->Nama : '-' }}</div>
                                <div class="text-truncate text-muted small" style="max-width: 200px;" title="{{ $row->AlamatPengiriman }}">
                                    <i class="bi bi-geo-alt text-danger me-1"></i>{{ $row->AlamatPengiriman }}
                                </div>
                            </td>
                            <td>
                                @if($row->pesanan && $row->pesanan->MetodePembayaran === 'COD')
                                    <span class="badge bg-success-subtle text-success border border-success-subtle py-1 px-2">
                                        <i class="bi bi-cash-stack me-1"></i> COD
                                    </span>
                                @else
                                    <span class="badge bg-primary-subtle text-primary border border-primary-subtle py-1 px-2">
                                        <i class="bi bi-credit-card me-1"></i> Transfer
                                    </span>
                                @endif
                            </td>
                            <td>
                                @if($row->NoResi)
                                    <span class="badge bg-light text-dark border font-monospace px-2 py-1" style="font-size: 0.85rem;">
                                        <i class="bi bi-upc-scan me-1 text-primary"></i>{{ $row->NoResi }}
                                    </span>
                                @else
                                    <span class="text-muted small fst-italic">
                                        <i class="bi bi-hourglass-split me-1 text-warning"></i>Menunggu input seller
                                    </span>
                                @endif
                            </td>
                            <td>
                                @if($row->StatusPengiriman == 'Dikemas')
                                    <span class="badge bg-warning text-dark py-1 px-2"><i class="bi bi-box-seam me-1"></i> Dikemas</span>
                                @elseif($row->StatusPengiriman == 'Dikirim')
                                    <span class="badge bg-info text-dark py-1 px-2"><i class="bi bi-truck me-1"></i> Dikirim</span>
                                @elseif($row->StatusPengiriman == 'Sampai Tujuan')
                                    <span class="badge bg-success py-1 px-2"><i class="bi bi-check-circle-fill me-1"></i> Sampai Tujuan</span>
                                @else
                                    <span class="badge bg-secondary py-1 px-2">{{ $row->StatusPengiriman }}</span>
                                @endif
                            </td>
                            <td class="text-center">
                                <a href="{{ route('admin.pesanan.detail', $row->PesananID) }}" class="btn btn-sm btn-outline-dark rounded-pill px-3" title="Buka Detail Invoice Pesanan">
                                    <i class="bi bi-eye me-1"></i> Invoice
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center text-muted py-5">
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