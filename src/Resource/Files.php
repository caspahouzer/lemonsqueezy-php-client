<?php

namespace LemonSqueezy\Resource;

use LemonSqueezy\Model\Entities\Files as FilesEntity;
use LemonSqueezy\Model\AbstractModel;
use LemonSqueezy\Exception\UnsupportedOperationException;

class Files extends AbstractResource
{
    public function getEndpoint(): string
    {
        return 'files';
    }

    public function getModelClass(): string
    {
        return FilesEntity::class;
    }

    /**
     * Create operation not supported for Files resource
     *
     * @throws UnsupportedOperationException
     */
    public function create(array $data, array $options = []): AbstractModel
    {
        throw new UnsupportedOperationException('files', 'create');
    }

    /**
     * Update operation not supported for Files resource
     *
     * @throws UnsupportedOperationException
     */
    public function update(string $id, array $data, array $options = []): AbstractModel
    {
        throw new UnsupportedOperationException('files', 'update');
    }

    /**
     * Delete operation not supported for Files resource
     *
     * @throws UnsupportedOperationException
     */
    public function delete(string $id, array $options = []): bool
    {
        throw new UnsupportedOperationException('files', 'delete');
    }
}
