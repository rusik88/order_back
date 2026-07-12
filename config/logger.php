<?php

return [
    /*
   |--------------------------------------------------------------------------
   | Set channels to logger
   |--------------------------------------------------------------------------
   |
   | Here you can set in array channels which handle log
   */

    'channels' => [
        'file'      => App\Services\Logger\Channels\FileLogger::class,
        'telegram'  => App\Services\Logger\Channels\TelegramLogger::class
    ],
];

