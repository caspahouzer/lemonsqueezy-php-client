<?php

namespace LemonSqueezy\Resource;

use LemonSqueezy\Model\Entities\SubscriptionItems as SubscriptionItemsEntity;

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
}