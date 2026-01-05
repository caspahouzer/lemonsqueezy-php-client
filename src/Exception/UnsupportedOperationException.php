<?php

namespace LemonSqueezy\Exception;

/**
 * Exception thrown when an unsupported operation is attempted on a resource
 *
 * The LemonSqueezy API has limitations on which operations each resource supports.
 * This exception is thrown when trying to perform an operation (create, update, delete)
 * that the API doesn't support for the target resource.
 *
 * @see https://docs.lemonsqueezy.com/api
 */
class UnsupportedOperationException extends LemonSqueezyException
{
    /**
     * Create a new unsupported operation exception
     *
     * @param string $resource The resource name (e.g., 'products')
     * @param string $operation The operation name (e.g., 'create', 'update', 'delete')
     * @param ?\Throwable $previous The previous exception
     */
    public function __construct(
        string $resource,
        string $operation,
        ?\Throwable $previous = null
    ) {
        $message = "Operation '{$operation}' is not supported by the LemonSqueezy API for the '{$resource}' resource.";
        parent::__construct($message, 0, null, $previous);
    }
}
