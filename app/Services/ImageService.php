<?php

namespace App\Services;

use App\Support\Media;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use RuntimeException;

class ImageService
{
    /**
     * Convert any supported upload to a compressed WebP and return its public
     * relative path. The original upload is never stored.
     */
    public function toWebp(UploadedFile $file, string $folder, int $maxWidth = 1800, int $quality = 84): string
    {
        $source = @imagecreatefromstring((string) file_get_contents($file->getRealPath()));

        if (! $source) {
            throw new RuntimeException('That image could not be read. Try a JPG, PNG or WebP file.');
        }

        $source = $this->orient($source, $file->getRealPath());
        $width = imagesx($source);
        $height = imagesy($source);

        if ($width < 1 || $height < 1) {
            imagedestroy($source);
            throw new RuntimeException('That image has invalid dimensions.');
        }

        $targetWidth = min($width, $maxWidth);
        $targetHeight = (int) round($height * ($targetWidth / $width));

        $canvas = imagecreatetruecolor($targetWidth, $targetHeight);
        imagealphablending($canvas, false);
        imagesavealpha($canvas, true);
        $transparent = imagecolorallocatealpha($canvas, 255, 255, 255, 127);
        imagefilledrectangle($canvas, 0, 0, $targetWidth, $targetHeight, $transparent);
        imagecopyresampled($canvas, $source, 0, 0, 0, 0, $targetWidth, $targetHeight, $width, $height);

        $directory = public_path('uploads/'.trim($folder, '/'));

        if (! File::isDirectory($directory)) {
            File::makeDirectory($directory, 0755, true);
        }

        $base = Str::slug(pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME)) ?: 'image';
        $name = $base.'-'.Str::lower(Str::random(8)).'.webp';
        $absolute = $directory.'/'.$name;

        $saved = imagewebp($canvas, $absolute, $quality);

        imagedestroy($source);
        imagedestroy($canvas);

        if (! $saved || ! is_file($absolute)) {
            throw new RuntimeException('The WebP file could not be written.');
        }

        return 'uploads/'.trim($folder, '/').'/'.$name;
    }

    public function delete(?string $path): void
    {
        if (! Media::isUploaded($path)) {
            return;
        }

        $absolute = public_path(ltrim($path, '/'));

        if (is_file($absolute)) {
            @unlink($absolute);
        }
    }

    /**
     * Apply the EXIF orientation so phone photos are not rotated.
     *
     * @param  \GdImage  $image
     * @return \GdImage
     */
    private function orient($image, string $path)
    {
        if (! function_exists('exif_read_data')) {
            return $image;
        }

        $exif = @exif_read_data($path);

        if (! $exif || empty($exif['Orientation'])) {
            return $image;
        }

        $rotated = match ((int) $exif['Orientation']) {
            2 => imageflip($image, IMG_FLIP_HORIZONTAL) ? $image : $image,
            3 => imagerotate($image, 180, 0),
            4 => imageflip($image, IMG_FLIP_VERTICAL) ? $image : $image,
            5 => imagerotate($image, -90, 0),
            6 => imagerotate($image, -90, 0),
            7 => imagerotate($image, 90, 0),
            8 => imagerotate($image, 90, 0),
            default => $image,
        };

        if ($rotated !== $image) {
            imagedestroy($image);
        }

        return $rotated ?: $image;
    }
}
