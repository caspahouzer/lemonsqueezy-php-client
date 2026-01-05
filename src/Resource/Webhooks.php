<?php

namespace LemonSqueezy\Resource;

use LemonSqueezy\Model\Entities\Webhooks as WebhooksEntity;

class Webhooks extends AbstractResource
{
    public function getEndpoint(): string
    {
        return 'webhooks';
    }

    public function getModelClass(): string
    {
        return WebhooksEntity::class;
    }
}
