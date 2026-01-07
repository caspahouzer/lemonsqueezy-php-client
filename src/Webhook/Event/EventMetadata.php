<?php

namespace LemonSqueezy\Webhook\Event;

/**
 * Metadata about a webhook event
 *
 * Stores information about event processing including timestamp,
 * signature verification status, and execution details.
 */
class EventMetadata
{
    /**
     * Create a new EventMetadata instance
     *
     * @param \DateTimeInterface $receivedAt Timestamp when webhook was received
     * @param bool $isVerified Whether the webhook signature was verified
     * @param ?string $algorithm The hash algorithm used for verification
     * @param ?array $executionInfo Optional execution metadata (handlers count, timings, etc.)
     */
    public function __construct(
        private \DateTimeInterface $receivedAt,
        private bool $isVerified = false,
        private ?string $algorithm = 'sha256',
        private ?array $executionInfo = null,
    ) {
    }

    /**
     * Get the timestamp when this webhook was received
     *
     * @return \DateTimeInterface
     */
    public function getReceivedAt(): \DateTimeInterface
    {
        return $this->receivedAt;
    }

    /**
     * Whether the webhook signature was verified
     *
     * @return bool
     */
    public function isVerified(): bool
    {
        return $this->isVerified;
    }

    /**
     * Mark the webhook as verified
     *
     * @return self
     */
    public function markVerified(): self
    {
        $this->isVerified = true;
        return $this;
    }

    /**
     * Get the hash algorithm used for signature verification
     *
     * @return ?string
     */
    public function getAlgorithm(): ?string
    {
        return $this->algorithm;
    }

    /**
     * Get optional execution information
     *
     * @return ?array
     */
    public function getExecutionInfo(): ?array
    {
        return $this->executionInfo;
    }

    /**
     * Set execution information (handler count, timings, etc.)
     *
     * @param array $info
     * @return self
     */
    public function setExecutionInfo(array $info): self
    {
        $this->executionInfo = $info;
        return $this;
    }

    /**
     * Convert metadata to array representation
     *
     * @return array
     */
    public function toArray(): array
    {
        return [
            'received_at' => $this->receivedAt->format(\DateTimeInterface::ATOM),
            'is_verified' => $this->isVerified,
            'algorithm' => $this->algorithm,
            'execution_info' => $this->executionInfo,
        ];
    }
}
