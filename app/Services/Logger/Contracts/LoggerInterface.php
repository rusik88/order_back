<?php
namespace App\Services\Logger\Contracts;

use App\Services\Logger\Data\LogData;

interface LoggerInterface
{
    public function log(LogData $log): void;
}
