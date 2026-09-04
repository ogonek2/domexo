<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Crypt;

class Orders extends Model
{
    use HasFactory;

    public const STATUS_NEW = 'new';
    public const STATUS_PROCESSING = 'processing';
    public const STATUS_ASSEMBLED = 'assembled';
    public const STATUS_SHIPPED = 'shipped';
    public const STATUS_POSTED = 'posted';
    public const STATUS_DELIVERED = 'delivered';
    public const STATUS_CANCELLED = 'cancelled';

    public const STATUSES = [
        self::STATUS_NEW => 'Новый',
        self::STATUS_PROCESSING => 'В обработке',
        self::STATUS_ASSEMBLED => 'Собран',
        self::STATUS_SHIPPED => 'Отправлено',
        self::STATUS_POSTED => 'Создан на почте',
        self::STATUS_DELIVERED => 'Доставлен',
        self::STATUS_CANCELLED => 'Отменён',
    ];

    public const STATUSES_WITH_TRACKING = [
        self::STATUS_SHIPPED,
        self::STATUS_POSTED,
    ];

    protected $fillable = [
        'delivery_service',
        'city',
        'warehouse',
        'manual_address',
        'name',
        'lastname',
        'fathername',
        'phone',
        'email',
        'comment',
        'cart',
        'total_price',
        'payment',
        'status',
        'tracking_number',
    ];

    protected $encryptable = [
        'delivery_service',
        'city',
        'warehouse',
        'manual_address',
        'name',
        'lastname',
        'fathername',
        'phone',
        'email',
        'comment',
        'cart',
        'total_price',
        'payment',
    ];

    public function setAttribute($key, $value)
    {
        if (in_array($key, $this->encryptable, true) && $value !== null) {
            $value = Crypt::encryptString((string) $value);
        }

        return parent::setAttribute($key, $value);
    }

    public function getAttribute($key)
    {
        $value = parent::getAttribute($key);

        if (in_array($key, $this->encryptable, true) && ! is_null($value)) {
            try {
                $value = Crypt::decryptString($value);
            } catch (\Exception $e) {
                return $value;
            }
        }

        return $value;
    }

    public function getFormattedTotalPriceAttribute(): string
    {
        $totalPrice = $this->total_price;
        if (is_numeric($totalPrice)) {
            return number_format((float) $totalPrice, 2, ',', ' ').' ₴';
        }

        return '0,00 ₴';
    }

    public function getNumericTotalPriceAttribute(): float
    {
        $totalPrice = $this->total_price;

        return is_numeric($totalPrice) ? (float) $totalPrice : 0;
    }

    public function getFullNameAttribute(): string
    {
        return trim(($this->name ?? '').' '.($this->lastname ?? ''));
    }

    public function getFormattedCreatedAtAttribute(): string
    {
        return $this->created_at ? $this->created_at->format('d.m.Y H:i') : 'Не указано';
    }

    public function getStatusLabelAttribute(): string
    {
        return self::STATUSES[$this->status ?? self::STATUS_NEW] ?? (string) $this->status;
    }

    public function requiresTrackingNotification(): bool
    {
        return in_array($this->status, self::STATUSES_WITH_TRACKING, true);
    }

    public function getCartItemsAttribute(): array
    {
        $cart = $this->cart;

        if (is_string($cart)) {
            $decoded = json_decode($cart, true);
            if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
                return $decoded;
            }

            try {
                $decrypted = Crypt::decryptString($cart);
                $decoded = json_decode($decrypted, true);
                if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
                    return $decoded;
                }

                if (is_string($decrypted)) {
                    $doubleDecrypted = Crypt::decryptString($decrypted);
                    $decoded = json_decode($doubleDecrypted, true);
                    if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
                        return $decoded;
                    }
                }
            } catch (\Exception $e) {
                // ignore
            }
        }

        return is_array($cart) ? $cart : [];
    }
}
