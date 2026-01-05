<?php

namespace LemonSqueezy\Resource;

use LemonSqueezy\Model\Entities\Customer;
use LemonSqueezy\Exception\UnsupportedOperationException;

class Customers extends AbstractResource
{
    public function getEndpoint(): string
    {
        return 'customers';
    }

    public function getModelClass(): string
    {
        return Customer::class;
    }

    /**
     * Delete operation not supported for Customers resource
     *
     * @throws UnsupportedOperationException
     */
    public function delete(string $id, array $options = []): bool
    {
        throw new UnsupportedOperationException('customers', 'delete');
    }
}
