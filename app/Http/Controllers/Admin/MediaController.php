<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Media;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
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
            'file' => ['required', 'file', 'max:20480'], // Allow uploads up to 20MB prior to compression
        ]);

        /** @var UploadedFile $file */
        $file = $request->file('file');
        $mimeType = $file->getClientMimeType();

        // Check if the uploaded file is an image
        if ($this->isCompressibleImage($mimeType)) {
            [$fileContent, $fileName, $mimeType, $extension] = $this->compressImageToTarget($file);
            $path = 'v2/media/' . Str::uuid() . '.' . $extension;
            $fileSize = strlen($fileContent);

            // Store compressed image in R2
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
            'image/bmp',
        ]);
    }

    /**
     * Compress and constrain an image to stay <= 1MB without perceptual quality loss.
     * Compatible with Intervention Image v2.
     *
     * @return array{0: string, 1: string, 2: string, 3: string}
     */
    private function compressImageToTarget(UploadedFile $file): array
    {
        $manager = new ImageManager(['driver' => 'gd']);
        $image = $manager->make($file->getRealPath());

        // Auto-orient based on camera EXIF data (prevents phone photos rotating sideways)
        if (method_exists($image, 'orientate')) {
            $image->orientate();
        }

        // 1. Cap dimensions at max 2560px preserving aspect ratio (never upscales smaller images)
        $image->resize(2560, 2560, function ($constraint) {
            $constraint->aspectRatio();
            $constraint->upsize();
        });

        // Determine target format (WebP if PHP GD supports it, otherwise fallback to JPEG)
        $format = function_exists('imagewebp') ? 'webp' : 'jpg';
        $mimeType = $format === 'webp' ? 'image/webp' : 'image/jpeg';

        // 2. Start at high quality (85%) and step down gradually only if over 1MB
        $quality = 85;
        $minQuality = 55;
        $encoded = null;

        do {
            $encoded = (string) $image->encode($format, $quality);

            // Exit immediately when <= 1MB or reached minimum quality threshold
            if (strlen($encoded) <= self::MAX_TARGET_BYTES || $quality <= $minQuality) {
                break;
            }

            $quality -= 5;
        } while ($quality >= $minQuality);

        // 3. Fallback: If still above 1MB (e.g. exceptionally complex images), reduce dimensions
        while (strlen($encoded) > self::MAX_TARGET_BYTES && $image->width() > 1200) {
            $newWidth = (int) ($image->width() * 0.85);
            $image->resize($newWidth, null, function ($constraint) {
                $constraint->aspectRatio();
            });
            $encoded = (string) $image->encode($format, 75);
        }

        $baseName = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
        $newFileName = $baseName . '.' . $format;

        return [$encoded, $newFileName, $mimeType, $format];
    }
}