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
    ) {}

    public function getField(): string
    {
        // Convert snake_case to camelCase for API compatibility
        $camelCaseField = $this->snakeToCamelCase($this->field);
        return $this->direction === 'desc' ? '-' . $camelCaseField : $camelCaseField;
    }

    /**
     * Convert snake_case to camelCase
     */
    private function snakeToCamelCase(string $string): string
    {
        $words = explode('_', $string);
        $camelCase = array_shift($words);
        foreach ($words as $word) {
            $camelCase .= ucfirst($word);
        }
        return $camelCase;
    }

    public function getDirection(): string
    {
        return $this->direction;
    }
}
