<?php

namespace App\Services;

use App\Models\Pesanan;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class MidtransService
{
    /**
     * Dapatkan Snap Token untuk transaksi pesanan.
     */
    public static function createSnapToken(Pesanan $pesanan): ?string
    {
        $serverKey = config('midtrans.server_key');
        $apiUrl = config('midtrans.snap_api_url');

        // Pastikan relasi detail & pengiriman termuat
        $pesanan->loadMissing(['detail.produk', 'user', 'pengiriman']);

        $user = $pesanan->user;
        $email = 'buyer@secondspace.test';
        $phone = '08123456789';

        if ($user && $user->NoHP_Email) {
            if (filter_var($user->NoHP_Email, FILTER_VALIDATE_EMAIL)) {
                $email = $user->NoHP_Email;
            } else {
                $phone = $user->NoHP_Email;
            }
        }

        $items = [];
        foreach ($pesanan->detail as $item) {
            $items[] = [
                'id' => 'PRD-' . $item->ProdukID,
                'price' => (int) $item->HargaSatuan,
                'quantity' => (int) $item->Jumlah,
                'name' => Str::limit($item->produk?->NamaProduk ?? 'Barang Thrift', 45, '...'),
            ];
        }

        // Midtrans Order ID: SS-{PesananID}-{timestamp} untuk mencegah duplicate order_id jika dicoba ulang
        $orderId = 'SS-' . $pesanan->PesananID . '-' . time();

        $payload = [
            'transaction_details' => [
                'order_id' => $orderId,
                'gross_amount' => (int) $pesanan->TotalHarga,
            ],
            'customer_details' => [
                'first_name' => $user?->Nama ?? 'Pelanggan SecondSpace',
                'email' => $email,
                'phone' => $phone,
                'shipping_address' => [
                    'first_name' => $user?->Nama ?? 'Penerima',
                    'address' => $pesanan->pengiriman?->AlamatPengiriman ?? 'Indonesia',
                ],
            ],
            'item_details' => $items,
            'enabled_payments' => [
                'bca_va', 'bni_va', 'bri_va', 'permata_va', 'other_va',
                'gopay', 'shopeepay', 'qris', 'echannel'
            ],
        ];

        try {
            $response = Http::withBasicAuth($serverKey, '')
                ->withHeaders([
                    'Accept' => 'application/json',
                    'Content-Type' => 'application/json',
                ])
                ->timeout(15)
                ->post($apiUrl, $payload);

            if ($response->successful()) {
                $data = $response->json();
                return $data['token'] ?? null;
            }

            Log::warning('Midtrans Snap Error: ' . $response->body());
            return null;
        } catch (\Throwable $e) {
            Log::error('Midtrans Connection Exception: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Verifikasi signature key notifikasi webhook dari Midtrans.
     */
    public static function verifySignature(string $orderId, string $statusCode, string $grossAmount, string $signatureKey): bool
    {
        $serverKey = config('midtrans.server_key');
        $expected = hash('sha512', $orderId . $statusCode . $grossAmount . $serverKey);
        return hash_equals($expected, $signatureKey);
    }

    /**
     * Ekstrak PesananID dari Midtrans order_id (Format: SS-{PesananID}-{timestamp} atau SS-{PesananID})
     */
    public static function parsePesananId(string $orderId): ?int
    {
        if (preg_match('/^SS-(\d+)/', $orderId, $matches)) {
            return (int) $matches[1];
        }

        return is_numeric($orderId) ? (int) $orderId : null;
    }
}