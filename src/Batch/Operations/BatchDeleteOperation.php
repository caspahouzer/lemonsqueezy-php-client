<?php

namespace LemonSqueezy\Batch\Operations;

/**
 * Represents a delete operation in a batch
 *
 * Example:
 * ```php
 * $op = new BatchDeleteOperation('products', 'prod-123');
 * ```
 */
class BatchDeleteOperation extends BatchOperation
{
    /**
     * Create a new delete operation
     *
     * @param string $resource The resource name (e.g., 'products', 'customers')
     * @param string $id The resource ID to delete
     */
    public function __construct(
        string $resource,
        private string $id
    ) {
        parent::__construct($resource);
    }

    /**
     * Get the resource ID
     */
    public function getId(): string
    {
        return $this->id;
    }

    /**
     * Get the operation type
     */
    public function getOperationType(): string
    {
        return 'delete';
    }

    /**
     * Get the HTTP method
     */
    public function getMethod(): string
    {
        return 'DELETE';
    }

    /**
     * Get the endpoint
     */
    public function getEndpoint(): string
    {
        return $this->resource . '/' . urlencode($this->id);
    }

    /**
     * Get the request payload
     */
    public function getPayload(): ?array
    {
        return null;
    }
}
