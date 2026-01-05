<?php

namespace LemonSqueezy\Query;

/**
 * Filter for query builder
 */
class Filter
{
    public function __construct(
        private string $key,
        private mixed $value,
        private string $operator = '='
    ) {}

    public function getKey(): string
    {
        return $this->key;
    }

    public function getValue(): mixed
    {
        return $this->value;
    }

    public function getOperator(): string
    {
        return $this->operator;
    }
}
