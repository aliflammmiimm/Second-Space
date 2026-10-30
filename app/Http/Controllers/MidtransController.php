<?php

namespace App\Http\Controllers;

use App\Models\Pembayaran;
use App\Models\Pesanan;
use App\Models\Produk;
use App\Services\MidtransService;
use App\Services\ReservasiService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class MidtransController extends Controller
{
    /**
     * Webhook Handler Callback dari Midtrans.
     * Tidak membutuhkan CSRF (dikecualikan di bootstrap/app.php).
     */
    public function notificationHandler(Request $request)
    {
        $payload = $request->all();
        Log::info('Midtrans Webhook Received:', $payload);

        $orderId = $payload['order_id'] ?? null;
        $statusCode = $payload['status_code'] ?? null;
        $grossAmount = $payload['gross_amount'] ?? null;
        $signatureKey = $payload['signature_key'] ?? null;
        $transactionStatus = $payload['transaction_status'] ?? null;
        $fraudStatus = $payload['fraud_status'] ?? null;
        $paymentType = $payload['payment_type'] ?? null;
        $transactionId = $payload['transaction_id'] ?? null;

        if (!$orderId || !$statusCode || !$grossAmount || !$signatureKey) {
            return response()->json(['message' => 'Invalid payload parameters'], 400);
        }

        // Verifikasi keaslian signature Midtrans
        if (!MidtransService::verifySignature($orderId, $statusCode, $grossAmount, $signatureKey)) {
            Log::warning('Midtrans Webhook: Invalid signature key for ' . $orderId);
            return response()->json(['message' => 'Invalid signature key'], 403);
        }

        $pesananId = MidtransService::parsePesananId($orderId);
        if (!$pesananId) {
            return response()->json(['message' => 'Order not found'], 404);
        }

        DB::transaction(function () use ($pesananId, $transactionStatus, $fraudStatus, $paymentType, $transactionId, $payload) {
            $pesanan = Pesanan::where('PesananID', $pesananId)->lockForUpdate()->first();
            if (!$pesanan) {
                return;
            }

            $pembayaran = Pembayaran::firstOrNew(['PesananID' => $pesanan->PesananID]);
            $pembayaran->TransactionID = $transactionId;
            $pembayaran->PaymentType = $paymentType;

            // Simpan detail pembayaran seperti nomor VA jika ada
            if (isset($payload['va_numbers'])) {
                $pembayaran->PaymentDetails = json_encode($payload['va_numbers']);
            } elseif (isset($payload['permata_va_number'])) {
                $pembayaran->PaymentDetails = json_encode(['bank' => 'permata', 'va_number' => $payload['permata_va_number']]);
            } elseif (isset($payload['bill_key'])) {
                $pembayaran->PaymentDetails = json_encode(['bill_key' => $payload['bill_key'], 'biller_code' => $payload['biller_code'] ?? '']);
            }

            // Skenario 1: Pembayaran Berhasil (Settlement / Capture Accept)
            if ($transactionStatus === 'settlement' || ($transactionStatus === 'capture' && $fraudStatus === 'accept')) {
                $pembayaran->StatusPembayaran = 'Valid';
                $pembayaran->save();

                if ($pesanan->StatusPesanan === 'Menunggu Pembayaran') {
                    $pesanan->StatusPesanan = 'Diproses';
                    $pesanan->BatasBayar = null;
                    $pesanan->save();

                    Produk::whereIn('ProdukID', $pesanan->detail()->pluck('ProdukID'))
                        ->where('Status', 'Dipesan')
                        ->update(['Status' => 'Terjual']);
                }
            }
            // Skenario 2: Menunggu Pembayaran (Pending)
            elseif ($transactionStatus === 'pending') {
                $pembayaran->StatusPembayaran = 'Menunggu';
                $pembayaran->save();
            }
            // Skenario 3: Kedaluwarsa / Dibatalkan / Gagal (Expire / Cancel / Deny)
            elseif (in_array($transactionStatus, ['expire', 'cancel', 'deny'], true)) {
                $pembayaran->StatusPembayaran = 'Tidak Valid';
                $pembayaran->save();

                if ($pesanan->StatusPesanan === 'Menunggu Pembayaran') {
                    ReservasiService::batalkan($pesanan, $transactionStatus === 'expire' ? 'Kedaluwarsa' : 'Dibatalkan');
                }
            }
        });

        return response()->json(['status' => 'OK']);
    }
}