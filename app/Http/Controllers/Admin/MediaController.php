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
            'file' => ['required', 'file', 'max:20480'], // Allow uploads up to 20MB before compression
        ]);

        /** @var UploadedFile $file */
        $file = $request->file('file');
        $mimeType = $file->getClientMimeType();
        $originalSize = $file->getSize();
        $originalName = $file->getClientOriginalName();

        Log::info('[MediaUpload] Upload received', [
            'filename'      => $originalName,
            'mime_type'     => $mimeType,
            'size_bytes'    => $originalSize,
            'size_formatted' => round($originalSize / 1024 / 1024, 2) . ' MB',
        ]);

        // Check if the uploaded file is an image
        if ($this->isCompressibleImage($mimeType)) {
            Log::info('[MediaUpload] File identified as image, beginning compression pipeline...');

            [$fileContent, $fileName, $mimeType] = $this->compressImageToTarget($file);
            $path = 'v2/media/' . Str::uuid() . '.webp';
            $fileSize = strlen($fileContent);

            Log::info('[MediaUpload] Writing compressed WebP image to R2', [
                'path' => $path,
                'size' => round($fileSize / 1024, 2) . ' KB',
            ]);

            Storage::disk('r2')->put($path, $fileContent);
        } else {
            Log::info('[MediaUpload] File is a document (non-image), bypassing compression.');

            $path = $file->store('v2/media', 'r2');
            $fileName = $originalName;
            $fileSize = $originalSize;
        }

        $url = Storage::disk('r2')->url($path);

        // Register the file in the media database table
        $media = Media::create([
            'file_path' => $path,
            'url'       => $url,
            'file_name' => $fileName,
            'mime_type' => $mimeType,
            'size'      => $fileSize,
        ]);

        Log::info('[MediaUpload] Upload complete and stored in database', [
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
     * Compress and constrain an image to stay <= 1MB without perceptual quality loss (v3).
     *
     * @return array{0: string, 1: string, 2: string}
     */
    private function compressImageToTarget(UploadedFile $file): array
    {
        $manager = ImageManager::gd();
        $image = $manager->read($file->getRealPath());

        $originalWidth = $image->width();
        $originalHeight = $image->height();

        // 1. Cap excessive dimensions to 2560px max width/height
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

            Log::info('[MediaUpload] WebP encoding attempt', [
                'quality'    => $quality,
                'size_bytes' => $currentBytes,
                'size_kb'    => round($currentBytes / 1024, 2) . ' KB',
                'target_1mb' => self::MAX_TARGET_BYTES,
                'under_1mb'  => $currentBytes <= self::MAX_TARGET_BYTES,
            ]);

            // Stop immediately if <= 1MB or reached quality floor
            if ($currentBytes <= self::MAX_TARGET_BYTES || $quality <= $minQuality) {
                break;
            }

            $quality -= 5;
        } while ($quality >= $minQuality);

        // 3. Fallback: If still above 1MB (rare complex images), downscale dimensions
        while (strlen($encoded) > self::MAX_TARGET_BYTES && $image->width() > 1200) {
            Log::warning('[MediaUpload] Still above 1MB at minQuality, resizing dimensions down further...');

            $newWidth = (int) ($image->width() * 0.85);
            $newHeight = (int) ($image->height() * 0.85);
            $image->resize($newWidth, $newHeight);
            $encoded = (string) $image->toWebp(75);

            Log::info('[MediaUpload] Post-dimension reduction size', [
                'new_resolution' => "{$newWidth}x{$newHeight}",
                'size_kb'        => round(strlen($encoded) / 1024, 2) . ' KB',
            ]);
        }

        $baseName = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
        $newFileName = $baseName . '.webp';

        Log::info('[MediaUpload] Compression finalized', [
            'final_filename' => $newFileName,
            'final_size_kb'  => round(strlen($encoded) / 1024, 2) . ' KB',
        ]);

        return [$encoded, $newFileName, 'image/webp'];
    }
}
