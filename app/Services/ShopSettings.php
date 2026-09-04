<?php

namespace App\Services;

use App\Models\ShopSetting;
use Illuminate\Support\Facades\Cache;
use Throwable;

class ShopSettings
{
    public const CACHE_KEY = 'shop.settings.v1';

    /**
     * Defaults used when DB is empty / unavailable.
     *
     * @var array<string, mixed>
     */
    public const DEFAULTS = [
        'min_order_total' => 1000,
        'min_order_enabled' => true,
        'free_delivery_from' => 10000,
        'free_delivery_enabled' => true,
        'announcement_text' => 'Безкоштовна доставка від {free_delivery_from}₴',
        'store_name' => 'DOMEXO',
        'currency_symbol' => '₴',
        'currency_label' => 'грн',
        'contact_phone' => '',
        'contact_email' => '',
        'contact_address' => '',
        'checkout_notice' => '',
    ];

    /**
     * @return array<string, mixed>
     */
    public static function all(): array
    {
        try {
            $stored = Cache::remember(self::CACHE_KEY, 3600, function () {
                return ShopSetting::query()
                    ->select(['key', 'value'])
                    ->orderBy('key')
                    ->get()
                    ->pluck('value', 'key')
                    ->all();
            });
        } catch (Throwable) {
            $stored = [];
        }

        $merged = self::DEFAULTS;
        foreach ($stored as $key => $value) {
            if (! array_key_exists($key, self::DEFAULTS)) {
                $merged[$key] = $value;
                continue;
            }
            $merged[$key] = self::castValue($key, $value);
        }

        return $merged;
    }

    /**
     * Public subset for storefront JS / Blade.
     *
     * @return array<string, mixed>
     */
    public static function public(): array
    {
        $all = self::all();

        return [
            'min_order_total' => (int) $all['min_order_total'],
            'min_order_enabled' => (bool) $all['min_order_enabled'],
            'free_delivery_from' => (int) $all['free_delivery_from'],
            'free_delivery_enabled' => (bool) $all['free_delivery_enabled'],
            'announcement_text' => self::announcementText(),
            'store_name' => (string) $all['store_name'],
            'currency_symbol' => (string) $all['currency_symbol'],
            'currency_label' => (string) $all['currency_label'],
            'contact_phone' => (string) $all['contact_phone'],
            'contact_email' => (string) $all['contact_email'],
            'contact_address' => (string) $all['contact_address'],
            'checkout_notice' => (string) $all['checkout_notice'],
        ];
    }

    public static function get(string $key, mixed $default = null): mixed
    {
        $all = self::all();

        if (array_key_exists($key, $all)) {
            return $all[$key];
        }

        return $default ?? (self::DEFAULTS[$key] ?? null);
    }

    public static function getInt(string $key, int $default = 0): int
    {
        return (int) self::get($key, $default);
    }

    public static function getBool(string $key, bool $default = false): bool
    {
        return (bool) self::get($key, $default);
    }

    public static function minOrderTotal(): int
    {
        if (! self::getBool('min_order_enabled', true)) {
            return 0;
        }

        return max(0, self::getInt('min_order_total', 1000));
    }

    public static function freeDeliveryFrom(): int
    {
        if (! self::getBool('free_delivery_enabled', true)) {
            return 0;
        }

        return max(0, self::getInt('free_delivery_from', 10000));
    }

    public static function announcementText(): string
    {
        $all = self::all();
        $template = trim((string) ($all['announcement_text'] ?? ''));
        if ($template === '') {
            $template = (string) self::DEFAULTS['announcement_text'];
        }

        return strtr($template, [
            '{free_delivery_from}' => number_format((int) $all['free_delivery_from'], 0, '.', ' '),
            '{min_order_total}' => number_format((int) $all['min_order_total'], 0, '.', ' '),
            '{store_name}' => (string) $all['store_name'],
            '{currency_symbol}' => (string) $all['currency_symbol'],
            '{currency_label}' => (string) $all['currency_label'],
        ]);
    }

    /**
     * @param  array<string, mixed>  $values
     */
    public static function setMany(array $values): void
    {
        foreach ($values as $key => $value) {
            if (! is_string($key) || $key === '') {
                continue;
            }

            $stored = self::serializeValue($key, $value);

            ShopSetting::query()->updateOrCreate(
                ['key' => $key],
                ['value' => $stored],
            );
        }

        self::forgetCache();
    }

    public static function forgetCache(): void
    {
        Cache::forget(self::CACHE_KEY);
    }

    private static function castValue(string $key, mixed $value): mixed
    {
        $default = self::DEFAULTS[$key] ?? null;

        if (is_bool($default)) {
            return in_array($value, [true, 1, '1', 'true', 'on', 'yes'], true);
        }

        if (is_int($default)) {
            return (int) $value;
        }

        return $value === null ? '' : (string) $value;
    }

    private static function serializeValue(string $key, mixed $value): string
    {
        $default = self::DEFAULTS[$key] ?? null;

        if (is_bool($default) || is_bool($value)) {
            return $value || $value === 1 || $value === '1' || $value === 'true' ? '1' : '0';
        }

        if (is_int($default) || is_numeric($value)) {
            return (string) ((int) $value);
        }

        return (string) ($value ?? '');
    }
}
