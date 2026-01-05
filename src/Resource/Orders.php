<?php

namespace LemonSqueezy\Resource;

use LemonSqueezy\Model\Entities\Orders as OrdersEntity;
use LemonSqueezy\Model\AbstractModel;
use LemonSqueezy\Exception\UnsupportedOperationException;

class Orders extends AbstractResource
{
    public function getEndpoint(): string
    {
        return 'orders';
    }

    public function getModelClass(): string
    {
        return OrdersEntity::class;
    }

    /**
     * Create operation not supported for Orders resource
     *
     * @throws UnsupportedOperationException
     */
    public function create(array $data, array $options = []): AbstractModel
    {
        throw new UnsupportedOperationException('orders', 'create');
    }

    /**
     * Update operation not supported for Orders resource
     *
     * @throws UnsupportedOperationException
     */
    public function update(string $id, array $data, array $options = []): AbstractModel
    {
        throw new UnsupportedOperationException('orders', 'update');
    }

    /**
     * Delete operation not supported for Orders resource
     *
     * @throws UnsupportedOperationException
     */
    public function delete(string $id, array $options = []): bool
    {
        throw new UnsupportedOperationException('orders', 'delete');
    }
}