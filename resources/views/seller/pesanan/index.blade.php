@extends('layouts.seller')
@section('title', 'Pesanan Masuk - Seller')

@section('seller_content')
@php($warna = ['Menunggu Pembayaran' => 'warning text-dark', 'Diproses' => 'info text-dark', 'Dikemas' => 'primary', 'Dikirim' => 'primary', 'Sampai Tujuan' => 'success', 'Selesai' => 'success', 'Dibatalkan' => 'danger', 'Kedaluwarsa' => 'secondary'])
@php($sudahAksi = [])
<div>
    <h4 class="fw-bold mb-3"><i class="bi bi-inbox text-danger me-2"></i> Pesanan Masuk &amp; Riwayat Penjualan</h4>
    <div class="card custom-card border-0 p-3">
        <div class="table-responsive">
            <table class="table align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Pesanan</th>
                        <th>Produk</th>
                        <th>Harga</th>
                        <th>Komisi</th>
                        <th>Diterima</th>
                        <th>Pembeli</th>
                        <th>Cara terima / Alamat</th>
                        <th>Status</th>
                        <th style="width: 180px;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                @forelse($item as $d)
                    @php($ps = $d->pesanan)
                    @php($pertama = !isset($sudahAksi[$d->PesananID]))
                    @php($sudahAksi[$d->PesananID] = true)
                    <tr>
                        <td>
                            #{{ $d->PesananID }}<br>
                            <small class="text-muted">{{ $ps->TanggalPesanan ? $ps->TanggalPesanan->format('d M Y H:i') : '-' }}</small>
                        </td>
                        <td>{{ $d->produk->NamaProduk ?? '(produk dihapus)' }}</td>
                        <td>Rp {{ number_format($d->HargaSatuan, 0, ',', '.') }}</td>
                        <td class="text-danger">- Rp {{ number_format($d->Komisi, 0, ',', '.') }}</td>
                        <td class="fw-bold text-success">Rp {{ number_format($d->PendapatanSeller, 0, ',', '.') }}</td>
                        <td>{{ $ps->user->Nama ?? '-' }}</td>
                        <td class="small">
                            <span class="badge bg-light text-dark border">{{ $ps->CaraTerima ?? 'Diantar' }}</span>
                            @if(($ps->Ongkir ?? 0) > 0)
                                <span class="text-success fw-semibold">+ ongkir Rp {{ number_format($ps->Ongkir, 0, ',', '.') }}</span>
                            @endif
                            <br>
                            {{ $ps->pengiriman->AlamatPengiriman ?? '-' }}<br>
                            @if($ps->MetodePembayaran === 'COD' || ($ps->pembayaran->StatusPembayaran ?? '') === 'Valid')
                                @if($ps->pengiriman?->NoHPPenerima)
                                    <i class="bi bi-telephone text-primary"></i> {{ $ps->pengiriman->NoHPPenerima }}<br>
                                @endif
                            @else
                                <span class="text-muted fst-italic">No. HP tampil setelah pembayaran valid</span><br>
                            @endif
                            @if($ps->pengiriman?->NoResi)
                                <span class="text-muted">Resi: <strong>{{ $ps->pengiriman->NoResi }}</strong></span>
                            @endif
                        </td>
                        <td>
                            <span class="badge bg-{{ $warna[$ps->StatusPesanan] ?? 'secondary' }}">{{ $ps->StatusPesanan }}</span>
                        </td>

                        {{-- ===================== AKSI ===================== --}}
                        <td style="width: 180px; max-width: 180px;">
                            @if(!$pertama)
                                <span class="text-muted" style="font-size: .75rem;">&uarr; sama seperti di atas</span>
                            @else
                                @php($status = $ps->StatusPesanan)
                                @php($diantar = ($ps->CaraTerima ?? 'Diantar') === 'Diantar')
                                @php($cod = $ps->MetodePembayaran === 'COD')

                                @if($status === 'Menunggu Pembayaran')
                                    <span class="text-muted small">Menunggu pembayaran</span>

                                @elseif(in_array($status, ['Dibatalkan', 'Kedaluwarsa'], true))
                                    <span class="text-muted small">{{ $status }}</span>

                                @elseif($status === 'Selesai')
                                    <span class="badge bg-success-subtle text-success border border-success-subtle py-1 px-2">
                                        <i class="bi bi-check2-circle me-1"></i> Selesai
                                    </span>

                                @else
                                    <div class="d-grid gap-1" style="max-width: 170px;">

                                        @if($status === 'Diproses')
                                            <form method="POST" action="{{ route('seller.pesanan.kemas', $d->PesananID) }}" class="m-0">
                                                @csrf
                                                @method('PUT')
                                                <button class="btn btn-sm btn-primary w-100 py-1 text-nowrap">
                                                    <i class="bi bi-box-seam me-1"></i> Tandai Dikemas
                                                </button>
                                            </form>
                                        @endif

                                        @if($diantar && in_array($status, ['Diproses', 'Dikemas'], true))
                                            <form method="POST" action="{{ route('seller.pesanan.kirim', $d->PesananID) }}" class="m-0"
                                                  onsubmit="return confirm('Tandai pesanan #{{ $d->PesananID }} sudah berangkat diantar?')">
                                                @csrf
                                                @method('PUT')
                                                <div class="input-group input-group-sm mb-1">
                                                    <input type="text" name="NoResi" class="form-control form-control-sm" placeholder="Resi (opsional)" maxlength="100">
                                                    <button class="btn btn-danger text-nowrap">Kirim</button>
                                                </div>
                                            </form>
                                        @endif

                                        @if(!$diantar && $status === 'Dikemas')
                                            <span class="text-muted lh-sm small"><i class="bi bi-geo-alt me-1 text-success"></i> Siap diambil di toko</span>
                                        @elseif($diantar && $status === 'Dikemas')
                                            <span class="text-muted lh-sm small"><i class="bi bi-box me-1 text-primary"></i> Siap diantar</span>
                                        @elseif($status === 'Dikirim')
                                            <span class="text-muted lh-sm small"><i class="bi bi-truck me-1 text-danger"></i> Sedang diantar</span>
                                        @endif

                                        @if($cod)
                                            <form method="POST" action="{{ route('seller.pesanan.selesaikan_cod', $d->PesananID) }}" class="m-0"
                                                  onsubmit="return confirm('Selesaikan pesanan COD #{{ $d->PesananID }}? Pastikan barang sudah diserahkan dan uang tunai diterima.')">
                                                @csrf
                                                @method('PUT')
                                                <div class="input-group input-group-sm flex-nowrap mt-1">
                                                    <input type="text" name="pin" class="form-control" placeholder="PIN COD"
                                                           maxlength="6" minlength="6" inputmode="numeric" pattern="[0-9]{6}"
                                                           autocomplete="off" required title="PIN 6 digit dari pembeli" style="min-width: 0;">
                                                    <button class="btn btn-success" title="Verifikasi PIN &amp; selesaikan">
                                                        <i class="bi bi-check-lg"></i>
                                                    </button>
                                                </div>
                                            </form>
                                        @elseif(in_array($status, ['Dikemas', 'Dikirim', 'Sampai Tujuan'], true))
                                            <span class="text-muted lh-sm small fst-italic">Tunggu konfirmasi pembeli</span>
                                        @endif
                                    </div>
                                @endif
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="9" class="text-center text-muted py-5">Belum ada pesanan untuk barang Anda.</td>
                    </tr>
                @endforelse
                </tbody>
            </table>
        </div>
        <div class="mt-3">{{ $item->links() }}</div>
    </div>
</div>
@endsection
