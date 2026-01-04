<?php

namespace LemonSqueezy\Model\Pagination;

/**
 * Pagination metadata
 */
class Pagination
{
    public function __construct(
        private int $currentPage,
        private int $pageSize,
        private int $total,
        private int $lastPage
    ) {
    }

    public function getCurrentPage(): int
    {
        return $this->currentPage;
    }

    public function getPageSize(): int
    {
        return $this->pageSize;
    }

    public function getTotal(): int
    {
        return $this->total;
    }

    public function getLastPage(): int
    {
        return $this->lastPage;
    }

    public function hasNextPage(): bool
    {
        return $this->currentPage < $this->lastPage;
    }

    public function hasPreviousPage(): bool
    {
        return $this->currentPage > 1;
    }
}
