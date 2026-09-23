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
use Intervention\Image\Drivers\Gd\Driver as GdDriver;
use Intervention\Image\ImageManager;

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
                [$fileContent, $fileName, $mimeType] = $this->compressImageToTarget($file);
                $path = 'v2/media/' . Str::uuid() . '.webp';
                $fileSize = strlen($fileContent);

                Log::info('[MediaUpload] Compression successful, storing to R2', [
                    'path'    => $path,
                    'size_kb' => round($fileSize / 1024, 2) . ' KB',
                ]);

                Storage::disk('r2')->put($path, $fileContent);
            } catch (\Throwable $e) {
                // Failsafe: if compression fails, log and fallback to storing the original file
                Log::error('[MediaUpload] Compression failed, saving uncompressed file as fallback', [
                    'error' => $e->getMessage(),
                    'trace' => $e->getTraceAsString(),
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
     * Compress and downscale image to <= 1MB without perceptual quality loss.
     *
     * @return array{0: string, 1: string, 2: string}
     */
    private function compressImageToTarget(UploadedFile $file): array
    {
        // Intervention Image v3 driver constructor
        $manager = new ImageManager(new GdDriver());
        $image = $manager->read($file->getRealPath());

        $originalWidth = $image->width();
        $originalHeight = $image->height();

        // 1. Cap excessive dimensions to 2560px max width/height (sharp on 4K/Retina displays)
        $image->scaleDown(width: 2560, height: 2560);

        Log::info('[MediaUpload] Dimensions checked/scaled', [
            'from' => "{$originalWidth}x{$originalHeight}",
            'to'   => "{$image->width()}x{$image->height()}",
        ]);

        // 2. Encode to WebP starting at high quality (85%)
        $quality = 85;
        $minQuality = 55;
        $encoded = null;

        do {
            $encoded = (string) $image->toWebp($quality);
            $currentBytes = strlen($encoded);

            Log::info('[MediaUpload] WebP attempt', [
                'quality'   => $quality,
                'size_kb'   => round($currentBytes / 1024, 2) . ' KB',
                'under_1mb' => $currentBytes <= self::MAX_TARGET_BYTES,
            ]);

            // Stop immediately when under 1MB or reached quality floor
            if ($currentBytes <= self::MAX_TARGET_BYTES || $quality <= $minQuality) {
                break;
            }

            $quality -= 5;
        } while ($quality >= $minQuality);

        // 3. Fallback: If still above 1MB (exceptionally complex textures), downscale dimensions
        while (strlen($encoded) > self::MAX_TARGET_BYTES && $image->width() > 1200) {
            Log::warning('[MediaUpload] Still above 1MB, adjusting pixel bounds...');

            $newWidth = (int) ($image->width() * 0.85);
            $newHeight = (int) ($image->height() * 0.85);
            $image->resize($newWidth, $newHeight);
            $encoded = (string) $image->toWebp(75);

            Log::info('[MediaUpload] Post-bound reduction', [
                'new_resolution' => "{$newWidth}x{$newHeight}",
                'size_kb'        => round(strlen($encoded) / 1024, 2) . ' KB',
            ]);
        }

        $baseName = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
        $newFileName = $baseName . '.webp';

        return [$encoded, $newFileName, 'image/webp'];
    }
}
