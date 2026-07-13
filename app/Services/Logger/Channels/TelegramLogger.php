<?php
namespace App\Services\Logger\Channels;

use App\Integrations\Telegram\TelegramClient;
use App\Integrations\Telegram\TelegramMessageFormatter;
use App\Services\Logger\Contracts\LoggerInterface;
use App\Services\Logger\Data\LogData;

class TelegramLogger extends AbstractChannelLogger implements LoggerInterface {

    public function log(LogData $log): void
    {
        $telegramClient = new TelegramClient();
        $telegramClient->sendMessage(TelegramMessageFormatter::formatException($log));
    }
}
