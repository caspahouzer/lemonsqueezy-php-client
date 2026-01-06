<?php

namespace LemonSqueezy\Model\Entities;

use LemonSqueezy\Model\AbstractModel;

class DiscountRedemptions extends AbstractModel
{
    /**
     * Get the entity type identifier
     *
     * @return string The entity type ('discount-redemptions')
     * @since 1.0.0
     */
    public function getType(): string
    {
        return 'discount-redemptions';
    }
}
