<?php

namespace LemonSqueezy\Resource;

use LemonSqueezy\Model\Entities\Checkouts as CheckoutsEntity;
use LemonSqueezy\Model\AbstractModel;
use LemonSqueezy\Exception\UnsupportedOperationException;

class Checkouts extends AbstractResource
{
    public function getEndpoint(): string
    {
        return 'checkouts';
    }

    public function getModelClass(): string
    {
        return CheckoutsEntity::class;
    }

    /**
     * Update operation not supported for Checkouts resource
     *
     * @throws UnsupportedOperationException
     */
    public function update(string $id, array $data, array $options = []): AbstractModel
    {
        throw new UnsupportedOperationException('checkouts', 'update');
    }

    /**
     * Delete operation not supported for Checkouts resource
     *
     * @throws UnsupportedOperationException
     */
    public function delete(string $id, array $options = []): bool
    {
        throw new UnsupportedOperationException('checkouts', 'delete');
    }
}