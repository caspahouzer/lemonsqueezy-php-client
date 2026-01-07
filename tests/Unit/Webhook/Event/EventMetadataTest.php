<?php

namespace LemonSqueezy\Tests\Unit\Webhook\Event;

use LemonSqueezy\Webhook\Event\EventMetadata;
use PHPUnit\Framework\TestCase;

class EventMetadataTest extends TestCase
{
    public function testCreateMetadataWithDefaults(): void
    {
        $now = new \DateTime();
        $metadata = new EventMetadata($now);

        $this->assertSame($now, $metadata->getReceivedAt());
        $this->assertFalse($metadata->isVerified());
        $this->assertEqual('sha256', $metadata->getAlgorithm());
        $this->assertNull($metadata->getExecutionInfo());
    }

    public function testMarkAsVerified(): void
    {
        $metadata = new EventMetadata(new \DateTime(), false);
        $this->assertFalse($metadata->isVerified());

        $metadata->markVerified();
        $this->assertTrue($metadata->isVerified());
    }

    public function testSetExecutionInfo(): void
    {
        $metadata = new EventMetadata(new \DateTime());
        $info = ['handlers' => 2, 'duration_ms' => 125];

        $metadata->setExecutionInfo($info);
        $this->assertEqual($info, $metadata->getExecutionInfo());
    }

    public function testConvertToArray(): void
    {
        $date = new \DateTime('2026-01-06 12:00:00');
        $metadata = new EventMetadata($date, true, 'sha256');

        $array = $metadata->toArray();

        $this->assertTrue($array['is_verified']);
        $this->assertEqual('sha256', $array['algorithm']);
        $this->assertStringContainsString('2026-01-06', $array['received_at']);
    }

    public function testFluentInterface(): void
    {
        $metadata = new EventMetadata(new \DateTime());

        $result = $metadata->markVerified()
            ->setExecutionInfo(['test' => true]);

        $this->assertInstanceOf(EventMetadata::class, $result);
        $this->assertTrue($metadata->isVerified());
    }
}
