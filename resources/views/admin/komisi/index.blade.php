@extends('layouts.app')
@section('title', 'Manajemen Komisi COD - Admin SecondSpace')

@section('content')
<div class="container py-4">
    <!-- HEADER -->
    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
        <div>
            <h2 class="fw-bold mb-1" style="color: #001B27;">
                <i class="bi bi-receipt-cutoff me-2" style="color: #00B8BE;"></i> Verifikasi &amp; Tagihan Komisi COD
            </h2>
            <p class="text-muted small mb-0">
                Monitoring komisi platform 10% dari transaksi COD. Penjual memiliki waktu maksimal 4 hari untuk mentransfer sebelum diblokir otomatis.
            </p>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('admin.seller_aktif.index') }}" class="btn btn-outline-dark rounded-pill btn-sm">
                <i class="bi bi-shop me-1"></i> Daftar Seller
            </a>
            <a href="{{ route('admin.dashboard') }}" class="btn btn-outline-secondary rounded-pill btn-sm">Dashboard</a>
        </div>
    </div>

    <!-- STAT CARDS -->
    <div class="row g-3 mb-4">
        <div class="col-sm-4">
            <div class="card custom-card border-0 p-3 h-100 border-start border-4" style="border-left-color: #00B8BE !important;">
                <div class="small fw-bold text-uppercase text-muted">Menunggu Verifikasi Bukti</div>
                <div class="fs-3 fw-bold mt-1" style="color: #00B8BE;">
                    Rp {{ number_format($totalMenungguVerifikasi, 0, ',', '.') }}
                </div>
                <small class="text-muted">Seller sudah unggah bukti transfer</small>
            </div>
        </div>

        <div class="col-sm-4">
            <div class="card custom-card border-0 p-3 h-100 border-start border-4" style="border-left-color: #e11d48 !important;">
                <div class="small fw-bold text-uppercase text-muted">Total Tunggakan (Lewat 4 Hari)</div>
                <div class="fs-3 fw-bold text-danger mt-1">
                    Rp {{ number_format($totalMenunggak, 0, ',', '.') }}
                </div>
                <small class="text-danger">Seller terindikasi diblokir sistem</small>
            </div>
        </div>

        <div class="col-sm-4">
            <div class="card custom-card border-0 p-3 h-100 border-start border-4" style="border-left-color: #005C66 !important;">
                <div class="small fw-bold text-uppercase text-muted">Total Komisi COD Lunas</div>
                <div class="fs-3 fw-bold mt-1" style="color: #005C66;">
                    Rp {{ number_format($totalLunas, 0, ',', '.') }}
                </div>
                <small class="text-muted">Sudah diverifikasi dan masuk kas platform</small>
            </div>
        </div>
    </div>

    <!-- FILTER TABS -->
    <div class="d-flex gap-2 mb-3 overflow-auto pb-2">
        <a href="{{ route('admin.komisi.index') }}" class="btn btn-sm rounded-pill px-3 {{ !request()->filled('status') ? 'btn-dark' : 'btn-outline-dark' }}">
            Semua Tagihan
        </a>
        <a href="{{ route('admin.komisi.index', ['status' => 'Menunggu Verifikasi']) }}" class="btn btn-sm rounded-pill px-3 {{ request('status') === 'Menunggu Verifikasi' ? 'btn-info text-white' : 'btn-outline-info' }}">
            Menunggu Verifikasi
        </a>
        <a href="{{ route('admin.komisi.index', ['status' => 'menunggak']) }}" class="btn btn-sm rounded-pill px-3 {{ request('status') === 'menunggak' ? 'btn-danger' : 'btn-outline-danger' }}">
            Menunggak (Lewat 4 Hari)
        </a>
        <a href="{{ route('admin.komisi.index', ['status' => 'Belum Dibayar']) }}" class="btn btn-sm rounded-pill px-3 {{ request('status') === 'Belum Dibayar' ? 'btn-warning text-dark' : 'btn-outline-warning text-dark' }}">
            Belum Dibayar (Dalam Batas)
        </a>
        <a href="{{ route('admin.komisi.index', ['status' => 'Lunas']) }}" class="btn btn-sm rounded-pill px-3 {{ request('status') === 'Lunas' ? 'btn-success' : 'btn-outline-success' }}">
            Lunas
        </a>
    </div>

    <!-- TABEL TAGIHAN ADMIN -->
    <div class="card custom-card border-0 p-3">
        <div class="table-responsive">
            <table class="table align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>ID / Pesanan</th>
                        <th>Seller</th>
                        <th>Komisi (10%)</th>
                        <th>Batas Waktu</th>
                        <th>Bukti Transfer</th>
                        <th>Status</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($tagihan as $t)
                        @php
                            $lewat = $t->isLewatBatas();
                        @endphp
                        <tr class="{{ $lewat ? 'table-danger' : '' }}">
                            <td>
                                <strong>#{{ $t->TagihanID }}</strong><br>
                                <small class="text-muted">Pesanan #{{ $t->PesananID }} &bull; {{ $t->created_at ? $t->created_at->format('d/m/Y') : '' }}</small>
                            </td>
                            <td>
                                <div class="fw-bold">{{ $t->seller->Nama ?? '-' }}</div>
                                <div class="small text-muted">{{ $t->seller->NoHP_Email ?? '-' }}</div>
                                @if(!empty($t->seller->is_blocked))
                                    <span class="badge bg-danger mt-1"><i class="bi bi-slash-circle me-1"></i> Toko Diblokir</span>
                                @endif
                            </td>
                            <td>
                                <strong class="fs-6 text-danger">Rp {{ number_format($t->JumlahKomisi, 0, ',', '.') }}</strong><br>
                                <small class="text-muted">Total COD: Rp {{ number_format($t->pesanan->TotalHarga ?? ($t->JumlahKomisi * 10), 0, ',', '.') }}</small>
                            </td>
                            <td>
                                @if($t->Status === 'Lunas')
                                    <span class="text-success small fw-semibold"><i class="bi bi-check-circle"></i> Selesai</span>
                                @elseif($lewat)
                                    <span class="badge bg-danger">Lewat Batas</span>
                                    <small class="text-danger d-block" style="font-size: 0.75rem;">Batas: {{ $t->BatasBayar->format('d/m/Y H:i') }}</small>
                                @else
                                    <span class="badge bg-warning text-dark">{{ $t->sisaWaktu() }}</span>
                                    <small class="text-muted d-block" style="font-size: 0.75rem;">Batas: {{ $t->BatasBayar->format('d/m/Y H:i') }}</small>
                                @endif
                            </td>
                            <td>
                                @if($t->BuktiTransfer)
                                    <a href="{{ $t->bukti_transfer_url }}" target="_blank" class="btn btn-sm btn-outline-primary rounded-pill px-3">
                                        <i class="bi bi-eye me-1"></i> Lihat Bukti
                                    </a>
                                    <small class="text-muted d-block mt-1" style="font-size: 0.7rem;">{{ $t->DiunggahPada ? $t->DiunggahPada->format('d/m H:i') : '' }}</small>
                                @else
                                    <span class="text-muted small">Belum diunggah</span>
                                @endif

                                @if($t->CatatanAdmin)
                                    <div class="small text-danger mt-1">Ditolak: {{ $t->CatatanAdmin }}</div>
                                @endif
                            </td>
                            <td>
                                @if($t->Status === 'Lunas')
                                    <span class="badge bg-success">Lunas</span>
                                    <small class="text-muted d-block mt-1" style="font-size: 0.7rem;">Oleh {{ $t->verifikator->Nama ?? 'Admin' }}</small>
                                @elseif($t->Status === 'Menunggu Verifikasi')
                                    <div class="d-flex gap-1 flex-column">
                                        <form action="{{ route('admin.komisi.verifikasi', $t->TagihanID) }}" method="POST" onsubmit="return confirm('Konfirmasi bahwa uang komisi Rp {{ number_format($t->JumlahKomisi, 0, ',', '.') }} sudah masuk ke rekening admin?');">
                                            @csrf @method('PUT')
                                            <input type="hidden" name="aksi" value="setujui">
                                            <button type="submit" class="btn btn-sm btn-success w-100 rounded-pill">
                                                <i class="bi bi-check-lg me-1"></i> Setujui (Lunas)
                                            </button>
                                        </form>

                                        <!-- Tombol Tolak Bukti -->
                                        <button type="button" class="btn btn-sm btn-outline-danger w-100 rounded-pill" data-bs-toggle="modal" data-bs-target="#modalTolak{{ $t->TagihanID }}">
                                            <i class="bi bi-x-lg me-1"></i> Tolak Bukti
                                        </button>
                                    </div>

                                    <!-- MODAL TOLAK BUKTI -->
                                    <div class="modal fade" id="modalTolak{{ $t->TagihanID }}" tabindex="-1" aria-hidden="true">
                                        <div class="modal-dialog">
                                            <div class="modal-content">
                                                <form action="{{ route('admin.komisi.verifikasi', $t->TagihanID) }}" method="POST">
                                                    @csrf @method('PUT')
                                                    <input type="hidden" name="aksi" value="tolak">
                                                    <div class="modal-header">
                                                        <h5 class="modal-title fw-bold">Tolak Bukti Transfer Komisi #{{ $t->TagihanID }}</h5>
                                                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                    </div>
                                                    <div class="modal-body">
                                                        <div class="mb-3">
                                                            <label class="form-label fw-bold">Alasan Penolakan</label>
                                                            <textarea name="catatan" class="form-control" rows="3" placeholder="Contoh: Bukti transfer buram, mutasi rekening belum masuk, dll." required></textarea>
                                                        </div>
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-secondary rounded-pill" data-bs-dismiss="modal">Batal</button>
                                                        <button type="submit" class="btn btn-danger rounded-pill px-4">Tolak Bukti</button>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                @else
                                    <span class="badge bg-secondary">Belum Dibayar</span>
                                    <form action="{{ route('admin.komisi.verifikasi', $t->TagihanID) }}" method="POST" class="mt-1" onsubmit="return confirm('Tandai tagihan ini lunas secara manual (misal seller bayar tunai langsung ke admin)?');">
                                        @csrf @method('PUT')
                                        <input type="hidden" name="aksi" value="setujui">
                                        <button type="submit" class="btn btn-sm btn-outline-success rounded-pill py-0 px-2" style="font-size: 0.75rem;">
                                            Setujui Manual
                                        </button>
                                    </form>
                                @endif
                            </td>
                            <td>
                                @if(!empty($t->seller->is_blocked))
                                    <form action="{{ route('admin.seller_aktif.buka_blokir', $t->seller->UserID) }}" method="POST" onsubmit="return confirm('Buka blokir toko {{ e($t->seller->Nama) }}?');">
                                        @csrf @method('PUT')
                                        <button class="btn btn-sm btn-outline-success text-nowrap rounded-pill px-2 py-0" style="font-size: 0.75rem;">
                                            <i class="bi bi-unlock me-1"></i> Buka Blokir
                                        </button>
                                    </form>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center text-muted py-5">
                                Belum ada data tagihan komisi COD.
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

