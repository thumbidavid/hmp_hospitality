<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Media;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class MediaController extends Controller
{
    private const MAX_TARGET_BYTES = 1048576; // 1MB

    /**
     * Store an uploaded file in R2 and register it in the database.
     */
    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'file' => ['required', 'file', 'max:20480'], // 20MB upper limit
        ]);

        /** @var UploadedFile $file */
        $file = $request->file('file');
        $mimeType = $file->getClientMimeType();
        $originalSize = $file->getSize();
        $originalName = $file->getClientOriginalName();

        Log::info('[MediaUpload] Upload received', [
            'filename'       => $originalName,
            'mime_type'      => $mimeType,
            'size_formatted' => round($originalSize / 1024 / 1024, 2) . ' MB',
        ]);

        $path = null;
        $fileSize = $originalSize;
        $fileName = $originalName;

        // Compress if it is an image
        if ($this->isCompressibleImage($mimeType)) {
            Log::info('[MediaUpload] File is image, starting compression pipeline...');

            try {
                [$fileContent, $fileName, $mimeType] = $this->compressImage($file);
                $path = 'v2/media/' . Str::uuid() . '.webp';
                $fileSize = strlen($fileContent);

                Log::info('[MediaUpload] Compression successful, storing to R2', [
                    'path'    => $path,
                    'size_kb' => round($fileSize / 1024, 2) . ' KB',
                ]);

                Storage::disk('r2')->put($path, $fileContent);
            } catch (\Throwable $e) {
                // Log the exact error line and message
                Log::error('[MediaUpload] Compression error: ' . $e->getMessage(), [
                    'exception' => get_class($e),
                    'file'      => $e->getFile() . ':' . $e->getLine(),
                ]);

                $path = $file->store('v2/media', 'r2');
            }
        } else {
            Log::info('[MediaUpload] File is document (PDF, etc.), storing as-is.');
            $path = $file->store('v2/media', 'r2');
        }

        $url = Storage::disk('r2')->url($path);

        // Save entry in media table
        $media = Media::create([
            'file_path' => $path,
            'url'       => $url,
            'file_name' => $fileName,
            'mime_type' => $mimeType,
            'size'      => $fileSize,
        ]);

        Log::info('[MediaUpload] Completed successfully', [
            'id'  => $media->id,
            'url' => $media->url,
        ]);

        return response()->json([
            'id'  => $media->id,
            'url' => $media->url,
        ]);
    }

    /**
     * Determine if the file mime type is a compressible image.
     */
    private function isCompressibleImage(?string $mimeType): bool
    {
        return in_array($mimeType, [
            'image/jpeg',
            'image/png',
            'image/webp',
            'image/bmp',
        ]);
    }

    /**
     * Compress using Intervention Image if available, or native PHP GD as bulletproof fallback.
     *
     * @return array{0: string, 1: string, 2: string}
     */
    private function compressImage(UploadedFile $file): array
    {
        // 1. Try Intervention Image (v3 or v2)
        if (class_exists(\Intervention\Image\ImageManager::class)) {
            try {
                return $this->compressViaIntervention($file);
            } catch (\Throwable $ex) {
                Log::warning('[MediaUpload] Intervention failed (' . $ex->getMessage() . '), attempting native GD fallback...');
            }
        }

        // 2. Pure Native PHP GD (Zero dependency fallback)
        return $this->compressViaNativeGd($file);
    }

    /**
     * Intervention Image compression.
     */
    private function compressViaIntervention(UploadedFile $file): array
    {
        // Check for v3 Driver class or fallback to string
        if (class_exists(\Intervention\Image\Drivers\Gd\Driver::class)) {
            $manager = new \Intervention\Image\ImageManager(new \Intervention\Image\Drivers\Gd\Driver());
        } elseif (class_exists(\Intervention\Image\Drivers\Imagick\Driver::class)) {
            $manager = new \Intervention\Image\ImageManager(new \Intervention\Image\Drivers\Imagick\Driver());
        } else {
            $manager = new \Intervention\Image\ImageManager(['driver' => 'gd']);
        }

        // v3 uses read(), v2 uses make()
        $image = method_exists($manager, 'read')
            ? $manager->read($file->getRealPath())
            : $manager->make($file->getRealPath());

        // Cap dimensions to 2560px
        if (method_exists($image, 'scaleDown')) {
            $image->scaleDown(width: 2560, height: 2560);
        } else {
            $image->resize(2560, 2560, function ($c) {
                $c->aspectRatio();
                $c->upsize();
            });
        }

        // Loop quality down to <= 1MB
        $quality = 85;
        $minQuality = 55;
        $encoded = null;

        do {
            $encoded = method_exists($image, 'toWebp')
                ? (string) $image->toWebp($quality)
                : (string) $image->encode('webp', $quality);

            if (strlen($encoded) <= self::MAX_TARGET_BYTES || $quality <= $minQuality) {
                break;
            }
            $quality -= 5;
        } while ($quality >= $minQuality);

        $baseName = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
        return [$encoded, $baseName . '.webp', 'image/webp'];
    }

    /**
     * Pure Native PHP GD compression — no library dependencies.
     */
    private function compressViaNativeGd(UploadedFile $file): array
    {
        $realPath = $file->getRealPath();
        [$origWidth, $origHeight, $imageType] = getimagesize($realPath);

        // Load image resource
        $src = match ($imageType) {
            IMAGETYPE_JPEG => imagecreatefromjpeg($realPath),
            IMAGETYPE_PNG  => imagecreatefrompng($realPath),
            IMAGETYPE_WEBP => imagecreatefromwebp($realPath),
            IMAGETYPE_BMP  => imagecreatefrombmp($realPath),
            default        => throw new \Exception('Unsupported GD image type: ' . $imageType),
        };

        if (!$src) {
            throw new \Exception('Failed to create GD image resource.');
        }

        // Handle alpha transparency
        imagealphablending($src, true);
        imagesavealpha($src, true);

        // Scale down to max 2560px
        $maxWidth = 2560;
        $maxHeight = 2560;
        $ratio = min($maxWidth / $origWidth, $maxHeight / $origHeight, 1.0);
        $newWidth = (int) round($origWidth * $ratio);
        $newHeight = (int) round($origHeight * $ratio);

        $dest = imagecreatetruecolor($newWidth, $newHeight);
        imagealphablending($dest, false);
        imagesavealpha($dest, true);
        imagecopyresampled($dest, $src, 0, 0, 0, 0, $newWidth, $newHeight, $origWidth, $origHeight);
        imagedestroy($src);

        // Quality reduction loop
        $quality = 85;
        $minQuality = 55;
        $output = '';

        do {
            ob_start();
            imagewebp($dest, null, $quality);
            $output = ob_get_clean();

            if (strlen($output) <= self::MAX_TARGET_BYTES || $quality <= $minQuality) {
                break;
            }
            $quality -= 5;
        } while ($quality >= $minQuality);

        imagedestroy($dest);

        $baseName = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
        return [$output, $baseName . '.webp', 'image/webp'];
    }
}
