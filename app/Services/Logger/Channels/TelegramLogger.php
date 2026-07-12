<?php
namespace App\Services\Logger\Channels;

use App\Integrations\Telegram\TelegramClient;
use App\Services\Logger\Contracts\LoggerInterface\LoggerInterface;
use App\Services\Logger\Data\LogData;

class TelegramLogger extends AbstractChannelLogger implements LoggerInterface {

    public function log(LogData $log): void
    {
        $telegramClient = new TelegramClient();
        $telegramClient->sendException($this->prepareData($log));
    }
}
