<?php
namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class TelegramNotifier
{
    public static function send(string $text): bool
    {
        $botToken = config('services.telegram.bot_token');
        $chatId   = config('services.telegram.chat_id');

        if (blank($botToken) || blank($chatId)) {
            return false;
        }

        $response = Http::post("https://api.telegram.org/bot{$botToken}/sendMessage", [
            'chat_id'    => $chatId,
            'text'       => $text,
            'parse_mode' => 'Markdown',
        ]);

        if (! $response->successful()) {
            Log::warning('Telegram send failed', ['response' => $response->body()]);
        }

        return $response->successful();
    }
}
