<?php
namespace App\Services\Logger\Channels;

use App\Services\Logger\Contracts\LoggerInterface;
use App\Services\Logger\Data\LogData;

class LoggerService implements LoggerInterface {

    /**
     * @var array<LoggerInterface>
     */
    private array $channels = [];

    public function __construct() {
        $this->initLogger();
    }

    public function log(LogData $log): void
    {

        if(!empty($this->channels)) {
            foreach ($this->channels as $channel) {
                app($channel)->log($log);
            }
        }
    }

    private function initLogger(): void {
        $this->channels = config('logger.channels', []);
    }
}
