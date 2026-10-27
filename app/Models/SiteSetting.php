<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class SiteSetting extends Model
{
    use HasFactory;

    protected $fillable = [
        'key',
        'group',
        'value',
    ];

    public static function allAsMap(): array
    {
        return Cache::remember('site_settings_map', 300, function () {
            try {
                return static::query()->pluck('value', 'key')->toArray();
            } catch (\Throwable $e) {
                return [];
            }
        });
    }

    public static function getVal(string $key, ?string $default = null): ?string
    {
        $map = static::allAsMap();
        return array_key_exists($key, $map) && $map[$key] !== null ? $map[$key] : $default;
    }

    public static function setVal(string $key, ?string $value, string $group = 'general'): void
    {
        static::updateOrCreate(
            ['key' => $key],
            ['value' => $value, 'group' => $group]
        );
        Cache::forget('site_settings_map');
    }
}
