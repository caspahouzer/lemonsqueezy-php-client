<?php

namespace LemonSqueezy\Resource;

use LemonSqueezy\Model\Entities\Subscriptions as SubscriptionsEntity;

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
}