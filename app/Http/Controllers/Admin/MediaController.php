<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Media;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class MediaController extends Controller
{
    /**
     * Store an uploaded file in R2 and register it in the database.
     */
    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'file' => ['required', 'file', 'max:10240'], // 10MB standard limit
        ]);

        $file = $request->file('file');

        // Store file securely inside R2
        $path = $file->store('v2/media', 'r2');
        $url = Storage::disk('r2')->url($path);

        // Register the file in the media database table
        $media = Media::create([
            'file_path' => $path,
            'url' => $url,
            'file_name' => $file->getClientOriginalName(),
            'mime_type' => $file->getClientMimeType(),
            'size' => $file->getSize(),
        ]);

        // Return the ID expected by FileUploader.vue
        return response()->json([
            'id' => $media->id,
            'url' => $media->url,
        ]);
    }
}
