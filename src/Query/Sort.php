<?php

namespace LemonSqueezy\Query;

/**
 * Sort for query builder
 */
class Sort
{
    public function __construct(
        private string $field,
        private string $direction = 'asc'
    ) {
    }

    public function getField(): string
    {
        return $this->direction === 'desc' ? '-' . $this->field : $this->field;
    }

    public function getDirection(): string
    {
        return $this->direction;
    }
}
