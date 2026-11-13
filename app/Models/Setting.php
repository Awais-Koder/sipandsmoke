<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    protected $fillable = ['key', 'value'];

    /** @var array<string, string|null>|null */
    protected static ?array $cache = null;

    /** @return array<string, string|null> */
    public static function values(): array
    {
        if (static::$cache === null) {
            static::$cache = static::query()->pluck('value', 'key')->all();
        }

        return static::$cache;
    }

    public static function get(string $key, mixed $fallback = null): mixed
    {
        $values = static::values();

        return array_key_exists($key, $values) && $values[$key] !== null
            ? $values[$key]
            : $fallback;
    }

    public static function put(string $key, mixed $value): void
    {
        static::query()->updateOrCreate(['key' => $key], ['value' => $value]);
        static::$cache = null;
    }

    public static function forgetCache(): void
    {
        static::$cache = null;
    }
}
