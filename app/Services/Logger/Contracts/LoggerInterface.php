<?php
namespace App\Services\Logger\Contracts\LoggerInterface;

use App\Services\Logger\Data\LogData;

interface LoggerInterface
{
    public function log(LogData $log): void;
}
