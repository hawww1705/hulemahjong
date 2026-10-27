<?php

namespace App\Support;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Str;

class UploadsHelper
{
    public static function savePublicImage(UploadedFile $file, string $subfolder = 'general'): string
    {
        $targetDir = public_path('uploads/' . trim($subfolder, '/'));
        if (!is_dir($targetDir)) {
            mkdir($targetDir, 0755, true);
        }

        $ext = strtolower($file->getClientOriginalExtension() ?: 'jpg');
        $filename = now()->format('YmdHis') . '-' . Str::random(8) . '.' . $ext;
        $file->move($targetDir, $filename);

        return 'uploads/' . trim($subfolder, '/') . '/' . $filename;
    }
}
