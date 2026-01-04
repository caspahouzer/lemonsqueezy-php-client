<?php

namespace LemonSqueezy\Resource;

use LemonSqueezy\Model\Entities\OrderItems as OrderItemsEntity;

class OrderItems extends AbstractResource
{
    public function getEndpoint(): string
    {
        return 'order-items';
    }

    public function getModelClass(): string
    {
        return OrderItemsEntity::class;
    }
}