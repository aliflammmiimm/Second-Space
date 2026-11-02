@extends('layouts.seller')
@section('title', 'Tagihan Komisi COD - Seller SecondSpace')

@section('seller_content')
<div>
    <!-- HEADER -->
    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
        <div>
            <h4 class="fw-bold mb-1" style="color: #001B27;">
                <i class="bi bi-receipt-cutoff me-2" style="color: #00B8BE;"></i> Tagihan Komisi Transaksi COD
            </h4>
            <p class="text-muted small mb-0">
                Pada transaksi COD, Anda menerima uang tunai langsung dari pembeli. Anda wajib mentransfer komisi 10% ke platform dalam maksimal 4 hari.
            </p>
        </div>
    </div>

    <!-- STAT CARDS -->
    <div class="row g-3 mb-4">
        <div class="col-sm-4">
            <div class="seller-stat border-start border-4" style="border-left-color: #e11d48 !important;">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <div class="label">Belum Dibayar</div>
                        <div class="angka text-danger mt-1" style="font-size: 1.35rem;">
                            Rp {{ number_format($totalBelumBayar, 0, ',', '.') }}
                        </div>
                    </div>
                    <div class="fs-3 text-danger opacity-75"><i class="bi bi-clock-history"></i></div>
                </div>
                <small class="text-muted d-block mt-2" style="font-size: 0.75rem;">Wajib dilunasi sebelum batas 4 hari</small>
            </div>
        </div>

        <div class="col-sm-4">
            <div class="seller-stat border-start border-4" style="border-left-color: #00B8BE !important;">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <div class="label">Menunggu Verifikasi</div>
                        <div class="angka mt-1" style="color: #00B8BE; font-size: 1.35rem;">
                            Rp {{ number_format($totalMenungguVerifikasi, 0, ',', '.') }}
                        </div>
                    </div>
                    <div class="fs-3 opacity-75" style="color: #00B8BE;"><i class="bi bi-hourglass-split"></i></div>
                </div>
                <small class="text-muted d-block mt-2" style="font-size: 0.75rem;">Bukti transfer sedang ditinjau admin</small>
            </div>
        </div>

        <div class="col-sm-4">
            <div class="seller-stat border-start border-4" style="border-left-color: #005C66 !important;">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <div class="label">Total Komisi Lunas</div>
                        <div class="angka mt-1" style="color: #005C66; font-size: 1.35rem;">
                            Rp {{ number_format($totalLunas, 0, ',', '.') }}
                        </div>
                    </div>
                    <div class="fs-3 opacity-75" style="color: #005C66;"><i class="bi bi-check2-circle"></i></div>
                </div>
                <small class="text-muted d-block mt-2" style="font-size: 0.75rem;">Histori komisi COD yang sudah diverifikasi</small>
            </div>
        </div>
    </div>

    <!-- PETUNJUK TRANSFER REKENING RESMI ADMIN -->
    <div class="card custom-card border-0 p-4 mb-4" style="background: linear-gradient(135deg, rgba(0, 184, 190, 0.08), rgba(0, 92, 102, 0.05)); border: 1px dashed rgba(0, 92, 102, 0.25) !important;">
        <div class="row align-items-center g-3">
            <div class="col-md-7">
                <h6 class="fw-bold mb-2" style="color: #001B27;">
                    <i class="bi bi-bank me-1" style="color: #00B8BE;"></i> Rekening Resmi Pembayaran Komisi Platform
                </h6>
                <p class="small text-muted mb-2">
                    Kirimkan transfer komisi 10% tepat sesuai nominal tagihan ke rekening resmi berikut:
                </p>
                <div class="d-flex flex-wrap gap-3 p-3 bg-white rounded-3 border">
                    <div>
                        <small class="text-muted d-block" style="font-size: 0.75rem;">NAMA BANK</small>
                        <strong style="color: #001B27;">BCA (Bank Central Asia)</strong>
                    </div>
                    <div>
                        <small class="text-muted d-block" style="font-size: 0.75rem;">NOMOR REKENING</small>
                        <strong class="fs-6" style="color: #005C66; letter-spacing: 1px;">8830-1928-31</strong>
                    </div>
                    <div>
                        <small class="text-muted d-block" style="font-size: 0.75rem;">ATAS NAMA</small>
                        <strong style="color: #001B27;">PT SecondSpace Official</strong>
                    </div>
                </div>
            </div>
            <div class="col-md-5">
                <div class="p-3 bg-white rounded-3 border border-warning-subtle">
                    <div class="fw-bold text-dark small mb-1"><i class="bi bi-exclamation-triangle-fill text-warning me-1"></i> Peraturan Batas Waktu 4 Hari:</div>
                    <p class="small text-muted mb-0" style="font-size: 0.8rem; line-height: 1.5;">
                        Jika dalam <strong>4 hari</strong> setelah pesanan COD selesai Anda belum mengunggah bukti transfer yang valid, akun toko Anda akan <strong>otomatis diblokir oleh sistem</strong> dan produk Anda disembunyikan.
                    </p>
                </div>
            </div>
        </div>
    </div>

    <!-- TABEL TAGIHAN -->
    <div class="card custom-card border-0 p-3">
        <h5 class="fw-bold mb-3 px-2 pt-2" style="color: #001B27;">Daftar Tagihan Komisi COD</h5>
        <div class="table-responsive">
            <table class="table align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>ID Tagihan / Pesanan</th>
                        <th>Total Transaksi COD</th>
                        <th>Komisi (10%)</th>
                        <th>Batas Pembayaran</th>
                        <th>Status</th>
                        <th>Bukti Transfer &amp; Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($tagihan as $t)
                        @php
                            $lewat = $t->isLewatBatas();
                        @endphp
                        <tr class="{{ $lewat ? 'table-danger' : '' }}">
                            <td>
                                <div class="fw-bold" style="color: #001B27;">Tagihan #{{ $t->TagihanID }}</div>
                                <small class="text-muted">Pesanan #{{ $t->PesananID }} &bull; {{ $t->created_at ? $t->created_at->format('d M Y') : '-' }}</small>
                            </td>
                            <td>
                                Rp {{ number_format($t->pesanan->TotalHarga ?? ($t->JumlahKomisi * 10), 0, ',', '.') }}
                            </td>
                            <td>
                                <strong class="fs-6 text-danger">Rp {{ number_format($t->JumlahKomisi, 0, ',', '.') }}</strong>
                            </td>
                            <td>
                                @if($t->Status === 'Lunas')
                                    <span class="badge bg-success-subtle text-success border border-success-subtle"><i class="bi bi-check-circle me-1"></i> Lunas</span>
                                @elseif($lewat)
                                    <span class="badge bg-danger"><i class="bi bi-exclamation-octagon-fill me-1"></i> Lewat Batas (Menunggak)</span>
                                    <small class="text-danger d-block mt-1" style="font-size: 0.75rem;">Batas: {{ $t->BatasBayar->format('d M Y, H:i') }}</small>
                                @else
                                    <span class="badge bg-warning text-dark"><i class="bi bi-hourglass-split me-1"></i> {{ $t->sisaWaktu() }}</span>
                                    <small class="text-muted d-block mt-1" style="font-size: 0.75rem;">Batas: {{ $t->BatasBayar->format('d M Y, H:i') }}</small>
                                @endif
                            </td>
                            <td>
                                @if($t->Status === 'Lunas')
                                    <span class="badge bg-success">Lunas</span>
                                    <small class="text-muted d-block mt-1" style="font-size: 0.7rem;">{{ $t->DiverifikasiPada ? $t->DiverifikasiPada->format('d/m/Y') : '' }}</small>
                                @elseif($t->Status === 'Menunggu Verifikasi')
                                    <span class="badge bg-info text-dark">Menunggu Verifikasi</span>
                                    <small class="text-muted d-block mt-1" style="font-size: 0.7rem;">Diunggah {{ $t->DiunggahPada ? $t->DiunggahPada->diffForHumans() : '' }}</small>
                                @else
                                    <span class="badge bg-secondary">Belum Dibayar</span>
                                @endif
                            </td>
                            <td>
                                @if($t->Status !== 'Lunas')
                                    <form action="{{ route('seller.komisi.upload_bukti', $t->TagihanID) }}" method="POST" enctype="multipart/form-data" class="d-flex flex-column gap-1">
                                        @csrf
                                        <div class="d-flex gap-1 align-items-center">
                                            <input type="file" name="bukti" accept="image/*" class="form-control form-control-sm" required style="max-width: 170px;">
                                            <button type="submit" class="btn btn-sm btn-danger text-nowrap">
                                                <i class="bi bi-upload me-1"></i> Unggah
                                            </button>
                                        </div>
                                    </form>
                                @endif

                                @if($t->BuktiTransfer)
                                    <div class="mt-1">
                                        <a href="{{ $t->bukti_transfer_url }}" target="_blank" class="small text-primary text-decoration-none">
                                            <i class="bi bi-image me-1"></i> Lihat Bukti Terunggah
                                        </a>
                                    </div>
                                @endif

                                @if($t->CatatanAdmin)
                                    <div class="alert alert-danger p-1 mt-1 mb-0 small" style="font-size: 0.75rem;">
                                        <strong>Catatan Admin:</strong> {{ $t->CatatanAdmin }}
                                    </div>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center text-muted py-5">
                                <i class="bi bi-check2-circle fs-1 text-success d-block mb-2"></i>
                                Anda tidak memiliki tagihan komisi COD saat ini. Semua transaksi lancar!
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="mt-3">{{ $tagihan->links() }}</div>
    </div>
</div>
@endsection

