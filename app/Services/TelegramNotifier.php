<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Throwable;

class TelegramNotifier
{
    public static function send(string $text): bool
    {
        $botToken = (string) env('TG_BOT_TOKEN', '');
        $chatId = (string) env('TG_CHAT_ID', '');

        if ($botToken === '' || $chatId === '') {
            Log::warning('Telegram credentials missing (TG_BOT_TOKEN / TG_CHAT_ID).');

            return false;
        }

        try {
            $http = Http::timeout(10);
            if (! app()->environment('production')) {
                $http = $http->withoutVerifying();
            }

            $response = $http
                ->asForm()
                ->post("https://api.telegram.org/bot{$botToken}/sendMessage", [
                    'chat_id' => $chatId,
                    'text' => $text,
                    'disable_web_page_preview' => true,
                ]);

            $body = $response->json();
            if (! $response->ok() || ! data_get($body, 'ok')) {
                throw new \RuntimeException((string) data_get($body, 'description', 'Telegram API error'));
            }

            return true;
        } catch (Throwable $e) {
            Log::error('Telegram send failed: '.$e->getMessage());

            return false;
        }
    }

    /**
     * @param  array<string, mixed>  $data
     */
    public static function sendContactRequest(array $data): bool
    {
        $lines = [
            '📩 Нове звернення з сайту',
            '👤 Імʼя: '.($data['name'] ?? '—'),
            '📞 Телефон: '.($data['phone'] ?? '—'),
            '💬 Повідомлення: '.(trim((string) ($data['message'] ?? '')) !== '' ? $data['message'] : 'не вказано'),
        ];

        return self::send(implode("\n", $lines));
    }

    /**
     * @param  array<string, mixed>  $order
     * @param  array<int, array<string, mixed>>  $items
     */
    public static function sendNewOrder(array $order, array $items, float $total): bool
    {
        $lines = [
            '🛒 Нове замовлення #'.($order['id'] ?? ''),
            '👤 '.trim(($order['lastname'] ?? '').' '.($order['name'] ?? '')),
            '📞 '.($order['phone'] ?? '—'),
            '✉️ '.($order['email'] ?? '—'),
            '🚚 '.($order['delivery_service'] ?? '—'),
            '💳 '.($order['payment'] ?? '—'),
            '💰 '.number_format($total, 0, '.', ' ').' ₴',
            '',
            'Товари:',
        ];

        foreach ($items as $item) {
            $lines[] = sprintf(
                '• %s × %s — %s ₴',
                $item['name'] ?? 'Товар',
                $item['quantity'] ?? 1,
                number_format((float) ($item['price'] ?? 0), 0, '.', ' ')
            );
        }

        return self::send(implode("\n", $lines));
    }
}
