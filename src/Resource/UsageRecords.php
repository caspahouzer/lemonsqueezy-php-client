<?php

namespace LemonSqueezy\Resource;

use LemonSqueezy\Model\Entities\UsageRecords as UsageRecordsEntity;
use LemonSqueezy\Model\AbstractModel;
use LemonSqueezy\Exception\UnsupportedOperationException;

class UsageRecords extends AbstractResource
{
    public function getEndpoint(): string
    {
        return 'usage-records';
    }

    public function getModelClass(): string
    {
        return UsageRecordsEntity::class;
    }

    /**
     * Update operation not supported for UsageRecords resource
     *
     * @throws UnsupportedOperationException
     */
    public function update(string $id, array $data, array $options = []): AbstractModel
    {
        throw new UnsupportedOperationException('usage-records', 'update');
    }

    /**
     * Delete operation not supported for UsageRecords resource
     *
     * @throws UnsupportedOperationException
     */
    public function delete(string $id, array $options = []): bool
    {
        throw new UnsupportedOperationException('usage-records', 'delete');
    }
}
