<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class UploadService
{
    public static function getTargetDirectory(string $subfolder = ''): string
    {
        $base = public_path('upload' . ($subfolder ? '/' . trim($subfolder, '/') : ''));
        if (!is_dir($base) || !is_writable($base)) {
            $base = '/tmp/upload' . ($subfolder ? '/' . trim($subfolder, '/') : '');
            if (!is_dir($base)) {
                @mkdir($base, 0755, true);
            }
        }
        return $base;
    }

    public static function saveFile($file, string $subfolder = '', ?string $customPrefix = null): string
    {
        $ext = method_exists($file, 'guessExtension') && $file->guessExtension()
            ? $file->guessExtension()
            : (method_exists($file, 'getClientOriginalExtension') ? $file->getClientOriginalExtension() : 'jpg');

        $prefix = $customPrefix ? $customPrefix . '_' : '';
        $name = $prefix . time() . '_' . Str::random(10) . '.' . $ext;
        $relativePath = ($subfolder ? trim($subfolder, '/') . '/' : '') . $name;

        // Baca data binary dan mime type sebelum dipindahkan
        $binary = null;
        $mime = 'image/jpeg';
        try {
            if (method_exists($file, 'getContent')) {
                $binary = $file->getContent();
            } elseif (method_exists($file, 'getRealPath') && file_exists($file->getRealPath())) {
                $binary = @file_get_contents($file->getRealPath());
            } elseif (method_exists($file, 'getPathname') && file_exists($file->getPathname())) {
                $binary = @file_get_contents($file->getPathname());
            }

            if (method_exists($file, 'getMimeType')) {
                $mime = $file->getMimeType() ?: 'image/jpeg';
            } elseif (method_exists($file, 'getClientMimeType')) {
                $mime = $file->getClientMimeType() ?: 'image/jpeg';
            }
        } catch (\Throwable $e) {
            Log::warning("Gagal membaca file upload: " . $e->getMessage());
        }

        // Kompresi & downscale otomatis agar upload cepat, hemat storage TiDB, dan loading instan di browser
        if ($binary !== null && strlen($binary) > 0) {
            $binary = self::compressImage($binary, $mime);
        }

        // 1. Simpan ke database gambar_storage agar selalu permanen di TiDB Cloud (serverless Vercel)
        if ($binary !== null && strlen($binary) > 0) {
            try {
                DB::table('gambar_storage')->updateOrInsert(
                    ['nama_file' => $relativePath],
                    [
                        'mime' => $mime,
                        'ukuran' => strlen($binary),
                        'data' => $binary,
                        'created_at' => now(),
                    ]
                );
            } catch (\Throwable $e) {
                Log::warning("Gagal simpan gambar_storage: " . $e->getMessage());
            }
        }

        // 2. Simpan juga ke disk lokal / tmp
        try {
            $targetDir = self::getTargetDirectory($subfolder);
            if ($binary !== null && strlen($binary) > 0) {
                file_put_contents($targetDir . '/' . $name, $binary);
            } else {
                $file->move($targetDir, $name);
            }
        } catch (\Throwable $e) {
            Log::warning("Gagal move/write file lokal: " . $e->getMessage());
        }

        return $name;
    }

    /**
     * Kompresi dan resize gambar agar ukuran file menjadi kecil (100-250 KB) tanpa mengurangi kualitas tampilan.
     */
    public static function compressImage(string $binary, string &$mime, int $maxWidth = 1200, int $quality = 82): string
    {
        if (!str_starts_with($mime, 'image/')) {
            return $binary;
        }

        if (!function_exists('imagecreatefromstring')) {
            return $binary;
        }

        try {
            $src = @imagecreatefromstring($binary);
            if (!$src) {
                return $binary;
            }

            $origW = imagesx($src);
            $origH = imagesy($src);

            if ($origW <= 0 || $origH <= 0) {
                imagedestroy($src);
                return $binary;
            }

            $newW = $origW;
            $newH = $origH;
            if ($origW > $maxWidth || $origH > $maxWidth) {
                if ($origW >= $origH) {
                    $newW = $maxWidth;
                    $newH = (int) round(($origH / $origW) * $maxWidth);
                } else {
                    $newH = $maxWidth;
                    $newW = (int) round(($origW / $origH) * $maxWidth);
                }
            }

            $dst = imagecreatetruecolor($newW, $newH);

            // Background putih jika gambar transparan dikonversi ke JPEG
            $white = imagecolorallocate($dst, 255, 255, 255);
            imagefilledrectangle($dst, 0, 0, $newW, $newH, $white);

            imagecopyresampled($dst, $src, 0, 0, 0, 0, $newW, $newH, $origW, $origH);

            ob_start();
            imagejpeg($dst, null, $quality);
            $compressed = ob_get_clean();

            imagedestroy($src);
            imagedestroy($dst);

            if ($compressed && strlen($compressed) > 0) {
                $mime = 'image/jpeg';
                return $compressed;
            }
        } catch (\Throwable $e) {
            Log::warning("Gagal kompresi gambar: " . $e->getMessage());
        }

        return $binary;
    }

    public static function deleteFile(?string $filename, string $subfolder = ''): void
    {
        if (!$filename || $filename === 'default.jpg' || str_starts_with($filename, 'http')) {
            return;
        }

        $relativePath = ($subfolder ? trim($subfolder, '/') . '/' : '') . $filename;

        // Hapus dari database cloud
        try {
            DB::table('gambar_storage')->where('nama_file', $relativePath)->delete();
        } catch (\Throwable $e) {}

        // Hapus dari disk lokal jika ada
        $local = public_path('upload/' . $relativePath);
        if (file_exists($local) && is_file($local)) {
            @unlink($local);
        }

        // Hapus dari tmp jika ada
        $tmp = '/tmp/upload/' . $relativePath;
        if (file_exists($tmp) && is_file($tmp)) {
            @unlink($tmp);
        }
    }
}
