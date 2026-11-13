<?php

use App\Support\Media;
use App\Support\SiteContent;

if (! function_exists('site')) {
    function site(string $key, mixed $fallback = null): mixed
    {
        return SiteContent::get($key, $fallback);
    }
}

if (! function_exists('media_url')) {
    function media_url(?string $path): string
    {
        return Media::url($path);
    }
}

if (! function_exists('media_dimensions')) {
    /** @return array{0:int,1:int} */
    function media_dimensions(?string $path, int $width = 1000, int $height = 1250): array
    {
        return Media::dimensions($path, $width, $height);
    }
}

if (! function_exists('hex_rgb')) {
    function hex_rgb(?string $hex, string $fallback = '18 35 58'): string
    {
        $hex = ltrim((string) $hex, '#');

        if (! preg_match('/^[0-9a-fA-F]{6}$/', $hex)) {
            return $fallback;
        }

        return implode(' ', [
            hexdec(substr($hex, 0, 2)),
            hexdec(substr($hex, 2, 2)),
            hexdec(substr($hex, 4, 2)),
        ]);
    }
}
