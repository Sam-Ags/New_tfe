<?php

namespace App\Http\Controllers;

use App\Models\UploadedMedia;
use Illuminate\Http\Response;

class MediaController extends Controller
{
    public function show(string $uuid): Response
    {
        $media = UploadedMedia::where('uuid', $uuid)->firstOrFail();
        $contents = base64_decode($media->contents, true);

        abort_if($contents === false, 404);

        return response($contents, 200, [
            'Content-Type' => $media->mime_type,
            'Content-Length' => (string) strlen($contents),
            'Cache-Control' => 'public, max-age=31536000, immutable',
            'Content-Disposition' => 'inline; filename="'.addslashes($media->original_name ?: $media->uuid).'"',
        ]);
    }
}
