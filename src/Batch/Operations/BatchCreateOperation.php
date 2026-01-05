<?php

namespace LemonSqueezy\Batch\Operations;

/**
 * Represents a create operation in a batch
 *
 * Example:
 * ```php
 * $op = new BatchCreateOperation('products', ['name' => 'New Product']);
 * ```
 */
class BatchCreateOperation extends BatchOperation
{
    /**
     * Create a new create operation
     *
     * @param string $resource The resource name (e.g., 'products', 'customers')
     * @param array $data The data to create
     */
    public function __construct(
        string $resource,
        private array $data
    ) {
        parent::__construct($resource);
    }

    /**
     * Get the data to create
     */
    public function getData(): array
    {
        return $this->data;
    }

    /**
     * Get the operation type
     */
    public function getOperationType(): string
    {
        return 'create';
    }

    /**
     * Get the HTTP method
     */
    public function getMethod(): string
    {
        return 'POST';
    }

    /**
     * Get the endpoint
     */
    public function getEndpoint(): string
    {
        return $this->resource;
    }

    /**
     * Get the request payload
     */
    public function getPayload(): array
    {
        return ['data' => $this->data];
    }
}
