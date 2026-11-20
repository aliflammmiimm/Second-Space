<?php

namespace App\Services;

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

        $targetDir = self::getTargetDirectory($subfolder);
        $file->move($targetDir, $name);

        return $name;
    }

    public static function deleteFile(?string $filename, string $subfolder = ''): void
    {
        if (!$filename || $filename === 'default.jpg' || str_starts_with($filename, 'http')) {
            return;
        }

        $local = public_path('upload/' . ($subfolder ? trim($subfolder, '/') . '/' : '') . $filename);
        if (file_exists($local) && is_file($local)) {
            @unlink($local);
        }

        $tmp = '/tmp/upload/' . ($subfolder ? trim($subfolder, '/') . '/' : '') . $filename;
        if (file_exists($tmp) && is_file($tmp)) {
            @unlink($tmp);
        }
    }
}
