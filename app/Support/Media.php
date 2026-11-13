<?php

namespace App\Support;

class Media
{
    /** @var array<string, array{0:int,1:int}> */
    protected static array $dimensions = [];

    public static function url(?string $path): string
    {
        if (! $path) {
            return '';
        }

        if (str_starts_with($path, 'http://') || str_starts_with($path, 'https://')) {
            return $path;
        }

        return asset(ltrim($path, '/'));
    }

    /**
     * Get native image dimensions without another database column.
     *
     * @return array{0:int,1:int}
     */
    public static function dimensions(?string $path, int $fallbackWidth = 1000, int $fallbackHeight = 1250): array
    {
        if (! $path) {
            return [$fallbackWidth, $fallbackHeight];
        }

        $cacheKey = (string) $path;

        if (isset(self::$dimensions[$cacheKey])) {
            return self::$dimensions[$cacheKey];
        }

        $absolute = public_path(ltrim($path, '/'));

        if (is_file($absolute)) {
            $size = @getimagesize($absolute);

            if ($size && $size[0] > 0 && $size[1] > 0) {
                return self::$dimensions[$cacheKey] = [(int) $size[0], (int) $size[1]];
            }
        }

        return self::$dimensions[$cacheKey] = [$fallbackWidth, $fallbackHeight];
    }

    public static function isUploaded(?string $path): bool
    {
        return $path && str_starts_with(ltrim($path, '/'), 'uploads/');
    }
}
