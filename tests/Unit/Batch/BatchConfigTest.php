<?php

namespace LemonSqueezy\Tests\Unit\Batch;

use LemonSqueezy\Batch\Configuration\BatchConfig;
use PHPUnit\Framework\TestCase;

class BatchConfigTest extends TestCase
{
    public function testMergeWithDefaultsReturnsAllDefaults(): void
    {
        $merged = BatchConfig::mergeWithDefaults([]);

        $this->assertEquals(BatchConfig::DEFAULT_DELAY_MS, $merged['delayMs']);
        $this->assertEquals(BatchConfig::DEFAULT_TIMEOUT, $merged['timeout']);
        $this->assertEquals(BatchConfig::DEFAULT_STOP_ON_ERROR, $merged['stopOnError']);
        $this->assertEquals(BatchConfig::DEFAULT_RETRY_ATTEMPTS, $merged['retryAttempts']);
    }

    public function testMergeWithDefaultsOverridesValues(): void
    {
        $options = [
            'delayMs' => 100,
            'timeout' => 60,
            'stopOnError' => true,
        ];

        $merged = BatchConfig::mergeWithDefaults($options);

        $this->assertEquals(100, $merged['delayMs']);
        $this->assertEquals(60, $merged['timeout']);
        $this->assertTrue($merged['stopOnError']);
        $this->assertEquals(BatchConfig::DEFAULT_RETRY_ATTEMPTS, $merged['retryAttempts']);
    }

    public function testValidateDelayMsRange(): void
    {
        // Valid: exactly 0
        BatchConfig::validate(['delayMs' => 0]);

        // Valid: within range
        BatchConfig::validate(['delayMs' => 200]);

        // Invalid: negative
        $this->expectException(\InvalidArgumentException::class);
        BatchConfig::validate(['delayMs' => -1]);
    }

    public function testValidateDelayMsMax(): void
    {
        // Valid: at max
        BatchConfig::validate(['delayMs' => BatchConfig::MAX_DELAY_MS]);

        // Invalid: over max
        $this->expectException(\InvalidArgumentException::class);
        BatchConfig::validate(['delayMs' => BatchConfig::MAX_DELAY_MS + 1]);
    }

    public function testValidateTimeoutRange(): void
    {
        // Valid: within range
        BatchConfig::validate(['timeout' => 30]);

        // Invalid: zero
        $this->expectException(\InvalidArgumentException::class);
        BatchConfig::validate(['timeout' => 0]);
    }

    public function testValidateTimeoutMax(): void
    {
        // Valid: at max
        BatchConfig::validate(['timeout' => BatchConfig::MAX_TIMEOUT]);

        // Invalid: over max
        $this->expectException(\InvalidArgumentException::class);
        BatchConfig::validate(['timeout' => BatchConfig::MAX_TIMEOUT + 1]);
    }

    public function testValidateRetryAttemptsRange(): void
    {
        // Valid: zero
        BatchConfig::validate(['retryAttempts' => 0]);

        // Valid: within range
        BatchConfig::validate(['retryAttempts' => 3]);

        // Invalid: negative
        $this->expectException(\InvalidArgumentException::class);
        BatchConfig::validate(['retryAttempts' => -1]);
    }

    public function testValidateRetryAttemptsMax(): void
    {
        // Valid: at max
        BatchConfig::validate(['retryAttempts' => BatchConfig::MAX_RETRY_ATTEMPTS]);

        // Invalid: over max
        $this->expectException(\InvalidArgumentException::class);
        BatchConfig::validate(['retryAttempts' => BatchConfig::MAX_RETRY_ATTEMPTS + 1]);
    }

    public function testValidateStopOnErrorType(): void
    {
        // Valid: boolean
        BatchConfig::validate(['stopOnError' => true]);
        BatchConfig::validate(['stopOnError' => false]);

        // Invalid: string
        $this->expectException(\InvalidArgumentException::class);
        BatchConfig::validate(['stopOnError' => 'true']);
    }

    public function testValidateMultipleOptions(): void
    {
        $options = [
            'delayMs' => 200,
            'timeout' => 30,
            'stopOnError' => true,
            'retryAttempts' => 2,
        ];

        // Should not throw
        BatchConfig::validate($options);
        $this->assertTrue(true);
    }

    public function testValidateEmptyOptions(): void
    {
        // Should not throw with empty array
        BatchConfig::validate([]);
        $this->assertTrue(true);
    }

    public function testValidateUnknownOptionsIgnored(): void
    {
        // Unknown options should be ignored
        BatchConfig::validate(['unknownOption' => 'value']);
        $this->assertTrue(true);
    }
}
