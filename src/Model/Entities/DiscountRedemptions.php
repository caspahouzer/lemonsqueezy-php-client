<?php

namespace LemonSqueezy\Model\Entities;

use LemonSqueezy\Model\AbstractModel;

class DiscountRedemptions extends AbstractModel
{
    public function getType(): string
    {
        return 'discount-redemptions';
    }
}