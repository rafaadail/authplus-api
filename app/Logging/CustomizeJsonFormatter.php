<?php

namespace App\Logging;

use Monolog\Formatter\JsonFormatter;

class CustomizeJsonFormatter
{
    /**
     * Aplica o JsonFormatter em todos os handlers do logger.
     */
    public function __invoke($logger)
    {
        foreach ($logger->getHandlers() as $handler) {
            // Uma linha por log, JSON puro
            $handler->setFormatter(new JsonFormatter(JsonFormatter::BATCH_MODE_NEWLINES));
        }
    }
}