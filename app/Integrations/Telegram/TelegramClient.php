<?php
namespace App\Integrations\Telegram;

use Illuminate\Support\Facades\Http;

class TelegramClient {

    public function sendMessage(string $message): void
    {
        $this->send($message);
    }

    private function send(string $message) {
        Http::post(
            sprintf(
                'https://api.telegram.org/bot%s/sendMessage',
                config('services.telegram.token')
            ),
            [
                'chat_id' => config('services.telegram.chat_id'),
                'text' => $message,
                'parse_mode' => 'HTML',
            ]
        );
    }
}
