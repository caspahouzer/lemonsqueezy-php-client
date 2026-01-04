<?php

namespace LemonSqueezy\Resource;

use LemonSqueezy\Model\Entities\Prices as PricesEntity;

class Prices extends AbstractResource
{
    public function getEndpoint(): string
    {
        return 'prices';
    }

    public function getModelClass(): string
    {
        return PricesEntity::class;
    }
}