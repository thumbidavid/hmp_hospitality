<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Media;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Laravel\Facades\Image;

class MediaController extends Controller
{
    private const MAX_IMAGE_BYTES = 1024 * 1024;  // compress down to 1MB
    private const MAX_STARTING_WIDTH = 2000;       // pre-resize cap in pixels

    /**
     * Store an uploaded file in R2 and register it in the database.
     */
    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'file' => ['required', 'file', 'max:20480'], // 20MB raw upload limit
        ]);

        $file = $request->file('file');

        [$contents, $extension] = $this->prepareFileContents($file);

        $filename = uniqid() . '.' . $extension;
        $path = 'v2/media/' . $filename;

        Storage::disk('r2')->put($path, $contents);
        $url = Storage::disk('r2')->url($path);

        $media = Media::create([
            'file_path' => $path,
            'url' => $url,
            'file_name' => $file->getClientOriginalName(),
            'mime_type' => $file->getMimeType(),
            'size' => strlen($contents),
        ]);

        return response()->json([
            'id' => $media->id,
            'url' => $media->url,
        ]);
    }

    /**
     * Compress the file if it's an image over the size limit.
     * Returns [binary contents, file extension to store with].
     */
    private function prepareFileContents(UploadedFile $file): array
    {
        $mime = $file->getMimeType();
        $isCompressible = in_array($mime, ['image/jpeg', 'image/png', 'image/webp'], true);

        if (!$isCompressible || $file->getSize() <= self::MAX_IMAGE_BYTES) {
            return [file_get_contents($file->getRealPath()), $file->getClientOriginalExtension()];
        }

        $image = Image::read($file->getRealPath());

        // Pre-resize: for large uploads (phone/DSLR photos), most of the
        // size comes from raw pixel count, not encoding quality. Cap the
        // starting width before quality-stepping so we're not wasting
        // passes trying to quality-compress a 6000px-wide image.
        if ($image->width() > self::MAX_STARTING_WIDTH) {
            $image = $image->scaleDown(width: self::MAX_STARTING_WIDTH);
        }

        $extension = $mime === 'image/png' && !$this->hasTransparency($image)
            ? 'jpg'
            : $this->extensionFor($mime);

        $binary = null;

        // Step quality down until under the target size, but don't go
        // below 60 — beyond that "smaller" starts meaning "visibly worse".
        for ($quality = 90; $quality >= 60; $quality -= 5) {
            $encoded = $extension === 'jpg'
                ? $image->toJpeg($quality)
                : ($extension === 'webp' ? $image->toWebp($quality) : $image->toPng());

            $binary = (string) $encoded;

            if (strlen($binary) <= self::MAX_IMAGE_BYTES || $extension === 'png') {
                return [$binary, $extension];
            }
        }

        // Still too big at quality 60: keep shrinking dimensions further,
        // re-encoding at quality 75 each step, until under the target.
        $width = $image->width();
        while (strlen($binary) > self::MAX_IMAGE_BYTES && $width > 640) {
            $width = (int) ($width * 0.85);
            $image = $image->scaleDown(width: $width);
            $binary = $extension === 'jpg' ? (string) $image->toJpeg(75) : (string) $image->toWebp(75);
        }

        return [$binary, $extension];
    }

    private function extensionFor(string $mime): string
    {
        return match ($mime) {
            'image/png' => 'png',
            'image/webp' => 'webp',
            default => 'jpg',
        };
    }

    private function hasTransparency($image): bool
    {
        try {
            $core = $image->core()->native();
            return imageistruecolor($core) && imagecolorat($core, 0, 0) >> 24 !== 0;
        } catch (\Throwable) {
            return true; // be conservative — keep PNG if unsure
        }
    }
}
