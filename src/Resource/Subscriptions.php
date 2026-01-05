<?php

namespace LemonSqueezy\Resource;

use LemonSqueezy\Model\Entities\Subscriptions as SubscriptionsEntity;
use LemonSqueezy\Model\AbstractModel;
use LemonSqueezy\Exception\UnsupportedOperationException;

class Subscriptions extends AbstractResource
{
    public function getEndpoint(): string
    {
        return 'subscriptions';
    }

    public function getModelClass(): string
    {
        return SubscriptionsEntity::class;
    }

    /**
     * Create operation not supported for Subscriptions resource
     *
     * @throws UnsupportedOperationException
     */
    public function create(array $data, array $options = []): AbstractModel
    {
        throw new UnsupportedOperationException('subscriptions', 'create');
    }

    /**
     * Delete operation not supported for Subscriptions resource
     *
     * @throws UnsupportedOperationException
     */
    public function delete(string $id, array $options = []): bool
    {
        throw new UnsupportedOperationException('subscriptions', 'delete');
    }
}