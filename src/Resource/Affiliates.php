<?php

namespace LemonSqueezy\Resource;

use LemonSqueezy\Model\Entities\Affiliates as AffiliatesEntity;
use LemonSqueezy\Model\AbstractModel;
use LemonSqueezy\Exception\UnsupportedOperationException;

class Affiliates extends AbstractResource
{
    public function getEndpoint(): string
    {
        return 'affiliates';
    }

    public function getModelClass(): string
    {
        return AffiliatesEntity::class;
    }

    /**
     * Create operation not supported for Affiliates resource
     *
     * @throws UnsupportedOperationException
     */
    public function create(array $data, array $options = []): AbstractModel
    {
        throw new UnsupportedOperationException('affiliates', 'create');
    }

    /**
     * Update operation not supported for Affiliates resource
     *
     * @throws UnsupportedOperationException
     */
    public function update(string $id, array $data, array $options = []): AbstractModel
    {
        throw new UnsupportedOperationException('affiliates', 'update');
    }

    /**
     * Delete operation not supported for Affiliates resource
     *
     * @throws UnsupportedOperationException
     */
    public function delete(string $id, array $options = []): bool
    {
        throw new UnsupportedOperationException('affiliates', 'delete');
    }
}
