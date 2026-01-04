<?php

namespace LemonSqueezy\Model;

/**
 * Base model class for all API response entities
 */
abstract class AbstractModel
{
    protected string $id = '';
    protected array $attributes = [];
    protected array $relationships = [];
    protected array $meta = [];

    /**
     * Create model from API response data
     */
    public function __construct(array $data = [])
    {
        if (isset($data['id'])) {
            $this->id = (string)$data['id'];
        }

        if (isset($data['attributes'])) {
            $this->attributes = $data['attributes'];
        }

        if (isset($data['relationships'])) {
            $this->relationships = $data['relationships'];
        }

        if (isset($data['meta'])) {
            $this->meta = $data['meta'];
        }
    }

    /**
     * Get the model ID
     */
    public function getId(): string
    {
        return $this->id;
    }

    /**
     * Get a specific attribute
     */
    public function getAttribute(string $key, mixed $default = null): mixed
    {
        return $this->attributes[$key] ?? $default;
    }

    /**
     * Get all attributes
     */
    public function getAttributes(): array
    {
        return $this->attributes;
    }

    /**
     * Get a specific relationship
     */
    public function getRelationship(string $key): ?AbstractModel
    {
        return $this->relationships[$key] ?? null;
    }

    /**
     * Get all relationships
     */
    public function getRelationships(): array
    {
        return $this->relationships;
    }

    /**
     * Get metadata
     */
    public function getMeta(): array
    {
        return $this->meta;
    }

    /**
     * Get model type name
     */
    abstract public function getType(): string;
}
