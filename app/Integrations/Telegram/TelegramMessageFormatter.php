<?php
namespace App\Integrations\Telegram;

use App\Services\Logger\Data\LogData;

class TelegramMessageFormatter
{
    static function formatException(LogData $log): string
    {
        return sprintf(
            "<b>API Exception</b>\n\n".
            "<b>User</b>\n".
            "• ID: <code>%s</code>\n".
            "• Name: <code>%s</code>\n".
            "• Email: <code>%s</code>\n\n".
            "<b>Entity:</b> <code>%s</code>\n".
            "<b>Action:</b> <code>%s</code>\n\n".
            "<b>Request</b>\n".
            "• Method: <code>%s</code>\n".
            "• URL:\n<code>%s</code>\n".
            "• IP: <code>%s</code>\n\n".
            "<b>Exception</b>\n".
            "• Class: <code>%s</code>\n".
            "• Message:\n<code>%s</code>\n".
            "• File: <code>%s:%d</code>",
            $log->user?->id,
            $log->user?->name,
            $log->user?->email,
            $log->entity,
            $log->action,
            $log->request->method(),
            $log->request->fullUrl(),
            $log->request->ip(),
            class_basename($log->exception),
            htmlspecialchars($log->exception->getMessage()),
            basename($log->exception->getFile()),
            $log->exception->getLine(),
        );
    }
}
