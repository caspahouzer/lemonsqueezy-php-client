<?php

namespace LemonSqueezy\Resource;

use LemonSqueezy\Model\Collection;
use LemonSqueezy\Model\AbstractModel;
use LemonSqueezy\Query\QueryBuilder;

/**
 * Contract for API resources
 */
interface ResourceInterface
{
    /**
     * List all resources with optional filtering, sorting, pagination
     *
     * @param ?QueryBuilder $query Optional query builder for filtering/sorting
     * @param array $options Additional options
     * @return Collection Collection of resources
     */
    public function list(?QueryBuilder $query = null, array $options = []): Collection;

    /**
     * Get a single resource by ID
     *
     * @param string $id The resource ID
     * @param array $options Additional options
     * @return AbstractModel The resource
     */
    public function get(string $id, array $options = []): AbstractModel;

    /**
     * Create a new resource
     *
     * @param array $data Resource data
     * @param array $options Additional options
     * @return AbstractModel The created resource
     */
    public function create(array $data, array $options = []): AbstractModel;

    /**
     * Update an existing resource
     *
     * @param string $id The resource ID
     * @param array $data Resource data to update
     * @param array $options Additional options
     * @return AbstractModel The updated resource
     */
    public function update(string $id, array $data, array $options = []): AbstractModel;

    /**
     * Delete a resource
     *
     * @param string $id The resource ID
     * @param array $options Additional options
     * @return bool True if successful
     */
    public function delete(string $id, array $options = []): bool;

    /**
     * Get the endpoint path for this resource
     */
    public function getEndpoint(): string;
}
