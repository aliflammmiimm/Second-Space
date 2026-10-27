@extends('layouts.seller')
@section('title', 'Pesanan Masuk - Seller')

@section('seller_content')
@php($warna = ['Menunggu Pembayaran' => 'warning text-dark', 'Diproses' => 'info text-dark', 'Dikemas' => 'primary', 'Dikirim' => 'primary', 'Sampai Tujuan' => 'success', 'Selesai' => 'success', 'Dibatalkan' => 'danger', 'Kedaluwarsa' => 'secondary'])
<div>
    <h4 class="fw-bold mb-3"><i class="bi bi-inbox text-danger me-2"></i> Pesanan Masuk &amp; Riwayat Penjualan</h4>
    <div class="card custom-card border-0 p-3"><div class="table-responsive">
        <table class="table align-middle mb-0">
            <thead class="table-light"><tr><th>Pesanan</th><th>Produk</th><th>Harga</th><th>Komisi</th><th>Diterima</th><th>Pembeli</th><th>Alamat kirim</th><th>Status</th><th>Aksi</th></tr></thead>
            <tbody>
            @forelse($item as $d)
                <tr>
                    <td>#{{ $d->PesananID }}<br><small class="text-muted">{{ $d->pesanan->TanggalPesanan->format('d M Y H:i') }}</small></td>
                    <td>{{ $d->produk->NamaProduk ?? '(produk dihapus)' }}</td>
                    <td>Rp {{ number_format($d->HargaSatuan, 0, ',', '.') }}</td>
                    <td class="text-danger">- Rp {{ number_format($d->Komisi, 0, ',', '.') }}</td>
                    <td class="fw-bold text-success">Rp {{ number_format($d->PendapatanSeller, 0, ',', '.') }}</td>
                    <td>{{ $d->pesanan->user->Nama ?? '-' }}</td>
                    <td class="small">{{ $d->pesanan->pengiriman->AlamatPengiriman ?? '-' }}<br>
                        <span class="text-muted">{{ $d->pesanan->pengiriman->StatusPengiriman ?? '' }} {{ $d->pesanan->pengiriman->NoResi ?? '' }}</span></td>
                    <td><span class="badge bg-{{ $warna[$d->pesanan->StatusPesanan] ?? 'secondary' }}">{{ $d->pesanan->StatusPesanan }}</span></td>
                    <td>
                        @if(in_array($d->pesanan->StatusPesanan, ['Diproses', 'Dikemas'], true))
                            <form method="POST" action="{{ route('seller.pesanan.kirim', $d->PesananID) }}" class="d-flex gap-1">
                                @csrf
                                @method('PUT')
                                <input type="text" name="NoResi" class="form-control form-control-sm" placeholder="No. resi" maxlength="100" required>
                                <button class="btn btn-sm btn-danger text-nowrap">Kirim</button>
                            </form>
                        @else
                            <span class="text-muted small">-</span>
                        @endif
                    </td>
                </tr>
            @empty
                <tr><td colspan="9" class="text-center text-muted py-5">Belum ada pesanan untuk barang Anda.</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
    <div class="mt-3">{{ $item->links() }}</div></div>
</div>
@endsection