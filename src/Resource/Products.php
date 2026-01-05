<?php

namespace LemonSqueezy\Resource;

use LemonSqueezy\Model\Entities\Products as ProductsEntity;
use LemonSqueezy\Model\AbstractModel;
use LemonSqueezy\Exception\UnsupportedOperationException;

class Products extends AbstractResource
{
    public function getEndpoint(): string
    {
        return 'products';
    }

    public function getModelClass(): string
    {
        return ProductsEntity::class;
    }

    /**
     * Create operation not supported for Products resource
     *
     * @throws UnsupportedOperationException
     */
    public function create(array $data, array $options = []): AbstractModel
    {
        throw new UnsupportedOperationException('products', 'create');
    }

    /**
     * Update operation not supported for Products resource
     *
     * @throws UnsupportedOperationException
     */
    public function update(string $id, array $data, array $options = []): AbstractModel
    {
        throw new UnsupportedOperationException('products', 'update');
    }

    /**
     * Delete operation not supported for Products resource
     *
     * @throws UnsupportedOperationException
     */
    public function delete(string $id, array $options = []): bool
    {
        throw new UnsupportedOperationException('products', 'delete');
    }
}