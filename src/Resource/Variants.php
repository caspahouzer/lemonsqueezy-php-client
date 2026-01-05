<?php

namespace LemonSqueezy\Resource;

use LemonSqueezy\Model\Entities\Variants as VariantsEntity;
use LemonSqueezy\Model\AbstractModel;
use LemonSqueezy\Exception\UnsupportedOperationException;

class Variants extends AbstractResource
{
    public function getEndpoint(): string
    {
        return 'variants';
    }

    public function getModelClass(): string
    {
        return VariantsEntity::class;
    }

    /**
     * Create operation not supported for Variants resource
     *
     * @throws UnsupportedOperationException
     */
    public function create(array $data, array $options = []): AbstractModel
    {
        throw new UnsupportedOperationException('variants', 'create');
    }

    /**
     * Update operation not supported for Variants resource
     *
     * @throws UnsupportedOperationException
     */
    public function update(string $id, array $data, array $options = []): AbstractModel
    {
        throw new UnsupportedOperationException('variants', 'update');
    }

    /**
     * Delete operation not supported for Variants resource
     *
     * @throws UnsupportedOperationException
     */
    public function delete(string $id, array $options = []): bool
    {
        throw new UnsupportedOperationException('variants', 'delete');
    }
}
