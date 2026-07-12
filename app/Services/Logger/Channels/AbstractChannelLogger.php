<?php
namespace App\Services\Logger\Channels;

use App\Services\Logger\Data\LogData;

abstract class AbstractChannelLogger {
    protected function prepareData(LogData $log) {
        return [
            'datetime' => now()->toDateTimeString(),

            'user' => [
                'id' => $log->user?->id,
                'name' => $log->user?->name,
                'email' => $log->user?->email,
            ],

            'entity' => $log->entity,
            'action' => $log->action,

            'request' => [
                'url' => $log->request->fullUrl(),
                'method' => $log->request->method(),
                'ip' => $log->request->ip(),
                'body' => $log->request->except([
                    'password',
                    'password_confirmation',
                    'token',
                ]),
            ],

            'exception' => [
                'class' => get_class($log->exception),
                'message' => $log->exception->getMessage(),
                'code' => $log->exception->getCode(),
                'file' => $log->exception->getFile(),
                'line' => $log->exception->getLine(),
                'trace' => $log->exception->getTraceAsString(),
            ],
        ];
    }
}
