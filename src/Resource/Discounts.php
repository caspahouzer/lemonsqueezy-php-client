<?php

namespace LemonSqueezy\Resource;

use LemonSqueezy\Model\Entities\Discounts as DiscountsEntity;

class Discounts extends AbstractResource
{
    public function getEndpoint(): string
    {
        return 'discounts';
    }

    public function getModelClass(): string
    {
        return DiscountsEntity::class;
    }
}
