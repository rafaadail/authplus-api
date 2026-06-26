<?php

namespace Tests\Unit\Services;

use PHPUnit\Framework\TestCase;
use Illuminate\Support\Facades\Log;
use App\Services\LoggerService;

class LoggerServiceTest extends TestCase
{
    private LoggerService $logger;

    protected function setUp(): void
    {
        parent::setUp();

        $this->logger = new LoggerService();
    }

    #[\PHPUnit\Framework\Attributes\DataProvider('logLevels')]
    public function test_logs_are_written(
        string $method,
        string $facadeMethod
    ): void {
        $this->expectNotToPerformAssertions();
        
        Log::shouldReceive($facadeMethod)
            ->once()
            ->with(
                'Test message',
                [
                    'event' => 'test.event',
                    'id' => 1,
                ]
            );

        $this->logger->{$method}(
            'test.event',
            'Test message',
            ['id' => 1]
        );
    }

    public static function logLevels(): array
    {
        return [
            ['logInfo', 'info'],
            ['logError', 'error'],
            ['logWarning', 'warning'],
            ['logCritical', 'critical'],
            ['logDebug', 'debug'],
        ];
    }
}
