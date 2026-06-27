<?php

namespace App\Services;

use GuzzleHttp\Client;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Str;
use RuntimeException;

class CloudinaryImageUploader
{
    private ?Client $client;

    public function __construct(?Client $client = null)
    {
        $this->client = $client;
    }

    public function upload(UploadedFile $file, string $folder, string $prefix): string
    {
        if (! config('services.cloudinary.cloud_name') || ! config('services.cloudinary.api_key') || ! config('services.cloudinary.api_secret')) {
            throw new RuntimeException('La configuration Cloudinary est incomplète.');
        }

        $path = $file->getPathname();

        if (! $file->isValid() || ! is_file($path) || (int) $file->getSize() <= 0) {
            throw new RuntimeException('Le fichier envoyé est vide ou illisible.');
        }

        $params = [
            'folder' => $this->folderPath($folder),
            'public_id' => $prefix.'_'.now()->format('YmdHis').'_'.Str::random(10),
            'timestamp' => time(),
        ];

        $handle = fopen($path, 'rb');

        if (! $handle) {
            throw new RuntimeException('Le fichier envoyé est illisible.');
        }

        try {
            $response = $this->client()->post($this->uploadUrl(), [
                'http_errors' => false,
                'multipart' => [
                    [
                        'name' => 'file',
                        'contents' => $handle,
                        'filename' => $file->getClientOriginalName(),
                    ],
                    [
                        'name' => 'api_key',
                        'contents' => config('services.cloudinary.api_key'),
                    ],
                    [
                        'name' => 'signature',
                        'contents' => $this->signature($params),
                    ],
                    ...$this->multipartParams($params),
                ],
            ]);
        } finally {
            if (is_resource($handle)) {
                fclose($handle);
            }
        }

        $data = json_decode((string) $response->getBody(), true);

        if ($response->getStatusCode() >= 400 || ! is_array($data) || empty($data['secure_url'])) {
            $message = is_array($data) ? ($data['error']['message'] ?? null) : null;

            throw new RuntimeException($message ?: 'Upload Cloudinary impossible.');
        }

        return $data['secure_url'];
    }

    private function client(): Client
    {
        return $this->client ??= new Client([
            'verify' => $this->caBundlePath(),
        ]);
    }

    private function uploadUrl(): string
    {
        return 'https://api.cloudinary.com/v1_1/'.config('services.cloudinary.cloud_name').'/image/upload';
    }

    private function multipartParams(array $params): array
    {
        return collect($params)
            ->map(fn ($value, string $name) => [
                'name' => $name,
                'contents' => (string) $value,
            ])
            ->values()
            ->all();
    }

    private function signature(array $params): string
    {
        ksort($params);

        $payload = collect($params)
            ->map(fn ($value, string $key) => $key.'='.$value)
            ->implode('&');

        return sha1($payload.config('services.cloudinary.api_secret'));
    }

    private function folderPath(string $folder): string
    {
        return collect([config('services.cloudinary.folder'), $folder])
            ->filter()
            ->map(fn (string $part) => trim($part, '/'))
            ->implode('/');
    }

    private function caBundlePath(): string|bool
    {
        $configuredPath = config('services.cloudinary.ca_bundle');

        if (is_string($configuredPath) && $configuredPath !== '' && file_exists($configuredPath)) {
            return $configuredPath;
        }

        foreach ([
            storage_path('app/private/cacert.pem'),
            storage_path('app/private/isrgrootx1.pem'),
            '/etc/ssl/certs/ca-certificates.crt',
            '/etc/pki/tls/certs/ca-bundle.crt',
            '/etc/ssl/cert.pem',
        ] as $path) {
            if (file_exists($path)) {
                return $path;
            }
        }

        return true;
    }
}
