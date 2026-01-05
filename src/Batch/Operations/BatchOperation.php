<?php

namespace LemonSqueezy\Batch\Operations;

/**
 * Abstract base class for batch operations
 *
 * Represents a single operation to be executed within a batch.
 */
abstract class BatchOperation
{
    /**
     * Create a new batch operation
     *
     * @param string $resource The resource name (e.g., 'products', 'customers')
     */
    public function __construct(
        protected string $resource
    ) {}

    /**
     * Get the resource name
     */
    public function getResource(): string
    {
        return $this->resource;
    }

    /**
     * Get the operation type (create, update, delete)
     */
    abstract public function getOperationType(): string;

    /**
     * Get the HTTP method for this operation
     */
    abstract public function getMethod(): string;

    /**
     * Get the endpoint for this operation
     */
    abstract public function getEndpoint(): string;

    /**
     * Get the request payload for this operation
     */
    abstract public function getPayload(): ?array;
}
