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
        $payload = $this->buildJsonApiPayload($data);

        $response = $this->client->request('POST', $endpoint, $payload);

        return $this->hydrateModel($response['data'] ?? $response);
    }

    /**
     * Update an existing resource
     */
    public function update(string $id, array $data, array $options = []): AbstractModel
    {
        $endpoint = $this->getEndpoint() . '/' . urlencode($id);
        $payload = $this->buildJsonApiPayload(array_merge(['id' => $id], $data));

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

    /**
     * Build a proper JSON:API formatted payload
     *
     * Converts data with relationship IDs (e.g., store_id) into proper JSON:API structure
     * with separate attributes and relationships sections.
     *
     * @param array $data Input data with attributes and relationship IDs
     * @return array JSON:API formatted payload
     */
    protected function buildJsonApiPayload(array $data): array
    {
        // Get the resource type from the endpoint
        $type = $this->getEndpoint();

        $attributes = [];
        $relationships = [];

        // Separate attributes from relationships
        // Convention: fields ending with '_id' are relationships
        foreach ($data as $key => $value) {
            if ($key === 'id') {
                // 'id' goes into the data object directly, not attributes
                continue;
            }

            // Convert snake_case _id fields to relationships
            if (str_ends_with($key, '_id')) {
                $relationshipName = substr($key, 0, -3); // Remove '_id' suffix
                $relationships[$relationshipName] = [
                    'data' => [
                        'type' => $this->getRelationshipType($relationshipName),
                        'id' => (string) $value,
                    ]
                ];
            } else {
                // Everything else is an attribute
                $attributes[$key] = $value;
            }
        }

        // Build JSON:API structure
        $payload = [
            'data' => [
                'type' => $type,
                'attributes' => $attributes,
            ]
        ];

        // Add ID if present (for PATCH requests)
        if (isset($data['id'])) {
            $payload['data']['id'] = $data['id'];
        }

        // Add relationships if any were found
        if (!empty($relationships)) {
            $payload['data']['relationships'] = $relationships;
        }

        return $payload;
    }

    /**
     * Get the relationship type from a relationship name
     *
     * Converts singular names to plural API resource types
     * (e.g., 'store' → 'stores', 'product' → 'products')
     *
     * @param string $relationshipName The relationship name (e.g., 'store', 'product')
     * @return string The API resource type (e.g., 'stores', 'products')
     */
    protected function getRelationshipType(string $relationshipName): string
    {
        // Common pluralization rules
        $irregular = [
            'store' => 'stores',
            'product' => 'products',
            'variant' => 'variants',
            'price' => 'prices',
            'file' => 'files',
            'customer' => 'customers',
            'order' => 'orders',
            'subscription' => 'subscriptions',
            'discount' => 'discounts',
            'webhook' => 'webhooks',
            'checkout' => 'checkouts',
            'affiliate' => 'affiliates',
            'user' => 'users',
        ];

        return $irregular[$relationshipName] ?? $relationshipName . 's';
    }
}
