<?php

declare(strict_types=1);

namespace LemonSqueezy\Logger;

use Psr\Log\AbstractLogger;

class FileLogger extends AbstractLogger
{
    private string $logFile;

    public function __construct(string $logFile)
    {
        $this->logFile = $logFile;
    }

    public function log($level, $message, array $context = []): void
    {
        $message = sprintf('[%s] %s: %s%s', date('Y-m-d H:i:s'), strtoupper($level), $message, PHP_EOL);

        file_put_contents($this->logFile, $message, FILE_APPEND);
    }
}
