<?php

namespace LemonSqueezy\Batch\Operations;

/**
 * Represents an update operation in a batch
 *
 * Example:
 * ```php
 * $op = new BatchUpdateOperation('products', 'prod-123', ['name' => 'Updated Name']);
 * ```
 */
class BatchUpdateOperation extends BatchOperation
{
    /**
     * Create a new update operation
     *
     * @param string $resource The resource name (e.g., 'products', 'customers')
     * @param string $id The resource ID to update
     * @param array $data The data to update
     */
    public function __construct(
        string $resource,
        private string $id,
        private array $data
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
     * Get the data to update
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
        return 'update';
    }

    /**
     * Get the HTTP method
     */
    public function getMethod(): string
    {
        return 'PATCH';
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
    public function getPayload(): array
    {
        return ['data' => array_merge(['id' => $this->id], $this->data)];
    }
}
