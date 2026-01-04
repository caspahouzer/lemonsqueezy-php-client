<?php

namespace LemonSqueezy\Model;

/**
 * Collection wrapper for list responses
 */
class Collection implements \IteratorAggregate, \Countable
{
    /**
     * @param array<AbstractModel> $items The items in the collection
     * @param ?array $paginationData Pagination metadata
     */
    public function __construct(
        private array $items = [],
        private ?array $paginationData = null,
    ) {
    }

    /**
     * Get all items
     *
     * @return array<AbstractModel>
     */
    public function items(): array
    {
        return $this->items;
    }

    /**
     * Count the items
     */
    public function count(): int
    {
        return count($this->items);
    }

    /**
     * Get iterator for foreach
     */
    public function getIterator(): \Iterator
    {
        return new \ArrayIterator($this->items);
    }

    /**
     * Get pagination data
     */
    public function getPaginationData(): ?array
    {
        return $this->paginationData;
    }

    /**
     * Check if there's pagination data
     */
    public function hasPagination(): bool
    {
        return $this->paginationData !== null;
    }

    /**
     * Get current page number
     */
    public function getCurrentPage(): int
    {
        return $this->paginationData['page'] ?? 1;
    }

    /**
     * Get items per page
     */
    public function getPerPage(): int
    {
        return $this->paginationData['per_page'] ?? count($this->items);
    }

    /**
     * Get total number of items
     */
    public function getTotal(): int
    {
        return $this->paginationData['total'] ?? count($this->items);
    }

    /**
     * Get last page number
     */
    public function getLastPage(): int
    {
        return $this->paginationData['last_page'] ?? $this->getCurrentPage();
    }

    /**
     * Check if there's a next page
     */
    public function hasNextPage(): bool
    {
        return $this->getCurrentPage() < $this->getLastPage();
    }

    /**
     * Check if there's a previous page
     */
    public function hasPreviousPage(): bool
    {
        return $this->getCurrentPage() > 1;
    }
}
