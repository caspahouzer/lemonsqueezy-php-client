<?php

namespace LemonSqueezy\Batch;

use LemonSqueezy\Client;
use LemonSqueezy\Batch\Operations\BatchOperation;
use LemonSqueezy\Batch\Configuration\BatchConfig;
use LemonSqueezy\Exception\BatchException;
use LemonSqueezy\Exception\LemonSqueezyException;

/**
 * Executes batch operations sequentially
 *
 * This executor takes a list of operations and executes them one by one
 * using the client, respecting rate limits and handling errors.
 */
class BatchOperationExecutor
{
    /**
     * Create a new batch operation executor
     *
     * @param Client $client The API client to use for operations
     */
    public function __construct(
        private Client $client
    ) {}

    /**
     * Execute a batch of operations
     *
     * @param array $operations Array of BatchOperation objects
     * @param array $options Execution options
     * @return BatchResult The result of the batch execution
     * @throws BatchException If critical error occurs and stopOnError is true
     */
    public function execute(array $operations, array $options = []): BatchResult
    {
        // Validate and merge options
        BatchConfig::validate($options);
        $options = BatchConfig::mergeWithDefaults($options);

        // Validate operations
        if (empty($operations)) {
            throw new \InvalidArgumentException('Operations array cannot be empty');
        }

        $this->validateOperations($operations);

        // Create result container
        $result = new BatchResult();
        $startTime = microtime(true);

        try {
            // Execute each operation sequentially
            foreach ($operations as $operation) {
                if (!($operation instanceof BatchOperation)) {
                    throw new \InvalidArgumentException(
                        'All items must be instances of BatchOperation'
                    );
                }

                try {
                    $this->executeOperation($operation, $result);

                    // Apply rate limiting delay
                    $this->applyRateLimit($options['delayMs']);

                } catch (LemonSqueezyException $e) {
                    // Handle operation failure
                    $this->handleOperationError(
                        $operation,
                        $e,
                        $result,
                        $options['stopOnError']
                    );

                    // Stop processing if stopOnError is true
                    if ($options['stopOnError']) {
                        throw new BatchException(
                            "Batch operation failed: {$e->getMessage()}",
                            $e->getCode() ?: 0,
                            $result,
                            $e->getResponse(),
                            $e
                        );
                    }
                } catch (\Exception $e) {
                    // Handle non-LemonSqueezy exceptions (network, etc.)
                    $this->handleOperationError(
                        $operation,
                        $e,
                        $result,
                        $options['stopOnError']
                    );

                    if ($options['stopOnError']) {
                        throw new BatchException(
                            "Batch operation failed: {$e->getMessage()}",
                            $e->getCode() ?: 0,
                            $result,
                            null,
                            $e
                        );
                    }
                }
            }

        } finally {
            // Always record execution time
            $executionTime = microtime(true) - $startTime;
            $result->setExecutionTime($executionTime);
        }

        return $result;
    }

    /**
     * Execute a single operation
     *
     * @param BatchOperation $operation The operation to execute
     * @param BatchResult $result The result container to add success to
     * @throws LemonSqueezyException If the operation fails
     */
    private function executeOperation(BatchOperation $operation, BatchResult $result): void
    {
        // Get resource by name
        $resourceName = $operation->getResource();

        // Convert resource name to method name (e.g., 'products' -> 'products')
        $resourceMethod = $this->getResourceMethod($resourceName);

        // Get the resource from the client
        if (!method_exists($this->client, $resourceMethod)) {
            throw new \RuntimeException(
                "Resource '{$resourceName}' is not available in the client"
            );
        }

        $resource = $this->client->{$resourceMethod}();

        // Execute the appropriate operation method
        $method = strtolower($operation->getOperationType());

        if (!method_exists($resource, $method)) {
            throw new \RuntimeException(
                "Operation type '{$operation->getOperationType()}' is not supported for resource '{$resourceName}'"
            );
        }

        // Call the operation method on the resource
        switch ($operation->getOperationType()) {
            case 'create':
                $operationResult = $resource->create($operation->getData());
                $statusCode = 201;
                break;

            case 'update':
                $operationResult = $resource->update(
                    $operation->getId(),
                    $operation->getData()
                );
                $statusCode = 200;
                break;

            case 'delete':
                $operationResult = $resource->delete($operation->getId());
                $statusCode = 204;
                break;

            default:
                throw new \RuntimeException(
                    "Unknown operation type: {$operation->getOperationType()}"
                );
        }

        // Add to successful results
        $result->addSuccess($operation, $operationResult, $statusCode);
    }

    /**
     * Handle an operation error
     *
     * @param BatchOperation $operation The operation that failed
     * @param \Throwable $exception The exception that was thrown
     * @param BatchResult $result The result container to add failure to
     * @param bool $stopOnError Whether this is a critical error
     */
    private function handleOperationError(
        BatchOperation $operation,
        \Throwable $exception,
        BatchResult $result,
        bool $stopOnError
    ): void {
        $statusCode = 400;
        $errorDetails = null;

        // Extract status code and error details if available
        if ($exception instanceof LemonSqueezyException) {
            $response = $exception->getResponse();
            if ($response && isset($response['status'])) {
                $statusCode = $response['status'];
            }
            $errorDetails = $response;
        }

        // Add to failed results
        $result->addFailure(
            $operation,
            $exception->getMessage(),
            $statusCode,
            $errorDetails
        );
    }

    /**
     * Validate that all items are BatchOperation instances
     *
     * @param array $operations The operations to validate
     * @throws \InvalidArgumentException If validation fails
     */
    private function validateOperations(array $operations): void
    {
        foreach ($operations as $index => $operation) {
            if (!($operation instanceof BatchOperation)) {
                throw new \InvalidArgumentException(
                    "Operation at index {$index} is not a BatchOperation instance"
                );
            }
        }
    }

    /**
     * Apply rate limiting delay
     *
     * @param int $delayMs Delay in milliseconds
     */
    private function applyRateLimit(int $delayMs): void
    {
        if ($delayMs > 0) {
            usleep($delayMs * 1000);
        }
    }

    /**
     * Convert resource name to client method name
     *
     * Converts plural resource names to their corresponding client methods:
     * - 'products' -> 'products'
     * - 'customers' -> 'customers'
     * - 'order-items' -> 'orderItems'
     * - 'subscription-invoices' -> 'subscriptionInvoices'
     *
     * @param string $resourceName The resource name
     * @return string The method name on the client
     */
    private function getResourceMethod(string $resourceName): string
    {
        // Handle hyphenated names (e.g., 'order-items' -> 'orderItems')
        if (strpos($resourceName, '-') !== false) {
            return lcfirst(str_replace(' ', '', ucwords(str_replace('-', ' ', $resourceName))));
        }

        return $resourceName;
    }
}
