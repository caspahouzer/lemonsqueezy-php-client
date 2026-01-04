<?php

namespace LemonSqueezy\Resource;

use LemonSqueezy\Model\Entities\Orders as OrdersEntity;

class Orders extends AbstractResource
{
    public function getEndpoint(): string
    {
        return 'orders';
    }

    public function getModelClass(): string
    {
        return OrdersEntity::class;
    }
}