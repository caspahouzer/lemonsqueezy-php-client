<?php

namespace LemonSqueezy\Resource;

use LemonSqueezy\Model\Entities\OrderItems as OrderItemsEntity;
use LemonSqueezy\Model\AbstractModel;
use LemonSqueezy\Exception\UnsupportedOperationException;

class OrderItems extends AbstractResource
{
    public function getEndpoint(): string
    {
        return 'order-items';
    }

    public function getModelClass(): string
    {
        return OrderItemsEntity::class;
    }

    /**
     * Create operation not supported for OrderItems resource
     *
     * @throws UnsupportedOperationException
     */
    public function create(array $data, array $options = []): AbstractModel
    {
        throw new UnsupportedOperationException('order-items', 'create');
    }

    /**
     * Update operation not supported for OrderItems resource
     *
     * @throws UnsupportedOperationException
     */
    public function update(string $id, array $data, array $options = []): AbstractModel
    {
        throw new UnsupportedOperationException('order-items', 'update');
    }

    /**
     * Delete operation not supported for OrderItems resource
     *
     * @throws UnsupportedOperationException
     */
    public function delete(string $id, array $options = []): bool
    {
        throw new UnsupportedOperationException('order-items', 'delete');
    }
}
