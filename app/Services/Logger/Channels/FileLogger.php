<?php
namespace App\Services\Logger\Channels;

use App\Services\Logger\Contracts\LoggerInterface;
use App\Services\Logger\Data\LogData;
use Illuminate\Support\Facades\Log;

class FileLogger extends AbstractChannelLogger implements LoggerInterface {

    public function log(LogData $log): void
    {
        Log::channel('api_exceptions')->error(
            $log->exception->getMessage(),
            $this->prepareData($log)
        );
    }
}
