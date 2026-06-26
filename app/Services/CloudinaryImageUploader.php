<?php

namespace App\Services;

use Cloudinary\Cloudinary;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Str;
use RuntimeException;

class CloudinaryImageUploader
{
    private ?Cloudinary $cloudinary;

    public function __construct(?Cloudinary $cloudinary = null)
    {
        $this->cloudinary = $cloudinary;
    }

    public function upload(UploadedFile $file, string $folder, string $prefix): string
    {
        if (! config('services.cloudinary.cloud_name') || ! config('services.cloudinary.api_key') || ! config('services.cloudinary.api_secret')) {
            throw new RuntimeException('La configuration Cloudinary est incomplète.');
        }

        $response = $this->cloudinary()->uploadApi()->upload($file->getRealPath(), [
            'folder' => $this->folderPath($folder),
            'public_id' => $prefix.'_'.now()->format('YmdHis').'_'.Str::random(10),
        ]);

        if (empty($response['secure_url'])) {
            throw new RuntimeException('Upload Cloudinary impossible.');
        }

        return $response['secure_url'];
    }

    private function cloudinary(): Cloudinary
    {
        return $this->cloudinary ??= new Cloudinary([
            'cloud' => [
                'cloud_name' => config('services.cloudinary.cloud_name'),
                'api_key' => config('services.cloudinary.api_key'),
                'api_secret' => config('services.cloudinary.api_secret'),
            ],
            'url' => [
                'secure' => true,
            ],
        ]);
    }

    private function folderPath(string $folder): string
    {
        return collect([config('services.cloudinary.folder'), $folder])
            ->filter()
            ->map(fn (string $part) => trim($part, '/'))
            ->implode('/');
    }
}
