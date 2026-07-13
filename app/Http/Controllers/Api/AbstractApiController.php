<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\Logger\Channels\LoggerService;
use App\Services\Logger\Contracts\LoggerInterface;
use App\Services\Logger\Data\LogData;
use Illuminate\Http\Request;

abstract class AbstractApiController extends Controller
{
    protected LoggerInterface $logger;

    public function __construct()
    {
        $this->logger = new LoggerService();
    }

    protected function log(Request $request, string $entity, string $action, $exception): void {
        $this->logger->log(
            new LogData(
                $request->user() !== null ? $request->user() : null,
                $entity,
                $action,
                $exception,
                $request
            )
        );
    }

}
