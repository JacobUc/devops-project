<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;

class LoggerService
{
    public static function info(string $message, array $context = [], string $channel = 'elastic'): void
    {
        Log::channel($channel)->info($message, $context);
    }

    public static function error(string $message, array $context = [], string $channel = 'elastic'): void
    {
        Log::channel($channel)->error($message, $context);
    }

    public static function debug(string $message, array $context = [], string $channel = 'elastic'): void
    {
        Log::channel($channel)->debug($message, $context);
    }
}
