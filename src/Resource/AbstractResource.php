<?php

namespace LemonSqueezy\Resource;

use LemonSqueezy\Client;
use LemonSqueezy\Model\Collection;
use LemonSqueezy\Model\AbstractModel;
use LemonSqueezy\Query\QueryBuilder;

/**
 * Base resource class providing common CRUD operations
 */
abstract class AbstractResource implements ResourceInterface
{
    /**
     * The endpoint path (e.g., 'users', 'customers', etc.)
     */
    abstract public function getEndpoint(): string;

    /**
     * The model class name for this resource
     */
    abstract protected function getModelClass(): string;

    public function __construct(protected Client $client)
    {
    }

    /**
     * List all resources
     */
    public function list(?QueryBuilder $query = null, array $options = []): Collection
    {
        $endpoint = $this->getEndpoint();
        $queryString = $query ? $this->buildQueryString($query) : '';

        $response = $this->client->request('GET', $endpoint . $queryString);

        return $this->hydrateCollection($response);
    }

    /**
     * Get a single resource by ID
     */
    public function get(string $id, array $options = []): AbstractModel
    {
        $endpoint = $this->getEndpoint() . '/' . urlencode($id);
        $response = $this->client->request('GET', $endpoint);

        return $this->hydrateModel($response['data'] ?? $response);
    }

    /**
     * Create a new resource
     */
    public function create(array $data, array $options = []): AbstractModel
    {
        $endpoint = $this->getEndpoint();
        $payload = ['data' => $data];

        $response = $this->client->request('POST', $endpoint, $payload);

        return $this->hydrateModel($response['data'] ?? $response);
    }

    /**
     * Update an existing resource
     */
    public function update(string $id, array $data, array $options = []): AbstractModel
    {
        $endpoint = $this->getEndpoint() . '/' . urlencode($id);
        $payload = ['data' => array_merge(['id' => $id], $data)];

        $response = $this->client->request('PATCH', $endpoint, $payload);

        return $this->hydrateModel($response['data'] ?? $response);
    }

    /**
     * Delete a resource
     */
    public function delete(string $id, array $options = []): bool
    {
        $endpoint = $this->getEndpoint() . '/' . urlencode($id);
        $this->client->request('DELETE', $endpoint);

        return true;
    }

    /**
     * Build query string from QueryBuilder
     */
    protected function buildQueryString(?QueryBuilder $query): string
    {
        if (!$query) {
            return '';
        }

        $params = [];

        // Pagination
        if ($query->hasPage()) {
            $params['page[number]'] = $query->getPage();
        }
        if ($query->hasPageSize()) {
            $params['page[size]'] = $query->getPageSize();
        }

        // Filters
        foreach ($query->getFilters() as $filter) {
            $params['filter[' . $filter->getKey() . ']'] = $filter->getValue();
        }

        // Sorting
        foreach ($query->getSorts() as $sort) {
            $params['sort'] = ($params['sort'] ?? '') . ($params['sort'] ? ',' : '') . $sort->getField();
        }

        // Includes
        if ($query->getIncludes()) {
            $params['include'] = implode(',', $query->getIncludes());
        }

        if (empty($params)) {
            return '';
        }

        return '?' . http_build_query($params);
    }

    /**
     * Hydrate a single model from response data
     */
    protected function hydrateModel(array $data): AbstractModel
    {
        $modelClass = $this->getModelClass();

        return new $modelClass($data);
    }

    /**
     * Hydrate a collection from response data
     */
    protected function hydrateCollection(array $response): Collection
    {
        $items = [];

        if (isset($response['data'])) {
            $data = $response['data'];

            if (is_array($data)) {
                foreach ($data as $item) {
                    $items[] = $this->hydrateModel($item);
                }
            }
        }

        $pagination = null;
        if (isset($response['meta']['page'])) {
            // Parse pagination data from LemonSqueezy API format
            $paginationData = $response['meta']['page'];
            $pagination = [
                'total' => $paginationData['total'] ?? 0,
                'page' => $paginationData['currentPage'] ?? 1,
                'per_page' => $paginationData['perPage'] ?? 50,
                'last_page' => $paginationData['lastPage'] ?? 1,
            ];
        }

        return new Collection($items, $pagination);
    }
}
