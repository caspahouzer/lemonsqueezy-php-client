<?php

namespace LemonSqueezy\Model\Entities;

use LemonSqueezy\Model\AbstractModel;

class OrderItems extends AbstractModel
{
    /**
     * Get the entity type identifier
     *
     * @return string The entity type ('order-items')
     * @since 1.0.0
     */
    public function getType(): string
    {
        return 'order-items';
    }
}
