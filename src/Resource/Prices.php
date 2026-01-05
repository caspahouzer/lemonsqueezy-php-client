<?php

namespace LemonSqueezy\Resource;

use LemonSqueezy\Model\Entities\Prices as PricesEntity;
use LemonSqueezy\Model\AbstractModel;
use LemonSqueezy\Exception\UnsupportedOperationException;

class Prices extends AbstractResource
{
    public function getEndpoint(): string
    {
        return 'prices';
    }

    public function getModelClass(): string
    {
        return PricesEntity::class;
    }

    /**
     * Create operation not supported for Prices resource
     *
     * @throws UnsupportedOperationException
     */
    public function create(array $data, array $options = []): AbstractModel
    {
        throw new UnsupportedOperationException('prices', 'create');
    }

    /**
     * Update operation not supported for Prices resource
     *
     * @throws UnsupportedOperationException
     */
    public function update(string $id, array $data, array $options = []): AbstractModel
    {
        throw new UnsupportedOperationException('prices', 'update');
    }

    /**
     * Delete operation not supported for Prices resource
     *
     * @throws UnsupportedOperationException
     */
    public function delete(string $id, array $options = []): bool
    {
        throw new UnsupportedOperationException('prices', 'delete');
    }
}
