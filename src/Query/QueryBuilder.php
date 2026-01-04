<?php

namespace LemonSqueezy\Query;

/**
 * Fluent query builder for filtering, sorting, pagination
 */
class QueryBuilder
{
    private ?int $page = null;
    private ?int $pageSize = null;
    private array $filters = [];
    private array $sorts = [];
    private array $includes = [];

    /**
     * Set the page number
     */
    public function page(int $page): self
    {
        $this->page = max(1, $page);

        return $this;
    }

    /**
     * Check if page is set
     */
    public function hasPage(): bool
    {
        return $this->page !== null;
    }

    /**
     * Get page number
     */
    public function getPage(): ?int
    {
        return $this->page;
    }

    /**
     * Set the page size
     */
    public function pageSize(int $size): self
    {
        $this->pageSize = max(1, $size);

        return $this;
    }

    /**
     * Check if page size is set
     */
    public function hasPageSize(): bool
    {
        return $this->pageSize !== null;
    }

    /**
     * Get page size
     */
    public function getPageSize(): ?int
    {
        return $this->pageSize;
    }

    /**
     * Add a filter
     */
    public function filter(string $key, mixed $value, string $operator = '='): self
    {
        $this->filters[] = new Filter($key, $value, $operator);

        return $this;
    }

    /**
     * Get all filters
     */
    public function getFilters(): array
    {
        return $this->filters;
    }

    /**
     * Add sorting
     */
    public function sort(string $field, string $direction = 'asc'): self
    {
        $this->sorts[] = new Sort($field, $direction);

        return $this;
    }

    /**
     * Get all sorts
     */
    public function getSorts(): array
    {
        return $this->sorts;
    }

    /**
     * Add includes
     */
    public function include(string ...$relationships): self
    {
        foreach ($relationships as $rel) {
            if (!in_array($rel, $this->includes)) {
                $this->includes[] = $rel;
            }
        }

        return $this;
    }

    /**
     * Get includes
     */
    public function getIncludes(): array
    {
        return $this->includes;
    }
}
