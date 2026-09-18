<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Media;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
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
            'file' => ['required', 'file', 'max:20480'], // Allow uploads up to 20MB prior to compression
        ]);

        /** @var UploadedFile $file */
        $file = $request->file('file');
        $mimeType = $file->getClientMimeType();

        // Check if the uploaded file is an image
        if ($this->isCompressibleImage($mimeType)) {
            [$fileContent, $fileName, $mimeType] = $this->compressImageToTarget($file);
            $path = 'v2/media/' . Str::uuid() . '.webp';
            $fileSize = strlen($fileContent);

            // Store compressed WebP in R2
            Storage::disk('r2')->put($path, $fileContent);
        } else {
            // PDF or regular documents: store directly as-is
            $path = $file->store('v2/media', 'r2');
            $fileName = $file->getClientOriginalName();
            $fileSize = $file->getSize();
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

        // Return the ID expected by FileUploader.vue
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
            'image/avif',
            'image/bmp',
        ]);
    }

    /**
     * Compress and constrain an image to stay <= 1MB without perceptual quality loss.
     *
     * @return array{0: string, 1: string, 2: string}
     */
    private function compressImageToTarget(UploadedFile $file): array
    {
        $manager = new ImageManager(new GdDriver());
        $image = $manager->read($file->getRealPath());

        // 1. Cap excessive dimensions to a 2560px boundary (maintains aspect ratio, never upscales)
        $image->scaleDown(width: 2560, height: 2560);

        // 2. Encode to WebP starting at high visual quality (85%)
        $quality = 85;
        $minQuality = 55;
        $encoded = null;

        do {
            $encoded = (string) $image->toWebp($quality);

            // Exit immediately once under 1MB or reached quality floor
            if (strlen($encoded) <= self::MAX_TARGET_BYTES || $quality <= $minQuality) {
                break;
            }

            $quality -= 5;
        } while ($quality >= $minQuality);

        // 3. Fallback downscale if an extremely complex image still exceeds 1MB
        while (strlen($encoded) > self::MAX_TARGET_BYTES && $image->width() > 1200) {
            $newWidth = (int) ($image->width() * 0.85);
            $newHeight = (int) ($image->height() * 0.85);
            $image->resize($newWidth, $newHeight);
            $encoded = (string) $image->toWebp(75);
        }

        $baseName = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
        $newFileName = $baseName . '.webp';

        return [$encoded, $newFileName, 'image/webp'];
    }
}
