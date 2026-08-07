<?php

namespace App\Services;

use Cloudinary\Cloudinary;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class ProductImageService
{
    public function upload(UploadedFile $file): string
    {
        if (! $this->usesCloudinary()) {
            return $file->store('products', 'public');
        }

        $result = $this->client()->uploadApi()->upload($file->getRealPath(), [
            'folder' => 'qr-restaurant/products',
            'resource_type' => 'image',
        ]);

        return (string) $result['public_id'];
    }

    public function delete(?string $image): void
    {
        if (! $image) {
            return;
        }

        if (! $this->usesCloudinary()) {
            Storage::disk('public')->delete($image);
            return;
        }

        $this->client()->uploadApi()->destroy($image, [
            'invalidate' => true,
            'resource_type' => 'image',
        ]);
    }

    public function url(?string $image): ?string
    {
        if (! $image) {
            return null;
        }

        if (! $this->usesCloudinary()) {
            return Storage::disk('public')->url($image);
        }

        return $this->client()->image($image)->toUrl();
    }

    private function usesCloudinary(): bool
    {
        return config('filesystems.default') === 'cloudinary';
    }

    private function client(): Cloudinary
    {
        $url = config('services.cloudinary.url');

        if (! $url) {
            throw new \RuntimeException('CLOUDINARY_URL is required when FILESYSTEM_DISK=cloudinary.');
        }

        return new Cloudinary($url);
    }
}
