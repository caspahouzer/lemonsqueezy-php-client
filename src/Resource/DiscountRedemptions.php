<?php

namespace LemonSqueezy\Resource;

use LemonSqueezy\Model\Entities\DiscountRedemptions as DiscountRedemptionsEntity;

class DiscountRedemptions extends AbstractResource
{
    public function getEndpoint(): string
    {
        return 'discount-redemptions';
    }

    public function getModelClass(): string
    {
        return DiscountRedemptionsEntity::class;
    }
}