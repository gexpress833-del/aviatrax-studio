<?php

namespace App\Services;

use Cloudinary\Cloudinary;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Str;

class CloudinaryMediaService
{
    protected Cloudinary $cloudinary;

    public function __construct(?Cloudinary $cloudinary = null)
    {
        $this->cloudinary = $cloudinary ?? new Cloudinary([
            'cloud' => [
                'cloud_name' => config('filesystems.disks.cloudinary.cloud'),
                'api_key' => config('filesystems.disks.cloudinary.key'),
                'api_secret' => config('filesystems.disks.cloudinary.secret'),
            ],
            'url' => [
                'secure' => true,
            ],
        ]);
    }

    public function buildProjectPath(int $projectId, string $stage): string
    {
        return sprintf('aviatrax/projects/%d/%s', $projectId, $stage);
    }

    public function upload(UploadedFile $file, int $projectId, string $stage, ?string $version = null): array
    {
        if (! $file->isValid()) {
            throw new \RuntimeException('Fichier invalide.');
        }

        $folder = $this->buildProjectPath($projectId, $stage);
        $publicId = Str::slug(pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME)) . '-' . time();

        if ($version) {
            $publicId .= '-' . Str::slug($version);
        }

        $resource = $this->cloudinary->uploadApi()->upload(
            $file->getRealPath(),
            [
                'folder' => $folder,
                'public_id' => $publicId,
                'resource_type' => $this->detectResourceType($file->getClientOriginalExtension()),
                'overwrite' => false,
            ]
        );

        return [
            'file_url' => $resource['secure_url'] ?? $resource['url'] ?? null,
            'public_id' => $resource['public_id'] ?? $publicId,
            'resource_type' => $resource['resource_type'] ?? $this->detectResourceType($file->getClientOriginalExtension()),
            'version' => $version,
            'file_size' => $file->getSize(),
        ];
    }

    public function signedUrl(string $publicId, string $resourceType = 'image', int $expirationMinutes = 60): string
    {
        $timestamp = now()->addMinutes($expirationMinutes)->timestamp;

        $params = [
            'public_id' => $publicId,
            'timestamp' => $timestamp,
            'resource_type' => $resourceType,
        ];

        $signature = hash('sha256', http_build_query($params) . config('filesystems.disks.cloudinary.secret'));

        return sprintf(
            'https://res.cloudinary.com/%s/%s/upload/%s?public_id=%s&timestamp=%s&signature=%s',
            config('filesystems.disks.cloudinary.cloud'),
            $resourceType,
            'authenticated',
            urlencode($publicId),
            $timestamp,
            $signature
        );
    }

    protected function detectResourceType(string $extension): string
    {
        $audioExtensions = ['mp3', 'wav', 'aac', 'flac', 'ogg', 'm4a'];
        $videoExtensions = ['mp4', 'mov', 'avi', 'mkv'];
        $imageExtensions = ['jpg', 'jpeg', 'png', 'webp'];

        if (in_array(strtolower($extension), $audioExtensions, true)) {
            return 'video';
        }

        if (in_array(strtolower($extension), $videoExtensions, true)) {
            return 'video';
        }

        if (in_array(strtolower($extension), $imageExtensions, true)) {
            return 'image';
        }

        return 'raw';
    }
}
