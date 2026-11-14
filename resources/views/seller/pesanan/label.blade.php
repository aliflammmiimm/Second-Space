<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Label Pengiriman #{{ $pesanan->PesananID }} - SecondSpace</title>
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <style>
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        body {
            background: #f0f4f5;
            color: #111827;
            padding: 20px;
            display: flex;
            flex-direction: column;
            align-items: center;
        }

        /* Top Action Bar (Hidden when printing) */
        .action-bar {
            width: 100%;
            max-width: 580px;
            margin-bottom: 16px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            background: #ffffff;
            padding: 12px 18px;
            border-radius: 12px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.06);
        }

        .btn {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 8px 16px;
            border-radius: 8px;
            font-size: 0.88rem;
            font-weight: 600;
            cursor: pointer;
            text-decoration: none;
            border: none;
            transition: all 0.15s ease;
        }

        .btn-print {
            background: #005C66;
            color: #ffffff;
        }
        .btn-print:hover {
            background: #00424a;
        }

        .btn-close-window {
            background: #e5e7eb;
            color: #374151;
        }
        .btn-close-window:hover {
            background: #d1d5db;
        }

        /* Shipping Label Container (100mm standard shipping label look) */
        .shipping-label {
            width: 100%;
            max-width: 580px;
            background: #ffffff;
            border: 2px solid #111827;
            padding: 16px 20px;
            border-radius: 6px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.08);
            position: relative;
        }

        /* Header */
        .label-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-bottom: 2px solid #111827;
            padding-bottom: 12px;
            margin-bottom: 12px;
        }

        .brand-logo {
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .brand-logo img {
            width: 36px;
            height: 36px;
            border-radius: 6px;
            object-fit: cover;
        }

        .brand-name {
            font-size: 1.25rem;
            font-weight: 900;
            letter-spacing: 0.5px;
            color: #001B27;
        }

        .brand-name span {
            color: #00B8BE;
        }

        .badge-method {
            padding: 4px 10px;
            border: 1.5px solid #111827;
            border-radius: 6px;
            font-size: 0.8rem;
            font-weight: 800;
            text-transform: uppercase;
        }

        .badge-cod {
            background: #fef08a;
            color: #854d0e;
        }

        .badge-paid {
            background: #dcfce7;
            color: #166534;
        }

        /* Invoice Number & Barcode */
        .invoice-barcode-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-bottom: 1px dashed #9ca3af;
            padding-bottom: 10px;
            margin-bottom: 12px;
        }

        .invoice-num {
            font-size: 1.15rem;
            font-weight: 800;
            color: #111827;
        }

        .invoice-date {
            font-size: 0.78rem;
            color: #6b7280;
        }

        .barcode-visual {
            font-family: 'Courier New', Courier, monospace;
            font-weight: 900;
            letter-spacing: 3px;
            font-size: 1.1rem;
            background: #f3f4f6;
            padding: 4px 10px;
            border-radius: 4px;
            border: 1px solid #d1d5db;
        }

        /* Grid Penerima & Pengirim */
        .address-grid {
            display: grid;
            grid-template-columns: 1.2fr 0.8fr;
            gap: 12px;
            border-bottom: 1.5px solid #111827;
            padding-bottom: 12px;
            margin-bottom: 12px;
        }

        .box-title {
            font-size: 0.72rem;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            color: #4b5563;
            margin-bottom: 4px;
        }

        .recipient-box {
            border-right: 1px dashed #d1d5db;
            padding-right: 12px;
        }

        .person-name {
            font-size: 1.05rem;
            font-weight: 800;
            color: #111827;
            margin-bottom: 2px;
        }

        .person-phone {
            font-size: 0.95rem;
            font-weight: 700;
            color: #005C66;
            margin-bottom: 6px;
        }

        .address-text {
            font-size: 0.85rem;
            line-height: 1.35;
            color: #1f2937;
        }

        .distance-tag {
            display: inline-block;
            margin-top: 6px;
            padding: 2px 8px;
            background: #e0f2fe;
            color: #0369a1;
            font-size: 0.75rem;
            font-weight: 700;
            border-radius: 4px;
            border: 1px solid #bae6fd;
        }

        /* Detail Barang */
        .items-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 0.84rem;
            border-bottom: 1.5px solid #111827;
            margin-bottom: 12px;
        }

        .items-table th {
            text-align: left;
            padding: 6px 4px;
            border-bottom: 1px solid #111827;
            font-size: 0.75rem;
            text-transform: uppercase;
            color: #4b5563;
        }

        .items-table td {
            padding: 8px 4px;
            border-bottom: 1px solid #f3f4f6;
            vertical-align: top;
        }

        /* Ringkasan Biaya & COD */
        .payment-summary {
            display: flex;
            justify-content: space-between;
            align-items: center;
            background: #f9fafb;
            padding: 10px 14px;
            border-radius: 6px;
            border: 1px solid #e5e7eb;
            margin-bottom: 12px;
        }

        .cod-notice {
            font-size: 1.1rem;
            font-weight: 900;
            color: #b45309;
        }

        .paid-notice {
            font-size: 1rem;
            font-weight: 800;
            color: #15803d;
        }

        .total-amount {
            font-size: 1.15rem;
            font-weight: 900;
            color: #001B27;
            text-align: right;
        }

        .total-amount small {
            display: block;
            font-size: 0.75rem;
            font-weight: 600;
            color: #6b7280;
        }

        /* Signatures Box */
        .signature-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 16px;
            text-align: center;
            font-size: 0.78rem;
            color: #4b5563;
        }

        .signature-line {
            margin-top: 36px;
            border-top: 1px dashed #9ca3af;
            padding-top: 4px;
        }

        /* Printing rules */
        @media print {
            body {
                background: #ffffff;
                padding: 0;
            }

            .action-bar {
                display: none !important;
            }

            .shipping-label {
                box-shadow: none;
                border: 2px solid #000000;
                width: 100%;
                max-width: 100%;
                page-break-inside: avoid;
            }
        }
    </style>
</head>
<body>

    <!-- ACTION BAR -->
    <div class="action-bar">
        <div>
            <span style="font-weight: 700; color: #001B27;">Label Pengiriman Paket</span>
            <span style="font-size: 0.8rem; color: #6b7280; margin-left: 6px;">Pesanan #{{ $pesanan->PesananID }}</span>
        </div>
        <div style="display: flex; gap: 8px;">
            <button class="btn btn-close-window" onclick="window.close()">
                <i class="bi bi-x-lg"></i> Tutup
            </button>
            <button class="btn btn-print" onclick="window.print()">
                <i class="bi bi-printer-fill"></i> Cetak Label
            </button>
        </div>
    </div>

    <!-- SHIPPING LABEL -->
    <div class="shipping-label">
        
        <!-- HEADER -->
        <div class="label-header">
            <div class="brand-logo">
                <img src="{{ asset('assets/images/logo.jpeg') }}" alt="Logo SecondSpace" onerror="this.style.display='none'">
                <div>
                    <div class="brand-name">SECOND<span>SPACE</span></div>
                    <div style="font-size: 0.7rem; color: #6b7280; font-weight: 600;">MAKASSAR PRE-LOVED &amp; THRIFT</div>
                </div>
            </div>

            <div>
                @php($isCod = $pesanan->MetodePembayaran === 'COD')
                @if($isCod)
                    <div class="badge-method badge-cod">
                        <i class="bi bi-cash-stack"></i> COD (BAYAR DI TEMPAT)
                    </div>
                @else
                    <div class="badge-method badge-paid">
                        <i class="bi bi-check-circle-fill"></i> LUNAS (QRIS / TRANSFER)
                    </div>
                @endif
            </div>
        </div>

        <!-- INVOICE & BARCODE -->
        <div class="invoice-barcode-row">
            <div>
                <div class="invoice-num">#SS-{{ $pesanan->PesananID }}</div>
                <div class="invoice-date">
                    Tgl: {{ $pesanan->TanggalPesanan ? $pesanan->TanggalPesanan->format('d M Y, H:i') : '-' }} WITA
                </div>
            </div>
            <div>
                <div class="barcode-visual">||| | |||| | |||</div>
                <div style="font-size: 0.7rem; text-align: right; color: #6b7280; margin-top: 2px;">
                    Kurir: {{ $pesanan->CaraTerima ?? 'Pengantaran Makassar' }}
                </div>
            </div>
        </div>

        <!-- ADDRESS GRID -->
        <div class="address-grid">
            <!-- PENERIMA -->
            <div class="recipient-box">
                <div class="box-title"><i class="bi bi-person-fill"></i> Kepada (Penerima):</div>
                <div class="person-name">{{ $pesanan->pengiriman?->NamaPenerima ?? $pesanan->user->Nama }}</div>
                <div class="person-phone"><i class="bi bi-telephone-fill"></i> {{ $pesanan->pengiriman?->NoHPPenerima ?? $pesanan->user->NoHP_Email }}</div>
                <div class="address-text">
                    {{ $pesanan->pengiriman?->AlamatPengiriman ?? 'Ambil sendiri di toko seller' }}
                </div>
                @if($pesanan->pengiriman?->JarakKM)
                    <div class="distance-tag">
                        <i class="bi bi-geo-alt-fill"></i> Estimasi Jarak: {{ $pesanan->pengiriman->JarakKM }} KM
                    </div>
                @endif
            </div>

            <!-- PENGIRIM -->
            <div>
                <div class="box-title"><i class="bi bi-shop"></i> Dari (Pengirim):</div>
                <div style="font-size: 0.95rem; font-weight: 800; color: #111827;">
                    {{ $seller->nama_toko ?: $seller->Nama }}
                </div>
                <div style="font-size: 0.85rem; font-weight: 700; color: #005C66; margin-bottom: 4px;">
                    <i class="bi bi-whatsapp"></i> {{ $seller->no_wa_toko ?: $seller->NoHP_Email }}
                </div>
                <div style="font-size: 0.78rem; color: #4b5563; line-height: 1.3;">
                    {{ $seller->alamat_toko ?: 'Kota Makassar, Sulawesi Selatan' }}
                </div>
            </div>
        </div>

        <!-- ITEMS TABLE -->
        <table class="items-table">
            <thead>
                <tr>
                    <th style="width: 60%;">Nama Barang (Pre-Loved)</th>
                    <th style="width: 20%; text-align: center;">Kondisi</th>
                    <th style="width: 20%; text-align: right;">Harga</th>
                </tr>
            </thead>
            <tbody>
                @foreach($pesanan->detail as $item)
                    <tr>
                        <td>
                            <strong>{{ $item->produk->NamaProduk ?? 'Barang Thrift' }}</strong>
                            <div style="font-size: 0.75rem; color: #6b7280;">1 Unit Thrift Eksklusif</div>
                        </td>
                        <td style="text-align: center;">
                            <span style="font-size: 0.75rem; font-weight: 600; padding: 2px 6px; background: #f3f4f6; border-radius: 4px;">
                                {{ $item->produk->Kondisi ?? 'Thrift' }}
                            </span>
                        </td>
                        <td style="text-align: right; font-weight: 700;">
                            Rp {{ number_format($item->HargaSatuan, 0, ',', '.') }}
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <!-- PAYMENT SUMMARY -->
        <div class="payment-summary">
            <div>
                @if($isCod)
                    <div class="cod-notice">
                        <i class="bi bi-cash-coin"></i> TAGIH COD: Rp {{ number_format($pesanan->TotalHarga, 0, ',', '.') }}
                    </div>
                    <div style="font-size: 0.75rem; color: #854d0e; font-weight: 600;">
                        *Kurir wajib menagih uang tunai &amp; meminta 6 Digit PIN COD pembeli.
                    </div>
                @else
                    <div class="paid-notice">
                        <i class="bi bi-patch-check-fill"></i> SUDAH LUNAS (QRIS)
                    </div>
                    <div style="font-size: 0.75rem; color: #166534; font-weight: 600;">
                        *Kurir TIDAK PERLU menagih biaya apapun ke pembeli.
                    </div>
                @endif
            </div>

            <div class="total-amount">
                Rp {{ number_format($pesanan->TotalHarga, 0, ',', '.') }}
                <small>Ongkir: Rp {{ number_format($pesanan->Ongkir ?? 0, 0, ',', '.') }}</small>
            </div>
        </div>

        <!-- SIGNATURES -->
        <div class="signature-row">
            <div>
                <span>Diterima Oleh (Pembeli):</span>
                <div class="signature-line">( ........................................ )</div>
            </div>
            <div>
                <span>Diserahkan Oleh (Kurir / Toko):</span>
                <div class="signature-line">( ........................................ )</div>
            </div>
        </div>

    </div>

</body>
</html>

