<?php

namespace LemonSqueezy\Resource;

use LemonSqueezy\Model\Entities\SubscriptionItems as SubscriptionItemsEntity;
use LemonSqueezy\Model\AbstractModel;
use LemonSqueezy\Exception\UnsupportedOperationException;

class SubscriptionItems extends AbstractResource
{
    public function getEndpoint(): string
    {
        return 'subscription-items';
    }

    public function getModelClass(): string
    {
        return SubscriptionItemsEntity::class;
    }

    /**
     * Create operation not supported for Subscription Items resource
     *
     * @throws UnsupportedOperationException
     */
    public function create(array $data, array $options = []): AbstractModel
    {
        throw new UnsupportedOperationException('subscription-items', 'create');
    }

    /**
     * Delete operation not supported for Subscription Items resource
     *
     * @throws UnsupportedOperationException
     */
    public function delete(string $id, array $options = []): bool
    {
        throw new UnsupportedOperationException('subscription-items', 'delete');
    }
}