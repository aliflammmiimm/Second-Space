@extends('layouts.seller')
@section('title', 'Saldo & Pencairan - Seller SecondSpace')

@section('seller_content')
@php
    $badgeStatus = [
        'Menunggu' => 'bg-warning text-dark',
        'Selesai' => 'bg-success text-white',
        'Ditolak' => 'bg-danger text-white',
    ];
@endphp

<div>
    <!-- HEADER -->
    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
        <div>
            <h4 class="fw-bold mb-1" style="color: #001B27;">
                <i class="bi bi-wallet2 me-2" style="color: #00B8BE;"></i> Saldo &amp; Pencairan Dana
            </h4>
            <p class="text-muted small mb-0">Kelola rekening tujuan transfer dan ajukan penarikan hasil penjualan toko Anda.</p>
        </div>
    </div>

    <!-- STAT CARDS (4 KOLOM SEJAJAR) -->
    <div class="row g-3 mb-4">
        <div class="col-sm-6 col-xl-3">
            <div class="seller-stat border-start border-4" style="border-left-color: #00B8BE !important;">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <div class="label">Saldo Siap Ditarik</div>
                        <div class="angka mt-1" style="color: #00B8BE; font-size: 1.35rem;">
                            Rp {{ number_format($saldo, 0, ',', '.') }}
                        </div>
                    </div>
                    <div class="fs-3 opacity-75" style="color: #00B8BE;"><i class="bi bi-cash-stack"></i></div>
                </div>
                <small class="text-muted d-block mt-2" style="font-size: 0.75rem;">Dana pesanan selesai & siap dicairkan</small>
            </div>
        </div>

        <div class="col-sm-6 col-xl-3">
            <div class="seller-stat border-start border-4" style="border-left-color: #eab308 !important;">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <div class="label">Dana Tertahan</div>
                        <div class="angka text-warning mt-1" style="font-size: 1.35rem;">
                            Rp {{ number_format($danaTertahan ?? 0, 0, ',', '.') }}
                        </div>
                    </div>
                    <div class="fs-3 text-warning opacity-75"><i class="bi bi-hourglass-split"></i></div>
                </div>
                <small class="text-muted d-block mt-2" style="font-size: 0.75rem;">Pesanan aktif yang sedang berjalan</small>
            </div>
        </div>

        <div class="col-sm-6 col-xl-3">
            <div class="seller-stat border-start border-4" style="border-left-color: #005C66 !important;">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <div class="label">Total Pendapatan</div>
                        <div class="angka mt-1" style="color: #005C66; font-size: 1.35rem;">
                            Rp {{ number_format($totalPendapatan, 0, ',', '.') }}
                        </div>
                    </div>
                    <div class="fs-3 opacity-75" style="color: #005C66;"><i class="bi bi-graph-up-arrow"></i></div>
                </div>
                <small class="text-muted d-block mt-2" style="font-size: 0.75rem;">Akumulasi bersih dari awal berjualan</small>
            </div>
        </div>

        <div class="col-sm-6 col-xl-3">
            <div class="seller-stat border-start border-4" style="border-left-color: #001B27 !important;">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <div class="label">Komisi Platform</div>
                        <div class="angka mt-1" style="color: #001B27; font-size: 1.35rem;">
                            {{ config('santara.komisi_persen') }}%
                        </div>
                    </div>
                    <div class="fs-3 opacity-75" style="color: #001B27;"><i class="bi bi-percent"></i></div>
                </div>
                <small class="text-muted d-block mt-2" style="font-size: 0.75rem;">Dipotong otomatis per penjualan</small>
            </div>
        </div>
    </div>

    <!-- FORM REKENING & AJUKAN PENCAIRAN (2 KOLOM SEIMBANG) -->
    <div class="row g-4 mb-4">
        
        <!-- KARTU 1: REKENING TUJUAN -->
        <div class="col-lg-6">
            <div class="card custom-card border-0 p-4 h-100 shadow-sm">
                <div class="d-flex align-items-center justify-content-between mb-3 pb-2 border-bottom">
                    <h6 class="fw-bold mb-0" style="color: #001B27;">
                        <i class="bi bi-bank2 me-1" style="color: #00B8BE;"></i> Rekening / E-Wallet Penerima
                    </h6>
                    @if($user->Bank && $user->NoRekening)
                        <span class="badge bg-success-subtle text-success border border-success-subtle px-2 py-1 small">
                            <i class="bi bi-check-circle me-1"></i> Terdaftar
                        </span>
                    @else
                        <span class="badge bg-warning-subtle text-warning-emphasis border border-warning-subtle px-2 py-1 small">
                            <i class="bi bi-exclamation-circle me-1"></i> Belum Diisi
                        </span>
                    @endif
                </div>

                <form method="POST" action="{{ route('seller.pencairan.rekening') }}">
                    @csrf
                    @method('PUT')

                    <div class="mb-3">
                        <label class="form-label fw-semibold small text-muted">Bank atau E-Wallet</label>
                        <input type="text" name="Bank" class="form-control" placeholder="Contoh: BCA / Mandiri / BRI / DANA / GoPay" value="{{ old('Bank', $user->Bank) }}" required>
                        @error('Bank')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold small text-muted">Nomor Rekening / No. HP E-Wallet</label>
                        <input type="text" name="NoRekening" class="form-control font-monospace" placeholder="Contoh: 1234567890" value="{{ old('NoRekening', $user->NoRekening) }}" required>
                        @error('NoRekening')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
                    </div>

                    <div class="mb-4">
                        <label class="form-label fw-semibold small text-muted">Atas Nama Pemilik Rekening</label>
                        <input type="text" name="AtasNama" class="form-control" placeholder="Nama lengkap sesuai buku tabungan" value="{{ old('AtasNama', $user->AtasNama) }}" required>
                        @error('AtasNama')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
                    </div>

                    <button type="submit" class="btn btn-dark rounded-pill px-4 fw-semibold shadow-sm">
                        <i class="bi bi-save me-1" style="color: #00B8BE;"></i> Simpan Data Rekening
                    </button>
                </form>
            </div>
        </div>

        <!-- KARTU 2: AJUKAN PENCAIRAN -->
        <div class="col-lg-6">
            <div class="card custom-card border-0 p-4 h-100 shadow-sm">
                <div class="d-flex align-items-center justify-content-between mb-3 pb-2 border-bottom">
                    <h6 class="fw-bold mb-0" style="color: #001B27;">
                        <i class="bi bi-send-check me-1" style="color: #005C66;"></i> Ajukan Pencairan Saldo
                    </h6>
                    <span class="small text-muted">Minimal: Rp {{ number_format($minimal, 0, ',', '.') }}</span>
                </div>

                <div class="p-3 rounded-3 mb-3" style="background: rgba(0, 184, 190, 0.08); border: 1px dashed rgba(0, 184, 190, 0.35);">
                    <div class="d-flex justify-content-between align-items-center">
                        <span class="small text-muted fw-semibold">Saldo yang Dapat Ditarik:</span>
                        <span class="fs-5 fw-bold" style="color: #005C66;">Rp {{ number_format($saldo, 0, ',', '.') }}</span>
                    </div>
                </div>

                <form method="POST" action="{{ route('seller.pencairan.store') }}">
                    @csrf

                    <div class="mb-3">
                        <label class="form-label fw-semibold small text-muted">Jumlah Penarikan (Rp)</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light border-end-0 fw-bold text-muted">Rp</span>
                            <input type="number" name="Jumlah" id="inputJumlah" class="form-control border-start-0" min="{{ $minimal }}" max="{{ max($saldo, 0) }}" placeholder="Masukkan nominal" value="{{ old('Jumlah') }}" required {{ $saldo < $minimal ? 'disabled' : '' }}>
                        </div>
                        @error('Jumlah')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
                        
                        @if($saldo >= $minimal)
                            <div class="d-flex gap-2 mt-2">
                                <button type="button" class="btn btn-sm btn-outline-secondary rounded-pill py-0 px-2" style="font-size: 0.78rem;" onclick="setJumlah({{ $saldo }})">
                                    Tarik Semua (Rp {{ number_format($saldo, 0, ',', '.') }})
                                </button>
                            </div>
                        @endif
                    </div>

                    <div class="alert alert-light border small text-muted mb-4 py-2 px-3" style="font-size: 0.82rem;">
                        <i class="bi bi-info-circle me-1" style="color: #00B8BE;"></i>
                        Dana akan ditransfer manual oleh Admin ke rekening tujuan terdaftar Anda.
                    </div>

                    <button type="submit" class="btn btn-danger rounded-pill px-4 py-2 fw-bold shadow-sm" {{ $saldo < $minimal ? 'disabled' : '' }}>
                        <i class="bi bi-arrow-down-circle me-1"></i> Ajukan Pencairan Sekarang
                    </button>
                </form>
            </div>
        </div>

    </div>

    <!-- TABEL RIWAYAT PENCAIRAN -->
    <div class="card custom-card border-0 p-4 shadow-sm">
        <h6 class="fw-bold mb-3" style="color: #001B27;">
            <i class="bi bi-clock-history me-1" style="color: #00B8BE;"></i> Riwayat Pengajuan Pencairan
        </h6>
        
        <div class="table-responsive">
            <table class="table align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>ID</th>
                        <th>Tanggal Pengajuan</th>
                        <th>Nominal</th>
                        <th>Rekening Tujuan</th>
                        <th class="text-center">Status</th>
                        <th>Catatan Admin</th>
                    </tr>
                </thead>
                <tbody>
                @forelse($riwayat as $r)
                    <tr>
                        <td class="fw-bold font-monospace text-muted">#{{ $r->PencairanID }}</td>
                        <td class="small">{{ $r->created_at ? $r->created_at->format('d M Y, H:i') : '-' }}</td>
                        <td class="fw-bold" style="color: #005C66;">Rp {{ number_format($r->Jumlah, 0, ',', '.') }}</td>
                        <td class="small">
                            <strong>{{ $r->Bank }}</strong> &bull; <span class="font-monospace">{{ $r->NoRekening }}</span><br>
                            <span class="text-muted">a.n. {{ $r->AtasNama }}</span>
                        </td>
                        <td class="text-center">
                            <span class="badge {{ $badgeStatus[$r->Status] ?? 'bg-secondary' }} px-3 py-2 rounded-pill">
                                {{ $r->Status }}
                            </span>
                        </td>
                        <td class="small text-muted">{{ $r->Catatan ?? '-' }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="text-center text-muted py-4">
                            <i class="bi bi-inbox fs-2 d-block mb-1 opacity-50"></i>
                            Belum ada riwayat pengajuan pencairan dana.
                        </td>
                    </tr>
                @endforelse
                </tbody>
            </table>
        </div>

        @if($riwayat->hasPages())
            <div class="mt-3 pt-3 border-top">
                {{ $riwayat->links() }}
            </div>
        @endif
    </div>

</div>

<script>
    function setJumlah(val) {
        const inp = document.getElementById('inputJumlah');
        if (inp) {
            inp.value = val;
        }
    }
</script>
@endsection