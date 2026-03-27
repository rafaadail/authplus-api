<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;

class LoggerService
{
    public function __construct(){}

    public function logInfo(string $event, string $message, array $context = [])
    {
        Log::info($message, array_merge(['event' => $event], $context));
    }

    public function logError(string $event, string $message, array $context = [])
    {
        Log::error($message, array_merge(['event' => $event], $context));
    }

    public function logCritical(string $event, string $message, array $context = [])
    {
        Log::critical($message, array_merge(['event' => $event], $context));
    }


    public function logWarning(string $event, string $message, array $context = [])
    {
        Log::warning($message, array_merge(['event' => $event], $context));
    }

    public function logDebug(string $event, string $message, array $context = [])
    {
        Log::debug($message, array_merge(['event' => $event], $context));
    }
}