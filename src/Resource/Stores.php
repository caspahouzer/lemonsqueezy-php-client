<?php

namespace LemonSqueezy\Resource;

use LemonSqueezy\Model\Entities\Stores as StoresEntity;
use LemonSqueezy\Model\AbstractModel;
use LemonSqueezy\Exception\UnsupportedOperationException;

class Stores extends AbstractResource
{
    public function getEndpoint(): string
    {
        return 'stores';
    }

    public function getModelClass(): string
    {
        return StoresEntity::class;
    }

    /**
     * Create operation not supported for Stores resource
     *
     * @throws UnsupportedOperationException
     */
    public function create(array $data, array $options = []): AbstractModel
    {
        throw new UnsupportedOperationException('stores', 'create');
    }

    /**
     * Update operation not supported for Stores resource
     *
     * @throws UnsupportedOperationException
     */
    public function update(string $id, array $data, array $options = []): AbstractModel
    {
        throw new UnsupportedOperationException('stores', 'update');
    }

    /**
     * Delete operation not supported for Stores resource
     *
     * @throws UnsupportedOperationException
     */
    public function delete(string $id, array $options = []): bool
    {
        throw new UnsupportedOperationException('stores', 'delete');
    }
}